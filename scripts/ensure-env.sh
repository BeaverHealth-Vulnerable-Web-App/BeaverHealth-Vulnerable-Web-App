#!/usr/bin/env sh

set -e

if [[ ! -f .env ]]; then
    echo "No .env found. Copying from .env.example..."
    cp .env.example .env

    echo "Generating APP_KEY..."
    key=$(./vendor/bin/sail artisan key:generate --show)
    sed -i "s|^APP_KEY=.*|APP_KEY=${key}|" .env
fi
