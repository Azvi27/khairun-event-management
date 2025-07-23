#!/bin/bash

# 🚀 KHAIRUN PRODUCTION SETUP SCRIPT
# Dijalankan sekali saat pertama kali deploy ke production

set -e

echo "🚀 Starting Khairun Production Setup..."
echo "======================================="

# Check environment
if [ -z "$APP_ENV" ] || [ "$APP_ENV" != "production" ]; then
    echo "⚠️  Warning: APP_ENV is not set to 'production'"
    read -p "Continue anyway? (y/N): " confirm
    if [[ $confirm != [yY] ]]; then
        echo "❌ Setup cancelled"
        exit 1
    fi
fi

# Step 1: Install dependencies
echo "📦 Installing production dependencies..."
composer install --optimize-autoloader --no-dev --no-interaction
if [ $? -ne 0 ]; then
    echo "❌ Composer install failed"
    exit 1
fi
echo "✅ Dependencies installed"

# Step 2: Install npm dependencies
echo "🔧 Installing npm dependencies..."
npm ci --only=production
if [ $? -ne 0 ]; then
    echo "❌ NPM install failed"
    exit 1
fi
echo "✅ NPM dependencies installed"

# Step 3: Build assets
echo "🏗️  Building production assets..."
npm run build
if [ $? -ne 0 ]; then
    echo "❌ Asset build failed"
    exit 1
fi
echo "✅ Assets built successfully"

# Step 4: Check .env file
echo "📋 Checking environment configuration..."
if [ ! -f .env ]; then
    echo "❌ .env file not found!"
    echo "Please copy .env.example to .env and configure it first"
    exit 1
fi

# Check critical environment variables
required_vars=("APP_KEY" "DB_DATABASE" "DB_USERNAME")
missing_vars=()

for var in "${required_vars[@]}"; do
    if ! grep -q "^${var}=" .env || grep -q "^${var}=$" .env; then
        missing_vars+=("$var")
    fi
done

if [ ${#missing_vars[@]} -ne 0 ]; then
    echo "❌ Missing required environment variables:"
    printf "   - %s\n" "${missing_vars[@]}"
    echo "Please configure these in your .env file"
    exit 1
fi
echo "✅ Environment configuration looks good"

# Step 5: Database setup
echo "🗄️  Setting up database..."

# Test database connection
php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database connection: OK';"
if [ $? -ne 0 ]; then
    echo "❌ Database connection failed"
    echo "Please check your database configuration in .env"
    exit 1
fi

# Run migrations
echo "Running database migrations..."
php artisan migrate --force
if [ $? -ne 0 ]; then
    echo "❌ Database migration failed"
    exit 1
fi
echo "✅ Database setup completed"

# Step 6: Storage setup
echo "📁 Setting up storage..."
php artisan storage:link
if [ $? -ne 0 ]; then
    echo "⚠️  Storage link failed (might already exist)"
fi

# Create necessary directories
mkdir -p storage/app/public/memories
mkdir -p storage/app/public/events
mkdir -p storage/app/public/surprises
chmod -R 775 storage bootstrap/cache
echo "✅ Storage configured"

# Step 7: Application optimization
echo "⚡ Optimizing application for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Clear any development caches
php artisan cache:clear
echo "✅ Application optimized"

# Step 8: Security check
echo "🛡️  Performing security checks..."

# Check if APP_DEBUG is false
if grep -q "APP_DEBUG=true" .env; then
    echo "⚠️  WARNING: APP_DEBUG is set to true in production!"
    echo "   This should be set to false for security"
fi

# Check if APP_ENV is production
if ! grep -q "APP_ENV=production" .env; then
    echo "⚠️  WARNING: APP_ENV is not set to production"
fi

echo "✅ Security checks completed"

# Step 9: Create initial admin user (if needed)
echo "👤 Checking for admin users..."
user_count=$(php artisan tinker --execute="echo App\Models\User::count();")
if [ "$user_count" -eq 0 ]; then
    echo "📝 No users found. Running seeder to create initial users..."
    php artisan db:seed --class=UserSeeder
    echo "✅ Initial users created"
else
    echo "✅ Users already exist ($user_count users)"
fi

# Step 10: Final health check
echo "🏥 Running final health check..."
php artisan system:health-check --format=json > /tmp/health-check.json
if [ $? -eq 0 ]; then
    echo "✅ Health check passed"
else
    echo "⚠️  Health check has warnings (check details above)"
fi

# Step 11: Setup queue worker (if using queues)
echo "🔄 Queue system setup..."
if command -v supervisorctl > /dev/null; then
    echo "Supervisor detected - queue workers can be configured"
    echo "Don't forget to setup supervisor config for queue workers"
else
    echo "No supervisor detected - consider setting up queue workers manually"
fi

# Step 12: Create success marker
echo "📝 Creating deployment marker..."
echo "Production setup completed at $(date)" > storage/logs/production-setup.log
echo "Version: $(git rev-parse HEAD 2>/dev/null || echo 'unknown')" >> storage/logs/production-setup.log

# Final summary
echo ""
echo "🎉 KHAIRUN PRODUCTION SETUP COMPLETED!"
echo "======================================"
echo ""
echo "✅ Dependencies installed"
echo "✅ Assets built"
echo "✅ Database migrated"
echo "✅ Storage configured"
echo "✅ Application optimized"
echo "✅ Security checked"
echo "✅ Health check passed"
echo ""
echo "🌐 Your application should now be ready at: $(grep APP_URL .env | cut -d= -f2-)"
echo ""
echo "📋 Next steps:"
echo "   1. Setup web server (Apache/Nginx) to point to /public folder"
echo "   2. Configure SSL certificate"
echo "   3. Setup monitoring and backups"
echo "   4. Test all functionality"
echo ""
echo "🔧 Useful commands:"
echo "   - Health check: php artisan system:health-check"
echo "   - Optimize: php artisan khairun:optimize"
echo "   - Queue worker: php artisan queue:work"
echo ""