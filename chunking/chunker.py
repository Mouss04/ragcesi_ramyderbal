import re
from typing import Dict, List, Optional


class TextChunker:
    """Split cleaned text into overlapping sentence-aware chunks."""

    def __init__(self, chunk_size: int = 400, overlap: int = 80) -> None:
        if overlap >= chunk_size:
            raise ValueError("overlap must be smaller than chunk_size")
        self.chunk_size = chunk_size
        self.overlap = overlap

    # Sentence boundary: ends with . ! ? followed by whitespace + capital letter,
    # or a newline. Avoids splitting on common abbreviations like "e.g." "Mr." etc.
    _SENT_END = re.compile(r'(?<=[.!?])\s+(?=[A-Z])|(?<=\n)\s*(?=\S)')

    def _split_sentences(self, text: str) -> List[str]:
        """Split text into sentences while preserving whitespace context."""
        parts = self._SENT_END.split(text.strip())
        return [p.strip() for p in parts if p.strip()]

    def chunk(self, documents: List[str], sources: Optional[List[str]] = None) -> List[Dict[str, str]]:
        """Create sentence-boundary-respecting chunks with rolling overlap."""
        chunks: List[Dict[str, str]] = []

        for i, doc in enumerate(documents):
            source = sources[i] if sources and i < len(sources) else f"document_{i + 1}"
            sentences = self._split_sentences(doc)
            if not sentences:
                continue

            current_words: List[str] = []
            current_len = 0

            for sentence in sentences:
                sent_words = sentence.split()
                sent_len = len(sent_words)

                # If adding this sentence would exceed chunk_size, flush current buffer.
                if current_len + sent_len > self.chunk_size and current_words:
                    chunks.append({"source": source, "text": " ".join(current_words)})
                    # Carry over `overlap` words from the tail for continuity.
                    overlap_words = current_words[-self.overlap:] if self.overlap else []
                    current_words = overlap_words + sent_words
                    current_len = len(current_words)
                else:
                    current_words.extend(sent_words)
                    current_len += sent_len

                # A single sentence longer than chunk_size is split into word-windows.
                while current_len >= self.chunk_size:
                    chunks.append({"source": source, "text": " ".join(current_words[:self.chunk_size])})
                    current_words = current_words[self.chunk_size - self.overlap:]
                    current_len = len(current_words)

            if current_words:
                chunks.append({"source": source, "text": " ".join(current_words)})

        return chunks
