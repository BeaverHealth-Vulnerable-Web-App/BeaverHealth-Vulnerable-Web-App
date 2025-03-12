#!/usr/bin/env bash

set -e

GREEN='\033[1;32m'
BLUE='\033[1;34m'
YELLOW='\033[1;33m'
RED='\033[1;31m'
CYAN='\033[1;36m'
NO_COLOR='\033[0m'

MAX_DATABASE_CONNECTION_ATTEMPTS=30

FRESH_DEPLOYMENT=false
INTERACTIVE=false

show_help() {
  echo -e "${CYAN}Usage: $0 [--fresh | --interactive | --help]${NO_COLOR}"
  echo -e "Options:"
  echo -e "  -f, --fresh           Perform a fresh deployment"
  echo -e "  -i, --interactive     Interactively choose deployment actions"
  echo -e "  -h, --help            Show this help message"
}

prompt_yes_no() {
  while true; do
    read -p "$(echo -e "$1") (y/n): " yn
    case $yn in
    [Yy]*) return 0 ;;
    [Nn]*) return 1 ;;
    *) echo "Please answer yes or no." ;;
    esac
  done
}

parse_args() {
  while [[ $# -gt 0 ]]; do
    case "$1" in
    -f | --fresh)
      if [[ "$INTERACTIVE" = true ]]; then
        echo -e "${RED}Error: Cannot use both --fresh and --interactive${NO_COLOR}"
        show_help
        exit 1
      fi
      FRESH_DEPLOYMENT=true
      ;;
    -i | --interactive)
      if [[ "$FRESH_DEPLOYMENT" = true ]]; then
        echo -e "${RED}Error: Cannot use both --fresh and --interactive${NO_COLOR}"
        show_help
        exit 1
      fi
      INTERACTIVE=true
      ;;
    -h | --help)
      show_help
      exit 0
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

preflight_check() {
  if ! command -v docker &>/dev/null; then
    echo -e "${RED}Error: Docker is not installed${NO_COLOR}"
    exit 1
  fi
  if ! docker info &>/dev/null; then
    echo -e "${RED}Error: Docker daemon is not running${NO_COLOR}"
    exit 1
  fi
}

setup_trap() {
  trap 'echo -e "\n${YELLOW}Deployment interrupted.${NO_COLOR}"; exit 1' INT
}

determine_actions() {
  if [[ "$FRESH_DEPLOYMENT" = true ]]; then
    echo -e "${CYAN}Performing fresh deployment...${NO_COLOR}"
    INSTALL_DEPS=true
    REBUILD_APP=true
    MIGRATE_DB=true
    CLEAR_CACHE=true
  elif [[ "$INTERACTIVE" = true ]]; then
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
  else
    echo -e "${RED}Error: Missing argument${NO_COLOR}"
    show_help
    exit 1
  fi
}

install_dependencies() {
  if [[ "$INSTALL_DEPS" = true ]]; then
    echo -e "${CYAN}Installing Laravel dependencies...${NO_COLOR}"
    if ! docker run --rm \
      -u "$(id -u):$(id -g)" \
      -v "$(pwd)":/var/www/html \
      -w /var/www/html \
      laravelsail/php82-composer:latest \
      composer install --optimize-autoloader; then
      echo -e "${RED}Error: Failed to install dependencies${NO_COLOR}"
      exit 1
    fi
  fi
}

ensure_vendor() {
  if [[ ! -d './vendor/' ]]; then
    echo -e "${RED}Missing Laravel dependencies. Make sure to use the --fresh flag for initial deployment.${NO_COLOR}"
    exit 1
  fi
}

build_application() {
  if [[ "$REBUILD_APP" = true ]]; then
    echo -e "${CYAN}Building application...${NO_COLOR}"
    if [[ "$USE_DOCKER_CACHE" = true ]]; then
      if ! ./vendor/bin/sail build; then
        echo -e "${RED}Error: Failed to build application${NO_COLOR}"
        exit 1
      fi
    else
      if ! ./vendor/bin/sail build --no-cache; then
        echo -e "${RED}Error: Failed to build application${NO_COLOR}"
        exit 1
      fi
    fi
  fi
}

start_application() {
  echo -e "${CYAN}Starting containers..."
  if ! ./vendor/bin/sail up -d; then
    echo -e "${RED}Error: Failed to start application${NO_COLOR}"
    exit 1
  fi
}

wait_for_database() {
  if [[ "$MIGRATE_DB" = true ]]; then
    echo -e "${YELLOW}Waiting for the database to be ready...${NO_COLOR}"
    attempt=1
    while ! ./vendor/bin/sail exec db mysqladmin ping --silent; do
      echo -e "${YELLOW}Attempt $attempt: Database not ready, waiting 2 seconds...${NO_COLOR}"
      sleep 2
      attempt=$((attempt + 1))
      if [[ "$attempt" -gt "$MAX_DATABASE_CONNECTION_ATTEMPTS" ]]; then
        echo -e "${RED}Database did not become available after $MAX_DATABASE_CONNECTION_ATTEMPTS attempts.${NO_COLOR}"
        exit 1
      fi
    done
    echo -e "${GREEN}Database is ready.${NO_COLOR}"
  fi
}

setup_database() {
  if [[ "$MIGRATE_DB" = true ]]; then
    if [[ "$FRESH_DEPLOYMENT" != true ]] && [[ "$WIPE_DB" = true ]]; then
      if ! ./vendor/bin/sail artisan db:wipe; then
        echo -e "${RED}Error: Failed to wipe database${NO_COLOR}"
        exit 1
      fi
    fi
    echo -e "${CYAN}Migrating database...${NO_COLOR}"
    if ! ./vendor/bin/sail artisan migrate; then
      echo -e "${RED}Error: Failed to migrate database${NO_COLOR}"
      exit 1
    fi
    echo -e "${CYAN}Seeding database...${NO_COLOR}"
    if ! ./vendor/bin/sail artisan db:seed; then
      echo -e "${RED}Error: Failed to seed database${NO_COLOR}"
      exit 1
    fi
  fi
}

clear_laravel_cache() {
  if [[ "$CLEAR_CACHE" = true ]]; then
    echo -e "${CYAN}Clearing Laravel caches...${NO_COLOR}"
    if ! ./vendor/bin/sail artisan optimize:clear; then
      echo -e "${RED}Error: Failed to clear Laravel caches${NO_COLOR}"
      exit 1
    fi
  fi
}

main() {
  parse_args "$@"
  preflight_check
  setup_trap
  determine_actions
  echo -e "${CYAN}Starting deployment...${NO_COLOR}"
  install_dependencies
  ensure_vendor
  build_application
  start_application
  wait_for_database
  setup_database
  clear_laravel_cache
  echo -e "${GREEN}Setup complete!${NO_COLOR}"
}

main "$@"
