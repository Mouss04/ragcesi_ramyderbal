"""Unit tests for VectorStore (cosine similarity via IndexFlatIP)."""
import math
import pytest
from vector_store.faiss_store import VectorStore


def cosine(a, b):
    dot = sum(x * y for x, y in zip(a, b))
    na = math.sqrt(sum(x ** 2 for x in a))
    nb = math.sqrt(sum(x ** 2 for x in b))
    return dot / (na * nb) if na * nb else 0.0


def unit_vec(v):
    norm = math.sqrt(sum(x ** 2 for x in v))
    return [x / norm for x in v] if norm else v


def make_store(vecs, docs=None):
    store = VectorStore()
    store.add_embeddings(vecs, documents=docs)
    return store


# ---------------------------------------------------------------------------
# Basic add / search
# ---------------------------------------------------------------------------

def test_add_and_search_returns_results():
    vecs = [[1.0, 0.0, 0.0], [0.0, 1.0, 0.0], [0.0, 0.0, 1.0]]
    store = make_store(vecs)
    results = store.search([1.0, 0.0, 0.0], top_k=1)
    assert len(results) == 1


def test_most_similar_vector_ranked_first():
    vecs = [unit_vec([1, 0, 0]), unit_vec([0, 1, 0]), unit_vec([0, 0, 1])]
    docs = [{"text": "A"}, {"text": "B"}, {"text": "C"}]
    store = make_store(vecs, docs)
    results = store.search(unit_vec([1, 0.01, 0]), top_k=3)
    assert results[0]["document"]["text"] == "A"


def test_score_is_high_for_identical_vector():
    vec = unit_vec([1.0, 2.0, 3.0])
    store = make_store([vec], docs=[{"text": "same"}])
    results = store.search(vec, top_k=1)
    assert results[0]["score"] > 0.99


def test_top_k_limits_results():
    vecs = [unit_vec([float(i), 0, 0]) for i in range(1, 11)]
    store = make_store(vecs)
    results = store.search(unit_vec([1, 0, 0]), top_k=3)
    assert len(results) == 3


def test_documents_metadata_attached():
    vecs = [[1.0, 0.0], [0.0, 1.0]]
    docs = [{"text": "hello", "source": "doc1"}, {"text": "world", "source": "doc2"}]
    store = make_store(vecs, docs)
    results = store.search([1.0, 0.0], top_k=2)
    found_sources = {r["document"]["source"] for r in results}
    assert "doc1" in found_sources


def test_empty_embeddings_raise():
    store = VectorStore()
    with pytest.raises(ValueError):
        store.add_embeddings([])


def test_search_without_index_raises():
    store = VectorStore()
    with pytest.raises(ValueError):
        store.search([1.0, 0.0], top_k=1)


def test_top_k_zero_raises():
    vecs = [[1.0, 0.0]]
    store = make_store(vecs)
    with pytest.raises(ValueError):
        store.search([1.0, 0.0], top_k=0)


def test_dimension_mismatch_raises():
    vecs = [[1.0, 0.0, 0.0]]
    store = make_store(vecs)
    with pytest.raises(ValueError):
        store.search([1.0, 0.0], top_k=1)


def test_save_and_load_roundtrip(tmp_path):
    vecs = [unit_vec([1, 0, 0]), unit_vec([0, 1, 0])]
    docs = [{"text": "A"}, {"text": "B"}]
    store = make_store(vecs, docs)
    idx_path = str(tmp_path / "test.index")
    meta_path = str(tmp_path / "test.meta.json")
    store.save_index(idx_path, meta_path)

    store2 = VectorStore()
    store2.load_index(idx_path, meta_path)
    results = store2.search(unit_vec([1, 0, 0]), top_k=1)
    assert results[0]["document"]["text"] == "A"


def test_rank_field_is_sequential():
    vecs = [unit_vec([float(i), 0, 0]) for i in range(1, 5)]
    store = make_store(vecs)
    results = store.search(unit_vec([1, 0, 0]), top_k=4)
    ranks = [r["rank"] for r in results]
    assert ranks == list(range(1, len(results) + 1))
