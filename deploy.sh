#!/bin/bash
set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m'

APP_DIR="/var/www/devlio-billing"
DB_NAME="devlio_billing"
DB_USER="devlio_billing"
DB_PASSWORD=""

show_menu() {
    clear
    echo -e "${GREEN}========================================${NC}"
    echo -e "${GREEN}    Devlio Billing - CLI Tool            ${NC}"
    echo -e "${GREEN}========================================${NC}"
    echo ""
    echo -e "  ${CYAN}1${NC}) Install full website (Ubuntu fresh setup)"
    echo -e "  ${CYAN}2${NC}) Create a new user"
    echo -e "  ${CYAN}3${NC}) Pull latest code + migrate + cache"
    echo -e "  ${CYAN}4${NC}) Run database migrations only"
    echo -e "  ${CYAN}5${NC}) Clear all caches"
    echo -e "  ${CYAN}6${NC}) Restart services (nginx, php-fpm, queue, supervisor)"
    echo -e "  ${CYAN}7${NC}) View application logs"
    echo -e "  ${CYAN}8${NC}) Backup database"
    echo -e "  ${CYAN}9${NC}) Generate Passport API keys"
    echo -e "  ${CYAN}10${NC}) Change admin password"
    echo -e "  ${CYAN}11${NC}) Check SSL certificate status"
    echo -e "  ${CYAN}12${NC}) Install/Update Filament assets"
    echo -e "  ${CYAN}0${NC}) Exit"
    echo ""
    read -p "Choose an option [0-12]: " MENU_CHOICE
    echo ""
}

create_user() {
    echo -e "${YELLOW}Create a New User${NC}"
    echo ""
    while [ -z "$NEW_NAME" ]; do
        read -p "Full name: " NEW_NAME
        if [ -z "$NEW_NAME" ]; then echo -e "${RED}Name is required.${NC}"; fi
    done
    while [ -z "$NEW_EMAIL" ]; do
        read -p "Email: " NEW_EMAIL
        if [ -z "$NEW_EMAIL" ]; then echo -e "${RED}Email is required.${NC}"; fi
    done
    while [ -z "$NEW_PASSWORD" ]; do
        read -s -p "Password: " NEW_PASSWORD
        echo ""
        if [ -z "$NEW_PASSWORD" ]; then echo -e "${RED}Password is required.${NC}"; fi
    done
    echo ""
    echo -e "Role: ${CYAN}admin${NC} (full access) or ${CYAN}customer${NC} (regular user)?"
    select NEW_ROLE in "admin" "customer"; do
        case $NEW_ROLE in
            admin|customer) break;;
        esac
    done

    cd "$APP_DIR"
    FIRST_NAME=$(echo "$NEW_NAME" | awk '{print $1}')
    LAST_NAME=$(echo "$NEW_NAME" | awk '{print $2}')
    [ -z "$LAST_NAME" ] && LAST_NAME="$FIRST_NAME"

    php artisan tinker --execute="
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'customer']);
        \$user = \App\Models\User::create([
            'first_name' => '${FIRST_NAME}',
            'last_name' => '${LAST_NAME}',
            'email' => '${NEW_EMAIL}',
            'password' => bcrypt('${NEW_PASSWORD}'),
        ]);
        \$user->assignRole('${NEW_ROLE}');
        echo 'User created: ' . \$user->email . ' as ${NEW_ROLE}' . PHP_EOL;
    "
    unset NEW_NAME NEW_EMAIL NEW_PASSWORD NEW_ROLE
    echo -e "${GREEN}User created successfully!${NC}"
}

