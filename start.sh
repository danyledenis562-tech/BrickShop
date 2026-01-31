#!/usr/bin/env bash
set -e

if [ -f artisan ]; then
  php artisan package:discover --ansi || true
  php artisan migrate --force || true
fi

php -S 0.0.0.0:${PORT:-10000} -t public
