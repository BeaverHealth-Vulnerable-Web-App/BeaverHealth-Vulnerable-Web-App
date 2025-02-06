#!/usr/bin/env bash

set -e

GREEN='\033[1;32m'
BLUE='\033[1;34m'
YELLOW='\033[1;33m'
RED='\033[1;31m'
CYAN='\033[1;36m'
NO_COLOR='\033[0m'

FRESH_DEPLOYMENT=false

show_help() {
    echo -e "${CYAN}Usage: $0 [options]${NO_COLOR}"
    echo -e "Options:"
    echo -e "  -f, --fresh           Build the app for the first time"
    echo -e "  -h, --help            Show this help message"
    exit 0
}

prompt_yes_no() {
    while true; do
        read -p "$(echo -e "$1") (y/n): " yn
        case $yn in
            [Yy]* ) return 0;;
            [Nn]* ) return 1;;
            * ) echo "Please answer yes or no."
        esac
    done
}

while [[ "$#" -gt 0 ]]; do
    case ${1:0:1} in
        -)
            while [[ ${1:0:1} == "-" ]]; do
                if [[ ${1:0:2} == "--" ]]; then
                    # Handle long options
                    case $1 in
                        --fresh) FRESH_DEPLOYMENT=true ;;
                        --help) show_help ;;
                        *) echo -e "${RED}Unknown parameter: $1${NO_COLOR}"; exit 1 ;;
                    esac
                    shift
                    break
                else
                    # Handle combined short options
                    for (( i=1; i<${#1}; i++ )); do
                        case ${1:$i:1} in
                            f) FRESH_DEPLOYMENT=true ;;
                            h) show_help ;;
                            *) echo -e "${RED}Unknown parameter: -${1:$i:1}${NO_COLOR}"; exit 1 ;;
                        esac
                    done
                    shift
                    break
                fi
            done
            ;;
        *)
            echo -e "${RED}Unknown parameter: $1${NO_COLOR}"
            exit 1
            ;;
    esac
done

if [[ "$FRESH_DEPLOYMENT" == true ]]; then
    INSTALL_DEPS=true
    REBUILD_APP=true
    MIGRATE_DB=true
    CLEAR_CACHE=true
else
    echo -e "${CYAN}Please select which actions to perform:${NO_COLOR}"
    prompt_yes_no "${BLUE}Install Laravel dependencies?${NO_COLOR}" && INSTALL_DEPS=true
    prompt_yes_no "${BLUE}Rebuild the application?${NO_COLOR}" && REBUILD_APP=true
    prompt_yes_no "${BLUE}Migrate and seed database?${NO_COLOR}" && MIGRATE_DB=true
    prompt_yes_no "${BLUE}Clear Laravel caches?${NO_COLOR}" && CLEAR_CACHE=true
fi

if [[ "$INSTALL_DEPS" == true ]]; then
    echo -e "${CYAN}Installing Laravel dependencies inside of a container...${NO_COLOR}"
    docker run --rm \
        -u "$(id -u):$(id -g)" \
        -v "$(pwd)":/var/www/html \
        -w /var/www/html \
        laravelsail/php82-composer:latest \
        composer install --optimize-autoloader
fi

if [[ "$REBUILD_APP" == true ]]; then
    USE_DOCKER_CACHE=true
    [[ "$FRESH_DEPLOYMENT" != true ]] && prompt_yes_no "${BLUE}Use Docker cache for rebuild?${NO_COLOR}" && USE_DOCKER_CACHE=true

    echo -e "${CYAN}Deploying application...${NO_COLOR}"
    if [[ "$USE_DOCKER_CACHE" == true ]]; then
        ./vendor/bin/sail build
    else
        ./vendor/bin/sail build --no-cache
    fi
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

    WIPE_DB=true
    [[ "$FRESH_DEPLOYMENT" != true ]] && prompt_yes_no "${BLUE}Wipe database before migrating?${NO_COLOR}" && WIPE_DB=true

    echo -e "${CYAN}Running migrations...${NO_COLOR}"
    [[ "$WIPE_DB" == true ]] && ./vendor/bin/sail artisan db:wipe
    ./vendor/bin/sail artisan migrate

    echo -e "${CYAN}Seeding database...${NO_COLOR}"
    ./vendor/bin/sail artisan db:seed
fi

if [[ "$CLEAR_CACHE" == true ]]; then
    echo -e "${CYAN}Clearing Laravel caches...${NO_COLOR}"
    ./vendor/bin/sail artisan config:clear
    ./vendor/bin/sail artisan cache:clear
    ./vendor/bin/sail artisan route:clear
    ./vendor/bin/sail artisan view:clear
fi

echo -e "${GREEN}Setup complete! Visit the app at http://localhost:9991${NO_COLOR}"
