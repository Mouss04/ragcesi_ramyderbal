from typing import Dict, List, Optional


class TextChunker:
    """Split cleaned text into overlapping word-based chunks."""

    def __init__(self, chunk_size: int = 500, overlap: int = 100) -> None:
        if overlap >= chunk_size:
            raise ValueError("overlap must be smaller than chunk_size")
        self.chunk_size = chunk_size
        self.overlap = overlap

    def chunk(self, documents: List[str], sources: Optional[List[str]] = None) -> List[Dict[str, str]]:
        """Create chunks with text and source document reference."""
        chunks: List[Dict[str, str]] = []
        step = self.chunk_size - self.overlap

        for i, doc in enumerate(documents):
            words = doc.split()
            if not words:
                continue

            source = sources[i] if sources and i < len(sources) else f"document_{i + 1}"
            for start in range(0, len(words), step):
                window = words[start : start + self.chunk_size]
                if not window:
                    continue
                chunks.append(
                    {
                        "source": source,
                        "text": " ".join(window),
                    }
                )
        return chunks
