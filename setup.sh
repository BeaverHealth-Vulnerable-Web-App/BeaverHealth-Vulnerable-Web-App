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
  echo -e "  -f, --fresh           Perform a fresh deployment, including:"
  echo -e "                        - Installing Laravel dependencies"
  echo -e "                        - Building the application"
  echo -e "                        - Migrating and seeding the database"
  echo -e "                        - Clearing Laravel caches"
  echo -e "                        Use this for initial deployments or when you need a clean slate.\n"
  echo -e "  -h, --help            Show this help message"
  exit 0
}

prompt_yes_no() {
  while true; do
    read -p "$(echo -e "$1") (y/n): " yn
    case $yn in
      [Yy]* ) return 0 ;;
      [Nn]* ) return 1 ;;
      * ) echo "Please answer yes or no." ;;
    esac
  done
}

parse_args() {
  while [[ $# -gt 0 ]]; do
    case "$1" in
      -f|--fresh)
        FRESH_DEPLOYMENT=true
        ;;
      -h|--help)
        show_help
        ;;
      *)
        echo -e "${RED}Unknown option: $1${NO_COLOR}" >&2
        show_help
        exit 1
        ;;
    esac
    shift
  done
}

check_docker() {
  if ! command -v docker &> /dev/null; then
    echo -e "${RED}Error: Docker is not installed${NO_COLOR}"
    exit 1
  fi
  if ! docker info &> /dev/null; then
    echo -e "${RED}Error: Docker daemon is not running${NO_COLOR}"
    exit 1
  fi
}

setup_trap() {
  trap 'echo -e "\n${YELLOW}Deployment interrupted. Cleaning up...${NO_COLOR}"; exit 1' INT
}

determine_actions() {
  if [ "$FRESH_DEPLOYMENT" = true ]; then
    INSTALL_DEPS=true
    REBUILD_APP=true
    MIGRATE_DB=true
    CLEAR_CACHE=true
  else
    echo -e "${CYAN}Please select which actions to perform:${NO_COLOR}"
    if prompt_yes_no "${BLUE}Install Laravel dependencies?${NO_COLOR}"; then
      INSTALL_DEPS=true
    fi
    if prompt_yes_no "${BLUE}Rebuild the application?${NO_COLOR}"; then
      REBUILD_APP=true
      if prompt_yes_no "${BLUE}Use Docker cache for rebuild?${NO_COLOR}"; then
          USE_DOCKER_CACHE=true
      fi
    fi
    if prompt_yes_no "${BLUE}Migrate and seed database?${NO_COLOR}"; then
      MIGRATE_DB=true
      if prompt_yes_no "${BLUE}Wipe database before migration?${NO_COLOR}"; then
          WIPE_DB=true
      fi
    fi
    if prompt_yes_no "${BLUE}Clear Laravel caches?${NO_COLOR}"; then
      CLEAR_CACHE=true
    fi
  fi
}

install_dependencies() {
  if [ "$INSTALL_DEPS" = true ]; then
    echo -e "${CYAN}Installing Laravel dependencies inside of a container...${NO_COLOR}"
    docker run --rm \
      -u "$(id -u):$(id -g)" \
      -v "$(pwd)":/var/www/html \
      -w /var/www/html \
      laravelsail/php82-composer:latest \
      composer install --optimize-autoloader
  fi
}

build_application() {
  if [ "$REBUILD_APP" = true ]; then
    echo -e "${CYAN}Building application...${NO_COLOR}"
    if [ "$USE_DOCKER_CACHE" = true ]; then
      ./vendor/bin/sail build
    else
      ./vendor/bin/sail build --no-cache
    fi
  fi
}

start_containers() {
  echo -e "${CYAN}Starting containers..."
  ./vendor/bin/sail up -d
}

wait_for_database() {
  if [ "$MIGRATE_DB" = true ]; then
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
  fi
}

setup_database() {
  if [ "$MIGRATE_DB" = true ]; then
    if [ "$FRESH_DEPLOYMENT" != true && "$WIPE_DB" = true ]; then
        ./vendor/bin/sail artisan db:wipe
    fi
    echo -e "${CYAN}Migrating database...${NO_COLOR}"
    ./vendor/bin/sail artisan migrate
    echo -e "${CYAN}Seeding database...${NO_COLOR}"
    ./vendor/bin/sail artisan db:seed
  fi
}

clear_laravel_cache() {
  if [ "$CLEAR_CACHE" = true ]; then
    echo -e "${CYAN}Clearing Laravel caches...${NO_COLOR}"
    ./vendor/bin/sail artisan config:clear
    ./vendor/bin/sail artisan cache:clear
    ./vendor/bin/sail artisan route:clear
    ./vendor/bin/sail artisan view:clear
  fi
}

main() {
  parse_args "$@"
  check_docker
  setup_trap
  determine_actions
  install_dependencies
  build_application
  start_containers
  wait_for_database
  setup_database
  clear_laravel_cache
  echo -e "${GREEN}Setup complete! Visit the app at http://localhost:9991${NO_COLOR}"
}

main "$@"
