from pathlib import Path
from typing import Dict, List

from pypdf import PdfReader


class DocumentIngestor:
    """Load documents from a source directory."""

    def __init__(self, data_dir: str = "data") -> None:
        self.data_dir = Path(data_dir)

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
        return ""

    def _read_pdf(self, path: Path) -> str:
        """Extract text from all pages of a PDF file."""
        try:
            reader = PdfReader(str(path))
            pages = [page.extract_text() or "" for page in reader.pages]
            return "\n".join(pages).strip()
        except Exception:
            # Skip malformed PDFs so ingestion can continue.
            return ""
