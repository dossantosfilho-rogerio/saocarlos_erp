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
GIT_BRANCH="master"
PHP_BIN="php8.3"           # ajuste se necessário (php, php8.2, etc.)
# -----------------------------------------------------------------------------

echo "==> Conectando à VPS $VPS_HOST…"

ssh -o StrictHostKeyChecking=accept-new "$VPS_USER@$VPS_HOST" bash <<REMOTE
set -euo pipefail

# ---- Instalar dependências do sistema (apenas primeira vez) ----------------
if ! command -v $PHP_BIN &>/dev/null; then
    apt-get update -q
    apt-get install -y -q $PHP_BIN ${PHP_BIN}-cli ${PHP_BIN}-fpm \
        ${PHP_BIN}-sqlite3 ${PHP_BIN}-mbstring ${PHP_BIN}-xml \
        ${PHP_BIN}-curl ${PHP_BIN}-zip ${PHP_BIN}-bcmath \
        nginx git curl unzip
fi

if ! command -v composer &>/dev/null; then
    curl -sS https://getcomposer.org/installer | $PHP_BIN
    mv composer.phar /usr/local/bin/composer
fi

if ! command -v node &>/dev/null; then
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
DB_PATH=\$($PHP_BIN artisan tinker --execute="echo config('database.connections.sqlite.database');" 2>/dev/null || true)
if [[ "\$DB_PATH" == *.sqlite* ]]; then
    mkdir -p "\$(dirname \$DB_PATH)"
    touch "\$DB_PATH"
fi

$PHP_BIN artisan migrate --force

# ---- Cache / otimização -----------------------------------------------------
$PHP_BIN artisan config:cache
$PHP_BIN artisan route:cache
$PHP_BIN artisan view:cache

# ---- Permissões -------------------------------------------------------------
chown -R www-data:www-data $APP_DIR/storage $APP_DIR/bootstrap/cache
chmod -R 775 $APP_DIR/storage $APP_DIR/bootstrap/cache

# ---- Nginx ------------------------------------------------------------------
NGINX_CONF="/etc/nginx/sites-available/saocarlos_erp"
if [ ! -f "\$NGINX_CONF" ]; then
    cat > "\$NGINX_CONF" <<'NGINX'
server {
    listen 80;
    server_name 145.223.29.87;   # troque pelo seu domínio quando tiver

    root /var/www/saocarlos_erp/public;
    index index.php index.html;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php\$ {
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;  # ajuste a versão
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
NGINX
    ln -sf "\$NGINX_CONF" /etc/nginx/sites-enabled/saocarlos_erp
    rm -f /etc/nginx/sites-enabled/default
    nginx -t
    systemctl reload nginx
    echo "  -> Nginx configurado."
fi

# ---- PHP-FPM ----------------------------------------------------------------
systemctl restart php8.3-fpm 2>/dev/null || true

echo ""
echo "=================================================="
echo "  Deploy concluído com sucesso!"
echo "  Acesse: http://145.223.29.87"
echo "=================================================="
REMOTE
