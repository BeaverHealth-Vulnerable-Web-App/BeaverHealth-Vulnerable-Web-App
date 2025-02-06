#!/usr/bin/env bash

set -e

GREEN='\033[1;32m'
BLUE='\033[1;34m'
YELLOW='\033[1;33m'
RED='\033[1;31m'
NO_COLOR='\033[0m'

FRESH_INSTALL=false
INSTALL_DEPS=false
REBUILD_APP=false
MIGRATE_DB=false
CLEAR_CACHE=false

show_help() {
    echo -e "${BLUE}Usage: $0 [options]${NO_COLOR}"
    echo -e "Options:"
    echo -e "  -f, --fresh           Build the app for the first time"
    echo -e "  -d, --dependencies    Install Laravel dependencies"
    echo -e "  -r, --rebuild         Rebuild the application"
    echo -e "  -m, --migrate         Wipe, migrate, and seed the database"
    echo -e "  -c, --cache           Clear Laravel caches"
    echo -e "  -h, --help            Show this help message"
    exit 0
}

while [[ "$#" -gt 0 ]]; do
    case $1 in
        -f|--fresh) FRESH_INSTALL=true ;;
        -d|--dependencies) INSTALL_DEPS=true ;;
        -r|--rebuild) REBUILD_APP=true ;;
        -m|--migrate) MIGRATE_DB=true ;;
        -c|--cache) CLEAR_CACHE=true ;;
        -h|--help) show_help ;;
        *) echo -e "${RED}Unknown parameter: $1${NO_COLOR}"; exit 1 ;;
    esac
    shift
done

if [[ "$FRESH_INSTALL" == true ]]; then
    INSTALL_DEPS=true
    REBUILD_APP=true
    MIGRATE_DB=true
    CLEAR_CACHE=true
fi

if [[ "$INSTALL_DEPS" == true ]]; then
    echo -e "${BLUE}Installing Laravel dependencies inside of a container...${NO_COLOR}"
    docker run --rm \
        -u "$(id -u):$(id -g)" \
        -v "$(pwd)":/var/www/html \
        -w /var/www/html \
        laravelsail/php82-composer:latest \
        composer install --optimize-autoloader
fi

if [[ "$REBUILD_APP" == true ]]; then
    echo -e "${BLUE}Rebuilding and starting app...${NO_COLOR}"
    ./vendor/bin/sail build --no-cache
    ./vendor/bin/sail up -d
else
    echo -e "${BLUE}Starting app...${NO_COLOR}"
    ./vendor/bin/sail up -d
fi

if [[ "$MIGRATE_DB" == true ]]; then
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
fi

if [[ "$CLEAR_CACHE" == true ]]; then
    echo -e "${BLUE}Clearing Laravel caches...${NO_COLOR}"
    ./vendor/bin/sail artisan config:clear
    ./vendor/bin/sail artisan cache:clear
    ./vendor/bin/sail artisan route:clear
    ./vendor/bin/sail artisan view:clear
fi

echo -e "${GREEN}Setup complete! Visit the app at http://localhost:9991${NO_COLOR}"
