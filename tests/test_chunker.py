"""Unit tests for TextChunker — sentence-aware chunking."""
import pytest
from chunking.chunker import TextChunker


def make_chunker(**kwargs) -> TextChunker:
    return TextChunker(**kwargs)


# ---------------------------------------------------------------------------
# Basic behaviour
# ---------------------------------------------------------------------------

def test_empty_document_produces_no_chunks():
    chunker = make_chunker()
    assert chunker.chunk([""]) == []


def test_none_after_filter_produces_no_chunks():
    chunker = make_chunker()
    assert chunker.chunk(["   "]) == []


def test_short_document_produces_single_chunk():
    chunker = make_chunker(chunk_size=100, overlap=20)
    result = chunker.chunk(["Hello world. This is a test."])
    assert len(result) == 1
    assert "Hello world" in result[0]["text"]


def test_source_is_propagated():
    chunker = make_chunker(chunk_size=50, overlap=10)
    text = " ".join(["word"] * 60)
    result = chunker.chunk([text], sources=["my_doc.pdf"])
    assert all(r["source"] == "my_doc.pdf" for r in result)


def test_default_source_when_none_provided():
    chunker = make_chunker(chunk_size=50, overlap=10)
    text = " ".join(["word"] * 60)
    result = chunker.chunk([text])
    assert all("document_" in r["source"] for r in result)


def test_overlap_creates_continuity():
    """Last `overlap` tokens of chunk N should appear at the start of chunk N+1."""
    chunker = make_chunker(chunk_size=50, overlap=10)
    text = " ".join([f"word{i}" for i in range(200)])
    result = chunker.chunk([text])
    assert len(result) >= 2
    # The tail of chunk 0 must be present in chunk 1.
    tail_words = result[0]["text"].split()[-10:]
    head_words = result[1]["text"].split()[:10]
    assert set(tail_words) & set(head_words), "Overlap words should appear in consecutive chunks"


def test_multiple_documents():
    chunker = make_chunker(chunk_size=50, overlap=10)
    docs = [" ".join(["a"] * 60), " ".join(["b"] * 60)]
    result = chunker.chunk(docs, sources=["doc_a", "doc_b"])
    sources_seen = {r["source"] for r in result}
    assert "doc_a" in sources_seen
    assert "doc_b" in sources_seen


def test_chunk_dict_keys():
    chunker = make_chunker()
    result = chunker.chunk(["Some text here."])
    assert all("text" in r and "source" in r for r in result)


def test_invalid_overlap_raises():
    with pytest.raises(ValueError, match="overlap must be smaller"):
        TextChunker(chunk_size=100, overlap=100)


def test_no_chunk_exceeds_size_much():
    """No chunk should exceed chunk_size * 2 words (sentence-aware may overshoot slightly)."""
    chunker = make_chunker(chunk_size=80, overlap=20)
    long_doc = ". ".join([" ".join(["word"] * 10) for _ in range(50)])
    result = chunker.chunk([long_doc])
    for r in result:
        assert len(r["text"].split()) <= chunker.chunk_size * 2 + chunker.overlap
