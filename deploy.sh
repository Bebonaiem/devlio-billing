#!/bin/bash
set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m'

echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}    Devlio Billing - Ubuntu Installer    ${NC}"
echo -e "${GREEN}========================================${NC}"

if [ "$EUID" -ne 0 ]; then
    echo -e "${RED}Please run as root (use sudo)${NC}"
    exit 1
fi

. /etc/os-release
if [ "$ID" != "ubuntu" ]; then
    echo -e "${RED}This script is for Ubuntu only${NC}"
    exit 1
fi

echo -e "${GREEN}Installing on Ubuntu $VERSION_ID${NC}"
echo ""

# ──────────────────────────────────────────────
# 1. HOST (IP or domain)
# ──────────────────────────────────────────────
echo -e "${YELLOW}Step 1: Server Host${NC}"
echo -e "Enter your server's ${CYAN}IP address${NC} or ${CYAN}domain name${NC}."
echo -e "If you enter a domain, SSL will be set up automatically."
read -p "Host (IP or domain): " HOST

APP_URL="http://$HOST"
if [[ "$HOST" =~ \. ]]; then
    APP_URL="https://$HOST"
fi

echo ""

# ──────────────────────────────────────────────
# 2. Admin user
# ──────────────────────────────────────────────
echo -e "${YELLOW}Step 2: Admin Account${NC}"
echo -e "Create the first user for the platform."
read -p "Name: " ADMIN_NAME
read -p "Email: " ADMIN_EMAIL
read -p "Password: " ADMIN_PASSWORD
echo ""
echo -e "Should this user be an ${CYAN}admin${NC} (full access) or a regular ${CYAN}user${NC}?"
select ADMIN_ROLE in "admin" "user"; do
    case $ADMIN_ROLE in
        admin|user) break;;
    esac
done
echo ""

# ──────────────────────────────────────────────
# 3. Pterodactyl
# ──────────────────────────────────────────────
echo -e "${YELLOW}Step 3: Pterodactyl Integration${NC}"
echo -e "You can skip this and configure later in the admin panel."
read -p "Pterodactyl Panel URL (or press Enter to skip): " PTERODACTYL_URL
if [ -n "$PTERODACTYL_URL" ]; then
    read -p "Pterodactyl Application API Key (ptlc_...): " PTERODACTYL_KEY
fi
echo ""

# ──────────────────────────────────────────────
# 4. Stripe (optional)
# ──────────────────────────────────────────────
echo -e "${YELLOW}Step 4: Stripe (optional)${NC}"
read -p "Stripe Public Key (pk_...): " STRIPE_PK
read -p "Stripe Secret Key (sk_...): " STRIPE_SK
read -p "Stripe Webhook Secret (whsec_...): " STRIPE_WH
echo ""

# ──────────────────────────────────────────────
# 5. PayPal (optional)
# ──────────────────────────────────────────────
echo -e "${YELLOW}Step 5: PayPal (optional)${NC}"
read -p "PayPal Client ID: " PAYPAL_CID
read -p "PayPal Secret: " PAYPAL_SEC
read -p "PayPal Webhook ID: " PAYPAL_WH
read -p "PayPal Mode (sandbox/live) [sandbox]: " PAYPAL_MODE
PAYPAL_MODE=${PAYPAL_MODE:-sandbox}
echo ""

# ──────────────────────────────────────────────
# 6. Discord (optional)
# ──────────────────────────────────────────────
echo -e "${YELLOW}Step 6: Discord Bot (optional)${NC}"
read -p "Discord Bot Token: " DISCORD_TOKEN
read -p "Discord Guild ID: " DISCORD_GUILD
read -p "Discord Notification Channel ID: " DISCORD_CHANNEL
echo ""

# ──────────────────────────────────────────────
# Confirm
# ──────────────────────────────────────────────
echo -e "${GREEN}Ready to install. Summary:${NC}"
echo -e "  Host:         $HOST"
echo -e "  Admin:        $ADMIN_NAME ($ADMIN_EMAIL) — role: ${CYAN}$ADMIN_ROLE${NC}"
echo -e "  Pterodactyl:  ${PTERODACTYL_URL:-Not configured}"
echo -e "  Stripe:       ${STRIPE_PK:+Configured}${STRIPE_PK:-Not configured}"
echo -e "  PayPal:       ${PAYPAL_CID:+Configured}${PAYPAL_CID:-Not configured}"
echo -e "  Discord:      ${DISCORD_TOKEN:+Configured}${DISCORD_TOKEN:-Not configured}"
echo ""
read -p "Proceed with installation? (Y/n): " CONFIRM
CONFIRM=${CONFIRM:-Y}
if [[ "$CONFIRM" =~ ^[Nn] ]]; then
    echo -e "${RED}Installation cancelled.${NC}"
    exit 1
