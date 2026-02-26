#!/usr/bin/env bash
set -e

cd /var/www

if [ ! -f .env ]; then
    cp .env.example .env
fi

composer install --no-interaction --prefer-dist

APP_KEY_LINE=$(grep -n "APP_KEY=" .env | cut -d: -f1)
if [ -z "$(grep "APP_KEY=base64:" .env)" ]; then
    echo "Generating application key..."
    php artisan key:generate --force
else
    echo "Application key already exists, skipping generation."
fi


if ! grep -qE '^DB_CONNECTION=' .env; then
  echo "DB_CONNECTION=sqlite" >> .env
fi
if ! grep -qE '^DB_DATABASE=' .env; then
  echo "DB_DATABASE=/var/www/database/database.sqlite" >> .env
fi
mkdir -p database
touch database/database.sqlite

if ! grep -qE '^APP_URL=' .env; then
  echo "APP_URL=http://localhost:8050" >> .env
fi

if [ ! -d node_modules ]; then
  npm install
else
  npm install
fi

rm -rf public/hot

mkdir -p storage bootstrap/cache
chmod -R ug+rwX storage bootstrap/cache || true

npm run build

php artisan serve --host 0.0.0.0 --port 8050