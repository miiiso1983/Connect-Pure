#!/bin/bash

# Connect Pure ERP - Cloudways Deployment Script
echo "🚀 Starting Connect Pure ERP Deployment..."

# Navigate to application directory
cd /home/master/applications/your-app-id/public_html

# Install Composer dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

# Install NPM dependencies and build assets
echo "🎨 Building frontend assets..."
npm install
npm run build

# Set proper permissions
echo "🔐 Setting file permissions..."
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Generate application key if not exists
echo "🔑 Generating application key..."
php artisan key:generate --force

# Clear and cache configurations
echo "⚡ Optimizing application..."
php artisan config:clear
php artisan config:cache
php artisan route:clear
php artisan route:cache
php artisan view:clear
php artisan view:cache

# Run database migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Seed database with initial data
echo "🌱 Seeding database..."
php artisan db:seed --force

# Clear application cache
echo "🧹 Clearing application cache..."
php artisan cache:clear

# Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link

echo "✅ Deployment completed successfully!"
echo "🌐 Your Connect Pure ERP is now live!"
