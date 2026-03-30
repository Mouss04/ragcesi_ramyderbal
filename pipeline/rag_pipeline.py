import os

from ingestion.loader import DocumentIngestor
from preprocessing.cleaner import TextPreprocessor
from chunking.chunker import TextChunker
from embeddings.embedder import EmbeddingGenerator
from vector_store.faiss_store import VectorStore
from retrieval.retriever import Retriever
from llm.client import LLMClient


class RAGPipeline:
    """Connect all RAG modules end-to-end."""

    def __init__(self, lmstudio_url: str, model_name: str = "mistral") -> None:
        self.ingestor = DocumentIngestor()
        self.preprocessor = TextPreprocessor()
        self.chunker = TextChunker()
        self.embedder = EmbeddingGenerator()
        self.vector_store = VectorStore()
        self.retriever = Retriever(self.vector_store)
        self.llm_client = LLMClient(base_url=lmstudio_url, model=model_name)

        # Allow local persistence between runs.
        self.index_path = os.path.join("data", "faiss.index")
        self.meta_path = os.path.join("data", "faiss.meta.json")

    def build(self) -> dict[str, int]:
        """Load, clean, chunk and index documents and return indexing stats."""
        records = self.ingestor.load_document_records()
        documents = [item["text"] for item in records]
        sources = [item["source"] for item in records]

        cleaned = self.preprocessor.clean(documents)
        chunks = self.chunker.chunk(cleaned, sources=sources)

        if len(chunks) == 0:
            raise ValueError("No chunks available. Add files in data/ first.")

        vectors = self.embedder.embed_texts(chunks)
        self.vector_store.add_embeddings(vectors, documents=chunks)
        self.vector_store.save_index(self.index_path, self.meta_path)
        return {
            "document_count": len(records),
            "chunk_count": len(chunks),
        }

    def load_index(self) -> None:
        """Load an existing FAISS index from local files."""
        self.vector_store.load_index(self.index_path, self.meta_path)

    def ask(self, query: str, top_k: int = 5) -> str:
        """Run retrieval and return generated answer."""
        query_vector = self.embedder.embed_query(query)
        contexts = self.retriever.retrieve(query_vector, top_k=top_k, query_text=query)
        return self.llm_client.generate_answer(query, contexts)
