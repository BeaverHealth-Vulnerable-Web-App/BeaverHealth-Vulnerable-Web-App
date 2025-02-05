#!/usr/bin/env bash

set -e

GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NO_COLOR='\033[0m'

echo -e "${BLUE}Installing Laravel dependencies inside of a container...${NO_COLOR}"
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd)":/var/www/html \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --optimize-autoloader

echo -e "${BLUE}Starting Laravel Sail...${NO_COLOR}"
./vendor/bin/sail build --no-cache
./vendor/bin/sail up -d

echo -e "${YELLOW}Waiting for the database to be ready...${NO_COLOR}"
max_attempts=30
attempt=1
while ! ./vendor/bin/sail exec db mysqladmin ping --silent; do
    echo -e "${YELLOW}Attempt $attempt: Database not ready, waiting 2 seconds...${NO_COLOR}"
    sleep 2
    attempt=$((attempt+1))
    if [ "$attempt" -gt "$max_attempts" ]; then
        echo -e "${RED}Database did not become available after $max_attempts attempts.${NO_COLOR}"
        exit 1
    fi
done

echo -e "${GREEN}Database is ready.${NO_COLOR}"

echo -e "${BLUE}Running migrations...${NO_COLOR}"
./vendor/bin/sail artisan db:wipe
./vendor/bin/sail artisan migrate

echo -e "${BLUE}Seeding database...${NO_COLOR}"
./vendor/bin/sail artisan db:seed

echo -e "${BLUE}Clearing Laravel caches...${NO_COLOR}"
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan route:clear
./vendor/bin/sail artisan view:clear

echo -e "${GREEN}Setup complete! Visit the app at http://localhost:9991${NO_COLOR}"
