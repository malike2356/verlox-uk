#!/usr/bin/env bash
#
# Zero-downtime-ish deploy for verlox.uk static site over SSH using rsync.
# Requirements:
# - SSH access to the target host
# - rsync installed locally and on the server
# - Environment variables exported or provided inline:
#     DEPLOY_SSH_HOST   # e.g. server.example.com or an SSH config alias
#     DEPLOY_SSH_USER   # e.g. deploy
#     DEPLOY_PATH       # e.g. /home/deploy/apps/verlox.uk/current
#
# Usage:
#   DEPLOY_SSH_HOST=host DEPLOY_SSH_USER=user DEPLOY_PATH=/path ./scripts/deploy.sh
#
set -euo pipefail

require() {
  if [[ -z "${!1:-}" ]]; then
    echo "Missing required env var: $1" >&2
    exit 1
  fi
}

require DEPLOY_SSH_HOST
require DEPLOY_SSH_USER
require DEPLOY_PATH

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
SRC_DIR="${ROOT_DIR}"

EXCLUDES=(
  ".git/"
  ".gitignore"
  "node_modules/"
  "vendor/"
  ".access.config"
  "*.log"
  "scripts/"
)

RSYNC_EXCLUDES=()
for e in "${EXCLUDES[@]}"; do
  RSYNC_EXCLUDES+=(--exclude "$e")
done

echo "Deploying to ${DEPLOY_SSH_USER}@${DEPLOY_SSH_HOST}:${DEPLOY_PATH}"
ssh "${DEPLOY_SSH_USER}@${DEPLOY_SSH_HOST}" "mkdir -p '${DEPLOY_PATH}'"

rsync -az --delete \
  "${RSYNC_EXCLUDES[@]}" \
  --checksum \
  --human-readable \
  --progress \
  "${SRC_DIR}/" "${DEPLOY_SSH_USER}@${DEPLOY_SSH_HOST}:${DEPLOY_PATH}/"

echo "Deploy complete."

