@echo off
echo ========================================
echo    KHAIRUN PRODUCTION DEPLOYMENT
echo ========================================
echo.

:: Check if we're in the right directory
if not exist "artisan" (
    echo ERROR: artisan file not found. Please run this script from Laravel root directory.
    pause
    exit /b 1
)

echo [1/8] Pulling latest code from repository...
git pull origin main
if %errorlevel% neq 0 (
    echo WARNING: Git pull failed or no git repository found.
    echo Continuing with local files...
)
echo.

echo [2/8] Installing PHP dependencies...
composer install --optimize-autoloader --no-dev --no-interaction
if %errorlevel% neq 0 (
    echo ERROR: Composer install failed!
    pause
    exit /b 1
)
echo.

echo [3/8] Installing Node.js dependencies...
npm ci --production --silent
if %errorlevel% neq 0 (
    echo ERROR: NPM install failed!
    pause
    exit /b 1
)
echo.

echo [4/8] Building frontend assets...
npm run build
if %errorlevel% neq 0 (
    echo ERROR: Asset build failed!
    pause
    exit /b 1
)
echo.

echo [5/8] Clearing application caches...
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan event:clear
echo.

echo [6/8] Optimizing for production...
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
echo.

echo [7/8] Running database migrations...
php artisan migrate --force
if %errorlevel% neq 0 (
    echo ERROR: Database migration failed!
    pause
    exit /b 1
)
echo.

echo [8/8] Setting file permissions...
:: For Windows, we'll use icacls instead of chmod
icacls storage /grant Everyone:(OI)(CI)F /T
icacls bootstrap\cache /grant Everyone:(OI)(CI)F /T
echo.

echo [BONUS] Testing email configuration...
php artisan tinker --execute="try { Mail::raw('Deployment successful at ' . now(), function(\$m) { \$m->to('nikha@qamar.my.id')->subject('Khairun Deployment Success'); }); echo 'Email test: SUCCESS'; } catch (Exception \$e) { echo 'Email test: FAILED - ' . \$e->getMessage(); }"
echo.

echo ========================================
echo     DEPLOYMENT COMPLETED SUCCESSFULLY!
echo ========================================
echo.
echo Next steps:
echo 1. Verify website is accessible
echo 2. Test user registration/login
echo 3. Test email functionality
echo 4. Check application logs
echo.
echo Health check: http://qamar.my.id/health
echo.
pause