# 🚀 Connect Pure ERP - Cloudways Deployment Guide

## Prerequisites
- Cloudways account
- GitHub repository: https://github.com/miiiso1983/Connect-Pure

## Step 1: Create Cloudways Application

1. **Login to Cloudways Dashboard**
2. **Create New Application**:
   - Application Type: **PHP**
   - Framework: **Laravel**
   - Server Size: **1GB RAM** (minimum)
   - Cloud Provider: **DigitalOcean**
   - Location: Choose closest to your users

## Step 2: Configure Git Deployment

1. **Enable Git Deployment**:
   - Go to Application → **Deployment Via Git**
   - Click **"Enable Git"**

2. **Repository Settings**:
   - Repository URL: `https://github.com/miiiso1983/Connect-Pure.git`
   - Branch: `main`
   - Deploy Path: `/public_html`

3. **Add Deploy Key to GitHub**:
   - Copy SSH key from Cloudways
   - Go to GitHub repo → Settings → Deploy keys
   - Add key with write access

## Step 3: Environment Configuration

Create `.env` file in Cloudways with these settings:

```env
APP_NAME="Connect Pure ERP"
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_KEY
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Connect Pure ERP"
```

## Step 4: Database Setup

1. **Create Database**:
   - Go to Application → **Database**
   - Create new database
   - Note credentials for .env file

## Step 5: Deploy Application

1. **Initial Deployment**:
   - Click **"Deploy Now"** in Git tab
   - Wait for deployment to complete

2. **Run Post-Deployment Commands**:
   ```bash
   cd /home/master/applications/YOUR_APP_ID/public_html
   composer install --no-dev --optimize-autoloader
   npm install && npm run build
   php artisan key:generate --force
   php artisan migrate --force
   php artisan db:seed --force
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan storage:link
   chmod -R 755 storage bootstrap/cache
   ```

## Step 6: SSL Certificate

1. **Enable SSL**:
   - Go to Application → **SSL Certificate**
   - Choose **Let's Encrypt**
   - Add your domain

## Step 7: Domain Configuration

1. **Add Domain**:
   - Go to Application → **Domain Management**
   - Add your custom domain
   - Update DNS records

## Step 8: Final Testing

1. **Test Application**:
   - Visit your domain
   - Test theme switching
   - Verify all features work
   - Check admin panel access

## Default Login Credentials

- **Admin**: admin@connectpure.com / password
- **User**: user@connectpure.com / password

## Troubleshooting

### Common Issues:

1. **500 Error**: Check file permissions
   ```bash
   chmod -R 755 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

2. **Database Connection**: Verify .env database credentials

3. **Assets Not Loading**: Run `npm run build` and clear cache

4. **Theme Not Working**: Ensure CSS files are properly compiled

## Maintenance Commands

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Security Checklist

- ✅ APP_DEBUG=false
- ✅ Strong APP_KEY generated
- ✅ Database credentials secure
- ✅ SSL certificate enabled
- ✅ File permissions set correctly
- ✅ .env file protected

## Support

For deployment issues, contact Cloudways support or check the application logs in the Cloudways dashboard.
