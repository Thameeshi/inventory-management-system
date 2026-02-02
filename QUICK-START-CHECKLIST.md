# Quick Start Checklist

## ✅ Files Created

- [x] `.github/workflows/laravel.yml` - GitHub Actions workflow
- [x] `tests/Unit/Services/InventoryServiceTest.php` - Service tests
- [x] `tests/Unit/Repositories/ItemRepositoryTest.php` - Repository tests
- [x] `tests/Feature/InventoryManagementTest.php` - Feature tests
- [x] `deploy.sh` - Deployment script
- [x] `CI-CD-SETUP.md` - Complete setup guide

## 🚀 Next Steps (In Order)

### 1. Run Tests Locally (Do This First!)
```bash
cd /Users/user/Desktop/Laravel/inventory-system
php artisan test
```

### 2. Fix Any Failing Tests
- Make sure all tests pass before pushing to GitHub
- Update test assertions if needed

### 3. Initialize Git Repository
```bash
git init
git add .
git commit -m "Add CI/CD pipeline and tests"
```

### 4. Create GitHub Repository
- Go to https://github.com/new
- Create repository named "inventory-system"
- Don't initialize with README (you already have one)

### 5. Push to GitHub
```bash
git remote add origin https://github.com/YOUR_USERNAME/inventory-system.git
git branch -M main
git push -u origin main
```

### 6. Watch First Workflow Run
- Go to GitHub → Your Repository → Actions tab
- Watch the "Laravel CI/CD" workflow run
- Fix any errors that appear

### 7. Setup Deployment Secrets (When Ready to Deploy)
Go to: GitHub Repository → Settings → Secrets → Actions

Add these secrets:
- `SSH_HOST` (your server IP/domain)
- `SSH_USER` (username on server)
- `SSH_PRIVATE_KEY` (your SSH private key)
- `DEPLOY_PATH` (e.g., /var/www/inventory-system)

### 8. Prepare Server (When Ready to Deploy)
```bash
# SSH to your server
ssh user@your-server.com

# Install required software
sudo apt update
sudo apt install -y nginx php8.2-fpm php8.2-mysql composer nodejs npm

# Clone repository
cd /var/www
git clone YOUR_GITHUB_REPO_URL inventory-system
cd inventory-system

# Setup
composer install --no-dev
cp .env.example .env
nano .env  # Edit production values
php artisan key:generate
php artisan migrate --force
```

### 9. Test Deployment
```bash
# Make a small change
echo "# Test" >> README.md
git add README.md
git commit -m "Test deployment"
git push origin main
```

### 10. Monitor
- Check GitHub Actions for success/failure
- Check your website is working
- Check logs: `ssh user@server 'tail -f /var/www/inventory-system/storage/logs/laravel.log'`

## 📊 Test Results

Run this command to see test results:
```bash
php artisan test --coverage
```

Expected tests:
- ✅ 12 tests in InventoryServiceTest
- ✅ 12 tests in ItemRepositoryTest
- ✅ 10 tests in InventoryManagementTest
- ✅ ~7 tests from Laravel Breeze (Auth)

**Total: ~41 tests**

## 🔧 Useful Commands

### Local Development
```bash
# Run tests
php artisan test

# Run specific test
php artisan test --filter InventoryServiceTest

# Code style check
./vendor/bin/pint

# Fix code style
./vendor/bin/pint

# Run dev server
php artisan serve
```

### Deployment
```bash
# Deploy manually
ssh user@server 'cd /var/www/inventory-system && git pull && composer install --no-dev && php artisan migrate --force && php artisan optimize'

# Or use script
ssh user@server 'bash -s' < deploy.sh
```

## 🐛 Common Issues

### Issue: Tests fail locally
**Solution:** Make sure database is migrated
```bash
php artisan migrate:fresh
php artisan test
```

### Issue: GitHub Actions fails
**Solution:** Check the workflow log in Actions tab for specific errors

### Issue: Deployment fails
**Solution:** 
- Verify SSH connection works: `ssh user@server`
- Check all GitHub secrets are set correctly
- Make sure server has git, composer, npm installed

### Issue: Permission denied on server
**Solution:**
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

## 📝 Notes

- The workflow runs automatically on every push/PR
- Deployment only happens on `main` branch pushes
- Tests must pass before deployment runs
- Keep your `.env` file secure (it's in .gitignore)
- Store sensitive data in GitHub Secrets, never in code

## 🎯 Success Criteria

✅ All tests pass locally
✅ Code pushed to GitHub
✅ GitHub Actions workflow runs successfully
✅ Green checkmark appears in GitHub
✅ (Optional) Deployment to production works
