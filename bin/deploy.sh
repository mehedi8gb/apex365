#!/bin/bash

set -e  # Exit if any command fails

# Log file
LOG_FILE="/var/www/replica-apex365/deploy.log"

echo "Starting deployment at $(date)" | tee -a $LOG_FILE

# Navigate to project directory
cd /var/www/replica-apex365 || { echo "Failed to change directory!"; exit 1; }

# Get the branch name from the environment variable (passed from GitHub Actions)
BRANCH_NAME=${BRANCH_NAME:-"master"}  # Default to master if not provided

echo "Deploying branch: $BRANCH_NAME" | tee -a $LOG_FILE

# Ensure we are on the correct branch and pull latest changes
git fetch origin | tee -a $LOG_FILE
git checkout $BRANCH_NAME | tee -a $LOG_FILE
git pull origin $BRANCH_NAME | tee -a $LOG_FILE

# Set maintenance mode (optional)
php artisan down || true

# Install dependencies
echo "Installing dependencies..." | tee -a $LOG_FILE
#composer install --no-dev --optimize-autoloader 2>&1 | tee -a $LOG_FILE

# Run database migrations
echo "Skipping migrations..." | tee -a $LOG_FILE
#php artisan migrate --force 2>&1 | tee -a $LOG_FILE

# Clear and cache config/routes/views
echo "Optimizing Laravel cache..." | tee -a $LOG_FILE
php artisan optimize:clear | tee -a $LOG_FILE
php artisan optimize | tee -a $LOG_FILE

# Set correct permissions (if needed)
chown -R www-data:www-data /var/www/replica-apex365
chmod -R 775 /var/www/replica-apex365/storage /var/www/replica-apex365/bootstrap/cache

# Restart queue workers (if using Laravel queue)
echo "Restarting queue workers..." | tee -a $LOG_FILE
php artisan queue:restart 2>&1 | tee -a $LOG_FILE

# Bring application back online
php artisan up || true

echo "Deployment completed successfully at $(date)" | tee -a $LOG_FILE
