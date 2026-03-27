from typing import Any, Dict, List

import requests


class LLMClient:
    """Handle interactions with a language model."""

    def __init__(self, base_url: str, model: str = "mistral") -> None:
        self.base_url = base_url.rstrip("/")
        self.model = model

    def _resolve_model(self) -> str:
        """Resolve to a loaded LM Studio model, fallback to requested model."""
        try:
            response = requests.get(f"{self.base_url}/v1/models", timeout=30)
            response.raise_for_status()
            data = response.json()
            models = data.get("data", [])
            if not models:
                return self.model

            available = [item.get("id", "") for item in models if item.get("id")]
            if self.model in available:
                return self.model
            return available[0]
        except Exception:
            return self.model

    def _build_context_block(self, contexts: List[Dict[str, Any]]) -> str:
        blocks: List[str] = []
        max_context_items = 4
        max_chars_per_context = 900

        for i, item in enumerate(contexts[:max_context_items], start=1):
            document = item.get("document", {})
            source = document.get("source", "unknown_source")
            text = str(document.get("text", ""))
            if len(text) > max_chars_per_context:
                text = text[:max_chars_per_context].rstrip() + " ..."
            blocks.append(f"[{i}] Source: {source}\n{text}")
        return "\n\n".join(blocks) if blocks else "No relevant context found."

    def generate_answer(self, query: str, contexts: List[Dict[str, Any]]) -> str:
        """Generate an answer using LM Studio's OpenAI-compatible API."""
        context_block = self._build_context_block(contexts)
        model_name = self._resolve_model()

        payload = {
            "model": model_name,
            "messages": [
                {
                    "role": "user",
                    "content": (
                        "You are a helpful RAG assistant. Use only the provided context when possible and cite sources.\n\n"
                        f"Question:\n{query}\n\n"
                        f"Context:\n{context_block}\n\n"
                        "Answer clearly. If context is insufficient, say so."
                    ),
                },
            ],
            "temperature": 0.2,
        }

        response = requests.post(
            f"{self.base_url}/v1/chat/completions",
            json=payload,
            timeout=120,
        )
        if not response.ok:
            details = response.text.strip()
            raise RuntimeError(
                f"LM Studio request failed with {response.status_code}: {details}"
            )
        data = response.json()
        return data["choices"][0]["message"]["content"].strip()
