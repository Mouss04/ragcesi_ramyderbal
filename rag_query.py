import json
import os
import sys
from pathlib import Path
from typing import Any, Dict, List

from pipeline.rag_pipeline import RAGPipeline


def _should_rebuild_index(data_dir: Path, index_path: Path, meta_path: Path) -> bool:
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


def build_pipeline() -> RAGPipeline:
    lmstudio_url = os.getenv("LMSTUDIO_URL", "http://172.20.10.8:1234")
    model_name = os.getenv("LMSTUDIO_MODEL", "mistral")
    pipeline = RAGPipeline(lmstudio_url=lmstudio_url, model_name=model_name)

    data_dir = Path("data")
    index_path = data_dir / "faiss.index"
    meta_path = data_dir / "faiss.meta.json"

    if _should_rebuild_index(data_dir, index_path, meta_path):
        pipeline.build()
    else:
        pipeline.load_index()

    return pipeline


def ask_once(query: str, top_k: int = 6) -> Dict[str, Any]:
    pipeline = build_pipeline()
    query_vector = pipeline.embedder.embed_query(query)
    contexts = pipeline.retriever.retrieve(query_vector, top_k=top_k, query_text=query)
    answer = pipeline.llm_client.generate_answer(query, contexts)

    sources: List[str] = []
    for item in contexts:
        document = item.get("document", {})
        source = document.get("source", "unknown_source")
        if source not in sources:
            sources.append(source)

    return {
        "answer": answer,
        "sources": sources,
    }


def main() -> int:
    if len(sys.argv) < 2:
        print("Missing question argument.", file=sys.stderr)
        return 1

    question = sys.argv[1].strip()
    if not question:
        print("Question cannot be empty.", file=sys.stderr)
        return 1

    try:
        result = ask_once(question)
    except Exception as exc:
        print(str(exc), file=sys.stderr)
        return 1

    print(json.dumps(result))
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
