import os
from pathlib import Path

from pipeline.rag_pipeline import RAGPipeline


def should_rebuild_index(data_dir: Path, index_path: Path, meta_path: Path) -> bool:
    """Rebuild when index files are missing or older than source documents."""
    if not index_path.exists() or not meta_path.exists():
        return True

    index_mtime = min(index_path.stat().st_mtime, meta_path.stat().st_mtime)
    ignored = {"faiss.index", "faiss.meta.json"}
    allowed_suffixes = {".pdf", ".txt", ".md"}

    for path in data_dir.glob("**/*"):
        if not path.is_file():
            continue
        if path.name in ignored:
            continue
        if path.suffix.lower() not in allowed_suffixes:
            continue
        if path.stat().st_mtime > index_mtime:
            return True

    return False


def main() -> None:
    lmstudio_url = os.getenv("LMSTUDIO_URL", "http://172.20.10.8:1234")
    model_name = os.getenv("LMSTUDIO_MODEL", "mistral")
    pipeline = RAGPipeline(lmstudio_url=lmstudio_url, model_name=model_name)

    data_dir = Path("data")
    index_path = data_dir / "faiss.index"
    meta_path = data_dir / "faiss.meta.json"

    # Rebuild if index is missing or older than source documents.
    if should_rebuild_index(data_dir, index_path, meta_path):
        pipeline.build()
    else:
        pipeline.load_index()

    print("RAG assistant ready. Type your question and press Enter.")
    print("Type 'quit' or 'exit' to stop.")

    while True:
        query = input("\nYou: ").strip()
        if not query:
            continue
        if query.lower() in {"quit", "exit"}:
            print("Goodbye.")
            break

        response = pipeline.ask(query)
        print(f"\nAssistant: {response}")


if __name__ == "__main__":
    main()
