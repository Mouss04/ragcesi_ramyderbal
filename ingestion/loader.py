import base64
import io
import os
from pathlib import Path
from typing import Dict, List, Optional

import fitz  # pymupdf
from pypdf import PdfReader

from ingestion.image_processor import VLMImageProcessor

IMAGE_EXTENSIONS = {".jpg", ".jpeg", ".png", ".gif", ".webp"}


class DocumentIngestor:
    """Load documents from a source directory."""

    def __init__(
        self,
        data_dir: str = "data",
        vlm_url: Optional[str] = None,
        vlm_model: Optional[str] = None,
    ) -> None:
        self.data_dir = Path(data_dir)
        self._vlm_url = vlm_url or os.getenv("VLM_URL", VLMImageProcessor.DEFAULT_VLM_URL)
        self._vlm_model = vlm_model or os.getenv("VLM_MODEL", VLMImageProcessor.DEFAULT_VLM_MODEL)
        self._vlm: Optional[VLMImageProcessor] = None

    @property
    def vlm(self) -> VLMImageProcessor:
        """Lazily create the VLM processor on first use."""
        if self._vlm is None:
            self._vlm = VLMImageProcessor(base_url=self._vlm_url, model=self._vlm_model)
        return self._vlm

    def load_documents(self) -> List[str]:
        """Return raw text documents found in the data directory."""
        return [item["text"] for item in self.load_document_records()]

    def load_document_records(self) -> List[Dict[str, str]]:
        """Return documents with both source path and text."""
        documents: List[Dict[str, str]] = []
        for path in self.data_dir.glob("**/*"):
            if not path.is_file():
                continue

            text = self._read_supported_file(path)
            if not text:
                continue

            documents.append({"source": str(path), "text": text})

        return documents

    def _read_supported_file(self, path: Path) -> str:
        """Read a supported file type and return extracted text."""
        suffix = path.suffix.lower()
        if suffix in {".txt", ".md"}:
            return path.read_text(encoding="utf-8", errors="ignore").strip()
        if suffix == ".pdf":
            return self._read_pdf(path)
        if suffix in IMAGE_EXTENSIONS:
            return self._describe_image(path)
        return ""

    def _read_pdf(self, path: Path) -> str:
        """Extract text from all pages of a PDF file.

        For image-based (scanned) PDFs where pypdf finds no text, each page is
        rendered to a PNG image in memory and described by the VLM.
        """
        try:
            reader = PdfReader(str(path))
            pages = [page.extract_text() or "" for page in reader.pages]
            text = "\n".join(pages).strip()
            if text:
                return text
        except Exception:
            pass

        # Fallback: render pages with pymupdf and describe each via the VLM.
        return self._describe_pdf_as_images(path)

    def _describe_pdf_as_images(self, path: Path) -> str:
        """Render each PDF page as a PNG and run it through the VLM describer."""
        try:
            doc = fitz.open(str(path))
            descriptions: list[str] = []
            for page_num, page in enumerate(doc, start=1):
                pix = page.get_pixmap(dpi=150)
                png_bytes = pix.tobytes("png")
                # Build a temporary in-memory Path-like object the VLM can encode.
                b64 = base64.b64encode(png_bytes).decode("utf-8")
                description = self._describe_pdf_page_b64(b64, page_num)
                if description:
                    descriptions.append(f"[Page {page_num}]\n{description}")
            return "\n\n".join(descriptions).strip()
        except Exception:
            return ""

    def _describe_pdf_page_b64(self, b64_data: str, page_num: int) -> str:
        """Send a base64-encoded PNG page to the VLM and return the description."""
        try:
            import requests
            model_name = self.vlm._resolve_model()
            payload = {
                "model": model_name,
                "messages": [
                    {
                        "role": "user",
                        "content": [
                            {
                                "type": "image_url",
                                "image_url": {
                                    "url": f"data:image/png;base64,{b64_data}",
                                },
                            },
                            {
                                "type": "text",
                                "text": self.vlm.PROMPT,
                            },
                        ],
                    }
                ],
                "max_tokens": 1024,
            }
            resp = requests.post(
                f"{self.vlm.base_url}/v1/chat/completions",
                json=payload,
                timeout=120,
            )
            resp.raise_for_status()
            return resp.json()["choices"][0]["message"]["content"].strip()
        except Exception:
            return ""

    def _describe_image(self, path: Path) -> str:
        """Use the VLM to generate a text description of the image."""
        try:
            return self.vlm.describe(path)
        except Exception:
            # Skip images that cannot be processed so ingestion can continue.
            return ""
