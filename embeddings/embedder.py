from typing import Dict, List, Sequence, Union

from sentence_transformers import SentenceTransformer


class EmbeddingGenerator:
    """Generate vector embeddings for text chunks."""

    # multilingual-e5-base: retrieval-optimised, handles French + English and 100+ other
    # languages. Requires "query: " prefix for queries and "passage: " prefix for passages.
    def __init__(self, model_name: str = "intfloat/multilingual-e5-base") -> None:
        self.model = SentenceTransformer(model_name)

    def embed_texts(self, chunks: Sequence[Union[str, Dict[str, str]]]) -> List[List[float]]:
        """Convert chunks to embedding vectors and return them as a list."""
        if not chunks:
            return []

        texts: List[str] = []
        for chunk in chunks:
            if isinstance(chunk, dict):
                raw = chunk.get("text", "")
            else:
                raw = chunk
            # multilingual-e5 expects "passage: " prefix for document passages.
            texts.append(f"passage: {raw}")

        vectors = self.model.encode(texts, convert_to_numpy=True, show_progress_bar=False)
        return vectors.tolist()

    def embed_query(self, query: str) -> List[float]:
        """Convert a single query to one embedding vector.

        multilingual-e5 requires "query: " prefix for retrieval queries.
        """
        vector = self.model.encode([f"query: {query}"], convert_to_numpy=True, show_progress_bar=False)
        return vector[0].tolist()
