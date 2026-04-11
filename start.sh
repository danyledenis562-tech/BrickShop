#!/usr/bin/env bash
set -e

if [ -f artisan ]; then
  php artisan package:discover --ansi || true
  php artisan storage:link || true
  php artisan migrate --force || true
  if [ "${SEED_ON_START:-false}" = "true" ]; then
    php artisan db:seed --force || true
  fi
  if [ "${MIRROR_IMAGES_ON_START:-false}" = "true" ]; then
    if [ "${MIRROR_IMAGES_FORCE:-false}" = "true" ]; then
      php artisan shop:mirror-product-images --force --no-interaction || true
    else
      php artisan shop:mirror-product-images --no-interaction || true
    fi
  fi
  if [ "${CLOUDINARY_SYNC_ON_START:-false}" = "true" ]; then
    if [ "${CLOUDINARY_SYNC_FORCE:-false}" = "true" ]; then
      php artisan shop:sync-product-images-to-cloudinary --force --no-interaction || true
    else
      php artisan shop:sync-product-images-to-cloudinary --no-interaction || true
    fi
  fi
  php artisan optimize || true
fi

php -d opcache.enable_cli=1 -S 0.0.0.0:${PORT:-10000} -t public
