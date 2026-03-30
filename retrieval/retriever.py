import math
import re
from typing import Any, Dict, List, Sequence


class Retriever:
    """Retrieve relevant chunks from a vector store."""

    def __init__(self, vector_store) -> None:
        self.vector_store = vector_store

    def retrieve(
        self,
        query_vector: Sequence[float],
        top_k: int = 5,
        query_text: str = "",
    ) -> List[Dict[str, Any]]:
        """Return top-k search hits using combined semantic + BM25-style lexical scores."""
        # Over-fetch to allow re-ranking across a large candidate pool.
        candidate_k = max(top_k * 25, 100)
        candidates = self.vector_store.search(query_vector, top_k=candidate_k)
        if not candidates:
            return []

        terms = self._query_terms(query_text)
        if not terms:
            return candidates[:top_k]

        # Build IDF weight for each term across the candidate pool.
        total_docs = len(candidates)
        doc_freq: Dict[str, int] = {term: 0 for term in terms}
        for item in candidates:
            text = str(item.get("document", {}).get("text", "")).lower()
            for term in terms:
                if term in text:
                    doc_freq[term] += 1
        idf: Dict[str, float] = {
            term: math.log((total_docs + 1) / (doc_freq[term] + 1)) + 1.0
            for term in terms
        }

        for rank, item in enumerate(candidates, start=1):
            document = item.get("document", {})
            text = str(document.get("text", ""))
            source = str(document.get("source", ""))
            # Normalised semantic score (cosine similarity is in [-1,1], higher is better).
            semantic = item.get("score", 0.0)
            # TF-IDF style lexical score on text.
            lexical_text = self._tfidf_score(terms, idf, text)
            # Light source-name bonus for queries that reference a document title.
            lexical_source = self._lexical_score(terms, source)
            item["_combined"] = (0.60 * semantic) + (0.30 * lexical_text) + (0.10 * lexical_source)

        candidates.sort(key=lambda item: item.get("_combined", 0.0), reverse=True)

        # Prefer source diversity so one long document does not crowd out others.
        selected: List[Dict[str, Any]] = []
        selected_sources: Dict[str, int] = {}
        leftovers: List[Dict[str, Any]] = []

        for item in candidates:
            source = str(item.get("document", {}).get("source", ""))
            count = selected_sources.get(source, 0)
            # Allow at most 2 chunks from the same source in the top results.
            if count < 2 and len(selected) < top_k:
                selected.append(item)
                selected_sources[source] = count + 1
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
            "the", "a", "an", "and", "or", "of", "to", "for", "in", "on",
            "from", "what", "which", "with", "is", "are", "does", "did",
            "by", "it", "that", "this", "how", "when", "where", "who",
            "was", "were", "has", "have", "had", "be", "been", "will", "can",
            "do", "at", "as", "its", "their", "they", "we", "you", "me",
        }
        return [token for token in tokens if token not in stop_words and len(token) > 1]

    def _tfidf_score(self, terms: List[str], idf: Dict[str, float], text: str) -> float:
        """TF-IDF inspired score: rewards both frequency and rarity of matched terms."""
        if not text or not terms:
            return 0.0
        lowered = text.lower()
        words = lowered.split()
        total_words = max(len(words), 1)
        score = 0.0
        for term in terms:
            tf = lowered.count(term) / total_words
            score += math.log(1 + tf) * idf.get(term, 1.0)
        return score / max(len(terms), 1)

    def _lexical_score(self, terms: List[str], text: str) -> float:
        if not text:
            return 0.0
        lowered = text.lower()
        matches = sum(1 for term in terms if term in lowered)
        return matches / max(len(terms), 1)