fi

# ──────────────────────────────────────────────
# System Packages
# ──────────────────────────────────────────────
echo -e "${YELLOW}Updating system packages...${NC}"
apt update && apt upgrade -y

echo -e "${YELLOW}Installing dependencies...${NC}"
apt install -y software-properties-common curl git nginx mysql-server redis-server supervisor certbot python3-certbot-nginx

echo -e "${YELLOW}Installing PHP 8.3...${NC}"
add-apt-repository ppa:ondrej/php -y
apt update
apt install -y php8.3-fpm php8.3-cli php8.3-mysql php8.3-xml php8.3-mbstring php8.3-curl php8.3-zip php8.3-bcmath php8.3-gd php8.3-intl php8.3-sodium

echo -e "${YELLOW}Installing Composer...${NC}"
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# ──────────────────────────────────────────────
# Database
# ──────────────────────────────────────────────
DB_NAME="devlio_billing"
DB_USER="devlio_billing"
DB_PASSWORD=$(openssl rand -base64 32)

echo -e "${YELLOW}Configuring MySQL...${NC}"
mysql -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -e "CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASSWORD';"
mysql -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

# ──────────────────────────────────────────────
# Application
# ──────────────────────────────────────────────
echo -e "${YELLOW}Setting up application...${NC}"
cd /var/www
if [ -d /var/www/devlio-billing ]; then
    echo "Directory exists, pulling updates..."
    cd /var/www/devlio-billing
    git pull
else
    git clone https://github.com/Bebonaiem/devlio-billing.git
    cd /var/www/devlio-billing
fi

cp .env.example .env
sed -i "s/APP_NAME=.*/APP_NAME=DevlioBilling/" .env
sed -i "s/APP_ENV=.*/APP_ENV=production/" .env
sed -i "s/APP_DEBUG=.*/APP_DEBUG=false/" .env
sed -i "s|APP_URL=.*|APP_URL=$APP_URL|" .env
sed -i "s/DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=$DB_USER/" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASSWORD/" .env

if [ -n "$PTERODACTYL_URL" ]; then
    sed -i "s|PTERODACTYL_PANEL_URL=.*|PTERODACTYL_PANEL_URL=$PTERODACTYL_URL|" .env
    sed -i "s/PTERODACTYL_API_KEY=.*/PTERODACTYL_API_KEY=$PTERODACTYL_KEY/" .env
fi
if [ -n "$STRIPE_PK" ]; then
    sed -i "s/STRIPE_PUBLIC_KEY=.*/STRIPE_PUBLIC_KEY=$STRIPE_PK/" .env
    sed -i "s/STRIPE_SECRET_KEY=.*/STRIPE_SECRET_KEY=$STRIPE_SK/" .env
    sed -i "s/STRIPE_WEBHOOK_SECRET=.*/STRIPE_WEBHOOK_SECRET=$STRIPE_WH/" .env
fi
if [ -n "$PAYPAL_CID" ]; then
    sed -i "s/PAYPAL_CLIENT_ID=.*/PAYPAL_CLIENT_ID=$PAYPAL_CID/" .env
    sed -i "s/PAYPAL_SECRET=.*/PAYPAL_SECRET=$PAYPAL_SEC/" .env
    sed -i "s/PAYPAL_WEBHOOK_ID=.*/PAYPAL_WEBHOOK_ID=$PAYPAL_WH/" .env
    sed -i "s/PAYPAL_MODE=.*/PAYPAL_MODE=$PAYPAL_MODE/" .env
fi
if [ -n "$DISCORD_TOKEN" ]; then
    sed -i "s/DISCORD_BOT_TOKEN=.*/DISCORD_BOT_TOKEN=$DISCORD_TOKEN/" .env
    sed -i "s/DISCORD_GUILD_ID=.*/DISCORD_GUILD_ID=$DISCORD_GUILD/" .env
    sed -i "s/DISCORD_NOTIFICATION_CHANNEL=.*/DISCORD_NOTIFICATION_CHANNEL=$DISCORD_CHANNEL/" .env
fi

php artisan key:generate --force
composer install --no-interaction --optimize-autoloader --no-dev
php artisan migrate --force
php artisan db:seed --class=DatabaseSeeder --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan event:cache

