import re
from typing import Any, Dict, List, Sequence


class Retriever:
    """Retrieve relevant chunks from a vector store."""

    def __init__(self, vector_store) -> None:
        self.vector_store = vector_store

    def retrieve(
        self,
        query_vector: Sequence[float],
        top_k: int = 3,
        query_text: str = "",
    ) -> List[Dict[str, Any]]:
        """Return top-k search hits, boosted by lexical overlap with query text."""
        candidate_k = max(top_k * 20, 60)
        candidates = self.vector_store.search(query_vector, top_k=candidate_k)
        if not candidates:
            return []

        terms = self._query_terms(query_text)
        if not terms:
            return candidates[:top_k]

        for rank, item in enumerate(candidates, start=1):
            document = item.get("document", {})
            text = str(document.get("text", ""))
            source = str(document.get("source", ""))
            lexical_text = self._lexical_score(terms, text)
            lexical_source = self._lexical_score(terms, source)
            semantic = 1.0 / rank
            item["_combined"] = (0.55 * semantic) + (0.3 * lexical_text) + (0.15 * lexical_source)

        candidates.sort(key=lambda item: item.get("_combined", 0.0), reverse=True)

        # Prefer source diversity so one long document does not crowd out newer uploads.
        selected: List[Dict[str, Any]] = []
        selected_sources = set()
        leftovers: List[Dict[str, Any]] = []

        for item in candidates:
            source = str(item.get("document", {}).get("source", ""))
            if source and source not in selected_sources and len(selected) < top_k:
                selected.append(item)
                selected_sources.add(source)
            else:
                leftovers.append(item)

        for item in leftovers:
            if len(selected) >= top_k:
                break
            selected.append(item)

        for item in candidates:
            item.pop("_combined", None)

        return selected[:top_k]

    def _query_terms(self, query_text: str) -> List[str]:
        tokens = re.findall(r"[a-z0-9]+", query_text.lower())
        stop_words = {
            "the",
            "a",
            "an",
            "and",
            "or",
            "of",
            "to",
            "for",
            "in",
            "on",
            "from",
            "what",
            "which",
            "with",
            "is",
            "are",
            "does",
            "did",
            "by",
            "it",
            "that",
            "this",
            "how",
            "when",
        }
        return [token for token in tokens if token not in stop_words and len(token) > 1]

    def _lexical_score(self, terms: List[str], text: str) -> float:
        if not text:
            return 0.0
        lowered = text.lower()
        matches = sum(1 for term in terms if term in lowered)
        return matches / max(len(terms), 1)
