"""Quality benchmark: test the live RAG system against specific questions.

These tests are SKIPPED unless the LMSTUDIO_URL environment variable is set
and LM Studio is reachable. Run with:

    LMSTUDIO_URL=http://192.168.100.67:1234 pytest tests/test_rag_quality.py -v

Each test validates that the returned answer contains expected keywords from
the actual PDFs indexed in the data/ directory (Airbus documents).
"""
import os
import pytest
from pathlib import Path

# Skip the entire module when LM Studio is not available.
LMSTUDIO_URL = os.getenv("LMSTUDIO_URL", "http://192.168.100.67:1234")


def _pipeline_available() -> bool:
    """Return True if the FAISS index exists (system has been indexed)."""
    return (Path("data") / "faiss.index").exists()


# ---------------------------------------------------------------------------
# Fixtures
# ---------------------------------------------------------------------------

@pytest.fixture(scope="module")
def pipeline():
    """Build or load the RAG pipeline once per module."""
    if not _pipeline_available():
        pytest.skip("FAISS index not found — run reindex.py first.")

    from pipeline.rag_pipeline import RAGPipeline
    p = RAGPipeline(lmstudio_url=LMSTUDIO_URL)
    p.load_index()
    return p


@pytest.fixture(scope="module")
def retrieval_only(pipeline):
    """Pipeline in retrieval-only mode (no LLM call)."""
    return pipeline


# ---------------------------------------------------------------------------
# Retrieval quality tests (no LLM needed)
# ---------------------------------------------------------------------------

class TestRetrievalQuality:
    """Validate chunk retrieval precision without touching the LLM."""

    CASES = [
        # English queries
        (
            "How many aircraft did Airbus deliver in 2024?",
            ["deliver", "aircraft", "766"],
        ),
        (
            "What is Airbus total revenue?",
            ["revenue", "billion", "airbus"],
        ),
        (
            "What are Airbus sustainability or carbon targets?",
            ["sustainab", "carbon", "emission", "net zero"],
        ),
        (
            "How many employees does Airbus have?",
            ["employ", "130", "people", "staff", "workforce"],
        ),
        (
            "What aircraft family has the A320?",
            ["a320", "family", "neo"],
        ),
        # French queries — must retrieve from data/20260328194406_future_c2ijcv.pdf
        (
            "De combien le prix des billets va-t-il diminuer ?",
            ["15", "billet", "prix", "diminuer"],
        ),
        (
            "Combien de passagers sont attendus en 2035 ?",
            ["7", "milliard", "passager", "2035"],
        ),
        (
            "Quelles villes utiliseront des taxis aériens ?",
            ["ville", "taxi", "urbain", "uam", "2030"],
        ),
    ]

    @pytest.mark.parametrize("query,expected_keywords", CASES)
    def test_retrieval_contains_keyword(self, retrieval_only, query, expected_keywords):
        qvec = retrieval_only.embedder.embed_query(query)
        results = retrieval_only.retriever.retrieve(qvec, top_k=5, query_text=query)
        combined = " ".join(r["document"]["text"] for r in results).lower()
        matched = [kw for kw in expected_keywords if kw in combined]
        assert matched, (
            f"Query: '{query}'\n"
            f"Expected one of {expected_keywords} in retrieved chunks.\n"
            f"Retrieved text (first 500 chars): {combined[:500]}"
        )


# ---------------------------------------------------------------------------
# End-to-end answer quality tests (require LM Studio)
# ---------------------------------------------------------------------------

