#!/usr/bin/env bash
set -euo pipefail

# Detect PHP binary (common cPanel/EA-PHP paths included)
find_php() {
  local candidates=(
    "${PHP:-}" php php82 php81 php80 php74
    /opt/cpanel/ea-php82/root/usr/bin/php
    /opt/cpanel/ea-php81/root/usr/bin/php
    /opt/cpanel/ea-php80/root/usr/bin/php
    /usr/local/bin/php /usr/bin/php
  )
  for p in "${candidates[@]}"; do
    if [ -n "$p" ] && command -v "$p" >/dev/null 2>&1; then
      echo "$p"; return 0
    fi
  done
  echo "php"
}

# Find Laravel app root (directory containing artisan)
find_app_root() {
  if [ -f "artisan" ]; then echo "$PWD"; return 0; fi
  if [ -f "public_html/artisan" ]; then echo "$PWD/public_html"; return 0; fi
  if [ -f "../artisan" ]; then echo "$(cd .. && pwd)"; return 0; fi
  echo "$PWD"
}

ensure_writable() {
  local path="$1"
  if [ ! -d "$path" ] && [ ! -f "$path" ]; then
    echo "[WARN] $path does not exist yet. Skipping permission check."; return 0
  fi
  if [ -w "$path" ]; then
    echo "[OK] $path is writable"; return 0
  fi
  echo "[WARN] $path not writable. Attempting chmod 775 (recursive)..."
  if chmod -R ug+rwx "$path" 2>/dev/null; then
    echo "[OK] Permission adjusted for $path"
  else
    echo "[FAIL] Could not change permissions for $path."
    echo "       Please set writable permissions via cPanel File Manager:"
    echo "       - Directories: 775 (or 755)"
    echo "       - Files: 664 (or 644)"
    echo "       And ensure ownership is your account user."
  fi
}

main() {
  local APP_ROOT; APP_ROOT="$(find_app_root)"
  cd "$APP_ROOT"

  local PHP_BIN; PHP_BIN="$(find_php)"
  echo "Using PHP: $PHP_BIN"
  echo "App root: $APP_ROOT"

  echo "== Step 1: Clear caches (safe) =="
  if ! "$PHP_BIN" artisan optimize:clear; then
    echo "[WARN] optimize:clear failed (may be due to permissions). Continuing."
  fi

  echo "== Step 2: Ensure writable directories =="
  ensure_writable storage
  ensure_writable bootstrap/cache

  echo "== Step 3: Run database migrations =="
  "$PHP_BIN" artisan migrate --force

  echo "== Step 4: Rebuild caches =="
  "$PHP_BIN" artisan config:cache || true
  "$PHP_BIN" artisan route:cache || true
  "$PHP_BIN" artisan view:cache || true

  echo "== Step 5: Restart queues (if any) =="
  "$PHP_BIN" artisan queue:restart || true

  echo "Deployment steps finished."
}

main "$@"

