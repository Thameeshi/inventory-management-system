#!/bin/bash

# Laravel Inventory System - Deployment Script
# This script handles deployment to your production server

set -e

echo "🚀 Starting deployment..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration - Update these values
APP_DIR="${DEPLOY_PATH:-/var/www/inventory-system}"
BRANCH="${DEPLOY_BRANCH:-main}"

# Functions
print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_info() {
    echo -e "${YELLOW}→ $1${NC}"
}

# Step 1: Pull latest code
print_info "Pulling latest code from $BRANCH..."
cd "$APP_DIR"
git pull origin "$BRANCH"
print_success "Code updated"

# Step 2: Install/Update Composer dependencies
print_info "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction
print_success "Composer dependencies installed"

# Step 3: Install/Update NPM dependencies
print_info "Installing NPM dependencies..."
npm ci --production
print_success "NPM dependencies installed"

# Step 4: Build frontend assets
print_info "Building frontend assets..."
npm run build
print_success "Assets built"

# Step 5: Run database migrations
print_info "Running database migrations..."
php artisan migrate --force
print_success "Migrations completed"

# Step 6: Clear and cache configuration
print_info "Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
print_success "Application optimized"

# Step 7: Restart queue workers
print_info "Restarting queue workers..."
php artisan queue:restart
print_success "Queue workers restarted"

# Step 8: Set permissions
print_info "Setting correct permissions..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
print_success "Permissions set"

# Step 9: Reload PHP-FPM (uncomment if using PHP-FPM)
# print_info "Reloading PHP-FPM..."
# sudo systemctl reload php8.2-fpm
# print_success "PHP-FPM reloaded"

# Step 10: Clear OPcache (if enabled)
print_info "Clearing OPcache..."
php artisan optimize:clear
print_success "OPcache cleared"

echo ""
print_success "🎉 Deployment completed successfully!"
echo ""