# ──────────────────────────────────────────────
# Permissions
# ──────────────────────────────────────────────
chown -R www-data:www-data /var/www/devlio-billing
chmod -R 755 /var/www/devlio-billing/storage
chmod -R 755 /var/www/devlio-billing/bootstrap/cache

# ──────────────────────────────────────────────
# Nginx
# ──────────────────────────────────────────────
echo -e "${YELLOW}Configuring Nginx...${NC}"
if [[ "$HOST" =~ \. ]]; then
    cat > /etc/nginx/sites-available/devlio-billing << EOF
server {
    listen 80;
    server_name $HOST;
    root /var/www/devlio-billing/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php\$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF
    ln -sf /etc/nginx/sites-available/devlio-billing /etc/nginx/sites-enabled/
    rm -f /etc/nginx/sites-enabled/default
    systemctl reload nginx

    echo -e "${YELLOW}Setting up SSL with Certbot...${NC}"
    certbot --nginx -d "$HOST" --non-interactive --agree-tos --email admin@"$HOST" || true
else
    cat > /etc/nginx/sites-available/devlio-billing << EOF
server {
    listen 80;
    server_name $HOST;
    root /var/www/devlio-billing/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php\$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF
    ln -sf /etc/nginx/sites-available/devlio-billing /etc/nginx/sites-enabled/
    rm -f /etc/nginx/sites-enabled/default
    systemctl reload nginx
fi

# ──────────────────────────────────────────────
# Queue Worker
# ──────────────────────────────────────────────
echo -e "${YELLOW}Configuring queue worker...${NC}"
cat > /etc/supervisor/conf.d/devlio-billing-worker.conf << 'EOF'
[program:devlio-billing-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/devlio-billing/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/devlio-billing/storage/logs/worker.log
stopwaitsecs=3600
EOF

supervisorctl reread
supervisorctl update
supervisorctl start devlio-billing-worker:* || true

# ──────────────────────────────────────────────
# Cron
# ──────────────────────────────────────────────
echo -e "${YELLOW}Setting up cron...${NC}"
crontab -l 2>/dev/null || true
(crontab -l 2>/dev/null; echo "* * * * * cd /var/www/devlio-billing && php artisan schedule:run >> /dev/null 2>&1") | crontab -

# ──────────────────────────────────────────────
# Create User
# ──────────────────────────────────────────────
echo -e "${YELLOW}Creating user account...${NC}"
cd /var/www/devlio-billing

if [ "$ADMIN_ROLE" = "admin" ]; then
    php artisan tinker --execute="
        \$user = \App\Models\User::create([
            'name' => '$ADMIN_NAME',
            'email' => '$ADMIN_EMAIL',
            'password' => bcrypt('$ADMIN_PASSWORD'),
        ]);
        \$user->assignRole('admin');
        echo 'Admin user created: ' . \$user->email . PHP_EOL;
    "
else
    php artisan tinker --execute="
        \$user = \App\Models\User::create([
            'name' => '$ADMIN_NAME',
            'email' => '$ADMIN_EMAIL',
            'password' => bcrypt('$ADMIN_PASSWORD'),
        ]);
        \$user->assignRole('customer');
        echo 'User created: ' . \$user->email . PHP_EOL;
    "
fi

# ──────────────────────────────────────────────
# Done
# ──────────────────────────────────────────────
echo ""
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}      Installation Complete!             ${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo -e "  Website:    ${GREEN}$APP_URL${NC}"
echo -e "  Admin:      ${GREEN}$APP_URL/admin${NC}"
echo -e "  Login:      ${CYAN}$ADMIN_EMAIL${NC} / the password you entered"
echo -e "  DB Name:    ${YELLOW}$DB_NAME${NC}"
echo -e "  DB User:    ${YELLOW}$DB_USER${NC}"
echo -e "  DB Pass:    ${YELLOW}$DB_PASSWORD${NC}"
echo ""

if [[ "$HOST" =~ \. ]]; then
    echo -e "${YELLOW}Webhook URLs:${NC}"
    echo -e "  Stripe:  ${GREEN}$APP_URL/webhook/stripe${NC}"
    echo -e "  PayPal:  ${GREEN}$APP_URL/webhook/paypal${NC}"
    echo ""
fi

echo -e "${YELLOW}What to do next:${NC}"
echo "  1. Visit the admin panel and create your first product + plan"
echo "  2. If you skipped API keys, configure them in Settings"
echo "  3. Set up Stripe/PayPal webhooks in their dashboards"
echo "  4. Point your DNS A record to this server (if using a domain)"
echo ""
echo -e "${GREEN}Thank you for using Devlio Billing!${NC}"
