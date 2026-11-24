#!/bin/sh
set -e

if [ "$RUN_MIGRATIONS" = "true" ]; then
  php artisan migrate --force
fi

if [ -n "$SEED_CLASS" ]; then
  php artisan db:seed --class="$SEED_CLASS" --force
fi

exec php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"