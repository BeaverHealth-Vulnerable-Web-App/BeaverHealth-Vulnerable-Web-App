#!/usr/bin/env sh

set -e

echo "Installing Laravel dependencies inside of a container..."
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd)":/var/www/html \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --optimize-autoloader

echo "Starting Laravel Sail..."
./vendor/bin/sail up -d

echo "Running migrations..."
./vendor/bin/sail artisan migrate

echo "Seeding database..."
./vendor/bin/sail artisan db:seed

echo "Setup complete! Visit the app at http://localhost:9991"
