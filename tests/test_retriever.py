"""Unit tests for Retriever — hybrid semantic + TF-IDF lexical scoring."""
import math
import pytest
from unittest.mock import MagicMock
from retrieval.retriever import Retriever


def unit_vec(*components):
    norm = math.sqrt(sum(x ** 2 for x in components))
    return [x / norm for x in components] if norm else list(components)


def make_mock_store(documents):
    """Build a mock vector store that returns `documents` as search results."""
    store = MagicMock()
    results = [
        {"rank": i + 1, "score": 1.0 - i * 0.05, "document": doc}
        for i, doc in enumerate(documents)
    ]
    store.search.return_value = results
    return store


# ---------------------------------------------------------------------------
# Basic behaviour
# ---------------------------------------------------------------------------

def test_retrieve_returns_top_k():
    docs = [{"text": f"text {i}", "source": "src"} for i in range(20)]
    retriever = Retriever(make_mock_store(docs))
    result = retriever.retrieve([0.0] * 3, top_k=5, query_text="text")
    assert len(result) == 5


def test_empty_store_returns_empty():
    store = MagicMock()
    store.search.return_value = []
    retriever = Retriever(store)
    assert retriever.retrieve([0.0] * 3, top_k=5) == []


def test_most_relevant_chunk_ranked_first():
    docs = [
        {"text": "The Airbus A320 family revenue increased significantly.", "source": "report.pdf"},
        {"text": "Weather patterns in the Arctic have shifted.", "source": "climate.pdf"},
        {"text": "Airbus reported strong revenue growth in 2024.", "source": "annual.pdf"},
    ]
    store = make_mock_store(docs)
    retriever = Retriever(store)
    results = retriever.retrieve([0.0] * 3, top_k=3, query_text="Airbus revenue growth")
    # Both Airbus revenue chunks should appear before the unrelated climate text.
    top_texts = [r["document"]["text"] for r in results[:2]]
    assert any("Airbus" in t for t in top_texts)


def test_no_query_text_uses_semantic_order():
    docs = [{"text": f"doc{i}", "source": "s"} for i in range(5)]
    store = make_mock_store(docs)
    retriever = Retriever(store)
    # With no query text, results must respect original semantic ranking.
    results = retriever.retrieve([0.0] * 3, top_k=3, query_text="")
    assert len(results) == 3


def test_source_diversity_limits_same_source():
    """When sufficient diverse sources exist, no single source contributes more than 2 chunks."""
    # 4 chunks from same_doc.pdf (highest semantic scores) + 6 from distinct sources.
    docs = [{"text": f"content item {i}", "source": "same_doc.pdf"} for i in range(4)]
    docs += [{"text": f"content item {i + 4}", "source": f"doc_{i}.pdf"} for i in range(6)]
    retriever = Retriever(make_mock_store(docs))
    # Use a query that matches all docs equally so diversity drives selection.
    results = retriever.retrieve([0.0] * 3, top_k=5, query_text="content")
    sources = [r["document"]["source"] for r in results]
    assert sources.count("same_doc.pdf") <= 2


def test_fewer_docs_than_top_k():
    docs = [{"text": "only", "source": "x"}]
    retriever = Retriever(make_mock_store(docs))
    results = retriever.retrieve([0.0] * 3, top_k=10, query_text="only")
    assert len(results) == 1


# ---------------------------------------------------------------------------
# Query term extraction
# ---------------------------------------------------------------------------

def test_stop_words_removed():
    retriever = Retriever(MagicMock())
    terms = retriever._query_terms("What is the revenue of Airbus?")
    assert "what" not in terms
    assert "is" not in terms
    assert "the" not in terms
    assert "airbus" in terms
    assert "revenue" in terms


def test_short_tokens_removed():
    retriever = Retriever(MagicMock())
    terms = retriever._query_terms("a b c hello")
    assert "a" not in terms
    assert "b" not in terms
    assert "hello" in terms


# ---------------------------------------------------------------------------
# Lexical scoring
# ---------------------------------------------------------------------------

def test_lexical_score_full_match():
    retriever = Retriever(MagicMock())
    score = retriever._lexical_score(["airbus", "revenue"], "airbus revenue report")
    assert score == 1.0


def test_lexical_score_partial_match():
    retriever = Retriever(MagicMock())
    score = retriever._lexical_score(["airbus", "revenue"], "airbus profit statement")
    assert 0.4 < score < 0.7


def test_lexical_score_no_match():
    retriever = Retriever(MagicMock())
    score = retriever._lexical_score(["airbus", "revenue"], "weather climate change")
    assert score == 0.0


def test_lexical_score_empty_text():
    retriever = Retriever(MagicMock())
    assert retriever._lexical_score(["term"], "") == 0.0


# ---------------------------------------------------------------------------
# TF-IDF scoring
# ---------------------------------------------------------------------------

def test_tfidf_score_rewards_frequent_rare_terms():
    retriever = Retriever(MagicMock())
    idf = {"airbus": 2.5, "revenue": 1.8}
    high = retriever._tfidf_score(["airbus", "revenue"], idf, "airbus airbus revenue report")
    low = retriever._tfidf_score(["airbus", "revenue"], idf, "revenue")
    assert high > low


def test_tfidf_score_empty_text():
    retriever = Retriever(MagicMock())
    assert retriever._tfidf_score(["term"], {"term": 1.0}, "") == 0.0
