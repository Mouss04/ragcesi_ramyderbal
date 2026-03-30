"""Unit tests for EmbeddingGenerator."""
import pytest
from embeddings.embedder import EmbeddingGenerator


@pytest.fixture(scope="module")
def embedder():
    return EmbeddingGenerator()


def test_embed_query_returns_list(embedder):
    result = embedder.embed_query("What is the revenue of Airbus?")
    assert isinstance(result, list)
    assert len(result) > 0


def test_embed_query_all_floats(embedder):
    result = embedder.embed_query("Test query")
    assert all(isinstance(v, float) for v in result)


def test_embed_query_has_nonzero_values(embedder):
    result = embedder.embed_query("Another test")
    assert any(abs(v) > 1e-6 for v in result)


def test_embed_texts_string_list(embedder):
    texts = ["Hello world", "Foo bar baz"]
    result = embedder.embed_texts(texts)
    assert len(result) == 2
    assert all(len(v) == len(result[0]) for v in result)


def test_embed_texts_dict_list(embedder):
    chunks = [{"text": "First chunk"}, {"text": "Second chunk"}]
    result = embedder.embed_texts(chunks)
    assert len(result) == 2


def test_embed_texts_empty_returns_empty(embedder):
    assert embedder.embed_texts([]) == []


def test_query_and_text_same_dimension(embedder):
    query_vec = embedder.embed_query("What are the sustainability goals?")
    text_vecs = embedder.embed_texts(["We target net zero emissions by 2050."])
    assert len(query_vec) == len(text_vecs[0])


def test_similar_queries_close_vectors(embedder):
    """Semantically similar queries should produce cosine-similar vectors."""
    import math
    q1 = embedder.embed_query("What is the annual revenue?")
    q2 = embedder.embed_query("How much money did the company make per year?")
    q3 = embedder.embed_query("What is the color of the sky?")

    def cosine(a, b):
        dot = sum(x * y for x, y in zip(a, b))
        na = math.sqrt(sum(x ** 2 for x in a))
        nb = math.sqrt(sum(x ** 2 for x in b))
        return dot / (na * nb) if na * nb else 0.0

    sim_related = cosine(q1, q2)
    sim_unrelated = cosine(q1, q3)
    assert sim_related > sim_unrelated, (
        f"Related queries ({sim_related:.3f}) should be closer than unrelated ({sim_unrelated:.3f})"
    )
