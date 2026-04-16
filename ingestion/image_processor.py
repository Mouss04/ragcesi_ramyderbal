import base64
import mimetypes
from pathlib import Path

import requests


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

    def __init__(self, base_url: str = DEFAULT_VLM_URL, model: str = DEFAULT_VLM_MODEL) -> None:
        self.base_url = base_url.rstrip("/")
        self.model = model

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