install_website() {
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

    echo -e "${YELLOW}Server Host${NC}"
    echo -e "Enter your server's ${CYAN}IP address${NC} or ${CYAN}domain name${NC}."
    while [ -z "$HOST" ]; do
        read -p "Host (IP or domain): " HOST
        HOST=$(echo "$HOST" | sed 's|/$||' | sed 's|^https\?://||')
        if [ -z "$HOST" ]; then
            echo -e "${RED}This field is required.${NC}"
        fi
    done
    APP_URL="http://$HOST"
    if [[ "$HOST" =~ \. ]]; then
        APP_URL="https://$HOST"
    fi
    echo ""

    echo -e "${YELLOW}Admin Account${NC}"
    while [ -z "$ADMIN_NAME" ]; do
        read -p "Name: " ADMIN_NAME
        if [ -z "$ADMIN_NAME" ]; then echo -e "${RED}Name is required.${NC}"; fi
    done
    while [ -z "$ADMIN_EMAIL" ]; do
        read -p "Email: " ADMIN_EMAIL
        if [ -z "$ADMIN_EMAIL" ]; then echo -e "${RED}Email is required.${NC}"; fi
    done
    while [ -z "$ADMIN_PASSWORD" ]; do
        read -s -p "Password: " ADMIN_PASSWORD
        echo ""
        if [ -z "$ADMIN_PASSWORD" ]; then echo -e "${RED}Password is required.${NC}"; fi
    done
    echo -e "Role: ${CYAN}admin${NC} or ${CYAN}customer${NC}?"
    ADMIN_ROLE="admin"
    echo ""

    echo -e "${YELLOW}Pterodactyl Integration${NC} (press Enter to skip)"
    read -p "Pterodactyl Panel URL: " PTERODACTYL_URL
    if [ -n "$PTERODACTYL_URL" ]; then
        read -p "Pterodactyl Application API Key (ptlc_...): " PTERODACTYL_KEY
    fi
    echo ""

    echo -e "${YELLOW}Stripe (optional, press Enter to skip)${NC}"
    read -p "Stripe Public Key (pk_...): " STRIPE_PK
    if [ -n "$STRIPE_PK" ]; then
        read -p "Stripe Secret Key (sk_...): " STRIPE_SK
        read -p "Stripe Webhook Secret (whsec_...): " STRIPE_WH
    fi
    echo ""

    echo -e "${YELLOW}PayPal (optional, press Enter to skip)${NC}"
    read -p "PayPal Client ID: " PAYPAL_CID
    if [ -n "$PAYPAL_CID" ]; then
        read -p "PayPal Secret: " PAYPAL_SEC
        read -p "PayPal Webhook ID: " PAYPAL_WH
        read -p "PayPal Mode (sandbox/live) [sandbox]: " PAYPAL_MODE
        PAYPAL_MODE=${PAYPAL_MODE:-sandbox}
    fi
    echo ""

    echo -e "${YELLOW}Discord Bot (optional, press Enter to skip)${NC}"
    read -p "Discord Bot Token: " DISCORD_TOKEN
    if [ -n "$DISCORD_TOKEN" ]; then
        read -p "Discord Guild ID: " DISCORD_GUILD
        read -p "Discord Notification Channel ID: " DISCORD_CHANNEL
    fi
    echo ""

    echo -e "${YELLOW}Email Piping (optional, press Enter to skip)${NC}"
    read -p "IMAP Host: " IMAP_HOST
    if [ -n "$IMAP_HOST" ]; then
        read -p "IMAP Username: " IMAP_USER
        read -s -p "IMAP Password: " IMAP_PASS
        echo ""
    fi
    echo ""

    echo -e "${GREEN}Ready to install. Summary:${NC}"
    echo -e "  Host:         $HOST"
    echo -e "  Admin:        $ADMIN_NAME ($ADMIN_EMAIL) — role: ${CYAN}$ADMIN_ROLE${NC}"
    echo -e "  Pterodactyl:  ${PTERODACTYL_URL:-Not configured}"
    echo -e "  Stripe:       ${STRIPE_PK:+Configured}${STRIPE_PK:-Not configured}"
    echo -e "  PayPal:       ${PAYPAL_CID:+Configured}${PAYPAL_CID:-Not configured}"
    echo -e "  Discord:      ${DISCORD_TOKEN:+Configured}${DISCORD_TOKEN:-Not configured}"
    echo -e "  Email Piping: ${IMAP_HOST:+Configured}${IMAP_HOST:-Not configured}"
    echo ""
    read -p "Proceed with installation? (Y/n): " CONFIRM
    CONFIRM=${CONFIRM:-Y}
    if [[ "$CONFIRM" =~ ^[Nn] ]]; then
        echo -e "${RED}Installation cancelled.${NC}"
        return
    fi

    echo -e "${YELLOW}Installing system packages...${NC}"
    rm -f /etc/apt/sources.list.d/ondrej-*.list /etc/apt/sources.list.d/php*.list 2>/dev/null || true
    sed -i '/ondrej\/php/d' /etc/apt/sources.list 2>/dev/null || true
    apt update 2>&1 | grep -v "404\|Err\|NotFound" || true
    apt upgrade -y 2>&1 | grep -v "404\|Err\|NotFound" || true
    apt install -y software-properties-common curl git nginx mysql-server redis-server supervisor certbot python3-certbot-nginx lsb-release

    echo -e "${YELLOW}Installing PHP 8.4...${NC}"
    UBUNTU_CODENAME=$(lsb_release -sc 2>/dev/null || echo "")
    UBUNTU_VERSION=$(lsb_release -sr 2>/dev/null || echo "")
    rm -f /etc/apt/sources.list.d/ondrej-*.list /etc/apt/sources.list.d/php*.list 2>/dev/null || true
    sed -i '/ondrej\/php/d' /etc/apt/sources.list 2>/dev/null || true
    if [ "$UBUNTU_CODENAME" = "resolute" ] || [ "$(echo "$UBUNTU_VERSION" | cut -d. -f1)" -ge 25 ]; then
        curl -sSLo /usr/share/keyrings/deb.sury.org-php.gpg https://packages.sury.org/php/apt.gpg
        echo "deb [signed-by=/usr/share/keyrings/deb.sury.org-php.gpg] https://packages.sury.org/php/ $UBUNTU_CODENAME main" > /etc/apt/sources.list.d/php.list
    else
        add-apt-repository ppa:ondrej/php -y
    fi
    apt update 2>&1 | grep -v "404\|Err\|NotFound" || apt update
    apt install -y php8.4-fpm php8.4-cli php8.4-mysql php8.4-xml php8.4-mbstring php8.4-curl php8.4-zip php8.4-bcmath php8.4-gd php8.4-intl php8.4-imap php8.4-redis

    echo -e "${YELLOW}Installing Composer...${NC}"
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

    DB_PASSWORD=$(openssl rand -base64 32)

    echo -e "${YELLOW}Configuring MySQL...${NC}"
    mysql -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    mysql -e "DROP USER IF EXISTS '$DB_USER'@'localhost';"
    mysql -e "DROP USER IF EXISTS '$DB_USER'@'127.0.0.1';"
    mysql -e "DROP USER IF EXISTS '$DB_USER'@'%';"
    mysql -e "CREATE USER '$DB_USER'@'localhost' IDENTIFIED WITH mysql_native_password BY '$DB_PASSWORD';"
    mysql -e "CREATE USER '$DB_USER'@'127.0.0.1' IDENTIFIED WITH mysql_native_password BY '$DB_PASSWORD';"
    mysql -e "CREATE USER '$DB_USER'@'%' IDENTIFIED WITH mysql_native_password BY '$DB_PASSWORD';"
    mysql -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';"
    mysql -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'127.0.0.1';"
    mysql -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'%';"
    mysql -e "FLUSH PRIVILEGES;"

    echo -e "${YELLOW}Setting up application...${NC}"
    cd /var/www
    if [ -d "$APP_DIR" ]; then
        echo "Directory exists, pulling updates..."
        cd "$APP_DIR"
        git pull
    else
        git clone https://github.com/Bebonaiem/devlio-billing.git
        cd "$APP_DIR"
    fi

    cp .env.example .env
    sed -i "s/APP_NAME=.*/APP_NAME=DevlioBilling/" .env
    sed -i "s/APP_ENV=.*/APP_ENV=production/" .env
    sed -i "s/APP_DEBUG=.*/APP_DEBUG=false/" .env
    sed -i "s|APP_URL=.*|APP_URL=$APP_URL|" .env
    sed -i "s|DB_HOST=.*|DB_HOST=localhost|" .env
    sed -i "s|DB_DATABASE=.*|DB_DATABASE=$DB_NAME|" .env
    sed -i "s|DB_USERNAME=.*|DB_USERNAME=$DB_USER|" .env
    sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=$DB_PASSWORD|" .env
    sed -i "s/SESSION_DRIVER=.*/SESSION_DRIVER=database/" .env
    sed -i "s/CACHE_STORE=.*/CACHE_STORE=database/" .env
    sed -i "s/QUEUE_CONNECTION=.*/QUEUE_CONNECTION=database/" .env

    if [ -n "$PTERODACTYL_URL" ]; then
        sed -i "s|PTERODACTYL_PANEL_URL=.*|PTERODACTYL_PANEL_URL=$PTERODACTYL_URL|" .env
        sed -i "s|PTERODACTYL_API_KEY=.*|PTERODACTYL_API_KEY=$PTERODACTYL_KEY|" .env
    fi
    if [ -n "$STRIPE_PK" ]; then
        sed -i "s|STRIPE_PUBLIC_KEY=.*|STRIPE_PUBLIC_KEY=$STRIPE_PK|" .env
        sed -i "s|STRIPE_SECRET_KEY=.*|STRIPE_SECRET_KEY=$STRIPE_SK|" .env
        sed -i "s|STRIPE_WEBHOOK_SECRET=.*|STRIPE_WEBHOOK_SECRET=$STRIPE_WH|" .env
    fi
    if [ -n "$PAYPAL_CID" ]; then
        sed -i "s|PAYPAL_CLIENT_ID=.*|PAYPAL_CLIENT_ID=$PAYPAL_CID|" .env
        sed -i "s|PAYPAL_SECRET=.*|PAYPAL_SECRET=$PAYPAL_SEC|" .env
        sed -i "s|PAYPAL_WEBHOOK_ID=.*|PAYPAL_WEBHOOK_ID=$PAYPAL_WH|" .env
        sed -i "s|PAYPAL_MODE=.*|PAYPAL_MODE=$PAYPAL_MODE|" .env
    fi
    if [ -n "$DISCORD_TOKEN" ]; then
        sed -i "s|DISCORD_BOT_TOKEN=.*|DISCORD_BOT_TOKEN=$DISCORD_TOKEN|" .env
        sed -i "s|DISCORD_GUILD_ID=.*|DISCORD_GUILD_ID=$DISCORD_GUILD|" .env
        sed -i "s|DISCORD_NOTIFICATION_CHANNEL=.*|DISCORD_NOTIFICATION_CHANNEL=$DISCORD_CHANNEL|" .env
    fi
    if [ -n "$IMAP_HOST" ]; then
        sed -i "s|IMAP_HOST=.*|IMAP_HOST=$IMAP_HOST|" .env
        sed -i "s|IMAP_USERNAME=.*|IMAP_USERNAME=$IMAP_USER|" .env
        sed -i "s|IMAP_PASSWORD=.*|IMAP_PASSWORD=$IMAP_PASS|" .env
    fi

    echo -e "${YELLOW}Installing dependencies...${NC}"
    composer install --no-interaction --optimize-autoloader --no-dev --ignore-platform-req=ext-intl

    echo -e "${YELLOW}Generating keys...${NC}"
    php artisan key:generate --force
    php artisan passport:keys --force 2>/dev/null || true

    echo -e "${YELLOW}Running migrations and seeding...${NC}"
    php artisan migrate --force
    php artisan db:seed --class=DatabaseSeeder --force 2>/dev/null || true

    echo -e "${YELLOW}Setting up storage and caches...${NC}"
    php artisan storage:link --force
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache 2>/dev/null || true

    echo -e "${YELLOW}Publishing Filament assets...${NC}"
    php artisan filament:assets 2>/dev/null || true

    echo -e "${YELLOW}Setting permissions...${NC}"
    chown -R www-data:www-data "$APP_DIR"
    chmod -R 755 "$APP_DIR/storage"
    chmod -R 755 "$APP_DIR/bootstrap/cache"
    chmod -R 770 "$APP_DIR/storage/logs"

    echo -e "${YELLOW}Configuring Nginx...${NC}"
    if [[ "$HOST" =~ \. ]]; then
        cat > /etc/nginx/sites-available/devlio-billing << EOF
server {
    listen 80;
    server_name $HOST;
    root $APP_DIR/public;
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";
    add_header Referrer-Policy "strict-origin-when-cross-origin";
    index index.php;
    charset utf-8;
    client_max_body_size 20M;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php\$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }
}
EOF
        ln -sf /etc/nginx/sites-available/devlio-billing /etc/nginx/sites-enabled/
        rm -f /etc/nginx/sites-enabled/default
        nginx -t && systemctl reload nginx
        echo -e "${YELLOW}Setting up SSL with Certbot...${NC}"
        certbot --nginx -d "$HOST" --non-interactive --agree-tos --email admin@"$HOST" || true
    else
        cat > /etc/nginx/sites-available/devlio-billing << EOF
