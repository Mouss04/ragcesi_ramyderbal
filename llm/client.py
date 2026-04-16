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
        max_context_items = 6
        max_chars_per_context = 1800

        for i, item in enumerate(contexts[:max_context_items], start=1):
            document = item.get("document", {})
            source = document.get("source", "unknown_source")
            text = str(document.get("text", ""))
            if len(text) > max_chars_per_context:
                text = text[:max_chars_per_context].rstrip() + " ..."
            blocks.append(f"[Source {i}: {source}]\n{text}")
        return "\n\n---\n\n".join(blocks) if blocks else "No relevant context found."

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
                        "You are a RAG assistant. Answer the question using ONLY the provided context passages.\n"
                        "Rules:\n"
                        "- IMPORTANT: Always respond in the same language as the question. "
                        "If the question is in French, answer in French. If in English, answer in English.\n"
                        "- Be accurate and complete: include all relevant details from the context.\n"
                        "- Cite sources inline when useful, e.g. (Source 1).\n"
                        "- If multiple passages contain complementary information, synthesize them.\n"
                        "- If the context does not contain enough information to answer, say so briefly "
                        "in the same language as the question.\n"
                        "- Do NOT invent facts beyond what is stated in the context.\n\n"
                        f"Question: {query}\n\n"
                        f"Context:\n{context_block}\n\n"
                        "Answer:"
                    ),
                },
            ],
            "temperature": 0.1,
            "max_tokens": 512,
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
