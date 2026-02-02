# CI/CD Setup Guide - Inventory System

## Overview

This project uses **GitHub Actions** for CI/CD automation.

## Workflow Structure

### 1. Testing (Runs on every push and PR)
- ✅ Code checkout
- ✅ PHP 8.2 setup
- ✅ Node.js 20 setup
- ✅ Install dependencies (Composer & NPM)
- ✅ Run database migrations
- ✅ Execute PHPUnit tests
- ✅ Code style check (Laravel Pint)

### 2. Deployment (Runs only on main branch)
- ✅ Deploys to production server via SSH
- ✅ Pulls latest code
- ✅ Installs dependencies
- ✅ Builds frontend assets
- ✅ Runs migrations
- ✅ Optimizes Laravel

## Setup Instructions

### Step 1: Push to GitHub

```bash
# Initialize git (if not already done)
cd /Users/user/Desktop/Laravel/inventory-system
git init
git add .
git commit -m "Initial commit with CI/CD setup"

# Add remote and push
git remote add origin https://github.com/YOUR_USERNAME/inventory-system.git
git branch -M main
git push -u origin main
```

### Step 2: Configure GitHub Secrets

Go to: **Your Repository → Settings → Secrets and variables → Actions**

Add these secrets:

#### Required for Deployment:
- `SSH_HOST` - Your server IP or domain (e.g., `192.168.1.100` or `example.com`)
- `SSH_USER` - SSH username (e.g., `root`, `ubuntu`, `forge`)
- `SSH_PRIVATE_KEY` - Your SSH private key (generate with `ssh-keygen`)
- `SSH_PORT` - SSH port (default: `22`)
- `DEPLOY_PATH` - Deployment path (e.g., `/var/www/inventory-system`)

#### Optional:
- `DISCORD_WEBHOOK` - For deployment notifications
- `SLACK_WEBHOOK` - For Slack notifications

### Step 3: Generate SSH Key (if needed)

On your local machine:

```bash
# Generate new SSH key pair
ssh-keygen -t ed25519 -C "deployment@inventory-system" -f ~/.ssh/deploy_key

# Copy public key to server
ssh-copy-id -i ~/.ssh/deploy_key.pub user@your-server.com

# Copy private key content to GitHub secrets
cat ~/.ssh/deploy_key
# Copy the output and paste in SSH_PRIVATE_KEY secret
```

### Step 4: Prepare Your Server

1. **Clone repository on server:**
```bash
cd /var/www
git clone https://github.com/YOUR_USERNAME/inventory-system.git
cd inventory-system
```

2. **Initial server setup:**
```bash
# Install dependencies
composer install --no-dev
npm install
npm run build

# Setup environment
cp .env.example .env
nano .env  # Edit with production values

# Generate key and migrate
php artisan key:generate
php artisan migrate --force

# Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

3. **Configure web server (Nginx example):**
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/inventory-system/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Step 5: Test the Workflow

1. Make a small change to your code
2. Commit and push:
```bash
git add .
git commit -m "Test CI/CD pipeline"
git push origin main
```

3. Watch the workflow in **GitHub → Actions** tab

## Manual Deployment

If you need to deploy manually:

```bash
# On server
cd /var/www/inventory-system
./deploy.sh
```

Or using the script from local:

```bash
# From local machine
ssh user@server 'bash -s' < deploy.sh
```

## Troubleshooting

### Tests Failing?
```bash
# Run tests locally first
php artisan test

# Check specific test
php artisan test --filter InventoryServiceTest
```

### Deployment Failing?
- Check SSH connection: `ssh -i ~/.ssh/deploy_key user@server`
- Verify secrets are set correctly in GitHub
- Check server logs: `tail -f /var/log/nginx/error.log`
- Check Laravel logs: `tail -f storage/logs/laravel.log`

### Permission Issues?
```bash
# On server
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

## Environment Variables

Make sure `.env` on server has:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventory_db
DB_USERNAME=db_user
DB_PASSWORD=secure_password

CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

## Current Test Coverage

- ✅ InventoryService (Unit Tests)
- ✅ ItemRepository (Unit Tests)
- ✅ Inventory Management (Feature Tests)
- ✅ Authentication (Laravel Breeze)

## Adding More Tests

```bash
# Create new test
php artisan make:test Feature/NewFeatureTest
php artisan make:test Unit/NewServiceTest --unit

# Run tests
php artisan test
```

## Workflow File Location

`.github/workflows/laravel.yml`

## Additional Resources

- [GitHub Actions Documentation](https://docs.github.com/en/actions)
- [Laravel Deployment](https://laravel.com/docs/deployment)
- [Laravel Testing](https://laravel.com/docs/testing)