server {
    listen 80;
    server_name $HOST;
    root $APP_DIR/public;
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";
    index index.php;
    charset utf-8;
    client_max_body_size 20M;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php\$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }
}
EOF
        ln -sf /etc/nginx/sites-available/devlio-billing /etc/nginx/sites-enabled/
        rm -f /etc/nginx/sites-enabled/default
        nginx -t && systemctl reload nginx
    fi

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

    echo -e "${YELLOW}Setting up cron...${NC}"
    (crontab -l 2>/dev/null | grep -v "devlio-billing"; echo "* * * * * cd $APP_DIR && php artisan schedule:run >> /dev/null 2>&1") | crontab -

    echo -e "${YELLOW}Creating admin user...${NC}"
    cd "$APP_DIR"
    FIRST_NAME=$(echo "$ADMIN_NAME" | awk '{print $1}')
    LAST_NAME=$(echo "$ADMIN_NAME" | awk '{print $2}')
    [ -z "$LAST_NAME" ] && LAST_NAME="$FIRST_NAME"
    php artisan tinker --execute="
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        \$user = \App\Models\User::create([
            'first_name' => '${FIRST_NAME}',
            'last_name' => '${LAST_NAME}',
            'email' => '${ADMIN_EMAIL}',
            'password' => bcrypt('${ADMIN_PASSWORD}'),
            'email_verified_at' => now(),
        ]);
        \$user->assignRole('admin');
        echo 'Admin user created: ' . \$user->email . PHP_EOL;
    "

    echo ""
    echo -e "${GREEN}========================================${NC}"
    echo -e "${GREEN}      Installation Complete!             ${NC}"
    echo -e "${GREEN}========================================${NC}"
    echo ""
    echo -e "  Website:    ${GREEN}$APP_URL${NC}"
    echo -e "  Admin:      ${GREEN}$APP_URL/admin${NC}"
    echo -e "  Login:      ${CYAN}$ADMIN_EMAIL${NC} / the password you entered"
    echo ""
    echo -e "${YELLOW}Database Credentials (SAVE THESE):${NC}"
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
}

