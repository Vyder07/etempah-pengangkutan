# E-Tempah - Vehicle Booking Management System

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## About E-Tempah

E-Tempah is a comprehensive vehicle booking management system built with Laravel 10, featuring real-time notifications, email verification, and an intuitive interface for managing vehicle reservations.

### Key Features

- 🚗 **Vehicle Booking Management** - Create, approve, and track vehicle reservations
- 👥 **Role-Based Access Control** - Separate Admin and Staff portals with different permissions
- 📧 **Email Verification** - Secure registration with email verification for both admin and staff
- 🔔 **Real-Time Notifications** - WebSocket-powered instant updates using Laravel Reverb
- 📅 **Interactive Calendar** - FullCalendar integration for visualizing bookings
- 📄 **PDF Export** - Generate booking reports in PDF format
- 🔍 **Global Search** - Quick search across bookings and users
- 📱 **Responsive Design** - Mobile-friendly interface with Tailwind CSS
- 📎 **Media Management** - File uploads with Spatie Media Library

### Technology Stack

- **Backend**: Laravel 10 (PHP 8.1+)
- **Frontend**: Blade Templates, Tailwind CSS, Alpine.js
- **Database**: MySQL 8.0+
- **WebSocket**: Laravel Reverb
- **Queue**: Redis (production) / Sync (development)
- **Cache**: Redis
- **Email**: SMTP (Gmail/SendGrid) or Mailhog (development)
- **PDF Generation**: DomPDF

## Quick Start

For a rapid setup, see [QUICKSTART.md](QUICKSTART.md)

For detailed documentation including production deployment, see [SETUP.md](SETUP.md)

### Installation (5 Minutes)

```bash
# Clone repository
git clone <repository-url>
cd e-tempah

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure database in .env, then:
php artisan migrate
php artisan storage:link

# Build assets
npm run build

# Start server
php artisan serve
```

Visit: http://localhost:8000

## Documentation

- **[Quick Start Guide](QUICKSTART.md)** - Get up and running in 5 minutes
- **[Full Setup Documentation](SETUP.md)** - Comprehensive setup for development and production
  - Email configuration (Gmail, Mailhog, SMTP)
  - WebSocket setup (Laravel Reverb)
  - Queue configuration
  - Broadcasting and events
  - Production deployment
  - Troubleshooting

## Project Structure

```
e-tempah/
├── app/
│   ├── Http/Controllers/
│   │   ├── AdminController.php      # Admin functionality
│   │   ├── StaffController.php      # Staff functionality
│   │   └── AuthController.php       # Authentication
│   ├── Models/
│   │   ├── Booking.php              # Booking model
│   │   ├── User.php                 # User model
│   │   └── EventBanner.php          # Event banner model
│   └── Notifications/
│       └── VerifyEmailNotification.php  # Email verification
├── resources/
│   ├── views/
│   │   ├── admin/                   # Admin views
│   │   ├── staff/                   # Staff views
│   │   └── layouts/                 # Layout templates
│   └── js/
│       └── app.js                   # Frontend JavaScript & Echo setup
├── routes/
│   └── web.php                      # Application routes
└── public/
    └── build/                       # Compiled assets
```

## Key Features Explained

### Email Verification Flow

1. User registers (admin or staff)
2. Verification email sent with unique token
3. User clicks verification link
4. Account activated and can login
5. Option to resend verification email if needed

### Real-Time Notifications

Using Laravel Reverb for WebSocket connections:
- Instant booking notifications
- Live booking status updates
- Admin dashboard updates
- No page refresh required

### PDF Export

Generate professional PDF reports for:
- Individual booking details
- Booking summaries
- Export from list views

### Global Search

Quick search functionality:
- **Admin**: Search all bookings and users
- **Staff**: Search own bookings only
- Real-time results with dropdown display
- Click to navigate to details

## Development Workflow

### Running Development Services

Open 4 terminal windows:

```bash
# Terminal 1 - Laravel Server
php artisan serve

# Terminal 2 - WebSocket Server
php artisan reverb:start --debug

# Terminal 3 - Frontend Build
npm run dev

# Terminal 4 - Email Testing (Mailhog)
mailhog
```

### Testing Email Verification

1. Start Mailhog: `mailhog`
2. Configure `.env` for Mailhog (see SETUP.md)
3. Register new user
4. Check emails at http://localhost:8025
5. Click verification link

### Testing WebSocket

1. Start Reverb: `php artisan reverb:start --debug`
2. Open browser console
3. Create a booking
4. Watch real-time notifications in admin dashboard

## About Laravel

This project is built on Laravel - a web application framework with expressive, elegant syntax. Laravel features:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Common Issues & Solutions

### WebSocket Not Connecting
```bash
# Ensure Reverb is running
php artisan reverb:start --debug
```

### Email Not Sending (Development)
```bash
# Start Mailhog
mailhog
# Check emails at http://localhost:8025
```

### Permission Errors
```bash
chmod -R 775 storage bootstrap/cache
```

For more troubleshooting, see [SETUP.md](SETUP.md#troubleshooting)

## System Requirements

- PHP 8.1 or higher
- Composer 2.x
- Node.js 16.x or higher
- MySQL 5.7 or higher
- Redis (recommended for production)

## Production Deployment

For production deployment including:
- SSL/HTTPS configuration
- Nginx setup
- Supervisor configuration for queues and WebSocket
- Redis caching
- Email SMTP setup

See the complete production guide in [SETUP.md](SETUP.md#production-environment-setup)

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks.

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Bootcamp](https://bootcamp.laravel.com)
- [Laracasts Video Tutorials](https://laracasts.com)

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Security

If you discover any security-related issues, please email the project maintainers instead of using the issue tracker.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
