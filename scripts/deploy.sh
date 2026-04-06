#!/usr/bin/env bash
set -euo pipefail

# 3-way deploy:
# 1) Push local branch to origin
# 2) SSH pull on one or more servers
#
# No secrets should be stored in this repo. Use SSH keys and/or env vars.
#
# Usage:
#   scripts/deploy.sh                 # push + pull on default targets
#   DEPLOY_TARGETS="u@h:/path u@h2:/path2" scripts/deploy.sh
#   DEPLOY_BRANCH=main scripts/deploy.sh
#   DEPLOY_REMOTE=origin scripts/deploy.sh
#
# Optional remote post-pull commands:
#   DEPLOY_AFTER_PULL='php artisan migrate --force' scripts/deploy.sh
#
# Example targets (shared hosting):
#   DEPLOY_TARGETS="n15dzk3l@bwehltd.com:~/verlox.uk" scripts/deploy.sh

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
DEPLOY_REMOTE="${DEPLOY_REMOTE:-origin}"
DEPLOY_BRANCH="${DEPLOY_BRANCH:-main}"
DEPLOY_TARGETS="${DEPLOY_TARGETS:-}"
DEPLOY_AFTER_PULL="${DEPLOY_AFTER_PULL:-}"

cd "$ROOT_DIR"

echo "==> Deploy: push ${DEPLOY_BRANCH} to ${DEPLOY_REMOTE}"
git rev-parse --is-inside-work-tree >/dev/null
git push "$DEPLOY_REMOTE" "$DEPLOY_BRANCH"

if [[ -z "$DEPLOY_TARGETS" ]]; then
  echo "==> No DEPLOY_TARGETS set; push complete."
  exit 0
fi

echo "==> Deploy: pulling on targets"

for t in $DEPLOY_TARGETS; do
  host="${t%%:*}"
  path="${t#*:}"

  if [[ -z "$host" || -z "$path" || "$host" == "$path" ]]; then
    echo "Invalid target: '$t' (expected user@host:/absolute/or/tilde/path)" >&2
    exit 2
  fi

  echo "----> $host:$path"
  ssh -o BatchMode=yes -o StrictHostKeyChecking=accept-new "$host" bash -lc "set -euo pipefail
    cd \"$path\"
    git fetch --prune \"$DEPLOY_REMOTE\"
    git checkout \"$DEPLOY_BRANCH\"
    git pull --ff-only \"$DEPLOY_REMOTE\" \"$DEPLOY_BRANCH\"
    if [[ -n \"$DEPLOY_AFTER_PULL\" ]]; then
      echo \"[after-pull] \$DEPLOY_AFTER_PULL\"
      eval \"$DEPLOY_AFTER_PULL\"
    fi
  "
done

echo "==> Deploy complete."

