#!/bin/sh

set -e



echo "Installing dependencies..."
composer install

# Check if .env file exists, if not, create it from .env.example
if [ ! -f .env ]; then
    echo ".env not found, creating from .env.example..."
    cp .env.example .env
    php artisan key:generate
fi

echo "Running migrations..."
php artisan migrate --force

echo "Clearing optimized files..."
php artisan optimize:clear

echo "Install npm packages"
npm i

echo "Build npm packages"

npm run build


echo "Starting Laravel server..."
php artisan serve --host=0.0.0.0 --port=8000
