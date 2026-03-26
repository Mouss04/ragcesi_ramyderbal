import json
from pathlib import Path
from typing import Any, Dict, List, Optional, Sequence

import faiss
import numpy as np


class VectorStore:
    """FAISS vector store with persistence and similarity search."""

    def __init__(self) -> None:
        self.index: faiss.Index | None = None
        self.documents: List[Dict[str, Any]] = []
        self.dimension: Optional[int] = None

    def add_embeddings(
        self,
        embeddings: Sequence[Sequence[float]],
        documents: Optional[Sequence[Dict[str, Any]]] = None,
    ) -> None:
        """Store embeddings and optional source-aware document metadata."""
        vectors = np.asarray(embeddings, dtype=np.float32)
        if vectors.size == 0:
            raise ValueError("Cannot store empty embeddings.")
        if vectors.ndim != 2:
            raise ValueError("Embeddings must be a 2D array-like structure.")

        self.dimension = int(vectors.shape[1])
        self.index = faiss.IndexFlatL2(self.dimension)
        self.index.add(vectors)

        if documents is None:
            self.documents = [{"id": i} for i in range(vectors.shape[0])]
        else:
            self.documents = [dict(item) for item in documents]

    def save_index(self, index_path: str, metadata_path: Optional[str] = None) -> None:
        """Save FAISS index and associated metadata to local files."""
        if self.index is None:
            raise ValueError("Cannot save an empty index.")

        index_file = Path(index_path)
        index_file.parent.mkdir(parents=True, exist_ok=True)
        faiss.write_index(self.index, str(index_file))

        meta_file = Path(metadata_path) if metadata_path else index_file.with_suffix(".meta.json")
        payload = {
            "dimension": self.dimension,
            "documents": self.documents,
        }
        meta_file.write_text(json.dumps(payload, indent=2), encoding="utf-8")

    def load_index(self, index_path: str, metadata_path: Optional[str] = None) -> None:
        """Load FAISS index and metadata from local files."""
        index_file = Path(index_path)
        if not index_file.exists():
            raise FileNotFoundError(f"Index file not found: {index_path}")

        self.index = faiss.read_index(str(index_file))
        self.dimension = self.index.d

        meta_file = Path(metadata_path) if metadata_path else index_file.with_suffix(".meta.json")
        if meta_file.exists():
            payload = json.loads(meta_file.read_text(encoding="utf-8"))
            self.documents = payload.get("documents", [])
            self.dimension = payload.get("dimension", self.dimension)
        else:
            total = self.index.ntotal
            self.documents = [{"id": i} for i in range(total)]

    def search(self, query_vector: Sequence[float], top_k: int = 3) -> List[Dict[str, Any]]:
        """Search similar vectors and return ranked metadata with distances."""
        if self.index is None:
            raise ValueError("FAISS index is not initialized.")
        if top_k <= 0:
            raise ValueError("top_k must be greater than 0.")

        query = np.asarray(query_vector, dtype=np.float32)
        if query.ndim == 1:
            query = np.expand_dims(query, axis=0)
        if query.ndim != 2:
            raise ValueError("Query vector must be 1D or 2D array-like.")

        if self.dimension is not None and query.shape[1] != self.dimension:
            raise ValueError(
                f"Query dimension ({query.shape[1]}) does not match index dimension ({self.dimension})."
            )

        k = min(top_k, len(self.documents)) if self.documents else top_k
        distances, indices = self.index.search(query, k)

        results: List[Dict[str, Any]] = []
        for idx, distance in zip(indices[0], distances[0]):
            if idx != -1:
                metadata = self.documents[idx] if idx < len(self.documents) else {"id": idx}
                results.append(
                    {
                        "rank": len(results) + 1,
                        "score": float(distance),
                        "document": metadata,
                    }
                )
        return results


FAISSVectorStore = VectorStore
