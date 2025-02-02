#!/usr/bin/env bash

set -e

echo "Installing Laravel dependencies inside of a container..."
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd)":/var/www/html \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --optimize-autoloader

echo "Starting Laravel Sail..."
./vendor/bin/sail build --no-cache
./vendor/bin/sail up -d

echo "Waiting for the database to be ready..."
max_attempts=30
attempt=1
while ! ./vendor/bin/sail exec db mysqladmin ping --silent; do
    echo "Attempt $attempt: Database not ready, waiting 2 seconds..."
    sleep 2
    attempt=$((attempt+1))
    if [ "$attempt" -gt "$max_attempts" ]; then
        echo "Database did not become available after $max_attempts attempts."
        exit 1
    fi
done

echo "Database is ready."

echo "Running migrations..."
./vendor/bin/sail artisan db:wipe
./vendor/bin/sail artisan migrate

echo "Seeding database..."
./vendor/bin/sail artisan db:seed

echo "Clearing Laravel caches..."
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan route:clear
./vendor/bin/sail artisan view:clear

echo "Setup complete! Visit the app at http://localhost:9991"
