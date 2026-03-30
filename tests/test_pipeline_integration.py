"""Integration test for the full RAG pipeline end-to-end (no real LLM call).

Uses mock data written to a temp directory and a stubbed LLMClient so the
test suite can run offline and without LM Studio running.
"""
import os
import math
import pytest
from unittest.mock import patch, MagicMock
from pathlib import Path


# ---------------------------------------------------------------------------
# Helpers
# ---------------------------------------------------------------------------

SAMPLE_DOC = """
Airbus is a global leader in aeronautics, space and related services.
In 2024, Airbus delivered 766 commercial aircraft and reported revenues of 69.2 billion euros.
The company has a strong commitment to sustainability and targets net zero carbon emissions by 2050.
The A320neo family remains the best-selling aircraft family in aviation history.
Airbus employs over 130,000 people worldwide across more than 30 countries.
"""


@pytest.fixture()
def data_dir(tmp_path):
    """Create a temporary data directory with a sample document."""
    doc_path = tmp_path / "sample.txt"
    doc_path.write_text(SAMPLE_DOC, encoding="utf-8")
    return tmp_path


# ---------------------------------------------------------------------------
# Pipeline unit-level integration (without LLM)
# ---------------------------------------------------------------------------

class TestRAGPipelineComponents:
    """Test index building and retrieval without calling the LLM."""

    def _build(self, data_dir):
        from pipeline.rag_pipeline import RAGPipeline
        pipeline = RAGPipeline.__new__(RAGPipeline)
        # Re-init with custom data_dir path
        from ingestion.loader import DocumentIngestor
        from preprocessing.cleaner import TextPreprocessor
        from chunking.chunker import TextChunker
        from embeddings.embedder import EmbeddingGenerator
        from vector_store.faiss_store import VectorStore
        from retrieval.retriever import Retriever

        pipeline.ingestor = DocumentIngestor(data_dir=str(data_dir))
        pipeline.preprocessor = TextPreprocessor()
        pipeline.chunker = TextChunker()
        pipeline.embedder = EmbeddingGenerator()
        pipeline.vector_store = VectorStore()
        pipeline.retriever = Retriever(pipeline.vector_store)
        pipeline.llm_client = MagicMock()
        pipeline.llm_client.generate_answer.return_value = "Mocked answer."
        pipeline.index_path = str(data_dir / "faiss.index")
        pipeline.meta_path = str(data_dir / "faiss.meta.json")
        return pipeline

    def test_build_produces_nonzero_chunks(self, data_dir):
        pipeline = self._build(data_dir)
        records = pipeline.ingestor.load_document_records()
        documents = [item["text"] for item in records]
        sources = [item["source"] for item in records]
        cleaned = pipeline.preprocessor.clean(documents)
        chunks = pipeline.chunker.chunk(cleaned, sources=sources)
        assert len(chunks) > 0

    def test_embed_and_store(self, data_dir):
        pipeline = self._build(data_dir)
        records = pipeline.ingestor.load_document_records()
        documents = [item["text"] for item in records]
        sources = [item["source"] for item in records]
        cleaned = pipeline.preprocessor.clean(documents)
        chunks = pipeline.chunker.chunk(cleaned, sources=sources)
        vectors = pipeline.embedder.embed_texts(chunks)
        assert len(vectors) == len(chunks)
        pipeline.vector_store.add_embeddings(vectors, documents=chunks)
        assert pipeline.vector_store.index is not None

    def test_retrieval_finds_relevant_chunks(self, data_dir):
        pipeline = self._build(data_dir)
        records = pipeline.ingestor.load_document_records()
        documents = [item["text"] for item in records]
        sources = [item["source"] for item in records]
        cleaned = pipeline.preprocessor.clean(documents)
        chunks = pipeline.chunker.chunk(cleaned, sources=sources)
        vectors = pipeline.embedder.embed_texts(chunks)
        pipeline.vector_store.add_embeddings(vectors, documents=chunks)

        query = "What are Airbus revenues?"
        query_vec = pipeline.embedder.embed_query(query)
        results = pipeline.retriever.retrieve(query_vec, top_k=3, query_text=query)
        assert len(results) > 0
        combined_text = " ".join(r["document"]["text"] for r in results).lower()
        assert "airbus" in combined_text or "revenue" in combined_text

    def test_emission_query_finds_sustainability_chunk(self, data_dir):
        pipeline = self._build(data_dir)
        records = pipeline.ingestor.load_document_records()
        documents = [item["text"] for item in records]
        sources = [item["source"] for item in records]
        cleaned = pipeline.preprocessor.clean(documents)
        chunks = pipeline.chunker.chunk(cleaned, sources=sources)
        vectors = pipeline.embedder.embed_texts(chunks)
        pipeline.vector_store.add_embeddings(vectors, documents=chunks)

        query = "What is Airbus carbon emissions target?"
        query_vec = pipeline.embedder.embed_query(query)
        results = pipeline.retriever.retrieve(query_vec, top_k=3, query_text=query)
        combined = " ".join(r["document"]["text"] for r in results).lower()
        assert "emission" in combined or "sustainability" in combined or "carbon" in combined

    def test_ask_calls_llm_with_contexts(self, data_dir):
        pipeline = self._build(data_dir)
        records = pipeline.ingestor.load_document_records()
        documents = [item["text"] for item in records]
        sources = [item["source"] for item in records]
        cleaned = pipeline.preprocessor.clean(documents)
        chunks = pipeline.chunker.chunk(cleaned, sources=sources)
        vectors = pipeline.embedder.embed_texts(chunks)
        pipeline.vector_store.add_embeddings(vectors, documents=chunks)

        query = "How many aircraft did Airbus deliver?"
        answer = pipeline.ask(query)
        assert answer == "Mocked answer."
        pipeline.llm_client.generate_answer.assert_called_once()
        call_args = pipeline.llm_client.generate_answer.call_args
        contexts_passed = call_args[0][1]
        assert len(contexts_passed) > 0

    def test_save_and_reload_index(self, data_dir, tmp_path):
        pipeline = self._build(data_dir)
        records = pipeline.ingestor.load_document_records()
        documents = [item["text"] for item in records]
        sources = [item["source"] for item in records]
        cleaned = pipeline.preprocessor.clean(documents)
        chunks = pipeline.chunker.chunk(cleaned, sources=sources)
        vectors = pipeline.embedder.embed_texts(chunks)
        pipeline.vector_store.add_embeddings(vectors, documents=chunks)
        pipeline.vector_store.save_index(pipeline.index_path, pipeline.meta_path)

        # Load into a fresh store.
        from vector_store.faiss_store import VectorStore
        fresh_store = VectorStore()
        fresh_store.load_index(pipeline.index_path, pipeline.meta_path)
        assert fresh_store.index.ntotal == pipeline.vector_store.index.ntotal


# ---------------------------------------------------------------------------
# Preprocessing
# ---------------------------------------------------------------------------

class TestTextPreprocessor:
    def test_strips_whitespace(self):
        from preprocessing.cleaner import TextPreprocessor
        proc = TextPreprocessor()
        result = proc.clean(["  hello  ", "  world  "])
        assert result == ["hello", "world"]

    def test_removes_empty_strings(self):
        from preprocessing.cleaner import TextPreprocessor
        proc = TextPreprocessor()
        result = proc.clean(["valid", "", "   "])
        assert result == ["valid"]

    def test_preserves_content(self):
        from preprocessing.cleaner import TextPreprocessor
        proc = TextPreprocessor()
        text = "Airbus revenue 2024."
        result = proc.clean([text])
        assert result == [text]
