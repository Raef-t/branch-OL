#!/usr/bin/env sh
set -e

# Always use .env.docker as the source of truth in production
if [ -f .env.docker ]; then
  cp .env.docker .env
fi


# if [ -f composer.json ]; then
#   composer dump-autoload --optimize --no-interaction || true
# fi

if [ -f .env ]; then
  if ! grep -q "^APP_KEY=base64:" .env; then
    php artisan key:generate --force || true
  fi
fi

if [ -f artisan ]; then
  # Wait for DB to be ready before migrating
  echo "Waiting for database connection..."
  until php artisan db:show > /dev/null 2>&1; do
    echo "  DB not ready yet, retrying in 3s..."
    sleep 3
  done
  echo "Database is ready!"

  php artisan config:cache || true
  php artisan migrate --force || true
  php artisan storage:link || true
fi

exec "$@"
