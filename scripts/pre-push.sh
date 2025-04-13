#!/usr/bin/env sh

set -e

DIR="$(cd "$(dirname "$0")" && pwd)"
cd "$DIR/.."

if [ ! -f .env ]; then
    echo "No .env found. Copying from .env.example..."
    cp .env.example .env

    echo "Generating APP_KEY..."
    key=$(./vendor/bin/sail artisan key:generate --show)
    sed -i "s|^APP_KEY=!!!REPLACE!!!|APP_KEY=${key}|" .env
fi

./vendor/bin/sail up -d
./vendor/bin/sail artisan test
