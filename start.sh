#!/usr/bin/env bash
set -e

if [ -f artisan ]; then
  php artisan package:discover --ansi || true
  php artisan storage:link || true
  php artisan migrate --force || true
  if [ "${SEED_ON_START:-false}" = "true" ]; then
    php artisan db:seed --force || true
  fi
  php artisan optimize || true
fi

php -d opcache.enable_cli=1 -S 0.0.0.0:${PORT:-10000} -t public