pull_and_migrate() {
    cd "$APP_DIR"
    echo -e "${YELLOW}Pulling latest code...${NC}"
    git pull
    echo -e "${YELLOW}Installing dependencies...${NC}"
    composer install --no-interaction --optimize-autoloader --no-dev --ignore-platform-req=ext-intl
    echo -e "${YELLOW}Running migrations...${NC}"
    php artisan migrate --force
    echo -e "${YELLOW}Publishing assets...${NC}"
    php artisan filament:assets 2>/dev/null || true
    echo -e "${YELLOW}Caching...${NC}"
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache 2>/dev/null || true
    echo -e "${YELLOW}Fixing permissions...${NC}"
    chown -R www-data:www-data "$APP_DIR"
    chmod -R 755 "$APP_DIR/storage"
    chmod -R 755 "$APP_DIR/bootstrap/cache"
    echo -e "${GREEN}Done!${NC}"
}

run_migrations() {
    cd "$APP_DIR"
    echo -e "${YELLOW}Running migrations...${NC}"
    php artisan migrate --force
    echo -e "${GREEN}Done!${NC}"
}

clear_cache() {
    cd "$APP_DIR"
    echo -e "${YELLOW}Clearing cache...${NC}"
    php artisan optimize:clear
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache 2>/dev/null || true
    echo -e "${GREEN}Done!${NC}"
}

