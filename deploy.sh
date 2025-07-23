#!/bin/bash

# 🚀 KHAIRUN PRODUCTION DEPLOYMENT SCRIPT

set -e

echo "🚀 Starting Khairun Production Deployment..."

# Check if we're in production
if [ "$APP_ENV" != "production" ]; then
    echo "❌ Error: This script should only be run in production environment"
    exit 1
fi

# Step 1: Update codebase
echo "📥 Updating codebase..."
git pull origin main

# Step 2: Install/update dependencies
echo "📦 Installing dependencies..."
composer install --optimize-autoloader --no-dev --no-interaction

# Step 3: Install npm dependencies
echo "🔧 Installing npm dependencies..."
npm ci --only=production

# Step 4: Build assets
echo "🏗️  Building production assets..."
npm run build

# Step 5: Run migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force

# Step 6: Optimize application
echo "⚡ Optimizing application..."
php artisan khairun:optimize --force

# Step 7: Restart services
echo "🔄 Restarting services..."
php artisan queue:restart
php artisan schedule:clear-cache

# Step 8: Health check
echo "🏥 Running health check..."
if php artisan system:health-check --format=json > /tmp/health-check.json; then
    echo "✅ Deployment successful!"
    echo "📊 Health check passed"
else
    echo "⚠️  Deployment completed with warnings"
    echo "📋 Check health status for details"
fi

# Step 9: Create deployment log
echo "📝 Creating deployment log..."
echo "$(date): Deployment completed" >> storage/logs/deployment.log

echo "🎉 Khairun deployment completed!"
echo "🌐 Application is ready at: $APP_URL" 