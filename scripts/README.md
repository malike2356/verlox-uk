# Deploy scripts

This folder contains **non-secret** deployment helpers.

## `deploy.sh`

Pushes the current repository to GitHub, then optionally SSHes into one or more servers and runs `git pull`.

Configuration is via environment variables:

- `DEPLOY_REMOTE` (default: `origin`)
- `DEPLOY_BRANCH` (default: `main`)
- `DEPLOY_TARGETS` (space-separated `user@host:/path` entries)
- `DEPLOY_AFTER_PULL` (optional shell command to run after pull, per target)

Example:

```bash
DEPLOY_TARGETS="n15dzk3l@bwehltd.com:~/verlox.uk" \
DEPLOY_AFTER_PULL="php artisan migrate --force" \
./scripts/deploy.sh
```

