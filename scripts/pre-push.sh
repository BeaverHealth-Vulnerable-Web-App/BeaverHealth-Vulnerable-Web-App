#!/usr/bin/env sh

set -e

DIR="$(cd "$(dirname "$0")" && pwd)"
cd "$DIR/.."

if [ ! -f .env ]; then
    echo "No .env found. Copying from .env.example..."
    cp .env.example .env
fi

echo "Generating fresh APP_KEY..."
key=$(./vendor/bin/sail artisan key:generate --show)
sed -i "s|^APP_KEY=.*|APP_KEY=${key}|" .env

./vendor/bin/sail up -d
./vendor/bin/sail artisan test