restart_services() {
    echo -e "${YELLOW}Restarting services...${NC}"
    systemctl restart nginx 2>/dev/null && echo -e "  ${GREEN}✓${NC} Nginx restarted" || echo -e "  ${RED}✗${NC} Nginx restart failed"
    systemctl restart php8.4-fpm 2>/dev/null && echo -e "  ${GREEN}✓${NC} PHP-FPM restarted" || echo -e "  ${RED}✗${NC} PHP-FPM restart failed"
    supervisorctl restart devlio-billing-worker:* 2>/dev/null && echo -e "  ${GREEN}✓${NC} Queue workers restarted" || echo -e "  ${RED}✗${NC} Queue worker restart failed"
    systemctl restart redis-server 2>/dev/null && echo -e "  ${GREEN}✓${NC} Redis restarted" || echo -e "  ${RED}✗${NC} Redis restart failed"
    echo -e "${GREEN}Done!${NC}"
}

view_logs() {
    cd "$APP_DIR"
    echo -e "${YELLOW}Log files:${NC}"
    echo -e "  ${CYAN}1${NC}) Application log"
    echo -e "  ${CYAN}2${NC}) Queue worker log"
    echo -e "  ${CYAN}3${NC}) Nginx error log"
    echo -e "  ${CYAN}4${NC}) PHP-FPM log"
    echo -e "  ${CYAN}5${NC}) Laravel Pail (live tail)"
    echo ""
    read -p "Choose log file [1-5]: " LOG_CHOICE
    case $LOG_CHOICE in
        1) tail -n 50 -f storage/logs/laravel.log ;;
        2) tail -n 50 -f storage/logs/worker.log ;;
        3) tail -n 50 -f /var/log/nginx/error.log ;;
        4) tail -n 50 -f /var/log/php8.4-fpm.log ;;
        5) php artisan pail --timeout=300 ;;
        *) echo -e "${RED}Invalid option${NC}" ;;
    esac
}

