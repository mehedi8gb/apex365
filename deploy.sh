#!/bin/bash

set -e  # Exit immediately if a command exits with a non-zero status

# Variables
APP_DIR="/var/www/apex365"
LOG_FILE="$APP_DIR/deploy.log"

# Start logging
echo "Deployment started at $(date)" >> $LOG_FILE

# Navigate to the app directory
cd $APP_DIR

# Pull the latest changes
echo "Pulling latest changes..." >> $LOG_FILE
git pull origin main >> $LOG_FILE 2>&1

# Set correct permissions
echo "Setting permissions..." >> $LOG_FILE
chown -R www-data:www-data $APP_DIR
chmod -R 775 $APP_DIR/storage $APP_DIR/bootstrap/cache

# Install dependencies
echo "Installing dependencies..." >> $LOG_FILE
composer install --no-dev --optimize-autoloader >> $LOG_FILE 2>&1

# Migrate and seed database
echo "Running migrations and seeding database..." >> $LOG_FILE
php artisan migrate:fresh --seed --force >> $LOG_FILE 2>&1

# Cache configurations
echo "Caching configurations..." >> $LOG_FILE
php artisan config:cache >> $LOG_FILE 2>&1
php artisan route:cache >> $LOG_FILE 2>&1
php artisan view:cache >> $LOG_FILE 2>&1

# Deployment completed
echo "Deployment completed successfully at $(date)" >> $LOG_FILE
