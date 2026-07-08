<div align="center">
  <h1>Devlio Billing</h1>
  <p><strong>Open-source game server billing platform</strong></p>
  <p>Automated provisioning via Pterodactyl &bull; Stripe &bull; PayPal &bull; Discord</p>
  <p>
    <a href="https://github.com/Bebonaiem/devlio-billing/actions"><img src="https://img.shields.io/github/actions/workflow/status/Bebonaiem/devlio-billing/ci.yml?branch=main&label=CI" alt="CI"></a>
    <a href="https://github.com/Bebonaiem/devlio-billing/blob/main/LICENSE"><img src="https://img.shields.io/github/license/Bebonaiem/devlio-billing" alt="License"></a>
    <a href="https://github.com/Bebonaiem/devlio-billing"><img src="https://img.shields.io/github/stars/Bebonaiem/devlio-billing?style=social" alt="Stars"></a>
  </p>
</div>

---

## One-Command Install (Ubuntu VPS)

```bash
sudo bash -c "$(curl -fsSL https://raw.githubusercontent.com/Bebonaiem/devlio-billing/main/deploy.sh)"
```

This installs everything: Nginx, PHP 8.3, MySQL, Redis, Supervisor, queue workers, SSL via Certbot, and the app itself — asking only for your domain and API keys along the way.

---

## Features

| Category | Capabilities |
|----------|-------------|
| **Game Server Plans** | CPU, RAM, disk, swap, databases, backups — per-plan config |
| **Pterodactyl** | Auto-create, suspend, unsuspend, terminate servers via API |
| **Stripe** | One-time payments, subscriptions, webhooks |
| **PayPal** | Checkout orders, subscriptions, webhook verification |
| **Billing Engine** | Auto invoices, renewals, overdue suspension (3d/14d grace) |
| **Customer Dashboard** | Servers, invoices, tickets, payment methods, affiliate |
| **Admin Panel** | Products, plans, orders, users, settings |
| **Discord** | Real-time alerts on orders, payments, suspensions |
| **Affiliates** | Referral tracking, commission management |
| **REST API** | Public endpoints for products, orders, servers, invoices |
| **Async Jobs** | Queue-driven provisioning via Redis + Supervisor |

---

## Quick Start (Manual)

```bash
git clone https://github.com/Bebonaiem/devlio-billing.git
cd devlio-billing
cp .env.example .env
# Edit .env with your DB, Stripe, PayPal, Pterodactyl credentials
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

### Queue Worker

```bash
php artisan queue:work
```

### Cron (billing automation)

```
* * * * * cd /var/www/devlio-billing && php artisan schedule:run >> /dev/null 2>&1
```

---

## Webhooks

| Gateway | Endpoint |
|---------|----------|
| Stripe | `https://your-domain.com/webhook/stripe` |
| PayPal | `https://your-domain.com/webhook/paypal` |

---

## Requirements

- PHP 8.2+
- MySQL 8.0+
- Composer
- Redis (for queues)
- Nginx
- Pterodactyl Panel v1.0+

---

## License

MIT — free to use, modify, and distribute.
