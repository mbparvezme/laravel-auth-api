#!/bin/bash
set -e

echo "==> Waiting for database..."
timeout=60
elapsed=0
until php -r "
    try {
        new PDO(
            'mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'),
            getenv('DB_USERNAME'),
            getenv('DB_PASSWORD')
        );
        exit(0);
    } catch (Exception \$e) {
        exit(1);
    }
" 2>/dev/null; do
    if [ "$elapsed" -ge "$timeout" ]; then
        echo "ERROR: Database did not become ready within ${timeout}s."
        exit 1
    fi
    echo "   ...retrying in 2s"
    sleep 2
    elapsed=$((elapsed + 2))
done
echo "==> Database ready."

echo "==> Running migrations..."
php artisan migrate --force

if [ "${APP_ENV}" = "production" ]; then
    echo "==> Caching config, routes, and views..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache
fi

echo "==> Starting PHP-FPM..."
exec "$@"
