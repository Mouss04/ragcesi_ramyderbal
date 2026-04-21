import os
import sys

from pipeline.rag_pipeline import RAGPipeline


def main() -> int:
    company_id = sys.argv[1].strip() if len(sys.argv) > 1 else None

    lmstudio_url = os.getenv("LMSTUDIO_URL", "http://192.168.100.67:1234")
    model_name = os.getenv("LMSTUDIO_MODEL", "mistral")
    vlm_url = os.getenv("VLM_URL", "http://192.168.100.67:1234")
    vlm_model = os.getenv("VLM_MODEL", "google/gemma-4-e2b")

    pipeline = RAGPipeline(
        lmstudio_url=lmstudio_url,
        model_name=model_name,
        vlm_url=vlm_url,
        vlm_model=vlm_model,
        company_id=company_id,
    )
    stats = pipeline.build()

    print(
        f"Indexed {stats['chunk_count']} chunks from {stats['document_count']} documents."
    )

    print("Reindex completed successfully.")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