backup_database() {
    cd "$APP_DIR"
    BACKUP_DIR="/var/backups/devlio-billing"
    mkdir -p "$BACKUP_DIR"
    TIMESTAMP=$(date +%Y%m%d_%H%M%S)
    BACKUP_FILE="$BACKUP_DIR/${DB_NAME}_${TIMESTAMP}.sql.gz"

    echo -e "${YELLOW}Backing up database...${NC}"

    # Read DB credentials from .env
    DB_HOST=$(grep DB_HOST .env | cut -d '=' -f2)
    DB_NAME=$(grep DB_DATABASE .env | cut -d '=' -f2)
    DB_USER=$(grep DB_USERNAME .env | cut -d '=' -f2)
    DB_PASS=$(grep DB_PASSWORD .env | cut -d '=' -f2)

    mysqldump -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" | gzip > "$BACKUP_FILE"

    if [ -f "$BACKUP_FILE" ]; then
        echo -e "${GREEN}Backup created: $BACKUP_FILE${NC}"
        ls -lh "$BACKUP_FILE"
    else
        echo -e "${RED}Backup failed!${NC}"
    fi
}

generate_passport_keys() {
    cd "$APP_DIR"
    echo -e "${YELLOW}Generating Passport API keys...${NC}"
    php artisan passport:keys --force
    echo -e "${GREEN}Passport keys generated!${NC}"
}

