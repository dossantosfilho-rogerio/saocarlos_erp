#!/usr/bin/env bash
# =============================================================================
# deploy.sh — Deploy do São Carlos ERP para VPS
# Uso: bash deploy.sh
# Pré-requisito local: git configurado e working tree limpa
# =============================================================================
set -euo pipefail

# --- Configurações -----------------------------------------------------------
VPS_HOST="145.223.29.87"
VPS_USER="root"
APP_DIR="/var/www/saocarlos_erp"
GIT_REPO="https://github.com/dossantosfilho-rogerio/saocarlos_erp.git"   # ajuste aqui
GIT_BRANCH="main"
PHP_BIN="php8.3"           # ajuste se necessário (php, php8.2, etc.)
DOMAIN="frigorificosaocarlos.online"
DOMAIN_WWW="www.frigorificosaocarlos.online"
# -----------------------------------------------------------------------------

echo "==> Conectando à VPS $VPS_HOST…"

ssh -o StrictHostKeyChecking=accept-new "$VPS_USER@$VPS_HOST" \
"APP_DIR='$APP_DIR' GIT_REPO='$GIT_REPO' GIT_BRANCH='$GIT_BRANCH' PHP_BIN='$PHP_BIN' DOMAIN='$DOMAIN' DOMAIN_WWW='$DOMAIN_WWW' bash -s" <<'REMOTE'
set -euo pipefail

# ---- Instalar dependências do sistema (apenas primeira vez) ----------------
if ! command -v $PHP_BIN &>/dev/null; then
    apt-get update -q
    apt-get install -y -q $PHP_BIN ${PHP_BIN}-cli ${PHP_BIN}-fpm \
        ${PHP_BIN}-sqlite3 ${PHP_BIN}-mbstring ${PHP_BIN}-xml \
        ${PHP_BIN}-curl ${PHP_BIN}-zip ${PHP_BIN}-bcmath \
        apache2 git curl unzip certbot python3-certbot-apache
fi

if ! command -v certbot &>/dev/null; then
    apt-get update -q
    apt-get install -y -q certbot python3-certbot-apache
fi

if ! command -v composer &>/dev/null; then
    curl -sS https://getcomposer.org/installer | $PHP_BIN
    mv composer.phar /usr/local/bin/composer
fi

export COMPOSER_ALLOW_SUPERUSER=1

NODE_MAJOR="0"
if command -v node &>/dev/null; then
    NODE_MAJOR="$(node -v | sed -E 's/^v([0-9]+).*/\1/')"
fi

# Vite do projeto exige Node >= 20.
if [ "$NODE_MAJOR" -lt 20 ]; then
    curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
    apt-get install -y -q nodejs
fi

# ---- Clonar ou atualizar o repositório -------------------------------------
if [ ! -d "$APP_DIR/.git" ]; then
    echo "  -> Clonando repositório…"
    git clone --branch $GIT_BRANCH $GIT_REPO $APP_DIR
else
    echo "  -> Atualizando repositório…"
    cd $APP_DIR
    git fetch origin
    git reset --hard origin/$GIT_BRANCH
fi

cd $APP_DIR

# ---- Arquivo .env ----------------------------------------------------------
if [ ! -f ".env" ]; then
    cp .env.example .env
    echo ""
    echo "  [!] Edite $APP_DIR/.env antes de continuar (APP_KEY, DB_DATABASE, etc.)"
    echo "      Execute: nano $APP_DIR/.env"
    echo "      Depois rode novamente: bash deploy.sh"
    exit 1
fi

# ---- Dependências PHP -------------------------------------------------------
composer install --no-dev --optimize-autoloader --no-interaction

# ---- APP_KEY ---------------------------------------------------------------
$PHP_BIN artisan key:generate --force

# ---- Dependências JS + build ------------------------------------------------
npm ci --silent
npm run build

# ---- Banco de dados ---------------------------------------------------------
# SQLite: garante que o arquivo existe
DB_PATH=$($PHP_BIN artisan tinker --execute="echo config('database.connections.sqlite.database');" 2>/dev/null || true)
if [[ "$DB_PATH" == *.sqlite* ]]; then
    mkdir -p "$(dirname $DB_PATH)"
    touch "$DB_PATH"
fi

$PHP_BIN artisan migrate --force

# ---- Cache / otimização -----------------------------------------------------
$PHP_BIN artisan config:cache
$PHP_BIN artisan route:cache
$PHP_BIN artisan view:cache

# ---- Permissões -------------------------------------------------------------
chown -R www-data:www-data $APP_DIR/storage $APP_DIR/bootstrap/cache
chmod -R 775 $APP_DIR/storage $APP_DIR/bootstrap/cache

# ---- Apache -----------------------------------------------------------------
APACHE_CONF="/etc/apache2/sites-available/saocarlos_erp.conf"
cat > "$APACHE_CONF" <<'APACHE'
<VirtualHost *:80>
    ServerName frigorificosaocarlos.online
    ServerAlias www.frigorificosaocarlos.online
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/saocarlos_erp/public

    <Directory /var/www/saocarlos_erp/public>
        AllowOverride All
        Require all granted
    </Directory>

    <FilesMatch \.php$>
        SetHandler "proxy:unix:/run/php/php8.3-fpm.sock|fcgi://localhost/"
    </FilesMatch>

    ErrorLog ${APACHE_LOG_DIR}/saocarlos_error.log
    CustomLog ${APACHE_LOG_DIR}/saocarlos_access.log combined
</VirtualHost>
APACHE

a2enmod rewrite headers proxy proxy_fcgi setenvif >/dev/null
a2enconf php8.3-fpm >/dev/null 2>&1 || true
a2ensite saocarlos_erp.conf >/dev/null
a2dissite 000-default.conf >/dev/null 2>&1 || true

# Desativa Nginx, caso esteja ativo.
systemctl stop nginx >/dev/null 2>&1 || true
systemctl disable nginx >/dev/null 2>&1 || true

# ---- PHP-FPM + Apache -------------------------------------------------------
systemctl restart php8.3-fpm 2>/dev/null || true
apache2ctl configtest
systemctl reload apache2

# ---- HTTPS (Let's Encrypt) --------------------------------------------------
certbot --apache \
    -d "$DOMAIN" \
    -d "$DOMAIN_WWW" \
    --non-interactive \
    --agree-tos \
    --register-unsafely-without-email \
    --redirect || true

systemctl enable --now certbot.timer >/dev/null 2>&1 || true

echo ""
echo "=================================================="
echo "  Deploy concluído com sucesso!"
echo "  Acesse: https://$DOMAIN"
echo "=================================================="
REMOTE
