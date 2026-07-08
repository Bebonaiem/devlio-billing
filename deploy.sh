#!/bin/bash
set -e

# GameBilling - Ubuntu VPS Auto-Installer
# Usage: curl -sSL https://your-server.com/deploy.sh | bash
# Or: sudo bash deploy.sh

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}  GameBilling - Ubuntu Auto Installer    ${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""

# Check if running as root
if [ "$EUID" -ne 0 ]; then
    echo -e "${RED}Please run as root (use sudo)${NC}"
    exit 1
fi

# Detect OS
if [ ! -f /etc/os-release ]; then
    echo -e "${RED}Unsupported OS${NC}"
    exit 1
fi

. /etc/os-release
if [ "$ID" != "ubuntu" ]; then
    echo -e "${RED}This script is for Ubuntu only${NC}"
    exit 1
fi

echo -e "${GREEN}Installing on Ubuntu $VERSION_ID${NC}"

# Configuration
DOMAIN=""
DB_NAME="gamebilling"
DB_USER="gamebilling"
DB_PASSWORD=$(openssl rand -base64 32)
APP_URL="http://localhost"

# Prompt for domain
read -p "Enter your domain (e.g., billing.yourhost.com): " DOMAIN
if [ -n "$DOMAIN" ]; then
    APP_URL="https://$DOMAIN"
fi

# Prompt for Pterodactyl details
echo ""
echo -e "${YELLOW}Pterodactyl Configuration${NC}"
read -p "Pterodactyl Panel URL (e.g., https://panel.yourhost.com): " PTERODACTYL_URL
read -p "Pterodactyl Application API Key (ptlc_...): " PTERODACTYL_KEY

# Prompt for Stripe
echo ""
echo -e "${YELLOW}Stripe Configuration${NC}"
read -p "Stripe Public Key (pk_...): " STRIPE_PK
read -p "Stripe Secret Key (sk_...): " STRIPE_SK
read -p "Stripe Webhook Secret (whsec_...): " STRIPE_WH

# Prompt for PayPal (optional)
echo ""
echo -e "${YELLOW}PayPal Configuration (optional)${NC}"
read -p "PayPal Client ID: " PAYPAL_CID
read -p "PayPal Secret: " PAYPAL_SEC
read -p "PayPal Webhook ID: " PAYPAL_WH
read -p "PayPal Mode (sandbox/live) [sandbox]: " PAYPAL_MODE
PAYPAL_MODE=${PAYPAL_MODE:-sandbox}

# Prompt for Discord (optional)
echo ""
echo -e "${YELLOW}Discord Configuration (optional)${NC}"
read -p "Discord Bot Token: " DISCORD_TOKEN
read -p "Discord Guild/Server ID: " DISCORD_GUILD
read -p "Discord Notification Channel ID: " DISCORD_CHANNEL

echo ""
echo -e "${GREEN}Starting installation...${NC}"

# Update system
echo -e "${YELLOW}Updating system packages...${NC}"
apt update && apt upgrade -y

# Install dependencies
echo -e "${YELLOW}Installing dependencies...${NC}"
apt install -y software-properties-common curl git nginx mysql-server redis-server supervisor certbot python3-certbot-nginx

# Install PHP 8.3+
echo -e "${YELLOW}Installing PHP 8.3...${NC}"
add-apt-repository ppa:ondrej/php -y
apt update
apt install -y php8.3-fpm php8.3-cli php8.3-mysql php8.3-xml php8.3-mbstring php8.3-curl php8.3-zip php8.3-bcmath php8.3-gd php8.3-intl php8.3-sodium

# Install Composer
echo -e "${YELLOW}Installing Composer...${NC}"
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Configure MySQL
echo -e "${YELLOW}Configuring MySQL...${NC}"
mysql -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -e "CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASSWORD';"
mysql -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

# Clone the application
echo -e "${YELLOW}Setting up application...${NC}"
cd /var/www
if [ -d /var/www/gamebilling ]; then
    echo "Directory exists, pulling updates..."
    cd /var/www/gamebilling
    git pull
else
    git clone https://github.com/yourusername/gamebilling.git
    cd /var/www/gamebilling
fi

# Create .env file
cp .env.example .env
sed -i "s/APP_NAME=.*/APP_NAME=GameBilling/" .env
sed -i "s/APP_ENV=.*/APP_ENV=production/" .env
sed -i "s/APP_DEBUG=.*/APP_DEBUG=false/" .env
sed -i "s|APP_URL=.*|APP_URL=$APP_URL|" .env
sed -i "s/DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=$DB_USER/" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASSWORD/" .env
sed -i "s|PTERODACTYL_PANEL_URL=.*|PTERODACTYL_PANEL_URL=$PTERODACTYL_URL|" .env
sed -i "s/PTERODACTYL_API_KEY=.*/PTERODACTYL_API_KEY=$PTERODACTYL_KEY/" .env
sed -i "s/STRIPE_PUBLIC_KEY=.*/STRIPE_PUBLIC_KEY=$STRIPE_PK/" .env
sed -i "s/STRIPE_SECRET_KEY=.*/STRIPE_SECRET_KEY=$STRIPE_SK/" .env
sed -i "s/STRIPE_WEBHOOK_SECRET=.*/STRIPE_WEBHOOK_SECRET=$STRIPE_WH/" .env
sed -i "s/PAYPAL_CLIENT_ID=.*/PAYPAL_CLIENT_ID=$PAYPAL_CID/" .env
sed -i "s/PAYPAL_SECRET=.*/PAYPAL_SECRET=$PAYPAL_SEC/" .env
sed -i "s/PAYPAL_WEBHOOK_ID=.*/PAYPAL_WEBHOOK_ID=$PAYPAL_WH/" .env
sed -i "s/PAYPAL_MODE=.*/PAYPAL_MODE=$PAYPAL_MODE/" .env
sed -i "s/DISCORD_BOT_TOKEN=.*/DISCORD_BOT_TOKEN=$DISCORD_TOKEN/" .env
sed -i "s/DISCORD_GUILD_ID=.*/DISCORD_GUILD_ID=$DISCORD_GUILD/" .env
sed -i "s/DISCORD_NOTIFICATION_CHANNEL=.*/DISCORD_NOTIFICATION_CHANNEL=$DISCORD_CHANNEL/" .env

# Generate app key
php artisan key:generate --force

# Install dependencies
composer install --no-interaction --optimize-autoloader --no-dev

# Run migrations and seed
php artisan migrate --force
php artisan db:seed --class=DatabaseSeeder --force

# Create storage link
php artisan storage:link

# Cache config and routes
php artisan config:cache
php artisan route:cache
php artisan event:cache

# Set permissions
echo -e "${YELLOW}Setting permissions...${NC}"
chown -R www-data:www-data /var/www/gamebilling
chmod -R 755 /var/www/gamebilling/storage
chmod -R 755 /var/www/gamebilling/bootstrap/cache

# Configure Nginx
echo -e "${YELLOW}Configuring Nginx...${NC}"
if [ -n "$DOMAIN" ]; then
    cat > /etc/nginx/sites-available/gamebilling << 'EOF'
server {
    listen 80;
    server_name DOMAIN_PLACEHOLDER;
    root /var/www/gamebilling/public;

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
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF
    sed -i "s/DOMAIN_PLACEHOLDER/$DOMAIN/" /etc/nginx/sites-available/gamebilling
    ln -sf /etc/nginx/sites-available/gamebilling /etc/nginx/sites-enabled/
    rm -f /etc/nginx/sites-enabled/default
    systemctl reload nginx

    # SSL
    echo -e "${YELLOW}Setting up SSL with Certbot...${NC}"
    certbot --nginx -d "$DOMAIN" --non-interactive --agree-tos --email admin@"$DOMAIN" || true
else
    cat > /etc/nginx/sites-available/gamebilling << 'EOF'
server {
    listen 80;
    server_name _;
    root /var/www/gamebilling/public;

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
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF
    ln -sf /etc/nginx/sites-available/gamebilling /etc/nginx/sites-enabled/
    rm -f /etc/nginx/sites-enabled/default
    systemctl reload nginx
fi

# Configure Queue Worker
echo -e "${YELLOW}Configuring queue worker...${NC}"
cat > /etc/supervisor/conf.d/gamebilling-worker.conf << 'EOF'
[program:gamebilling-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/gamebilling/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/gamebilling/storage/logs/worker.log
stopwaitsecs=3600
EOF

supervisorctl reread
supervisorctl update
supervisorctl start gamebilling-worker:*

# Set up cron for scheduler
echo -e "${YELLOW}Setting up cron...${NC}"
crontab -l 2>/dev/null || true
(crontab -l 2>/dev/null; echo "* * * * * cd /var/www/gamebilling && php artisan schedule:run >> /dev/null 2>&1") | crontab -

# Create admin user
echo ""
echo -e "${YELLOW}Creating admin user...${NC}"
read -p "Admin Email: " ADMIN_EMAIL
read -p "Admin Password: " ADMIN_PASSWORD
read -p "Admin Name: " ADMIN_NAME

cd /var/www/gamebilling
php artisan tinker --execute="
    \$user = \App\Models\User::create([
        'name' => '$ADMIN_NAME',
        'email' => '$ADMIN_EMAIL',
        'password' => bcrypt('$ADMIN_PASSWORD'),
    ]);
    \$user->assignRole('admin');
    echo 'Admin user created: ' . \$user->email . PHP_EOL;
"

echo ""
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}  Installation Complete!                ${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo -e "Website: ${GREEN}$APP_URL${NC}"
echo -e "Admin Login: ${GREEN}$APP_URL/admin${NC}"
echo -e "Database: ${YELLOW}$DB_NAME${NC}"
echo -e "DB User: ${YELLOW}$DB_USER${NC}"
echo -e "DB Password: ${YELLOW}$DB_PASSWORD${NC}"
echo ""
echo -e "${YELLOW}Important next steps:${NC}"
echo "1. Configure Stripe webhook endpoint: $APP_URL/webhook/stripe"
echo "2. Configure PayPal webhook endpoint: $APP_URL/webhook/paypal"
echo "3. Update your DNS A record to point to this server"
echo "4. Visit the admin panel to create products and plans"
echo ""
echo -e "${GREEN}Thank you for using GameBilling!${NC}"
