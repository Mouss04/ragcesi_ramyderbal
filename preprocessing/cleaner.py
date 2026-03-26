from typing import List


class TextPreprocessor:
    """Clean and normalize raw document text."""

    def clean(self, documents: List[str]) -> List[str]:
        """Apply basic text cleaning rules to each document."""
        return [doc.strip() for doc in documents if doc and doc.strip()]
