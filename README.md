# Devlio Billing

A gaming-focused billing platform with automated server provisioning via Pterodactyl, Stripe/PayPal payments, Discord notifications, and full customer management.

## Features

- **Product & Plan Management** — Create game server packages with configurable CPU, RAM, disk, swap, databases, backups
- **Pterodactyl Integration** — Auto-create, suspend, unsuspend, and terminate game servers via API
- **Stripe Payments** — One-time and recurring subscription billing with webhooks
- **PayPal Payments** — Checkout orders and subscription billing with webhook verification
- **Billing Engine** — Automated invoice generation, renewal reminders, overdue suspension, and termination
- **Customer Dashboard** — View servers, invoices, payment methods, support tickets
- **Admin Panel** — Manage products, plans, orders, users, and settings
- **Affiliate System** — Referral tracking with commission management
- **Support Ticketing** — Ticket system per order/account
- **Discord Notifications** — Real-time alerts for orders, payments, suspensions
- **REST API** — Public API for products, orders, servers, invoices
- **Async Jobs** — Queue-driven provisioning for non-blocking server creation

## Quick Start

### Manual Setup

```bash
cp .env.example .env
# Edit .env with your database, Stripe, PayPal, Pterodactyl credentials
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

### Ubuntu VPS Auto-Deploy

```bash
# On a fresh Ubuntu 22.04/24.04 VPS:
sudo bash deploy.sh
```

The script will install all dependencies (Nginx, PHP 8.3, MySQL, Redis, Supervisor), configure the app, set up SSL via Certbot, and create the admin user.

### Queue Worker (required for server provisioning)

```bash
php artisan queue:work
```

### Cron (required for billing automation)

```
* * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
```

## Webhook Configuration

| Gateway | Endpoint |
|---------|----------|
| Stripe | `https://your-domain.com/webhook/stripe` |
| PayPal | `https://your-domain.com/webhook/paypal` |

## Requirements

- PHP 8.2+
- MySQL 8.0+
- Composer
- Redis (for queues)
- Nginx
- Pterodactyl Panel (v1.0+)

## License

MIT
