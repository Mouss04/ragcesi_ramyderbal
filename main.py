import os

from pipeline.rag_pipeline import RAGPipeline


def main() -> None:
    lmstudio_url = os.getenv("LMSTUDIO_URL", "http://192.168.100.67:1234")
    model_name = os.getenv("LMSTUDIO_MODEL", "mistral")
    pipeline = RAGPipeline(lmstudio_url=lmstudio_url, model_name=model_name)

    # Build index if it does not exist, otherwise load it.
    if os.path.exists(os.path.join("data", "faiss.index")):
        pipeline.load_index()
    else:
        pipeline.build()

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
