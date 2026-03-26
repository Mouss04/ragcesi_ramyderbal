from typing import Any, Dict, List, Sequence


class Retriever:
    """Retrieve relevant chunks from a vector store."""

    def __init__(self, vector_store) -> None:
        self.vector_store = vector_store

    def retrieve(self, query_vector: Sequence[float], top_k: int = 3) -> List[Dict[str, Any]]:
        """Return top-k search hits with source and text metadata."""
        return self.vector_store.search(query_vector, top_k=top_k)
