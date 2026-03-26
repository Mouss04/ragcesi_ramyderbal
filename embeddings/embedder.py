from typing import Dict, List, Sequence, Union

from sentence_transformers import SentenceTransformer


class EmbeddingGenerator:
    """Generate vector embeddings for text chunks."""

    def __init__(self, model_name: str = "all-MiniLM-L6-v2") -> None:
        self.model = SentenceTransformer(model_name)

    def embed_texts(self, chunks: Sequence[Union[str, Dict[str, str]]]) -> List[List[float]]:
        """Convert chunks to embedding vectors and return them as a list."""
        if not chunks:
            return []

        texts: List[str] = []
        for chunk in chunks:
            if isinstance(chunk, dict):
                texts.append(chunk.get("text", ""))
            else:
                texts.append(chunk)

        vectors = self.model.encode(texts, convert_to_numpy=True, show_progress_bar=False)
        return vectors.tolist()

    def embed_query(self, query: str) -> List[float]:
        """Convert a single query to one embedding vector."""
        vector = self.model.encode([query], convert_to_numpy=True, show_progress_bar=False)
        return vector[0].tolist()
