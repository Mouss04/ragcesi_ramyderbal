#!/usr/bin/env python3
"""Describe a single image using the VLM and print the result as JSON.

Usage:
    python describe_image.py <image_path>

Environment variables:
    VLM_URL   – base URL of the Vision Language Model server (default: http://192.168.100.67:1234)
    VLM_MODEL – model identifier (default: google/gemma-4-e2b)

Exit codes:
    0 – success, JSON {"description": "..."}
    1 – error,   JSON {"error": "..."}
"""

import json
import os
import sys
from pathlib import Path

from ingestion.image_processor import ImageTooBlurryError, VLMImageProcessor


def main() -> int:
    if len(sys.argv) < 2:
        print(json.dumps({"error": "No image path provided"}))
        return 1

    image_path = Path(sys.argv[1])
    if not image_path.exists():
        print(json.dumps({"error": f"File not found: {image_path}"}))
        return 1

    vlm_url = os.getenv("VLM_URL", "http://192.168.100.67:1234")
    vlm_model = os.getenv("VLM_MODEL", "google/gemma-4-e2b")

    try:
        processor = VLMImageProcessor(base_url=vlm_url, model=vlm_model)

        # Ask the VLM whether the image is blurry anywhere (including corners/edges).
        processor.check_blur_with_vlm(image_path)

        description = processor.describe(image_path)

        # Fallback: if the VLM's own description mentions blur/noise, reject the image.
        if VLMImageProcessor.description_has_blur(description):
            raise ImageTooBlurryError()

        print(json.dumps({"description": description}))
        return 0
    except ImageTooBlurryError as exc:
        print(json.dumps({"error": str(exc), "blurry": True}))
        return 2  # distinct exit code: blurry image
    except Exception as exc:  # noqa: BLE001
        print(json.dumps({"error": str(exc)}))
        return 1


if __name__ == "__main__":
    raise SystemExit(main())
