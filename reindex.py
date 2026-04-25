import os
import sys

from pipeline.rag_pipeline import RAGPipeline


def main() -> int:
    args = sys.argv[1:]
    text_only = "--text-only" in args
    positional = [a for a in args if not a.startswith("--")]
    company_id = positional[0].strip() if positional else None

    lmstudio_url = os.getenv("LMSTUDIO_URL", "http://192.168.100.67:1234")
    model_name = os.getenv("LMSTUDIO_MODEL", "mistral")
    vlm_url = None if text_only else os.getenv("VLM_URL", "http://192.168.100.67:1234")
    vlm_model = None if text_only else os.getenv("VLM_MODEL", "google/gemma-4-e2b")

    pipeline = RAGPipeline(
        lmstudio_url=lmstudio_url,
        model_name=model_name,
        vlm_url=vlm_url,
        vlm_model=vlm_model,
        text_only=text_only,
        company_id=company_id,
    )

    def _progress(pct: int, label: str) -> None:
        print(f"PROGRESS:{pct}:{label}", flush=True)

    stats = pipeline.build(progress_callback=_progress)

    print(
        f"Indexed {stats['chunk_count']} chunks from {stats['document_count']} documents."
    )

    print("Reindex completed successfully.")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
