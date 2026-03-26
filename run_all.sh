#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
LARAVEL_DIR="$ROOT_DIR/laravel_ui"
VENV_PY="$ROOT_DIR/.venv/bin/python"
HOST="${HOST:-127.0.0.1}"
PORT="${PORT:-8092}"

if [[ "${1:-}" == "--help" ]]; then
  cat <<'EOF'
Usage: ./run_all.sh [--no-install]

Options:
  --no-install   Skip dependency installation steps.

Environment overrides:
  LMSTUDIO_URL   Default: http://127.0.0.1:1234
  LMSTUDIO_MODEL Default: mistral
  HOST           Default: 127.0.0.1
  PORT           Default: 8092
EOF
  exit 0
fi

NO_INSTALL="false"
if [[ "${1:-}" == "--no-install" ]]; then
  NO_INSTALL="true"
fi

if [[ ! -x "$VENV_PY" ]]; then
  echo "Error: Python venv not found at $VENV_PY"
  echo "Create it first with: python3 -m venv .venv"
  exit 1
fi

if [[ ! -d "$LARAVEL_DIR" ]]; then
  echo "Error: Laravel folder not found at $LARAVEL_DIR"
  exit 1
fi

export LMSTUDIO_URL="${LMSTUDIO_URL:-http://127.0.0.1:1234}"
export LMSTUDIO_MODEL="${LMSTUDIO_MODEL:-mistral}"

echo "[1/4] Using project root: $ROOT_DIR"
echo "[2/4] LMSTUDIO_URL=$LMSTUDIO_URL"
echo "[2/4] LMSTUDIO_MODEL=$LMSTUDIO_MODEL"

if [[ "$NO_INSTALL" == "false" ]]; then
  echo "[3/4] Installing Python dependencies (if needed)..."
  "$VENV_PY" -m pip install -r "$ROOT_DIR/requirements.txt"

  echo "[3/4] Installing Laravel dependencies (if needed)..."
  cd "$LARAVEL_DIR"
  composer install
else
  echo "[3/4] Skipping installs (--no-install)"
  cd "$LARAVEL_DIR"
fi

if [[ ! -f "$LARAVEL_DIR/.env" ]]; then
  cp "$LARAVEL_DIR/.env.example" "$LARAVEL_DIR/.env"
fi

echo "[4/4] Starting Laravel UI at http://$HOST:$PORT"
echo "Open your browser and ask in the RAG box."
exec php artisan serve --host="$HOST" --port="$PORT"