change_admin_password() {
    cd "$APP_DIR"
    echo -e "${YELLOW}Change Admin Password${NC}"
    echo ""
    read -p "Admin email: " ADMIN_EMAIL
    while [ -z "$NEW_PASSWORD" ]; do
        read -s -p "New password: " NEW_PASSWORD
        echo ""
        if [ -z "$NEW_PASSWORD" ]; then echo -e "${RED}Password is required.${NC}"; fi
    done
    while [ -z "$CONFIRM_PASSWORD" ]; do
        read -s -p "Confirm password: " CONFIRM_PASSWORD
        echo ""
        if [ "$NEW_PASSWORD" != "$CONFIRM_PASSWORD" ]; then
            echo -e "${RED}Passwords do not match.${NC}"
            CONFIRM_PASSWORD=""
        fi
    done

    php artisan tinker --execute="
        \$user = \App\Models\User::where('email', '${ADMIN_EMAIL}')->first();
        if (\$user) {
            \$user->update(['password' => bcrypt('${NEW_PASSWORD}')]);
            echo 'Password updated for: ' . \$user->email . PHP_EOL;
        } else {
            echo 'User not found!' . PHP_EOL;
        }
    "
    unset NEW_PASSWORD CONFIRM_PASSWORD
    echo -e "${GREEN}Password changed successfully!${NC}"
}

check_ssl_certificate() {
    if [ -z "$1" ]; then
        read -p "Domain name: " DOMAIN
    else
        DOMAIN="$1"
    fi

    echo -e "${YELLOW}Checking SSL certificate for: $DOMAIN${NC}"
    echo ""

    CERT_INFO=$(echo | openssl s_client -servername "$DOMAIN" -connect "$DOMAIN":443 2>/dev/null | openssl x509 -noout -dates -subject 2>/dev/null)

    if [ -n "$CERT_INFO" ]; then
        echo -e "${GREEN}Certificate found:${NC}"
        echo "$CERT_INFO"
        echo ""

        EXPIRY=$(echo "$CERT_INFO" | grep "notAfter" | cut -d= -f2)
        if [ -n "$EXPIRY" ]; then
            EXPIRY_EPOCH=$(date -d "$EXPIRY" +%s 2>/dev/null || echo "0")
            NOW_EPOCH=$(date +%s)
            DAYS_LEFT=$(( (EXPIRY_EPOCH - NOW_EPOCH) / 86400 ))

            if [ "$DAYS_LEFT" -gt 30 ]; then
                echo -e "${GREEN}Certificate expires in $DAYS_LEFT days${NC}"
            elif [ "$DAYS_LEFT" -gt 0 ]; then
                echo -e "${YELLOW}Certificate expires in $DAYS_LEFT days - RENEW NOW!${NC}"
                echo -e "Run: ${CYAN}certbot renew --nginx${NC}"
            else
                echo -e "${RED}Certificate has expired!${NC}"
                echo -e "Run: ${CYAN}certbot renew --nginx${NC}"
            fi
        fi
    else
        echo -e "${RED}No SSL certificate found or connection failed.${NC}"
    fi
}

update_filament_assets() {
    cd "$APP_DIR"
    echo -e "${YELLOW}Publishing Filament assets...${NC}"
    php artisan filament:assets
    echo -e "${YELLOW}Fixing permissions...${NC}"
    chown -R www-data:www-data "$APP_DIR/public"
    echo -e "${GREEN}Filament assets updated!${NC}"
}

while true; do
    show_menu
    case $MENU_CHOICE in
        1) install_website ;;
        2) create_user ;;
        3) pull_and_migrate ;;
        4) run_migrations ;;
        5) clear_cache ;;
        6) restart_services ;;
        7) view_logs ;;
        8) backup_database ;;
        9) generate_passport_keys ;;
        10) change_admin_password ;;
        11) check_ssl_certificate ;;
        12) update_filament_assets ;;
        0) echo -e "${GREEN}Goodbye!${NC}"; exit 0 ;;
        *) echo -e "${RED}Invalid option${NC}" ;;
    esac
    echo ""
    read -p "Press Enter to continue..."
done