class TestAnswerQuality:
    """Validate that full RAG answers are relevant and non-empty."""

    @pytest.mark.skipif(
        not os.getenv("LMSTUDIO_URL"),
        reason="Set LMSTUDIO_URL env var to run LLM quality tests",
    )
    @pytest.mark.parametrize("query,must_contain", [
        (
            "How many commercial aircraft did Airbus deliver in 2024?",
            ["766", "aircraft", "deliver"],
        ),
        (
            "What is Airbus revenue and how does it compare to previous years?",
            ["revenue", "billion", "airbus"],
        ),
        (
            "What is Airbus commitment to sustainability and carbon emissions?",
            ["sustainab", "carbon", "emission"],
        ),
        (
            "What are the main Airbus commercial aircraft product families?",
            ["a320", "a350", "aircraft"],
        ),
        (
            "How many people does Airbus employ globally?",
            ["130", "employ", "people"],
        ),
        (
            "What does Airbus do in the space sector?",
            ["space", "satellit", "airbus"],
        ),
        (
            "What are Airbus financial highlights for 2024?",
            ["revenue", "2024", "billion"],
        ),
    ])
    def test_answer_contains_relevant_info(self, pipeline, query, must_contain):
        answer = pipeline.ask(query)
        assert answer, "Answer must not be empty"
        answer_lower = answer.lower()
        matched = [kw for kw in must_contain if kw in answer_lower]
        # Allow partial match: at least 1 expected keyword should appear.
        assert matched, (
            f"Query: '{query}'\n"
            f"Expected at least one of {must_contain} in the answer.\n"
            f"Got: {answer}"
        )

    @pytest.mark.skipif(
        not os.getenv("LMSTUDIO_URL"),
        reason="Set LMSTUDIO_URL env var to run LLM quality tests",
    )
    def test_unknown_topic_says_not_enough_info(self, pipeline):
        """A query on a topic not in the documents should produce a graceful refusal."""
        query = "What is the recipe for chocolate cake?"
        answer = pipeline.ask(query)
        # The model should not hallucinate; it should say it doesn't know.
        refusal_signals = [
            "not contain", "don't know", "do not know",
            "no information", "cannot answer", "insufficient",
        ]
        answer_lower = answer.lower()
        assert any(sig in answer_lower for sig in refusal_signals), (
            f"Expected a refusal for off-topic query, got: {answer}"
        )

    @pytest.mark.skipif(
        not os.getenv("LMSTUDIO_URL"),
        reason="Set LMSTUDIO_URL env var to run LLM quality tests",
    )
    def test_answer_length_is_reasonable(self, pipeline):
        """Answers should be detailed but not excessively long."""
        query = "Describe Airbus main business activities."
        answer = pipeline.ask(query)
        word_count = len(answer.split())
        assert 10 <= word_count <= 600, (
            f"Answer word count ({word_count}) is outside expected range [10, 600].\n"
            f"Answer: {answer}"
        )


# ---------------------------------------------------------------------------
# French language answer quality tests (require LM Studio)
# ---------------------------------------------------------------------------

class TestFrenchAnswerQuality:
    """Validate that French queries produce correct French answers."""

    @pytest.mark.skipif(
        not os.getenv("LMSTUDIO_URL"),
        reason="Set LMSTUDIO_URL env var to run LLM quality tests",
    )
    @pytest.mark.parametrize("query,must_contain", [
        (
            "De combien le prix des billets va-t-il diminuer ?",
            ["15"],
        ),
        (
            "Combien de passagers sont attendus en 2035 ?",
            ["7", "milliard"],
        ),
        (
            "Combien de villes lanceront des taxis aériens d'ici 2030 ?",
            ["20"],
        ),
        (
            "De combien les erreurs humaines seront-elles réduites grâce à l'automatisation ?",
            ["35"],
        ),
        (
            "Quelle est la portée maximale des avions électriques ?",
            ["500"],
        ),
    ])
    def test_french_answer_contains_relevant_info(self, pipeline, query, must_contain):
        answer = pipeline.ask(query)
        assert answer, "Answer must not be empty"
        answer_lower = answer.lower()
        matched = [kw for kw in must_contain if kw in answer_lower]
        assert matched, (
            f"French query: '{query}'\n"
            f"Expected at least one of {must_contain} in the answer.\n"
            f"Got: {answer}"
        )

    @pytest.mark.skipif(
        not os.getenv("LMSTUDIO_URL"),
        reason="Set LMSTUDIO_URL env var to run LLM quality tests",
    )
    def test_french_query_answered_in_french(self, pipeline):
        """A French question should receive a French-language answer."""
        query = "Combien de passagers sont attendus en 2035 ?"
        answer = pipeline.ask(query)
        # French answers should contain common French function words.
        french_signals = ["de", "le", "la", "les", "en", "par", "est", "par an", "milliard"]
        answer_lower = answer.lower()
        matched = [s for s in french_signals if s in answer_lower]
        assert matched, (
            f"Expected a French-language answer, got: {answer}"
        )
