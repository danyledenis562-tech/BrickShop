#!/usr/bin/env bash
set -e

if [ -f artisan ]; then
  php artisan package:discover --ansi || true
  php artisan migrate --force || true
  if [ "${SEED_ON_START:-false}" = "true" ]; then
    php artisan db:seed --force || true
  fi
fi

php -S 0.0.0.0:${PORT:-10000} -t public
