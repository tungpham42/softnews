#!/usr/bin/env bash
set -Eeuo pipefail

# Newsroom CMS deployment script
# Usage:
#   chmod +x deploy-newsroom.sh
#   ./deploy-newsroom.sh
#
# Optional environment overrides:
#   PROJECT_ROOT=/home/soft/apps/softnews
#   FRONTEND_DIR=/home/soft/apps/softnews/frontend
#   BACKEND_DIR=/home/soft/apps/softnews/backend
#   WEB_ROOT=/home/soft/domains/news.soft.io.vn/public_html
#   API_PORT=8000
#   SERVICE_NAME=newsroom-api

PROJECT_ROOT="${PROJECT_ROOT:-/home/soft/domains/news.soft.io.vn/app}"
FRONTEND_DIR="${FRONTEND_DIR:-$PROJECT_ROOT/frontend}"
BACKEND_DIR="${BACKEND_DIR:-$PROJECT_ROOT/backend}"
WEB_ROOT="${WEB_ROOT:-/home/soft/domains/news.soft.io.vn/public_html}"
API_PORT="${API_PORT:-8000}"
SERVICE_NAME="${SERVICE_NAME:-newsroom-api}"
API_HEALTH_URL="${API_HEALTH_URL:-http://127.0.0.1:${API_PORT}/api/posts}"

log() {
    printf '\n[%s] %s\n' "$(date '+%Y-%m-%d %H:%M:%S')" "$*"
}

fail() {
    printf '\nERROR: %s\n' "$*" >&2
    exit 1
}

trap 'printf "\nERROR: deployment failed at line %s\n" "$LINENO" >&2' ERR

need_cmd() {
    command -v "$1" >/dev/null 2>&1 || fail "Required command not found: $1"
}

run_root() {
    if [[ "$(id -u)" -eq 0 ]]; then
        "$@"
    else
        sudo "$@"
    fi
}

log "Checking environment"
need_cmd node
need_cmd npm
need_cmd composer
need_cmd php
need_cmd rsync
need_cmd curl
need_cmd git

[[ -d "$FRONTEND_DIR" ]] || fail "Frontend directory not found: $FRONTEND_DIR"
[[ -d "$BACKEND_DIR" ]] || fail "Backend directory not found: $BACKEND_DIR"
[[ -d "$WEB_ROOT" ]] || fail "Web root not found: $WEB_ROOT"

log "Versions"
node --version
npm --version
php --version | head -n 1
composer --version

log "Updating source code (optional)"
if [[ "${SKIP_GIT_PULL:-0}" != "1" ]]; then
    if git -C "$FRONTEND_DIR" rev-parse --is-inside-work-tree >/dev/null 2>&1; then
        git -C "$FRONTEND_DIR" pull --ff-only
    fi
    if git -C "$BACKEND_DIR" rev-parse --is-inside-work-tree >/dev/null 2>&1; then
        git -C "$BACKEND_DIR" pull --ff-only
    fi
else
    log "SKIP_GIT_PULL=1; skipping git pull"
fi

log "Building React frontend"
cd "$FRONTEND_DIR"
export NODE_ENV=production
if [[ -f package-lock.json ]]; then
    npm ci
else
    npm install
fi
npm run build

[[ -f "$FRONTEND_DIR/dist/index.html" ]] || fail "React build did not produce dist/index.html"

log "Deploying React build to OpenLiteSpeed document root"
rsync -a --delete "$FRONTEND_DIR/dist/" "$WEB_ROOT/"

log "Installing Symfony production dependencies"
cd "$BACKEND_DIR"
composer install --no-dev --optimize-autoloader --no-interaction

log "Checking Symfony environment"
export APP_ENV=prod
export APP_DEBUG=0
php bin/console about --env=prod >/dev/null

log "Running database migrations"
php bin/console doctrine:migrations:migrate --no-interaction --env=prod

log "Clearing Symfony cache"
php bin/console cache:clear --env=prod

log "Restarting Symfony backend service"
if run_root systemctl cat "$SERVICE_NAME" >/dev/null 2>&1; then
    run_root systemctl restart "$SERVICE_NAME"
    run_root systemctl enable "$SERVICE_NAME" >/dev/null 2>&1 || true
else
    log "systemd service '$SERVICE_NAME' was not found"
    log "Starting Symfony CLI in background instead"
    if command -v symfony >/dev/null 2>&1; then
        symfony server:stop --dir="$BACKEND_DIR" >/dev/null 2>&1 || true
        (cd "$BACKEND_DIR" && symfony server:start -d --no-tls --allow-http --port="$API_PORT" --listen-ip=0.0.0.0)
    else
        fail "Symfony CLI is not installed and systemd service '$SERVICE_NAME' does not exist"
    fi
fi

log "Backend service status"
if run_root systemctl cat "$SERVICE_NAME" >/dev/null 2>&1; then
    run_root systemctl --no-pager --full status "$SERVICE_NAME" || true
fi

log "Checking API on port $API_PORT"
sleep 2
if curl --fail --silent --show-error --max-time 15 "$API_HEALTH_URL" >/tmp/newsroom-api-check.json; then
    printf 'API OK: %s\n' "$API_HEALTH_URL"
    printf 'Response: '
    head -c 500 /tmp/newsroom-api-check.json
    printf '\n'
else
    printf 'WARNING: API health check failed: %s\n' "$API_HEALTH_URL" >&2
    printf 'Check logs with:\n'
    printf '  sudo journalctl -u %s -n 100 --no-pager\n' "$SERVICE_NAME"
fi

log "Checking frontend"
if curl --fail --silent --show-error --max-time 15 -I "https://news.soft.io.vn/" >/dev/null; then
    printf 'Frontend OK: https://news.soft.io.vn/\n'
else
    printf 'WARNING: frontend check failed: https://news.soft.io.vn/\n' >&2
fi

rm -f /tmp/newsroom-api-check.json

log "Deployment completed"
printf '\nFrontend: https://news.soft.io.vn/\n'
printf 'Backend:  http://127.0.0.1:%s\n' "$API_PORT"
printf 'API:      %s\n' "$API_HEALTH_URL"
printf '\n'
