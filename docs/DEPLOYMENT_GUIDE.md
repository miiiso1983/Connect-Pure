# Connect Pure ERP - Deployment Guide

## Table of Contents

1. [System Requirements](#system-requirements)
2. [Pre-deployment Checklist](#pre-deployment-checklist)
3. [Server Setup](#server-setup)
4. [Application Deployment](#application-deployment)
5. [Database Configuration](#database-configuration)
6. [Environment Configuration](#environment-configuration)
7. [Security Configuration](#security-configuration)
8. [Performance Optimization](#performance-optimization)
9. [Monitoring and Logging](#monitoring-and-logging)
10. [Backup and Recovery](#backup-and-recovery)
11. [Post-deployment Testing](#post-deployment-testing)
12. [Troubleshooting](#troubleshooting)

## System Requirements

### Minimum Hardware Requirements

#### Production Environment
- **CPU**: 4 cores, 2.4 GHz or higher
- **RAM**: 8 GB minimum, 16 GB recommended
- **Storage**: 100 GB SSD minimum, 500 GB recommended
- **Network**: 1 Gbps network interface

#### Development Environment
- **CPU**: 2 cores, 2.0 GHz or higher
- **RAM**: 4 GB minimum, 8 GB recommended
- **Storage**: 50 GB SSD minimum
- **Network**: 100 Mbps network interface

### Software Requirements

#### Operating System
- **Linux**: Ubuntu 20.04 LTS or CentOS 8+ (recommended)
- **Windows**: Windows Server 2019 or later
- **macOS**: macOS 10.15 or later (development only)

#### Web Server
- **Nginx**: 1.18+ (recommended)
- **Apache**: 2.4+ (alternative)

#### Database
- **MySQL**: 8.0+ (recommended)
- **PostgreSQL**: 12+ (alternative)
- **SQLite**: 3.25+ (development only)

#### PHP Requirements
- **PHP**: 8.1 or 8.2
- **Extensions**: 
  - BCMath
  - Ctype
  - Fileinfo
  - JSON
  - Mbstring
  - OpenSSL
  - PDO
  - Tokenizer
  - XML
  - GD or Imagick
  - Redis (for caching)

#### Additional Software
- **Composer**: 2.0+
- **Node.js**: 16+ (for asset compilation)
- **NPM**: 8+
- **Redis**: 6.0+ (for caching and sessions)
- **Supervisor**: For queue management

## Pre-deployment Checklist

### Infrastructure Preparation
- [ ] Server provisioned and accessible
- [ ] Domain name configured and DNS propagated
- [ ] SSL certificate obtained and installed
- [ ] Firewall rules configured
- [ ] Load balancer configured (if applicable)
- [ ] CDN configured (if applicable)

### Software Installation
- [ ] Operating system updated
- [ ] Web server installed and configured
- [ ] PHP installed with required extensions
- [ ] Database server installed and secured
- [ ] Redis installed and configured
- [ ] Composer installed globally
- [ ] Node.js and NPM installed

### Security Preparation
- [ ] SSH keys configured
- [ ] Non-root user created for deployment
- [ ] Database user created with limited privileges
- [ ] Backup storage configured
- [ ] Monitoring tools installed

## Server Setup

### Ubuntu 20.04 LTS Setup

#### 1. System Update
```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y curl wget git unzip software-properties-common
```

#### 2. PHP Installation
```bash
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring \
    php8.2-curl php8.2-zip php8.2-gd php8.2-bcmath php8.2-redis php8.2-intl
```

#### 3. Nginx Installation
```bash
sudo apt install -y nginx
sudo systemctl enable nginx
sudo systemctl start nginx
```

#### 4. MySQL Installation
```bash
sudo apt install -y mysql-server
sudo mysql_secure_installation
```

#### 5. Redis Installation
```bash
sudo apt install -y redis-server
sudo systemctl enable redis-server
sudo systemctl start redis-server
```

#### 6. Composer Installation
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
```

#### 7. Node.js Installation
```bash
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs
```

### CentOS 8 Setup

#### 1. System Update
```bash
sudo dnf update -y
sudo dnf install -y curl wget git unzip
```

#### 2. PHP Installation
```bash
sudo dnf install -y epel-release
sudo dnf install -y https://rpms.remirepo.net/enterprise/remi-release-8.rpm
sudo dnf module enable php:remi-8.2 -y
sudo dnf install -y php php-fpm php-mysqlnd php-xml php-mbstring \
    php-curl php-zip php-gd php-bcmath php-redis php-intl
```

#### 3. Nginx Installation
```bash
sudo dnf install -y nginx
sudo systemctl enable nginx
sudo systemctl start nginx
```

#### 4. MySQL Installation
```bash
sudo dnf install -y mysql-server
sudo systemctl enable mysqld
sudo systemctl start mysqld
sudo mysql_secure_installation
```

## Application Deployment

### 1. Create Deployment User
```bash
sudo adduser deploy
sudo usermod -aG www-data deploy
sudo mkdir -p /var/www/connectpure
sudo chown deploy:www-data /var/www/connectpure
```

### 2. Clone Repository
```bash
sudo -u deploy git clone https://github.com/your-org/connect-pure.git /var/www/connectpure
cd /var/www/connectpure
```

### 3. Install Dependencies
```bash
# PHP dependencies
sudo -u deploy composer install --no-dev --optimize-autoloader

# Node.js dependencies
sudo -u deploy npm install

# Build assets
sudo -u deploy npm run production
```

### 4. Set Permissions
```bash
sudo chown -R deploy:www-data /var/www/connectpure
sudo chmod -R 755 /var/www/connectpure
sudo chmod -R 775 /var/www/connectpure/storage
sudo chmod -R 775 /var/www/connectpure/bootstrap/cache
```

### 5. Environment Configuration
```bash
sudo -u deploy cp .env.example .env
sudo -u deploy php artisan key:generate
```

## Database Configuration

### 1. Create Database and User
```sql
-- Connect to MySQL as root
mysql -u root -p

-- Create database
CREATE DATABASE connectpure CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user
CREATE USER 'connectpure'@'localhost' IDENTIFIED BY 'secure_password_here';

-- Grant privileges
GRANT ALL PRIVILEGES ON connectpure.* TO 'connectpure'@'localhost';
FLUSH PRIVILEGES;

-- Exit MySQL
EXIT;
```

### 2. Configure Database Connection
Edit `/var/www/connectpure/.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=connectpure
DB_USERNAME=connectpure
DB_PASSWORD=secure_password_here
```

### 3. Run Migrations
```bash
cd /var/www/connectpure
sudo -u deploy php artisan migrate --force
sudo -u deploy php artisan db:seed --force
```

## Environment Configuration

### Complete .env Configuration
```env
# Application
APP_NAME="Connect Pure ERP"
APP_ENV=production
APP_KEY=base64:generated_key_here
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=connectpure
DB_USERNAME=connectpure
DB_PASSWORD=secure_password_here

# Cache
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-server.com
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="Connect Pure ERP"

# File Storage
FILESYSTEM_DISK=local

# Logging
LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

# Broadcasting
BROADCAST_DRIVER=log

# Queue
QUEUE_CONNECTION=redis

# Session
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_PATH=/
SESSION_DOMAIN=your-domain.com
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=strict

# Security
SANCTUM_STATEFUL_DOMAINS=your-domain.com
```

### Optimize Application
```bash
cd /var/www/connectpure
sudo -u deploy php artisan config:cache
sudo -u deploy php artisan route:cache
sudo -u deploy php artisan view:cache
sudo -u deploy php artisan event:cache
sudo -u deploy php artisan optimize
```

## Security Configuration

### 1. Nginx Configuration
Create `/etc/nginx/sites-available/connectpure`:
```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name your-domain.com www.your-domain.com;
    root /var/www/connectpure/public;
    index index.php;

    # SSL Configuration
    ssl_certificate /path/to/ssl/certificate.crt;
    ssl_certificate_key /path/to/ssl/private.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    # Gzip Compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_proxied expired no-cache no-store private must-revalidate auth;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss;

    # File Upload Limits
    client_max_body_size 100M;

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

    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff|woff2|ttf|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

### 2. Enable Site
```bash
sudo ln -s /etc/nginx/sites-available/connectpure /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 3. PHP-FPM Configuration
Edit `/etc/php/8.2/fpm/pool.d/www.conf`:
```ini
; Process management
pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 500

; Security
security.limit_extensions = .php
```

### 4. Firewall Configuration
```bash
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
sudo ufw enable
```

## Performance Optimization

### 1. PHP Configuration
Edit `/etc/php/8.2/fpm/php.ini`:
```ini
; Memory and execution
memory_limit = 512M
max_execution_time = 300
max_input_time = 300

; File uploads
upload_max_filesize = 100M
post_max_size = 100M

; OPcache
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
opcache.save_comments=1
opcache.fast_shutdown=1

; JIT (PHP 8.0+)
opcache.jit_buffer_size=256M
opcache.jit=1255
```

### 2. MySQL Configuration
Edit `/etc/mysql/mysql.conf.d/mysqld.cnf`:
```ini
[mysqld]
# Performance
innodb_buffer_pool_size = 2G
innodb_log_file_size = 256M
innodb_flush_log_at_trx_commit = 2
innodb_flush_method = O_DIRECT

# Query cache
query_cache_type = 1
query_cache_size = 256M

# Connections
max_connections = 200
```

### 3. Redis Configuration
Edit `/etc/redis/redis.conf`:
```ini
# Memory
maxmemory 1gb
maxmemory-policy allkeys-lru

# Persistence
save 900 1
save 300 10
save 60 10000

# Security
requirepass your_redis_password
```

## Monitoring and Logging

### 1. Supervisor Configuration
Create `/etc/supervisor/conf.d/connectpure-worker.conf`:
```ini
[program:connectpure-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/connectpure/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=deploy
numprocs=4
redirect_stderr=true
stdout_logfile=/var/www/connectpure/storage/logs/worker.log
stopwaitsecs=3600
```

### 2. Log Rotation
Create `/etc/logrotate.d/connectpure`:
```
/var/www/connectpure/storage/logs/*.log {
    daily
    missingok
    rotate 52
    compress
    delaycompress
    notifempty
    create 644 deploy www-data
    postrotate
        sudo systemctl reload php8.2-fpm
    endscript
}
```

### 3. System Monitoring
Install monitoring tools:
```bash
# Install htop for system monitoring
sudo apt install -y htop

# Install netdata for real-time monitoring
bash <(curl -Ss https://my-netdata.io/kickstart.sh)
```

## Backup and Recovery

### 1. Database Backup Script
Create `/home/deploy/backup-db.sh`:
```bash
#!/bin/bash
BACKUP_DIR="/var/backups/connectpure"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="connectpure"
DB_USER="connectpure"
DB_PASS="secure_password_here"

mkdir -p $BACKUP_DIR

# Create database backup
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/db_backup_$DATE.sql.gz

# Keep only last 30 days of backups
find $BACKUP_DIR -name "db_backup_*.sql.gz" -mtime +30 -delete

echo "Database backup completed: db_backup_$DATE.sql.gz"
```

### 2. Application Backup Script
Create `/home/deploy/backup-app.sh`:
```bash
#!/bin/bash
BACKUP_DIR="/var/backups/connectpure"
DATE=$(date +%Y%m%d_%H%M%S)
APP_DIR="/var/www/connectpure"

mkdir -p $BACKUP_DIR

# Backup storage directory
tar -czf $BACKUP_DIR/storage_backup_$DATE.tar.gz -C $APP_DIR storage

# Backup .env file
cp $APP_DIR/.env $BACKUP_DIR/env_backup_$DATE

# Keep only last 30 days of backups
find $BACKUP_DIR -name "storage_backup_*.tar.gz" -mtime +30 -delete
find $BACKUP_DIR -name "env_backup_*" -mtime +30 -delete

echo "Application backup completed: storage_backup_$DATE.tar.gz"
```

### 3. Automated Backup Schedule
Add to crontab (`sudo crontab -e`):
```bash
# Database backup every 6 hours
0 */6 * * * /home/deploy/backup-db.sh

# Application backup daily at 2 AM
0 2 * * * /home/deploy/backup-app.sh

# System cleanup weekly
0 3 * * 0 /usr/bin/apt autoremove -y && /usr/bin/apt autoclean
```

## Post-deployment Testing

### 1. Application Health Check
```bash
# Check application status
curl -I https://your-domain.com

# Test database connection
cd /var/www/connectpure
sudo -u deploy php artisan tinker --execute="DB::connection()->getPdo();"

# Test cache connection
sudo -u deploy php artisan tinker --execute="Cache::put('test', 'value', 60); echo Cache::get('test');"

# Test queue system
sudo -u deploy php artisan queue:work --once
```

### 2. Functional Testing
- [ ] User registration and login
- [ ] Password reset functionality
- [ ] Email notifications
- [ ] File upload functionality
- [ ] Database operations (CRUD)
- [ ] Report generation
- [ ] API endpoints
- [ ] Mobile responsiveness

### 3. Performance Testing
```bash
# Install Apache Bench for load testing
sudo apt install -y apache2-utils

# Basic load test (100 requests, 10 concurrent)
ab -n 100 -c 10 https://your-domain.com/

# Test specific endpoints
ab -n 50 -c 5 https://your-domain.com/modules/hr/employees
```

### 4. Security Testing
- [ ] SSL certificate validation
- [ ] Security headers verification
- [ ] SQL injection testing
- [ ] XSS protection testing
- [ ] CSRF protection testing
- [ ] File upload security
- [ ] Authentication bypass testing

## Troubleshooting

### Common Issues and Solutions

#### 1. Application Not Loading
**Symptoms**: White screen, 500 error, or application not accessible

**Solutions**:
```bash
# Check Nginx error logs
sudo tail -f /var/log/nginx/error.log

# Check PHP-FPM logs
sudo tail -f /var/log/php8.2-fpm.log

# Check application logs
sudo tail -f /var/www/connectpure/storage/logs/laravel.log

# Verify file permissions
sudo chown -R deploy:www-data /var/www/connectpure
sudo chmod -R 755 /var/www/connectpure
sudo chmod -R 775 /var/www/connectpure/storage
sudo chmod -R 775 /var/www/connectpure/bootstrap/cache

# Clear application cache
cd /var/www/connectpure
sudo -u deploy php artisan cache:clear
sudo -u deploy php artisan config:clear
sudo -u deploy php artisan view:clear
```

#### 2. Database Connection Issues
**Symptoms**: Database connection errors, migration failures

**Solutions**:
```bash
# Test database connection
mysql -u connectpure -p connectpure

# Check database service status
sudo systemctl status mysql

# Verify database credentials in .env file
cat /var/www/connectpure/.env | grep DB_

# Reset database connection
cd /var/www/connectpure
sudo -u deploy php artisan config:clear
sudo -u deploy php artisan migrate:status
```

#### 3. Queue Jobs Not Processing
**Symptoms**: Jobs stuck in queue, emails not sending

**Solutions**:
```bash
# Check supervisor status
sudo supervisorctl status

# Restart queue workers
sudo supervisorctl restart connectpure-worker:*

# Check queue status
cd /var/www/connectpure
sudo -u deploy php artisan queue:work --once

# Clear failed jobs
sudo -u deploy php artisan queue:flush
```

#### 4. File Upload Issues
**Symptoms**: File upload failures, permission errors

**Solutions**:
```bash
# Check storage permissions
ls -la /var/www/connectpure/storage/

# Fix storage permissions
sudo chmod -R 775 /var/www/connectpure/storage
sudo chown -R deploy:www-data /var/www/connectpure/storage

# Check PHP upload limits
php -i | grep upload_max_filesize
php -i | grep post_max_size

# Check Nginx upload limits
grep client_max_body_size /etc/nginx/sites-available/connectpure
```

#### 5. Performance Issues
**Symptoms**: Slow page loads, high server load

**Solutions**:
```bash
# Check system resources
htop
df -h
free -m

# Optimize application
cd /var/www/connectpure
sudo -u deploy php artisan optimize
sudo -u deploy php artisan config:cache
sudo -u deploy php artisan route:cache
sudo -u deploy php artisan view:cache

# Check slow queries
sudo mysql -e "SHOW PROCESSLIST;"

# Restart services
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm
sudo systemctl restart mysql
sudo systemctl restart redis-server
```

### Log File Locations
- **Nginx Access**: `/var/log/nginx/access.log`
- **Nginx Error**: `/var/log/nginx/error.log`
- **PHP-FPM**: `/var/log/php8.2-fpm.log`
- **MySQL**: `/var/log/mysql/error.log`
- **Redis**: `/var/log/redis/redis-server.log`
- **Application**: `/var/www/connectpure/storage/logs/laravel.log`
- **Queue Workers**: `/var/www/connectpure/storage/logs/worker.log`

### Emergency Procedures

#### 1. Application Rollback
```bash
# Stop services
sudo systemctl stop nginx
sudo supervisorctl stop connectpure-worker:*

# Restore from backup
cd /var/www
sudo mv connectpure connectpure-failed
sudo tar -xzf /var/backups/connectpure/app_backup_YYYYMMDD.tar.gz

# Restore database
mysql -u connectpure -p connectpure < /var/backups/connectpure/db_backup_YYYYMMDD.sql

# Start services
sudo systemctl start nginx
sudo supervisorctl start connectpure-worker:*
```

#### 2. Database Recovery
```bash
# Stop application
sudo systemctl stop nginx

# Restore database from backup
mysql -u root -p
DROP DATABASE connectpure;
CREATE DATABASE connectpure CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

gunzip < /var/backups/connectpure/db_backup_YYYYMMDD.sql.gz | mysql -u connectpure -p connectpure

# Start application
sudo systemctl start nginx
```

#### 3. Emergency Maintenance Mode
```bash
# Enable maintenance mode
cd /var/www/connectpure
sudo -u deploy php artisan down --message="System maintenance in progress"

# Perform maintenance tasks
# ...

# Disable maintenance mode
sudo -u deploy php artisan up
```

## Scaling and High Availability

### Load Balancer Configuration
For high-traffic deployments, consider implementing load balancing:

```nginx
# /etc/nginx/conf.d/upstream.conf
upstream connectpure_backend {
    server 10.0.1.10:80 weight=3;
    server 10.0.1.11:80 weight=2;
    server 10.0.1.12:80 weight=1;
}

server {
    listen 80;
    server_name your-domain.com;

    location / {
        proxy_pass http://connectpure_backend;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

### Database Replication
For database high availability:

```sql
-- Master configuration (my.cnf)
[mysqld]
server-id = 1
log-bin = mysql-bin
binlog-do-db = connectpure

-- Slave configuration (my.cnf)
[mysqld]
server-id = 2
relay-log = mysql-relay-bin
log-slave-updates = 1
read-only = 1
```

### Session Storage
For multi-server deployments, use Redis for session storage:

```env
# .env configuration
SESSION_DRIVER=redis
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=redis-cluster.example.com
REDIS_PASSWORD=secure_redis_password
REDIS_PORT=6379
```

## Maintenance Procedures

### Regular Maintenance Tasks

#### Daily Tasks
- Monitor system resources and performance
- Check application and system logs for errors
- Verify backup completion
- Monitor queue job processing

#### Weekly Tasks
- Update system packages
- Clean up temporary files and logs
- Review security logs
- Test backup restoration procedures

#### Monthly Tasks
- Update application dependencies
- Review and optimize database performance
- Security audit and vulnerability assessment
- Capacity planning review

### Update Procedures

#### Application Updates
```bash
# 1. Enable maintenance mode
cd /var/www/connectpure
sudo -u deploy php artisan down

# 2. Backup current version
sudo tar -czf /var/backups/connectpure/app_backup_$(date +%Y%m%d).tar.gz /var/www/connectpure

# 3. Pull latest changes
sudo -u deploy git pull origin main

# 4. Update dependencies
sudo -u deploy composer install --no-dev --optimize-autoloader
sudo -u deploy npm install && npm run production

# 5. Run migrations
sudo -u deploy php artisan migrate --force

# 6. Clear caches
sudo -u deploy php artisan optimize

# 7. Disable maintenance mode
sudo -u deploy php artisan up
```

#### System Updates
```bash
# Update packages
sudo apt update && sudo apt upgrade -y

# Restart services if needed
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm
sudo systemctl restart mysql
```

## Contact and Support

### Emergency Contacts
- **System Administrator**: admin@your-company.com
- **Database Administrator**: dba@your-company.com
- **Security Team**: security@your-company.com

### Support Resources
- **Documentation**: https://docs.connectpure.com
- **Issue Tracker**: https://github.com/your-org/connect-pure/issues
- **Community Forum**: https://community.connectpure.com

---

**Document Version**: 1.0
**Last Updated**: {{ date('Y-m-d') }}
**Next Review**: {{ date('Y-m-d', strtotime('+6 months')) }}

*This deployment guide should be reviewed and updated regularly to reflect changes in the application and infrastructure requirements.*
