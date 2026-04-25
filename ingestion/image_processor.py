import base64
import mimetypes
from pathlib import Path

import requests


class ImageTooBlurryError(Exception):
    """Raised when an image fails any quality check (blur, resolution, repetitive content)."""

    _MESSAGES: dict = {
        "blurry": (
            "L'image téléversée est trop floue pour être traitée. "
            "Veuillez téléverser une version plus nette."
        ),
        "low_resolution": (
            "L'image téléversée a une résolution trop faible. "
            "Veuillez téléverser une image d'au moins 100 × 100 pixels."
        ),
        "repetitive": (
            "L'image téléversée ne contient pas de contenu analysable de façon fiable. "
            "Veuillez téléverser une image claire avec un contenu lisible et non répétitif."
        ),
        "low_information": (
            "L'image téléversée semble vide ou ne contient pas d'information utile. "
            "Veuillez téléverser une image avec un contenu visible."
        ),
    }

    def __init__(
        self,
        variance: float = 0.0,
        threshold: float = 0.0,
        reason: str = "blurry",
    ) -> None:
        self.variance = variance
        self.threshold = threshold
        self.reason = reason
        super().__init__(self._MESSAGES.get(reason, self._MESSAGES["blurry"]))


class VLMImageProcessor:
    """Convert image files to descriptive text using a Vision Language Model."""

    DEFAULT_VLM_URL = "http://192.168.100.67:1234"
    DEFAULT_VLM_MODEL = "google/gemma-4-e2b"

    PROMPT = (
        "You are an AI assistant helping to build a knowledge base. "
        "Describe this image in rich detail so that the description can be indexed and retrieved later. "
        "Include: all visible text (transcribe it exactly), objects, people, diagrams, charts, tables, "
        "colors, spatial layout, and any other relevant information. "
        "Be thorough and precise. Do not add commentary — only describe what is shown."
    )

    VLM_BLUR_CHECK_PROMPT = (
        "Carefully inspect every pixel of this image, including all corners, edges, and background areas. "
        "Does ANY part of this image appear blurry, out-of-focus, hazy, noisy, grainy, pixelated, "
        "low-resolution, or unclear in any way? Even slight or partial blur counts. "
        "Answer with only YES or NO."
    )

    BLUR_INDICATORS: frozenset = frozenset({
        "blurr", "out-of-focus", "out of focus", "hazy", "noisy", "noise",
        "grainy", "grain", "pixelated", "low-resolution", "low resolution",
        "indistinct", "illegible", "soft focus", "fuzzy", "distorted",
        "artifact", "blurred", "blurry",
    })

    @staticmethod
    def description_has_blur(description: str) -> bool:
        """Return True if the description contains blur/noise/quality indicators.

        Used as a fallback: if the VLM's own description of the image mentions
        that the image is blurry or noisy, the image should be rejected.
        """
        lower = description.lower()
        return any(indicator in lower for indicator in VLMImageProcessor.BLUR_INDICATORS)

    def __init__(self, base_url: str = DEFAULT_VLM_URL, model: str = DEFAULT_VLM_MODEL) -> None:
        self.base_url = base_url.rstrip("/")
        self.model = model

    @staticmethod
    def check_blur(path: Path, threshold: float = 500.0) -> None:
        """Check image sharpness via Laplacian variance.

        Raises :class:`ImageTooBlurryError` if the computed variance falls below
        *threshold*, indicating the image is too blurry for reliable OCR.
        """
        import numpy as np
        from PIL import Image  # type: ignore[import-untyped]

        img = Image.open(path).convert("L")
        arr = np.array(img, dtype=np.float32)

        # Laplacian applied via explicit neighbor sums (no extra dependency).
        padded = np.pad(arr, 1, mode="edge")
        lap = (
            padded[1:-1, 1:-1] * (-4.0)
            + padded[0:-2, 1:-1]   # top
            + padded[2:,   1:-1]   # bottom
            + padded[1:-1, 0:-2]   # left
            + padded[1:-1, 2:]     # right
        )
        variance = float(np.var(lap))
        if variance < threshold:
            raise ImageTooBlurryError(variance, threshold, reason="blurry")

    @staticmethod
    def check_resolution(path: Path, min_pixels: int = 10_000) -> None:
        """Reject images whose total pixel count is below *min_pixels*.

        Very small images (e.g. thumbnails, icons) cannot contain enough
        information for reliable VLM description and should be rejected early.
        Raises :class:`ImageTooBlurryError` with reason ``"low_resolution"``.
        """
        from PIL import Image  # type: ignore[import-untyped]

        with Image.open(path) as img:
            w, h = img.size
        if w * h < min_pixels:
            raise ImageTooBlurryError(
                variance=float(w * h),
                threshold=float(min_pixels),
                reason="low_resolution",
            )

    @staticmethod
    def description_is_repetitive(
        description: str,
        max_repeat_ratio: float = 0.35,
        min_sentences: int = 5,
        max_absolute_repeats: int = 3,
    ) -> bool:
        """Return True if *description* contains heavily repeated content.

        This detects VLM hallucination loops where the model emits the same
        sentence (or near-identical phrasing) over and over, which happens when
        the source image contains unreadable, fake, or Lorem-ipsum-style text.

        Detection strategy:
        - Split the description into sentences (on .!? boundaries).
        - Ignore very short fragments (< 25 chars) to avoid false positives
          from short repeated words like "Moreover".
        - If any single sentence appears more than *max_absolute_repeats* times
          → repetitive.
        - If the most-common sentence accounts for > *max_repeat_ratio* of all
          sentences → repetitive.
        """
        import re
        from collections import Counter

        sentences = [
            s.strip()
            for s in re.split(r"[.!?]+", description)
            if len(s.strip()) >= 25
        ]
        if len(sentences) < min_sentences:
            return False

        counts = Counter(sentences)
        top_sentence, top_count = counts.most_common(1)[0]

        if top_count > max_absolute_repeats:
            return True
        if top_count / len(sentences) > max_repeat_ratio:
            return True
        return False

    def check_blur_with_vlm(self, path: Path) -> None:
        """Ask the VLM whether the image contains any blurry or out-of-focus area.

        Raises :class:`ImageTooBlurryError` if the VLM answers YES.
        This catches partial blur (corners, edges) that the Laplacian method misses.
        """
        b64_data, mime_type = self._encode_image(path)
        model_name = self._resolve_model()

        payload = {
            "model": model_name,
            "messages": [
                {
                    "role": "user",
                    "content": [
                        {
                            "type": "image_url",
                            "image_url": {
                                "url": f"data:{mime_type};base64,{b64_data}",
                            },
                        },
                        {
                            "type": "text",
                            "text": self.VLM_BLUR_CHECK_PROMPT,
                        },
                    ],
                }
            ],
            "temperature": 0.0,
            "max_tokens": 10,
        }

        response = requests.post(
            f"{self.base_url}/v1/chat/completions",
            json=payload,
            timeout=120,
        )
        response.raise_for_status()
        data = response.json()
        answer = data["choices"][0]["message"]["content"].strip().upper()

        if "YES" in answer:
            raise ImageTooBlurryError(reason="blurry")

    def _resolve_model(self) -> str:
        """Return the loaded VLM model id, falling back to the configured one."""
        try:
            response = requests.get(f"{self.base_url}/v1/models", timeout=30)
            response.raise_for_status()
            models = response.json().get("data", [])
            available = [m.get("id", "") for m in models if m.get("id")]
            if self.model in available:
                return self.model
            return available[0] if available else self.model
        except Exception:
            return self.model

    def _encode_image(self, path: Path) -> tuple[str, str]:
        """Return (base64_data, mime_type) for the image file."""
        mime_type, _ = mimetypes.guess_type(str(path))
        if not mime_type or not mime_type.startswith("image/"):
            mime_type = "image/jpeg"
        data = base64.b64encode(path.read_bytes()).decode("utf-8")
        return data, mime_type

    def describe(self, path: Path) -> str:
        """Send the image to the VLM and return a descriptive text string."""
        b64_data, mime_type = self._encode_image(path)
        model_name = self._resolve_model()

        payload = {
            "model": model_name,
            "messages": [
                {
                    "role": "user",
                    "content": [
                        {
                            "type": "image_url",
                            "image_url": {
                                "url": f"data:{mime_type};base64,{b64_data}",
                            },
                        },
                        {
                            "type": "text",
                            "text": self.PROMPT,
                        },
                    ],
                }
            ],
            "temperature": 0.1,
            "max_tokens": 1024,
        }

        response = requests.post(
            f"{self.base_url}/v1/chat/completions",
            json=payload,
            timeout=120,
        )
        response.raise_for_status()
        data = response.json()
        return data["choices"][0]["message"]["content"].strip()
