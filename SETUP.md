# E-Tempah - Setup Documentation

## Table of Contents
1. [Project Overview](#project-overview)
2. [System Requirements](#system-requirements)
3. [Development Environment Setup](#development-environment-setup)
4. [Production Environment Setup](#production-environment-setup)
5. [Configuration Details](#configuration-details)
6. [Troubleshooting](#troubleshooting)

---

## Project Overview

E-Tempah is a vehicle booking management system built with Laravel 10, featuring:
- Admin and Staff role-based access
- Real-time notifications via WebSocket (Laravel Reverb)
- Email verification for user registration
- Booking management with calendar interface (FullCalendar)
- PDF export functionality
- Global search functionality
- Media library integration (Spatie Media Library)

---

## System Requirements

### Minimum Requirements
- PHP 8.1 or higher
- Composer 2.x
- Node.js 16.x or higher
- NPM 8.x or higher
- MySQL 5.7 or higher / MariaDB 10.3 or higher
- Git

### Recommended for Production
- PHP 8.2+
- MySQL 8.0+
- Redis (for caching and queue management)
- Supervisor (for queue workers)
- SSL Certificate (for HTTPS)

---

## Development Environment Setup

### 1. Clone the Repository

```bash
git clone <repository-url>
cd e-tempah
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node Dependencies

```bash
npm install
```

### 4. Environment Configuration

```bash
# Copy the example environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Configure Database

Edit `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=e_tempah
DB_USERNAME=root
DB_PASSWORD=your_password
```

Create the database:

```bash
mysql -u root -p
CREATE DATABASE e_tempah CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;
```

### 6. Run Database Migrations

```bash
php artisan migrate
```

### 7. Email Configuration (Development)

For local development, use **Mailhog** to catch all emails:

#### Install Mailhog

**macOS (using Homebrew):**
```bash
brew install mailhog
brew services start mailhog
```

**Windows (using Chocolatey):**
```bash
choco install mailhog
mailhog
```

**Linux:**
```bash
# Download binary
wget https://github.com/mailhog/MailHog/releases/download/v1.0.1/MailHog_linux_amd64
chmod +x MailHog_linux_amd64
sudo mv MailHog_linux_amd64 /usr/local/bin/mailhog

# Run mailhog
mailhog
```

#### Configure `.env` for Mailhog

```env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=noreply@e-tempah.test
MAIL_FROM_NAME="Sistem eTempah Kenderaan Proton"
```

Access Mailhog web interface at: `http://localhost:8025`

### 8. WebSocket Configuration (Laravel Reverb)

Laravel Reverb is already installed in this project for real-time notifications.

#### Configure `.env` for Reverb (Development)

```env
BROADCAST_DRIVER=reverb

# Reverb Configuration
REVERB_APP_ID=214402
REVERB_APP_KEY=4jndqk7mgcxaihb2j2kt
REVERB_APP_SECRET=sofwm55ltjqowc3l897f
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

# Vite Reverb Configuration
VITE_REVERB_APP_KEY=4jndqk7mgcxaihb2j2kt
VITE_REVERB_HOST=localhost
VITE_REVERB_PORT=8080
VITE_REVERB_SCHEME=http
```

#### Start Reverb Server

```bash
php artisan reverb:start
```

Or run in debug mode:

```bash
php artisan reverb:start --debug
```

### 9. Queue Configuration (Development)

For development, you can use the `sync` driver (default):

```env
QUEUE_CONNECTION=sync
```

Or use `database` driver for better testing:

```env
QUEUE_CONNECTION=database
```

If using database queue, run migrations:

```bash
php artisan queue:table
php artisan migrate
```

Then start the queue worker:

```bash
php artisan queue:work
```

### 10. Storage Setup

Create symbolic link for public storage:

```bash
php artisan storage:link
```

Set correct permissions:

```bash
chmod -R 775 storage bootstrap/cache
```

### 11. Build Frontend Assets

```bash
# Development build with hot reload
npm run dev

# Or build for production
npm run build
```

### 12. Start Development Server

```bash
php artisan serve
```

The application will be available at: `http://localhost:8000`

### 13. Running All Services Together

Open multiple terminal windows/tabs:

**Terminal 1 - Laravel Server:**
```bash
php artisan serve
```

**Terminal 2 - Reverb WebSocket Server:**
```bash
php artisan reverb:start --debug
```

**Terminal 3 - Queue Worker (if using database queue):**
```bash
php artisan queue:work
```

**Terminal 4 - Vite Dev Server:**
```bash
npm run dev
```

**Terminal 5 - Mailhog:**
```bash
mailhog
```

---

## Production Environment Setup

### 1. Server Requirements

- VPS or dedicated server (Ubuntu 22.04 LTS recommended)
- Nginx or Apache web server
- PHP 8.2+ with required extensions
- MySQL 8.0+
- Redis
- Supervisor (for queue workers and Reverb)
- SSL Certificate (Let's Encrypt recommended)

### 2. Install Required PHP Extensions

```bash
sudo apt update
sudo apt install php8.2 php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring \
  php8.2-curl php8.2-zip php8.2-gd php8.2-redis php8.2-bcmath
```

### 3. Clone and Setup Application

```bash
cd /var/www
sudo git clone <repository-url> e-tempah
cd e-tempah

# Set ownership
sudo chown -R www-data:www-data /var/www/e-tempah

# Install dependencies (as www-data user)
sudo -u www-data composer install --optimize-autoloader --no-dev
sudo -u www-data npm ci
sudo -u www-data npm run build
```

### 4. Production Environment Configuration

```bash
sudo -u www-data cp .env.example .env
sudo -u www-data php artisan key:generate
```

Edit `.env` for production:

```env
APP_NAME="E-Tempah"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=e_tempah
DB_USERNAME=e_tempah_user
DB_PASSWORD=strong_secure_password

# Broadcasting
BROADCAST_DRIVER=reverb

# Cache & Session
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail (Production - Gmail or SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-specific-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Sistem eTempah Kenderaan Proton"

# Reverb (Production)
REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST=yourdomain.com
REVERB_PORT=443
REVERB_SCHEME=https

# Vite Reverb (Production)
VITE_REVERB_APP_KEY=your-app-key
VITE_REVERB_HOST=yourdomain.com
VITE_REVERB_PORT=443
VITE_REVERB_SCHEME=https
```

### 5. Generate New Reverb Credentials

```bash
# Generate new secure credentials for production
php artisan reverb:install

# Or manually generate:
APP_ID=$(openssl rand -hex 6)
APP_KEY=$(openssl rand -hex 16)
APP_SECRET=$(openssl rand -hex 16)

echo "REVERB_APP_ID=$APP_ID"
echo "REVERB_APP_KEY=$APP_KEY"
echo "REVERB_APP_SECRET=$APP_SECRET"
```

### 6. Database Setup

Create production database:

```bash
mysql -u root -p
CREATE DATABASE e_tempah CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'e_tempah_user'@'localhost' IDENTIFIED BY 'strong_secure_password';
GRANT ALL PRIVILEGES ON e_tempah.* TO 'e_tempah_user'@'localhost';
FLUSH PRIVILEGES;
exit;
```

Run migrations:

```bash
sudo -u www-data php artisan migrate --force
```

### 7. Storage and Permissions

```bash
sudo -u www-data php artisan storage:link

# Set correct permissions
sudo chown -R www-data:www-data /var/www/e-tempah
sudo chmod -R 775 /var/www/e-tempah/storage
sudo chmod -R 775 /var/www/e-tempah/bootstrap/cache
```

### 8. Optimize for Production

```bash
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
sudo -u www-data php artisan event:cache
```

### 9. Nginx Configuration

Create Nginx configuration file:

```bash
sudo nano /etc/nginx/sites-available/e-tempah
```

Add this configuration:

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/e-tempah/public;

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

    # WebSocket proxy for Laravel Reverb
    location /app {
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "Upgrade";
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_pass http://127.0.0.1:8080;
    }
}
```

Enable the site:

```bash
sudo ln -s /etc/nginx/sites-available/e-tempah /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### 10. SSL Certificate (Let's Encrypt)

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

The Nginx configuration will be automatically updated with SSL.

### 11. Supervisor Configuration

Install Supervisor:

```bash
sudo apt install supervisor
```

#### Queue Worker Configuration

Create `/etc/supervisor/conf.d/e-tempah-worker.conf`:

```ini
[program:e-tempah-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/e-tempah/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/e-tempah/storage/logs/worker.log
stopwaitsecs=3600
```

#### Reverb WebSocket Server Configuration

Create `/etc/supervisor/conf.d/e-tempah-reverb.conf`:

```ini
[program:e-tempah-reverb]
command=php /var/www/e-tempah/artisan reverb:start --host=127.0.0.1 --port=8080
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/e-tempah/storage/logs/reverb.log
```

Start Supervisor services:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start all

# Check status
sudo supervisorctl status
```

### 12. Cron Jobs

Add Laravel scheduler to crontab:

```bash
sudo crontab -e -u www-data
```

Add this line:

```cron
* * * * * cd /var/www/e-tempah && php artisan schedule:run >> /dev/null 2>&1
```

### 13. Redis Setup (Optional but Recommended)

Install Redis:

```bash
sudo apt install redis-server
sudo systemctl enable redis-server
sudo systemctl start redis-server
```

Test Redis connection:

```bash
redis-cli ping
# Should return: PONG
```

---

## Configuration Details

### Email Configuration

#### Gmail Setup

1. Enable 2-Factor Authentication in your Google Account
2. Generate an App Password:
   - Go to Google Account Settings → Security
   - Select "2-Step Verification"
   - Scroll to bottom → "App passwords"
   - Generate password for "Mail" app
3. Use the generated 16-character password (no spaces) in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=abcdefghijklmnop  # 16-char app password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Sistem eTempah Kenderaan Proton"
```

#### Other SMTP Providers

**SendGrid:**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
```

**Mailgun:**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=your-mailgun-username
MAIL_PASSWORD=your-mailgun-password
MAIL_ENCRYPTION=tls
```

### WebSocket (Laravel Reverb) Configuration

#### How It Works

1. **Server Side**: Reverb runs as a standalone WebSocket server
2. **Client Side**: Laravel Echo connects to Reverb and listens for events
3. **Broadcasting**: Events are broadcast to Reverb, which pushes to connected clients

#### Testing WebSocket Connection

```javascript
// In browser console
Echo.connector.pusher.connection.bind('connected', function() {
    console.log('WebSocket connected!');
});

Echo.connector.pusher.connection.bind('error', function(err) {
    console.log('WebSocket error:', err);
});
```

#### Common Reverb Commands

```bash
# Start Reverb server
php artisan reverb:start

# Start with debug output
php artisan reverb:start --debug

# Start on specific host/port
php artisan reverb:start --host=0.0.0.0 --port=8080

# Restart Reverb (when using Supervisor)
sudo supervisorctl restart e-tempah-reverb
```

#### Firewall Configuration for WebSocket

If using a firewall, allow traffic on Reverb port:

```bash
# UFW (Ubuntu)
sudo ufw allow 8080/tcp

# For production with SSL proxy, ensure 443 is open
sudo ufw allow 443/tcp
```

### Event Broadcasting Configuration

The application uses broadcasting for:
- **Booking notifications**: Real-time updates when bookings are created/updated
- **Admin notifications**: Instant alerts for pending approvals

#### Key Broadcast Events

Located in `app/Events/`:

**Example: BookingCreated Event**
```php
class BookingCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function broadcastOn()
    {
        return new PrivateChannel('admin-notifications');
    }

    public function broadcastAs()
    {
        return 'booking.created';
    }
}
```

#### Listening to Events (Frontend)

In `resources/js/app.js`:

```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT,
    wssPort: import.meta.env.VITE_REVERB_PORT,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

// Listen to private channel
Echo.private('admin-notifications')
    .listen('.booking.created', (e) => {
        console.log('New booking:', e);
        // Show notification
    });
```

### Queue Configuration

#### Queue Drivers

**Development**: Use `sync` (runs immediately)
```env
QUEUE_CONNECTION=sync
```

**Production**: Use `redis` (recommended)
```env
QUEUE_CONNECTION=redis
```

#### Jobs in Queue

- Email sending (verification emails)
- Notification dispatching
- PDF generation (if queued)

#### Monitoring Queue

```bash
# Check failed jobs
php artisan queue:failed

# Retry failed job
php artisan queue:retry <job-id>

# Retry all failed jobs
php artisan queue:retry all

# Clear failed jobs
php artisan queue:flush
```

---

## Troubleshooting

### Common Issues

#### 1. WebSocket Connection Failed

**Problem**: Browser console shows "WebSocket connection failed"

**Solutions**:
- Check if Reverb server is running: `php artisan reverb:start`
- Verify `.env` configuration matches `vite.config.js`
- Check firewall allows port 8080
- For production, ensure SSL proxy is configured in Nginx

**Test connection manually**:
```bash
# Install wscat
npm install -g wscat

# Test WebSocket
wscat -c ws://localhost:8080/app/your-app-key
```

#### 2. Email Not Sending

**Problem**: Verification emails not received

**Solutions**:

**Development (Mailhog)**:
- Verify Mailhog is running: `http://localhost:8025`
- Check `.env` has correct Mailhog settings

**Production (Gmail/SMTP)**:
- Verify SMTP credentials are correct
- Check Gmail App Password (not regular password)
- Review Laravel logs: `tail -f storage/logs/laravel.log`
- Test email manually:

```bash
php artisan tinker
Mail::raw('Test email', function($msg) {
    $msg->to('test@example.com')->subject('Test');
});
```

#### 3. Queue Not Processing

**Problem**: Jobs stuck in queue

**Solutions**:
- Start queue worker: `php artisan queue:work`
- Check Supervisor status: `sudo supervisorctl status`
- Restart queue worker: `php artisan queue:restart`
- Check failed jobs: `php artisan queue:failed`

#### 4. Storage Link Not Working

**Problem**: Uploaded files return 404

**Solutions**:
```bash
# Remove existing link
rm public/storage

# Recreate link
php artisan storage:link

# Verify permissions
sudo chmod -R 775 storage
sudo chown -R www-data:www-data storage
```

#### 5. Cache Issues After Deployment

**Problem**: Changes not reflected after deployment

**Solutions**:
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild caches (production only)
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### 6. Permission Denied Errors

**Problem**: Laravel cannot write to storage/logs

**Solutions**:
```bash
# Fix ownership
sudo chown -R www-data:www-data storage bootstrap/cache

# Fix permissions
sudo chmod -R 775 storage bootstrap/cache

# If using Homestead/Vagrant
sudo chown -R vagrant:www-data storage bootstrap/cache
```

### Debugging Commands

```bash
# Check application status
php artisan about

# Test database connection
php artisan db:show

# List all routes
php artisan route:list

# Check environment configuration
php artisan config:show

# View logs in real-time
tail -f storage/logs/laravel.log

# Check queue status
php artisan queue:monitor

# Test broadcasting
php artisan tinker
event(new App\Events\BookingCreated($booking));
```

### Log Files Location

- **Application logs**: `storage/logs/laravel.log`
- **Nginx logs**: `/var/log/nginx/error.log` and `/var/log/nginx/access.log`
- **PHP-FPM logs**: `/var/log/php8.2-fpm.log`
- **Supervisor logs**: `/var/log/supervisor/`
- **Queue worker logs**: `storage/logs/worker.log`
- **Reverb logs**: `storage/logs/reverb.log`

---

## Security Best Practices

### Production Checklist

- [ ] Set `APP_DEBUG=false`
- [ ] Set `APP_ENV=production`
- [ ] Use strong database passwords
- [ ] Generate new `APP_KEY`
- [ ] Generate new Reverb credentials
- [ ] Enable HTTPS with SSL certificate
- [ ] Set up firewall (UFW)
- [ ] Configure rate limiting
- [ ] Regular backups of database
- [ ] Keep Laravel and dependencies updated
- [ ] Monitor error logs regularly
- [ ] Use Redis for session/cache in production
- [ ] Implement regular security updates

### Environment Variables Security

Never commit `.env` file to version control. Add to `.gitignore`:

```gitignore
.env
.env.backup
.env.production
```

### Database Backups

Create automated backup script:

```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u e_tempah_user -p e_tempah > /backup/e_tempah_$DATE.sql
# Keep only last 7 days
find /backup -name "e_tempah_*.sql" -mtime +7 -delete
```

Add to crontab:
```cron
0 2 * * * /path/to/backup-script.sh
```

---

## Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Reverb Documentation](https://laravel.com/docs/reverb)
- [Laravel Broadcasting Documentation](https://laravel.com/docs/broadcasting)
- [Spatie Media Library Documentation](https://spatie.be/docs/laravel-medialibrary)

---

## Support and Maintenance

For issues and questions:
1. Check logs first: `storage/logs/laravel.log`
2. Review this documentation
3. Check Laravel documentation
4. Contact system administrator

## Version History

- **v1.0.0** - Initial release with email verification and WebSocket notifications
