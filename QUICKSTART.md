# E-Tempah - Quick Start Guide

## For Local Development (5 Minutes Setup)

### Prerequisites
- PHP 8.1+
- Composer
- Node.js & NPM
- MySQL
- Git

### Setup Steps

1. **Clone & Install**
   ```bash
   git clone <repository-url>
   cd e-tempah
   composer install
   npm install
   ```

2. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Configure Database**
   
   Edit `.env`:
   ```env
   DB_DATABASE=e_tempah
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```
   
   Create database:
   ```bash
   mysql -u root -p -e "CREATE DATABASE e_tempah"
   php artisan migrate
   ```

4. **Storage Setup**
   ```bash
   php artisan storage:link
   chmod -R 775 storage bootstrap/cache
   ```

5. **Email Setup (Mailhog)**
   
   Install Mailhog:
   ```bash
   # macOS
   brew install mailhog
   brew services start mailhog
   
   # Or download from: https://github.com/mailhog/MailHog
   ```
   
   Update `.env`:
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=127.0.0.1
   MAIL_PORT=1025
   MAIL_USERNAME=null
   MAIL_PASSWORD=null
   MAIL_ENCRYPTION=null
   ```

6. **Start All Services**
   
   Open 4 terminal windows:
   
   **Terminal 1 - Laravel:**
   ```bash
   php artisan serve
   ```
   
   **Terminal 2 - Reverb WebSocket:**
   ```bash
   php artisan reverb:start --debug
   ```
   
   **Terminal 3 - Vite (Frontend):**
   ```bash
   npm run dev
   ```
   
   **Terminal 4 - Mailhog:**
   ```bash
   mailhog
   ```

7. **Access Application**
   - App: http://localhost:8000
   - Mailhog: http://localhost:8025
   - Admin Login: http://localhost:8000/admin/login
   - Staff Login: http://localhost:8000/staff/login

### Test Registration

1. Go to http://localhost:8000/admin/register
2. Fill in registration form
3. Check Mailhog (http://localhost:8025) for verification email
4. Click verification link
5. Login with your credentials

## Default Configuration

Your `.env` should have these key settings:

```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

BROADCAST_DRIVER=reverb

QUEUE_CONNECTION=sync

# Reverb
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

# Mailhog
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_ENCRYPTION=null
```

## Common Commands

```bash
# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Run migrations
php artisan migrate

# Rebuild frontend
npm run build

# Check application status
php artisan about
```

## Troubleshooting

### WebSocket not connecting?
```bash
# Make sure Reverb is running
php artisan reverb:start --debug
```

### Emails not sending?
```bash
# Check if Mailhog is running
# Visit http://localhost:8025
```

### Frontend not updating?
```bash
# Make sure Vite is running
npm run dev
```

### Permission errors?
```bash
chmod -R 775 storage bootstrap/cache
```

## Next Steps

- Read full documentation: [SETUP.md](SETUP.md)
- Create your first booking
- Explore admin dashboard
- Test real-time notifications

## Need Help?

Check the logs:
```bash
tail -f storage/logs/laravel.log
```
