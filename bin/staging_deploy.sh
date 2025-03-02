#!/bin/bash

set -e  # Exit if any command fails

# Log file
LOG_FILE="/var/www/staging-apex365/staging_deploy.log"

echo "Starting development deployment at $(date)" | tee -a $LOG_FILE

# Navigate to project directory
cd /var/www/staging-apex365 || { echo "Failed to change directory!"; exit 1; }

# Get the branch name from the environment variable (passed from GitHub Actions)
BRANCH_NAME=${BRANCH_NAME:-"dev"}  # Default to dev if not provided
BASE_BRANCH=$(git branch --show-current)
BASE_COMMIT=$(git merge-base origin/$BASE_BRANCH HEAD)

echo "Deploying branch: $BRANCH_NAME" | tee -a $LOG_FILE

# Fetch and checkout the branch
git fetch origin | tee -a $LOG_FILE
git checkout $BRANCH_NAME | tee -a $LOG_FILE
git reset --hard origin/$BRANCH_NAME | tee -a $LOG_FILE  # Force reset to the latest state of the branch

# Set maintenance mode (optional)
php artisan down || true

# Check for database/migrations changes
#if git diff --name-only $BASE_COMMIT HEAD | grep -q "^database/migrations/"; then
#    echo "Migration changes detected, running migrations..."
#    php artisan migrate:fresh --seed --force | tee -a $LOG_FILE
#else
#    echo "No migration changes detected, skipping migrations."
#fi

# Check for composer.json changes
if git diff --name-only $BASE_COMMIT HEAD | grep -q "^composer.json$"; then
    echo "Composer.json changes detected, removing composer.lock and reinstalling dependencies..."
    rm -f composer.lock
    composer install | tee -a $LOG_FILE
else
    echo "No composer.json changes detected, skipping composer install."
fi

# Clear and cache config/routes/views
echo "Optimizing Laravel cache..." | tee -a $LOG_FILE
php artisan optimize:clear | tee -a $LOG_FILE
php artisan optimize | tee -a $LOG_FILE

# Set correct permissions
echo "Setting permissions..." | tee -a $LOG_FILE
chown -R www-data:www-data /var/www/staging-apex365
chmod -R 775 /var/www/staging-apex365/storage /var/www/staging-apex365/bootstrap/cache

# Bring application back online
php artisan up || true

echo "Development deployment completed successfully at $(date)" | tee -a $LOG_FILE
