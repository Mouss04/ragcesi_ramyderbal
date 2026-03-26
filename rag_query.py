import json
import os
import sys
from typing import Any, Dict, List

from pipeline.rag_pipeline import RAGPipeline


def build_pipeline() -> RAGPipeline:
    lmstudio_url = os.getenv("LMSTUDIO_URL", "http://192.168.100.67:1234")
    model_name = os.getenv("LMSTUDIO_MODEL", "mistral")
    pipeline = RAGPipeline(lmstudio_url=lmstudio_url, model_name=model_name)

    if os.path.exists(os.path.join("data", "faiss.index")):
        pipeline.load_index()
    else:
        pipeline.build()

    return pipeline


def ask_once(query: str, top_k: int = 3) -> Dict[str, Any]:
    pipeline = build_pipeline()
    query_vector = pipeline.embedder.embed_query(query)
    contexts = pipeline.retriever.retrieve(query_vector, top_k=top_k)
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
