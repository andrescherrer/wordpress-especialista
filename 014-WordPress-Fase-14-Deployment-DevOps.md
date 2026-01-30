# 🚀 FASE 14: Deployment e DevOps - Guia Completo

## 14.1 Development Environment - Docker para WordPress

### 14.1.1 Docker Compose File Completo

```yaml
version: '3.9'

services:
  # Banco de dados MySQL
  db:
    image: mysql:8.0
    container_name: wordpress_db_dev
    environment:
      MYSQL_ROOT_PASSWORD: root_password_dev
      MYSQL_DATABASE: wordpress_dev
      MYSQL_USER: wordpress_user
      MYSQL_PASSWORD: wordpress_password
    ports:
      - "3306:3306"
    volumes:
      - db_data:/var/lib/mysql
      - ./docker/mysql/my.cnf:/etc/mysql/conf.d/my.cnf:ro
    networks:
      - wordpress_network
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-h", "localhost"]
      interval: 10s
      timeout: 5s
      retries: 5

  # PHP-FPM
  php:
    build:
      context: ./docker/php
      dockerfile: Dockerfile
    container_name: wordpress_php_dev
    depends_on:
      db:
        condition: service_healthy
    environment:
      DB_HOST: db
      DB_NAME: wordpress_dev
      DB_USER: wordpress_user
      DB_PASSWORD: wordpress_password
      WP_ENV: development
      WP_DEBUG: "true"
      WP_DEBUG_LOG: "/var/www/html/wp-content/debug.log"
    volumes:
      - ./:/var/www/html
      - ./docker/php/php.ini:/usr/local/etc/php/conf.d/custom.ini:ro
      - ./docker/php/www.conf:/usr/local/etc/php-fpm.d/www.conf:ro
    networks:
      - wordpress_network
    user: "1000:1000"

  # Nginx
  nginx:
    image: nginx:alpine
    container_name: wordpress_nginx_dev
    depends_on:
      - php
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./:/var/www/html
      - ./docker/nginx/nginx.conf:/etc/nginx/nginx.conf:ro
      - ./docker/nginx/conf.d/default.conf:/etc/nginx/conf.d/default.conf:ro
      - ./docker/nginx/ssl:/etc/nginx/ssl:ro
    networks:
      - wordpress_network
    environment:
      - NGINX_HOST=wordpress.local
      - NGINX_PORT=80

  # Redis para caching
  redis:
    image: redis:7-alpine
    container_name: wordpress_redis_dev
    ports:
      - "6379:6379"
    volumes:
      - redis_data:/data
    networks:
      - wordpress_network
    healthcheck:
      test: ["CMD", "redis-cli", "ping"]
      interval: 10s
      timeout: 5s
      retries: 5

  # Mailhog para testes de email
  mailhog:
    image: mailhog/mailhog:latest
    container_name: wordpress_mailhog_dev
    ports:
      - "1025:1025"
      - "8025:8025"
    networks:
      - wordpress_network

  # WP-CLI para administração
  wp-cli:
    image: wordpress:cli
    container_name: wordpress_cli_dev
    depends_on:
      - db
      - php
    volumes:
      - ./:/var/www/html
    environment:
      DB_HOST: db
      DB_NAME: wordpress_dev
      DB_USER: wordpress_user
      DB_PASSWORD: wordpress_password
    networks:
      - wordpress_network
    user: "33:33"
    entrypoint: wp

volumes:
  db_data:
  redis_data:

networks:
  wordpress_network:
    driver: bridge
```

### 14.1.2 Dockerfile PHP-FPM Otimizado

```dockerfile
FROM php:8.2-fpm-alpine

# Instalar dependências do sistema
RUN apk add --no-cache \
    build-base \
    libzip-dev \
    imagemagick-dev \
    postgresql-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    git \
    curl \
    wget \
    nano

# Instalar extensões PHP
RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg && \
    docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    pdo_pgsql \
    zip \
    gd \
    intl \
    opcache \
    bcmath

# Instalar extensões adicionais via PECL
RUN pecl install imagick redis xdebug && \
    docker-php-ext-enable imagick redis xdebug

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar configurações customizadas
COPY php.ini /usr/local/etc/php/conf.d/custom.ini
COPY www.conf /usr/local/etc/php-fpm.d/www.conf

# Criar usuário www-data com permissões corretas
RUN addgroup -g 1000 wordpress && \
    adduser -D -u 1000 -G wordpress wordpress && \
    mkdir -p /var/www/html && \
    chown -R wordpress:wordpress /var/www/html

WORKDIR /var/www/html

EXPOSE 9000

CMD ["php-fpm"]
```

### 14.1.3 Configuração PHP (php.ini)

```ini
[PHP]
display_errors = On
display_startup_errors = On
error_reporting = E_ALL
log_errors = On
error_log = /var/www/html/wp-content/debug.log

[Memory]
memory_limit = 256M
max_execution_time = 300
max_input_time = 300
post_max_size = 100M
upload_max_filesize = 100M

[OpCache]
opcache.enable = 1
opcache.enable_cli = 1
opcache.memory_consumption = 128
opcache.interned_strings_buffer = 8
opcache.max_accelerated_files = 4000
opcache.revalidate_freq = 60
opcache.fast_shutdown = 1
opcache.validate_timestamps = 1

[Session]
session.save_handler = redis
session.save_path = "tcp://redis:6379/0"

[Limits]
max_file_uploads = 20

[Date]
date.timezone = America/Sao_Paulo

[XDebug]
xdebug.mode = develop,debug
xdebug.start_with_request = yes
xdebug.client_host = host.docker.internal
xdebug.client_port = 9003
xdebug.idekey = docker
```

### 14.1.4 Configuração Nginx

```nginx
server {
    listen 80;
    server_name wordpress.local;
    root /var/www/html;
    index index.php;

    # Logs
    access_log /var/log/nginx/access.log;
    error_log /var/log/nginx/error.log;

    # Segurança
    client_max_body_size 100M;

    # Desabilitar listagem de diretórios
    autoindex off;

    # Bloquear acesso a arquivos sensíveis
    location ~ /\. {
        deny all;
        access_log off;
        log_not_found off;
    }

    location ~ ~$ {
        deny all;
        access_log off;
        log_not_found off;
    }

    # Reescrita de URLs WordPress
    location / {
        try_files $uri $uri/ /index.php?$args;
    }

    # Processar PHP
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass php:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }

    # Cache estático
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }

    # Bloquear WP-Admin para IPs específicos (development)
    location ~ /wp-admin/ {
        # Aqui você pode adicionar restrição por IP se necessário
        fastcgi_pass php:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    # Desabilitar acesso a wp-config
    location ~ /wp-config\.php {
        deny all;
    }
}
```

### 14.1.5 Iniciar Environment de Desenvolvimento

```bash
# Construir e iniciar containers
docker-compose up -d

# Verificar status
docker-compose ps

# Ver logs
docker-compose logs -f php

# Parar containers
docker-compose down

# Destruir volumes (CUIDADO!)
docker-compose down -v
```

### 14.1.6 Configurar WordPress em Desenvolvimento

```bash
# Entrar no container PHP
docker-compose exec php bash

# Usar WP-CLI para configuração
docker-compose exec wp-cli wp core install \
    --url=http://wordpress.local \
    --title="WordPress Dev" \
    --admin_user=admin \
    --admin_password=password \
    --admin_email=dev@example.com

# Gerar salt keys
docker-compose exec wp-cli wp config shuffle-salts

# Criar tabelas adicionais
docker-compose exec wp-cli wp db tables
```

### 14.1.7 .dockerignore

```
.git
.gitignore
.DS_Store
node_modules
npm-debug.log
.idea
.vscode
*.log
.env.local
.env.*.local
*.swp
*.swo
*~
.cache
.vagrant
.env
docker-compose.override.yml
```

---

## 14.2 Staging Environment - Replicar Production

### 14.2.1 Docker Compose para Staging

```yaml
version: '3.9'

services:
  db:
    image: mysql:8.0
    container_name: wordpress_db_staging
    environment:
      MYSQL_ROOT_PASSWORD: ${DB_ROOT_PASSWORD}
      MYSQL_DATABASE: ${DB_NAME}
      MYSQL_USER: ${DB_USER}
      MYSQL_PASSWORD: ${DB_PASSWORD}
    ports:
      - "3307:3306"
    volumes:
      - db_staging_data:/var/lib/mysql
      - ./backups/staging.sql.gz:/docker-entrypoint-initdb.d/init.sql.gz
    networks:
      - wordpress_staging
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-h", "localhost"]
      interval: 10s
      timeout: 5s
      retries: 5

  php:
    build:
      context: ./docker/php
      dockerfile: Dockerfile.staging
    container_name: wordpress_php_staging
    depends_on:
      db:
        condition: service_healthy
    environment:
      DB_HOST: db
      DB_NAME: ${DB_NAME}
      DB_USER: ${DB_USER}
      DB_PASSWORD: ${DB_PASSWORD}
      WP_ENV: staging
      WP_DEBUG: "false"
      WP_DEBUG_LOG: "/var/www/html/wp-content/debug.log"
    volumes:
      - ./:/var/www/html
      - ./docker/php/php.staging.ini:/usr/local/etc/php/conf.d/custom.ini:ro
    networks:
      - wordpress_staging

  nginx:
    image: nginx:alpine
    container_name: wordpress_nginx_staging
    depends_on:
      - php
    ports:
      - "8080:80"
    volumes:
      - ./:/var/www/html
      - ./docker/nginx/nginx.staging.conf:/etc/nginx/nginx.conf:ro
    networks:
      - wordpress_staging

  redis:
    image: redis:7-alpine
    container_name: wordpress_redis_staging
    volumes:
      - redis_staging_data:/data
    networks:
      - wordpress_staging

volumes:
  db_staging_data:
  redis_staging_data:

networks:
  wordpress_staging:
    driver: bridge
```

### 14.2.2 Script de Sincronização de Database

```bash
#!/bin/bash

# sync-database.sh
# Sincroniza database de production para staging

set -e

PROD_DB_HOST=${PROD_DB_HOST:-"prod-server.com"}
PROD_DB_USER=${PROD_DB_USER}
PROD_DB_PASSWORD=${PROD_DB_PASSWORD}
PROD_DB_NAME=${PROD_DB_NAME:-"wordpress_prod"}

STAGING_CONTAINER="wordpress_db_staging"
STAGING_DB_USER=${STAGING_DB_USER}
STAGING_DB_PASSWORD=${STAGING_DB_PASSWORD}
STAGING_DB_NAME=${STAGING_DB_NAME:-"wordpress_staging"}

BACKUP_DIR="./backups"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_FILE="${BACKUP_DIR}/staging_backup_${TIMESTAMP}.sql.gz"

echo "🔄 Iniciando sincronização de database..."

# Criar diretório de backups se não existir
mkdir -p "$BACKUP_DIR"

# Fazer backup do staging antes de sincronizar
echo "📦 Fazendo backup do staging..."
docker-compose exec -T "$STAGING_CONTAINER" mysqldump \
    -u "$STAGING_DB_USER" \
    -p"$STAGING_DB_PASSWORD" \
    "$STAGING_DB_NAME" | gzip > "$BACKUP_FILE"
echo "✅ Backup salvo em $BACKUP_FILE"

# Fazer dump da production
echo "📥 Fazendo dump da database de production..."
mysqldump \
    -h "$PROD_DB_HOST" \
    -u "$PROD_DB_USER" \
    -p"$PROD_DB_PASSWORD" \
    "$PROD_DB_NAME" | \
    # Substituir URLs
    sed "s|${PROD_URL}|${STAGING_URL}|g" | \
    gzip > "${BACKUP_DIR}/prod_dump_${TIMESTAMP}.sql.gz"

# Importar para staging
echo "📤 Importando para staging..."
gunzip < "${BACKUP_DIR}/prod_dump_${TIMESTAMP}.sql.gz" | \
    docker-compose exec -T "$STAGING_CONTAINER" mysql \
    -u "$STAGING_DB_USER" \
    -p"$STAGING_DB_PASSWORD" \
    "$STAGING_DB_NAME"

# Limpar dados sensíveis em staging
echo "🧹 Limpando dados sensíveis..."
docker-compose exec -T "$STAGING_CONTAINER" mysql \
    -u "$STAGING_DB_USER" \
    -p"$STAGING_DB_PASSWORD" \
    "$STAGING_DB_NAME" << 'EOF'
-- Remover usuários de admin
DELETE FROM wp_users WHERE ID > 1;
DELETE FROM wp_usermeta WHERE user_id > 1;

-- Remover informações de pagamento
UPDATE wp_postmeta SET meta_value = '' 
    WHERE meta_key LIKE '%payment%' 
    OR meta_key LIKE '%credit%';

-- Resetar URLs
UPDATE wp_options SET option_value = 'http://localhost:8080' 
    WHERE option_name = 'siteurl' 
    OR option_name = 'home';
EOF

echo "✅ Sincronização concluída!"
```

### 14.2.3 Script de Sincronização de Assets (wp-content)

```bash
#!/bin/bash

# sync-assets.sh
# Sincroniza arquivos de media e uploads

set -e

PROD_SERVER=${PROD_SERVER:-"user@prod-server.com"}
PROD_PATH="/var/www/html/wp-content/uploads"
STAGING_PATH="./wp-content/uploads"

BACKUP_DIR="./backups/uploads"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

echo "🔄 Sincronizando assets de production..."

# Criar diretório de backups
mkdir -p "$BACKUP_DIR"

# Fazer backup local antes
if [ -d "$STAGING_PATH" ]; then
    echo "📦 Fazendo backup local dos uploads..."
    tar -czf "${BACKUP_DIR}/uploads_backup_${TIMESTAMP}.tar.gz" "$STAGING_PATH"
    echo "✅ Backup salvo"
fi

# Sincronizar com rsync
echo "📥 Sincronizando arquivos..."
rsync -avz \
    --delete \
    --exclude '*.cache' \
    --exclude 'backup' \
    "${PROD_SERVER}:${PROD_PATH}/" \
    "$STAGING_PATH/"

echo "✅ Sincronização de assets concluída!"

# Corrigir permissões
echo "🔐 Corrigindo permissões..."
chmod -R 755 "$STAGING_PATH"
chown -R www-data:www-data "$STAGING_PATH"
```

### 14.2.4 Workflow de Testes em Staging

```bash
#!/bin/bash

# run-staging-tests.sh

set -e

echo "🧪 Executando testes em staging..."

# Copiar arquivo .env.staging
cp .env.staging .env

# Iniciar containers
docker-compose -f docker-compose.staging.yml up -d

# Esperar serviços estarem prontos
sleep 10

# Verificar saúde dos containers
echo "🏥 Verificando saúde dos serviços..."
docker-compose -f docker-compose.staging.yml ps

# Executar testes de conectividade
echo "🔗 Testando conectividade..."
docker-compose -f docker-compose.staging.yml exec -T php \
    curl -f http://nginx/wp-admin/admin-ajax.php || exit 1

# Executar testes unitários
echo "🧬 Executando testes unitários..."
docker-compose -f docker-compose.staging.yml exec -T php \
    ./vendor/bin/phpunit tests/

# Executar análise de código
echo "📊 Executando análise de código..."
docker-compose -f docker-compose.staging.yml exec -T php \
    ./vendor/bin/phpstan analyse app/ --level=9

# Executar testes de segurança
echo "🔒 Verificando vulnerabilidades..."
docker-compose -f docker-compose.staging.yml exec -T php \
    composer audit

# Limpar
docker-compose -f docker-compose.staging.yml down

echo "✅ Todos os testes passaram!"
```

---

## 14.3 Production Environment - Configuração de Production

### 14.3.1 Setup Inicial de Servidor (Ubuntu 22.04)

```bash
#!/bin/bash

# production-setup.sh
# Script de setup inicial do servidor de production

set -e

# Atualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar dependências
sudo apt install -y \
    curl \
    wget \
    git \
    vim \
    htop \
    net-tools \
    nginx \
    mysql-server \
    php8.2-fpm \
    php8.2-mysql \
    php8.2-cli \
    php8.2-zip \
    php8.2-gd \
    php8.2-intl \
    php8.2-xml \
    php8.2-bcmath \
    composer \
    redis-server \
    certbot \
    python3-certbot-nginx \
    fail2ban \
    unattended-upgrades \
    apt-listchanges

# Configurar timezone
sudo timedatectl set-timezone America/Sao_Paulo

# Criar usuário para aplicação
sudo useradd -m -s /bin/bash -G www-data wordpress || true

# Criar estrutura de diretórios
sudo mkdir -p /var/www/wordpress
sudo mkdir -p /var/backups/wordpress
sudo chown -R wordpress:www-data /var/www/wordpress
sudo chmod -R 755 /var/www/wordpress

# Configurar permissões SSH
sudo mkdir -p /home/wordpress/.ssh
sudo chmod 700 /home/wordpress/.ssh

echo "✅ Setup básico concluído!"
```

### 14.3.2 Configuração PHP para Production

```ini
# /etc/php/8.2/fpm/php-production.ini

[PHP]
display_errors = Off
display_startup_errors = Off
error_reporting = E_ALL
log_errors = On
error_log = /var/log/php/error.log
date.timezone = America/Sao_Paulo

[Memory]
memory_limit = 512M
max_execution_time = 300
max_input_time = 300
post_max_size = 100M
upload_max_filesize = 100M

[OpCache]
opcache.enable = 1
opcache.enable_cli = 1
opcache.memory_consumption = 256
opcache.interned_strings_buffer = 16
opcache.max_accelerated_files = 8000
opcache.revalidate_freq = 60
opcache.fast_shutdown = 1
opcache.validate_timestamps = 0
opcache.save_comments = 1

[Session]
session.save_handler = redis
session.save_path = "tcp://127.0.0.1:6379/1"
session.gc_maxlifetime = 86400
session.gc_probability = 1

[Security]
disable_functions = exec,passthru,shell_exec,system,proc_open
expose_php = Off
```

### 14.3.3 Configuração Nginx para Production

```nginx
# /etc/nginx/sites-available/wordpress

upstream php_backend {
    server unix:/run/php/php8.2-fpm.sock;
}

# Rate limiting
limit_req_zone $binary_remote_addr zone=general:10m rate=10r/s;
limit_req_zone $binary_remote_addr zone=login:10m rate=5r/m;

server {
    listen 80;
    server_name example.com www.example.com;
    
    # Redirecionar para HTTPS
    location / {
        return 301 https://$server_name$request_uri;
    }
    
    # Let's Encrypt validation
    location /.well-known/acme-challenge/ {
        root /var/www/certbot;
    }
}

server {
    listen 443 ssl http2;
    server_name example.com www.example.com;
    
    root /var/www/wordpress;
    index index.php;

    # SSL
    ssl_certificate /etc/letsencrypt/live/example.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/example.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;

    # HSTS
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    # Segurança Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;

    # Logs
    access_log /var/log/nginx/wordpress_access.log combined;
    error_log /var/log/nginx/wordpress_error.log;

    # Limites
    client_max_body_size 100M;
    client_body_timeout 10s;
    client_header_timeout 10s;

    # Desabilitar listagem de diretórios
    autoindex off;

    # Bloquear arquivos sensíveis
    location ~ /\. {
        deny all;
        access_log off;
        log_not_found off;
    }

    location ~ wp-config\.php$ {
        deny all;
        access_log off;
        log_not_found off;
    }

    # Reescrita de URLs
    location / {
        try_files $uri $uri/ /index.php?$args;
    }

    # PHP FPM
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass php_backend;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }

    # Cache estático
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2)$ {
        expires 365d;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    # Bloquear WP-Admin com rate limiting
    location ~ /wp-admin/ {
        limit_req zone=login burst=5 nodelay;
        fastcgi_pass php_backend;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    # Proteger xmlrpc
    location = /xmlrpc.php {
        deny all;
        access_log off;
        log_not_found off;
    }
}
```

### 14.3.4 Configuração MySQL para Production

```sql
-- Criar database e usuário
CREATE DATABASE wordpress_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER 'wordpress_prod'@'localhost' IDENTIFIED BY 'strong_password_here';

GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, 
      CREATE TEMPORARY TABLES, LOCK TABLES, EXECUTE, CREATE VIEW, 
      SHOW VIEW, CREATE ROUTINE, ALTER ROUTINE, TRIGGER 
      ON wordpress_prod.* TO 'wordpress_prod'@'localhost';

FLUSH PRIVILEGES;

-- Otimizações MySQL
SET GLOBAL max_connections = 1000;
SET GLOBAL innodb_buffer_pool_size = 2G;
SET GLOBAL query_cache_size = 0;
SET GLOBAL innodb_log_file_size = 512M;
```

### 14.3.5 wp-config.php para Production

```php
<?php
// wp-config.php - Production

// Database
define('DB_NAME', getenv('DB_NAME') ?: 'wordpress_prod');
define('DB_USER', getenv('DB_USER') ?: 'wordpress_prod');
define('DB_PASSWORD', getenv('DB_PASSWORD'));
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', 'utf8mb4_unicode_ci');

// Security Keys (gerar com wp-cli config shuffle-salts)
define('AUTH_KEY',         'put your unique phrase here');
define('SECURE_AUTH_KEY',  'put your unique phrase here');
define('LOGGED_IN_KEY',    'put your unique phrase here');
define('NONCE_KEY',        'put your unique phrase here');
define('AUTH_SALT',        'put your unique phrase here');
define('SECURE_AUTH_SALT', 'put your unique phrase here');
define('LOGGED_IN_SALT',   'put your unique phrase here');
define('NONCE_SALT',       'put your unique phrase here');

// WordPress Table Prefix
$table_prefix = 'wp_';

// Environment
define('WP_ENV', 'production');
define('WP_DEBUG', false);
define('WP_DEBUG_LOG', false);
define('SCRIPT_DEBUG', false);

// Performance
define('WP_CACHE', true);
define('WP_CACHE_KEY_SALT', 'wordpress_prod_');

// URLs
define('WP_HOME', 'https://example.com');
define('WP_SITEURL', 'https://example.com');

// Segurança
define('DISALLOW_FILE_EDIT', true);
define('DISALLOW_FILE_MODS', true);
define('FORCE_SSL_ADMIN', true);
define('FORCE_SSL_LOGIN', true);

// Performance
define('EMPTY_TRASH_DAYS', 30);
define('WP_AUTO_UPDATE_CORE', 'minor');

// Absolute path to the WordPress directory.
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__) . '/');
}

// Sets up WordPress vars and included files.
require_once(ABSPATH . 'wp-settings.php');
```

### 14.3.6 Redis para Sessions e Cache

```bash
# /etc/redis/redis.conf

port 6379
bind 127.0.0.1
databases 16
save 900 1
save 300 10
save 60 10000
appendonly yes
appendfsync everysec
maxmemory 512mb
maxmemory-policy allkeys-lru
```

Plugin PHP para usar Redis:

```php
// mu-plugins/redis-config.php
<?php
/**
 * Redis Configuration
 * 
 * Este arquivo está em /wp-content/mu-plugins/redis-config.php
 * e será carregado automaticamente pelo WordPress
 */

// Conexão Redis para Cache
define('WP_REDIS_HOST', '127.0.0.1');
define('WP_REDIS_PORT', 6379);
define('WP_REDIS_DATABASE', 0);

// Conexão Redis para Sessions (usando banco diferente)
define('SESSION_REDIS_HOST', '127.0.0.1');
define('SESSION_REDIS_PORT', 6379);
define('SESSION_REDIS_DATABASE', 1);

// Se usando plugin WP Redis
if (defined('WP_CACHE') && WP_CACHE) {
    define('REDIS_CACHE_TYPE', 'phpredis');
}
```

---

## 14.4 Version Control (Git) - Estratégias e Configurações

### 14.4.1 .gitignore Completo para WordPress

```
# .gitignore

# WordPress
wp-config.php
wp-content/cache/
wp-content/uploads/*
!wp-content/uploads/.gitkeep
wp-content/plugins/*
!wp-content/plugins/.gitkeep
wp-content/themes/*
!wp-content/themes/seu-tema/

# Environment
.env
.env.local
.env.*.local
.env.production
.env.staging

# IDE
.vscode/
.idea/
*.swp
*.swo
*~
.DS_Store
Thumbs.db

# Node
node_modules/
npm-debug.log
yarn-error.log
.npm

# Composer
vendor/
composer.lock

# Docker
docker-compose.override.yml
.docker/

# Logs
*.log
logs/
debug.log

# Backups
backups/
*.sql
*.sql.gz
*.zip

# OS
.DS_Store
.AppleDouble
.LSOverride
.TemporaryItems
.Spotlight-V100
.Trashes

# Testing
coverage/
.phpunit.result.cache

# Cache
.cache/
tmp/
```

### 14.4.2 Configuração de Commits com Conventional Commits

```bash
# .gitmessage (template de commit)

# <type>(<scope>): <subject>
#
# <body>
#
# <footer>
#
# Type can be:
#   feat:     A new feature
#   fix:      A bug fix
#   refactor: Code change that doesn't fix a bug or add a feature
#   perf:     Performance improvement
#   test:     Adding missing tests
#   docs:     Documentation change
#   chore:    Other changes that don't affect code or tests
#   ci:       CI configuration changes
#   build:    Build system changes
#   style:    Code style changes
#
# Scope is optional and specifies what part of the codebase
#
# Subject should be imperative, not past tense
# No dot (.) at the end
# Max 50 characters

# Configurar git para usar este template
# git config commit.template .gitmessage
```

### 14.4.3 Branching Strategy (Git Flow)

```
Estrutura de branches:

main (production)
├── Só merges de release/hotfix
└── Sempre com tag de versão

develop (staging)
├── Base para feature branches
└── Merges de features testadas

feature/* (desenvolvimento)
├── feature/new-plugin
├── feature/payment-integration
└── feature/performance-optimization

release/* (preparação para produção)
├── release/1.2.0
├── Testes finais
└── Fixes não encontrados

hotfix/* (correções de emergência)
├── hotfix/security-patch
└── Mergeia em main E develop
```

### 14.4.4 Scripts para Gerenciar Branches

```bash
#!/bin/bash
# scripts/git-feature-start.sh

usage() {
    echo "Usage: ./git-feature-start.sh <feature-name>"
    exit 1
}

if [ -z "$1" ]; then
    usage
fi

FEATURE_NAME=$1
BRANCH_NAME="feature/$FEATURE_NAME"

# Atualizar develop
git checkout develop
git pull origin develop

# Criar nova feature branch
git checkout -b "$BRANCH_NAME"

echo "✅ Feature branch '$BRANCH_NAME' criada!"
echo "Trabalhe na sua feature e execute:"
echo "  ./scripts/git-feature-finish.sh"
```

```bash
#!/bin/bash
# scripts/git-feature-finish.sh

# Verificar se está em uma feature branch
CURRENT_BRANCH=$(git rev-parse --abbrev-ref HEAD)

if [[ ! "$CURRENT_BRANCH" =~ ^feature/ ]]; then
    echo "❌ Você não está em uma feature branch!"
    exit 1
fi

# Testes
echo "🧪 Rodando testes..."
./vendor/bin/phpunit tests/ || exit 1

# Linter
echo "📊 Rodando linter..."
./vendor/bin/phpstan analyse app/ || exit 1

# Voltar para develop
git checkout develop
git pull origin develop

# Merge da feature
git merge --no-ff "$CURRENT_BRANCH" -m "Merge branch '$CURRENT_BRANCH' into develop"

# Push
git push origin develop
git push origin --delete "$CURRENT_BRANCH"

echo "✅ Feature branch merged e deletada!"
```

### 14.4.5 Pre-commit Hook

```bash
#!/bin/bash
# .git/hooks/pre-commit

# Executar antes de fazer commit

echo "🔍 Verificando código antes do commit..."

# 1. PHPStan
echo "  → Executando PHPStan..."
./vendor/bin/phpstan analyse app/ --level=9 --no-progress || exit 1

# 2. PHP-CS-Fixer
echo "  → Verificando estilo de código..."
./vendor/bin/php-cs-fixer fix --dry-run --diff app/ || exit 1

# 3. Verificar se há console.log deixado
if git diff --cached | grep -q "console\.log\|var_dump\|dd("; then
    echo "  ❌ Debug code encontrado!"
    exit 1
fi

# 4. Verificar tamanho dos arquivos
MAX_SIZE=5000000
while IFS= read -r file; do
    SIZE=$(du -b "$file" | cut -f1)
    if [ $SIZE -gt $MAX_SIZE ]; then
        echo "  ❌ Arquivo muito grande: $file ($(($SIZE / 1024)) KB)"
        exit 1
    fi
done < <(git diff --cached --name-only)

echo "✅ Todas as verificações passaram!"
```

---

## 14.5 CI/CD Pipeline

### 14.5.1 GitHub Actions Workflow Completo

```yaml
# .github/workflows/deploy.yml

name: Deploy Pipeline

on:
  push:
    branches:
      - develop
      - main
  pull_request:
    branches:
      - develop
      - main

env:
  REGISTRY: ghcr.io
  IMAGE_NAME: ${{ github.repository }}

jobs:
  # Job 1: Testes
  test:
    runs-on: ubuntu-latest
    
    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_ROOT_PASSWORD: root
          MYSQL_DATABASE: wordpress_test
        options: >-
          --health-cmd="mysqladmin ping"
          --health-interval=10s
          --health-timeout=5s
          --health-retries=3
        ports:
          - 3306:3306

      redis:
        image: redis:7-alpine
        options: >-
          --health-cmd="redis-cli ping"
          --health-interval=10s
          --health-timeout=5s
          --health-retries=3
        ports:
          - 6379:6379

    steps:
      - uses: actions/checkout@v4

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: mysql,redis,gd,intl,bcmath
          tools: composer, phpunit, phpstan

      - name: Cache Composer
        uses: actions/cache@v3
        with:
          path: vendor
          key: ${{ runner.os }}-composer-${{ hashFiles('**/composer.lock') }}
          restore-keys: |
            ${{ runner.os }}-composer-

      - name: Install Dependencies
        run: composer install --prefer-dist --no-progress

      - name: Wait for MySQL
        run: |
          until mysql -h127.0.0.1 -uroot -proot -e "select 1" > /dev/null 2>&1; do
            echo 'waiting for mysql'
            sleep 1
          done

      - name: Setup WordPress Test Database
        run: |
          mysql -h127.0.0.1 -uroot -proot wordpress_test < tests/database.sql

      - name: Run PHPUnit
        run: ./vendor/bin/phpunit tests/

      - name: Run PHPStan
        run: ./vendor/bin/phpstan analyse app/ --level=9

      - name: Upload Coverage
        uses: codecov/codecov-action@v3
        with:
          files: ./coverage/coverage.xml

  # Job 2: Security
  security:
    runs-on: ubuntu-latest
    
    steps:
      - uses: actions/checkout@v4

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'

      - name: Install Composer
        run: composer install --no-progress

      - name: Check Vulnerabilities
        run: composer audit

      - name: Run PHPCS
        run: ./vendor/bin/phpcs app/ --standard=PSR12 || true

      - name: SonarCloud Scan
        uses: SonarSource/sonarcloud-github-action@master
        env:
          GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}
          SONAR_TOKEN: ${{ secrets.SONAR_TOKEN }}

  # Job 3: Build Docker Image
  build:
    needs: [test, security]
    runs-on: ubuntu-latest
    if: github.event_name == 'push'

    permissions:
      contents: read
      packages: write

    steps:
      - uses: actions/checkout@v4

      - name: Set up Docker Buildx
        uses: docker/setup-buildx-action@v2

      - name: Log in to Container Registry
        uses: docker/login-action@v2
        with:
          registry: ${{ env.REGISTRY }}
          username: ${{ github.actor }}
          password: ${{ secrets.GITHUB_TOKEN }}

      - name: Extract Metadata
        id: meta
        uses: docker/metadata-action@v4
        with:
          images: ${{ env.REGISTRY }}/${{ env.IMAGE_NAME }}
          tags: |
            type=ref,event=branch
            type=sha,prefix={{branch}}-
            type=semver,pattern={{version}}

      - name: Build and Push Docker Image
        uses: docker/build-push-action@v4
        with:
          context: .
          push: true
          tags: ${{ steps.meta.outputs.tags }}
          labels: ${{ steps.meta.outputs.labels }}
          cache-from: type=gha
          cache-to: type=gha,mode=max

  # Job 4: Deploy para Staging
  deploy-staging:
    needs: build
    runs-on: ubuntu-latest
    if: github.ref == 'refs/heads/develop'

    steps:
      - uses: actions/checkout@v4

      - name: Deploy to Staging
        uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.STAGING_HOST }}
          username: ${{ secrets.STAGING_USER }}
          key: ${{ secrets.STAGING_SSH_KEY }}
          script: |
            cd /var/www/wordpress-staging
            git fetch origin
            git checkout develop
            git pull origin develop
            
            # Backup database
            mysqldump -u wordpress_staging -p${{ secrets.STAGING_DB_PASSWORD }} \
              wordpress_staging > /var/backups/staging_$(date +%Y%m%d_%H%M%S).sql
            
            # Install dependencies
            composer install --no-dev --optimize-autoloader
            
            # Run migrations if needed
            wp db optimize
            
            # Clear cache
            wp cache flush
            
            # Fix permissions
            sudo chown -R www-data:www-data /var/www/wordpress-staging
            sudo chmod -R 755 /var/www/wordpress-staging

      - name: Notify Slack
        uses: slackapi/slack-github-action@v1
        with:
          payload: |
            {
              "text": "✅ Staging deployment successful",
              "blocks": [
                {
                  "type": "section",
                  "text": {
                    "type": "mrkdwn",
                    "text": "*Staging Deployment*\nCommit: ${{ github.sha }}\nAuthor: ${{ github.actor }}"
                  }
                }
              ]
            }
        env:
          SLACK_WEBHOOK_URL: ${{ secrets.SLACK_WEBHOOK }}
          SLACK_WEBHOOK_TYPE: INCOMING_WEBHOOK

  # Job 5: Deploy para Production
  deploy-production:
    needs: build
    runs-on: ubuntu-latest
    if: github.ref == 'refs/heads/main'
    environment: production

    steps:
      - uses: actions/checkout@v4

      - name: Deploy to Production
        uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.PROD_HOST }}
          username: ${{ secrets.PROD_USER }}
          key: ${{ secrets.PROD_SSH_KEY }}
          script: |
            cd /var/www/wordpress
            
            # Backup completo
            tar -czf /var/backups/prod_$(date +%Y%m%d_%H%M%S).tar.gz .
            mysqldump -u wordpress_prod -p${{ secrets.PROD_DB_PASSWORD }} \
              wordpress_prod > /var/backups/prod_$(date +%Y%m%d_%H%M%S).sql
            
            # Deploy
            git fetch origin
            git checkout main
            git pull origin main
            
            # Composer
            composer install --no-dev --optimize-autoloader
            
            # Database
            wp db optimize
            
            # Cache
            wp cache flush
            redis-cli FLUSHALL
            
            # Permissões
            sudo chown -R www-data:www-data /var/www/wordpress
            sudo chmod -R 755 /var/www/wordpress
            
            # Reload PHP-FPM
            sudo systemctl reload php8.2-fpm

      - name: Health Check
        run: |
          curl -f https://example.com/health || exit 1

      - name: Notify
        uses: slackapi/slack-github-action@v1
        with:
          payload: |
            {
              "text": "🚀 Production deployment successful",
              "blocks": [
                {
                  "type": "section",
                  "text": {
                    "type": "mrkdwn",
                    "text": "*Production Deployment*\nVersion: ${{ github.ref }}\nCommit: ${{ github.sha }}\nAuthor: ${{ github.actor }}"
                  }
                }
              ]
            }
        env:
          SLACK_WEBHOOK_URL: ${{ secrets.SLACK_WEBHOOK }}
          SLACK_WEBHOOK_TYPE: INCOMING_WEBHOOK

      - name: Create Release
        uses: actions/create-release@v1
        env:
          GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}
        with:
          tag_name: v${{ github.run_number }}
          release_name: Release ${{ github.run_number }}
          body: |
            Production deployment completed
            Commit: ${{ github.sha }}
```

### 14.5.2 GitLab CI/CD Alternative

```yaml
# .gitlab-ci.yml

stages:
  - test
  - build
  - deploy-staging
  - deploy-production

variables:
  DOCKER_DRIVER: overlay2
  DOCKER_TLS_CERTDIR: "/certs"

before_script:
  - composer install --prefer-dist --no-progress

# Stage: Test
unit_tests:
  stage: test
  image: php:8.2-cli
  services:
    - mysql:8.0
    - redis:7-alpine
  script:
    - ./vendor/bin/phpunit tests/
  artifacts:
    reports:
      junit: build/junit.xml
    paths:
      - coverage/
  only:
    - merge_requests
    - develop
    - main

phpstan:
  stage: test
  image: php:8.2-cli
  script:
    - ./vendor/bin/phpstan analyse app/ --level=9
  only:
    - merge_requests
    - develop
    - main

phpcs:
  stage: test
  image: php:8.2-cli
  script:
    - ./vendor/bin/phpcs app/ --standard=PSR12
  allow_failure: true

# Stage: Build
build_docker:
  stage: build
  image: docker:latest
  services:
    - docker:dind
  script:
    - docker build -t $CI_REGISTRY_IMAGE:$CI_COMMIT_SHA .
    - docker tag $CI_REGISTRY_IMAGE:$CI_COMMIT_SHA $CI_REGISTRY_IMAGE:latest
    - docker push $CI_REGISTRY_IMAGE:$CI_COMMIT_SHA
    - docker push $CI_REGISTRY_IMAGE:latest
  only:
    - develop
    - main

# Stage: Deploy Staging
deploy_staging:
  stage: deploy-staging
  image: alpine:latest
  before_script:
    - apk add --no-cache openssh-client
    - mkdir -p ~/.ssh
    - echo "$STAGING_SSH_KEY" | base64 -d > ~/.ssh/id_rsa
    - chmod 600 ~/.ssh/id_rsa
    - ssh-keyscan -H $STAGING_HOST >> ~/.ssh/known_hosts
  script:
    - |
      ssh $STAGING_USER@$STAGING_HOST << 'DEPLOY'
      cd /var/www/wordpress-staging
      git fetch origin
      git checkout develop
      git pull origin develop
      composer install --no-dev
      wp cache flush
      DEPLOY
  environment:
    name: staging
  only:
    - develop

# Stage: Deploy Production
deploy_production:
  stage: deploy-production
  image: alpine:latest
  before_script:
    - apk add --no-cache openssh-client
    - mkdir -p ~/.ssh
    - echo "$PROD_SSH_KEY" | base64 -d > ~/.ssh/id_rsa
    - chmod 600 ~/.ssh/id_rsa
    - ssh-keyscan -H $PROD_HOST >> ~/.ssh/known_hosts
  script:
    - |
      ssh $PROD_USER@$PROD_HOST << 'DEPLOY'
      cd /var/www/wordpress
      tar -czf /var/backups/backup_$(date +%Y%m%d_%H%M%S).tar.gz .
      git fetch origin
      git checkout main
      git pull origin main
      composer install --no-dev --optimize-autoloader
      wp cache flush
      DEPLOY
  environment:
    name: production
  only:
    - main
  when: manual
```

---

## 14.6 Automated Testing in Pipeline

### 14.6.1 PHPUnit Configuration

```xml
<!-- phpunit.xml -->

<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/9.5/phpunit.xsd"
         bootstrap="tests/bootstrap.php"
         colors="true"
         verbose="true"
         failOnRisky="true"
         failOnWarning="true">
    <testsuites>
        <testsuite name="Unit Tests">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Integration Tests">
            <directory>tests/Integration</directory>
        </testsuite>
        <testsuite name="WordPress Tests">
            <directory>tests/WordPress</directory>
        </testsuite>
    </testsuites>

    <coverage processUncoveredFiles="true">
        <include>
            <directory suffix=".php">app</directory>
        </include>
        <exclude>
            <directory>app/Resources</directory>
            <directory>app/Migrations</directory>
        </exclude>
        <report>
            <html outputDirectory="coverage/html"/>
            <xml outputDirectory="coverage/xml"/>
            <text outputFile="php://stdout" showUncoveredFiles="true"/>
        </report>
    </coverage>

    <php>
        <ini name="display_errors" value="1"/>
        <ini name="error_reporting" value="-1"/>
        <const name="PHPUNIT_TESTSUITE" value="true"/>
        <env name="APP_ENV" value="test"/>
        <env name="DB_HOST" value="localhost"/>
        <env name="DB_NAME" value="wordpress_test"/>
        <env name="DB_USER" value="root"/>
        <env name="DB_PASSWORD" value="root"/>
    </php>
</phpunit>
```

### 14.6.2 Exemplo de Testes Unitários

```php
<?php
// tests/Unit/PaymentProcessorTest.php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\PaymentProcessor;
use App\Exceptions\PaymentException;

class PaymentProcessorTest extends TestCase
{
    private PaymentProcessor $processor;

    protected function setUp(): void
    {
        $this->processor = new PaymentProcessor();
    }

    public function testProcessPaymentSuccess(): void
    {
        $result = $this->processor->process([
            'amount' => 100.00,
            'currency' => 'BRL',
            'method' => 'credit_card',
        ]);

        $this->assertTrue($result['success']);
        $this->assertIsString($result['transaction_id']);
    }

    public function testProcessPaymentFailsWithInvalidAmount(): void
    {
        $this->expectException(PaymentException::class);
        
        $this->processor->process([
            'amount' => -100.00,
            'currency' => 'BRL',
            'method' => 'credit_card',
        ]);
    }

    public function testProcessPaymentWithDifferentCurrencies(): void
    {
        $currencies = ['BRL', 'USD', 'EUR'];
        
        foreach ($currencies as $currency) {
            $result = $this->processor->process([
                'amount' => 50.00,
                'currency' => $currency,
                'method' => 'credit_card',
            ]);
            
            $this->assertTrue($result['success']);
        }
    }
}
```

### 14.6.3 Testes de Integração WordPress

```php
<?php
// tests/Integration/WordPressIntegrationTest.php

namespace Tests\Integration;

use WP_UnitTestCase;

class WordPressIntegrationTest extends WP_UnitTestCase
{
    public function testWordPressInitialization(): void
    {
        $this->assertTrue(function_exists('get_bloginfo'));
        $this->assertTrue(function_exists('get_post'));
    }

    public function testCreatePost(): void
    {
        $post_id = wp_insert_post([
            'post_title' => 'Test Post',
            'post_content' => 'Test content',
            'post_type' => 'post',
            'post_status' => 'publish',
        ]);

        $this->assertIsInt($post_id);
        
        $post = get_post($post_id);
        $this->assertEquals('Test Post', $post->post_title);
    }

    public function testCustomPostTypeFunctionality(): void
    {
        register_post_type('book', [
            'public' => true,
            'supports' => ['title', 'editor'],
        ]);

        $book_id = wp_insert_post([
            'post_type' => 'book',
            'post_title' => 'Laravel Best Practices',
        ]);

        $post = get_post($book_id);
        $this->assertEquals('book', $post->post_type);
    }

    public function testPluginActivation(): void
    {
        activate_plugin('our-plugin/our-plugin.php');
        
        $this->assertTrue(is_plugin_active('our-plugin/our-plugin.php'));
    }
}
```

### 14.6.4 Code Quality Checks com PHPStan

```neon
# phpstan.neon

parameters:
    level: 9
    paths:
        - app
        - tests
    excludePaths:
        - app/Resources
        - app/Migrations
    
    ignoreErrors:
        - '#Call to function get_the_title with 0 parameters#'
    
    reportUnmatchedIgnoredErrors: false
    
    typeAliases:
        WpPost: 'WP_Post|null'
        WpError: 'WP_Error'

rules:
    - PHPStan\Rules\Strings\InvalidStringOffsetAccess
    - PHPStan\Rules\Strings\OffsetAccessOnString
    - PHPStan\Rules\Properties\AccessStaticPropertiesInheritanceRule
```

### 14.6.5 Security Scanning com Psalm

```xml
<!-- psalm.xml -->

<?xml version="1.0"?>
<psalm
    xmlns="https://getpsalm.org"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="https://getpsalm.org https://raw.githubusercontent.com/vimeo/psalm/master/config.xsd"
    errorLevel="3"
    findUnusedCode="true"
    findUnusedVariables="true"
    resolveFromConfigFile="true">
    
    <projectFiles>
        <directory name="app"/>
        <ignoreFiles>
            <directory name="vendor"/>
            <directory name="tests"/>
        </ignoreFiles>
    </projectFiles>

    <plugins>
        <pluginClass class="Psalm\PhpVersionProvider"/>
    </plugins>

    <issueHandlers>
        <MissingReturnType errorLevel="error"/>
        <MissingParamType errorLevel="error"/>
        <TooFewArguments errorLevel="error"/>
    </issueHandlers>
</psalm>
```

---

## 14.7 Automated Deployment

### 14.7.1 Deploy Script Completo

```bash
#!/bin/bash

# deploy.sh
# Script de deployment automático para production

set -euo pipefail

# Configurações
DEPLOY_USER="wordpress"
DEPLOY_HOST="${DEPLOY_HOST:-prod-server.com}"
DEPLOY_PATH="/var/www/wordpress"
REPOSITORY="https://github.com/seu-usuario/seu-repo.git"
BRANCH="${1:-main}"

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

log_info() {
    echo -e "${GREEN}ℹ ${1}${NC}"
}

log_warn() {
    echo -e "${YELLOW}⚠ ${1}${NC}"
}

log_error() {
    echo -e "${RED}✗ ${1}${NC}"
}

log_success() {
    echo -e "${GREEN}✓ ${1}${NC}"
}

# Validações
if [ -z "${DEPLOY_HOST}" ]; then
    log_error "DEPLOY_HOST não configurado"
    exit 1
fi

log_info "Iniciando deployment para ${BRANCH} em ${DEPLOY_HOST}..."

# SSH into server and deploy
ssh "${DEPLOY_USER}@${DEPLOY_HOST}" << EOFSH || exit 1
    set -euo pipefail
    
    # Variáveis
    DEPLOY_PATH="${DEPLOY_PATH}"
    BACKUP_PATH="/var/backups"
    BRANCH="${BRANCH}"
    TIMESTAMP=\$(date +%Y%m%d_%H%M%S)
    
    cd "\${DEPLOY_PATH}"
    
    # 1. Backup completo
    log_info "Criando backup..."
    tar -czf "\${BACKUP_PATH}/wordpress_\${TIMESTAMP}.tar.gz" . || true
    
    # 2. Backup database
    log_info "Fazendo backup do banco..."
    mysqldump -u wordpress_prod -p${PROD_DB_PASSWORD} wordpress_prod | \
        gzip > "\${BACKUP_PATH}/database_\${TIMESTAMP}.sql.gz"
    
    # 3. Atualizar código
    log_info "Atualizando código..."
    git fetch origin
    git checkout \${BRANCH}
    git pull origin \${BRANCH}
    
    # 4. Instalar dependências
    log_info "Instalando dependências..."
    composer install --no-dev --optimize-autoloader --no-interaction
    
    # 5. Database migrations
    log_info "Rodando migrations..."
    wp db optimize --allow-root
    wp db repair --allow-root || true
    
    # 6. Limpar cache
    log_info "Limpando cache..."
    wp cache flush --allow-root
    redis-cli FLUSHALL || true
    
    # 7. Atualizar plugins/temas (opcional)
    log_info "Atualizando plugins..."
    wp plugin update --all --allow-root || true
    wp theme update --all --allow-root || true
    
    # 8. Permissões
    log_info "Corrigindo permissões..."
    sudo chown -R www-data:www-data "\${DEPLOY_PATH}"
    sudo chmod -R 755 "\${DEPLOY_PATH}"
    sudo chmod 600 "\${DEPLOY_PATH}/wp-config.php"
    
    # 9. Reload PHP-FPM
    log_info "Recarregando PHP-FPM..."
    sudo systemctl reload php8.2-fpm
    
    # 10. Verificar saúde
    log_info "Verificando saúde da aplicação..."
    if curl -sf https://example.com/health > /dev/null; then
        log_success "Deployment concluído com sucesso!"
    else
        log_error "Health check falhou!"
        exit 1
    fi

EOFSH

log_success "✓ Deployment finalizado!"
```

### 14.7.2 Database Migrations com WP-CLI

```bash
#!/bin/bash

# run-migrations.sh

set -e

log() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $1"
}

log "Iniciando database migrations..."

# Backup antes de migrar
log "Backup do database..."
wp db export /var/backups/pre-migration_$(date +%s).sql

# Exemplo de migração custom
log "Executando migrations customizadas..."

# Deletar opções antigas
wp option delete old_option_name || true

# Atualizar posts
wp post list --post_type=custom_post --format=ids | while read post_id; do
    wp post meta set "$post_id" new_meta_key "new_value"
done

# Limpar postmeta órfã
wp db query "DELETE FROM wp_postmeta WHERE post_id NOT IN (SELECT ID FROM wp_posts);"

log "Migrations concluídas!"
```

### 14.7.3 Rollback Strategy

```bash
#!/bin/bash

# rollback.sh
# Script para reverter para versão anterior

set -e

BACKUP_FILE="${1}"
DEPLOY_PATH="/var/www/wordpress"
BACKUP_PATH="/var/backups"

if [ -z "$BACKUP_FILE" ]; then
    echo "Backups disponíveis:"
    ls -lh $BACKUP_PATH/wordpress_*.tar.gz
    echo ""
    echo "Uso: ./rollback.sh <backup-file>"
    exit 1
fi

if [ ! -f "$BACKUP_PATH/$BACKUP_FILE" ]; then
    echo "❌ Arquivo de backup não encontrado: $BACKUP_FILE"
    exit 1
fi

echo "⚠️  Você está prestes a reverter para: $BACKUP_FILE"
echo "Pressione ENTER para confirmar (Ctrl+C para cancelar)..."
read

echo "🔄 Iniciando rollback..."

# Backup da versão atual antes de reverter
CURRENT_TIMESTAMP=$(date +%Y%m%d_%H%M%S)
tar -czf "$BACKUP_PATH/pre-rollback_${CURRENT_TIMESTAMP}.tar.gz" "$DEPLOY_PATH"

# Restaurar from backup
cd "$DEPLOY_PATH"
tar -xzf "$BACKUP_PATH/$BACKUP_FILE"

# Restaurar database
DB_BACKUP=$(echo "$BACKUP_FILE" | sed 's/wordpress_/database_/' | sed 's/.tar.gz/.sql.gz/')
if [ -f "$BACKUP_PATH/$DB_BACKUP" ]; then
    echo "Restaurando database..."
    gunzip < "$BACKUP_PATH/$DB_BACKUP" | mysql -u wordpress_prod -p
fi

# Reload
sudo systemctl reload php8.2-fpm
wp cache flush

echo "✅ Rollback concluído!"
```

---

## 14.8 Monitoring e Logging

### 14.8.1 Sentry Integration

```php
<?php
// wp-content/mu-plugins/sentry-config.php

if (function_exists('sentry_sdk')) {
    \Sentry\init([
        'dsn' => getenv('SENTRY_DSN'),
        'environment' => WP_ENV,
        'release' => getenv('APP_VERSION'),
        'tracesSampleRate' => WP_ENV === 'production' ? 0.1 : 1.0,
        'attach_stacktrace' => true,
        'error_types' => E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED,
    ]);

    // Breadcrumbs
    add_action('init', function () {
        \Sentry\captureMessage('WordPress initialized', 'info');
    });

    // Monitorar erros
    set_error_handler(function ($errno, $errstr, $errfile, $errline) {
        \Sentry\captureException(new ErrorException($errstr, 0, $errno, $errfile, $errline));
        return false;
    });

    // Monitorar exceções não capturadas
    set_exception_handler(function ($exception) {
        \Sentry\captureException($exception);
    });
}
```

### 14.8.2 Application Logging

```php
<?php
// app/Services/Logger.php

namespace App\Services;

use Monolog\Logger;
use Monolog\Handlers\StreamHandler;
use Monolog\Handlers\SyslogHandler;
use Monolog\Formatters\JsonFormatter;

class Logger
{
    private static Logger $logger;

    public static function init(): Logger
    {
        self::$logger = new Logger('wordpress');

        // File handler
        $fileHandler = new StreamHandler(WP_CONTENT_DIR . '/logs/app.log');
        $fileHandler->setFormatter(new JsonFormatter());
        self::$logger->pushHandler($fileHandler);

        // Syslog handler for production
        if (defined('WP_ENV') && WP_ENV === 'production') {
            $syslogHandler = new SyslogHandler('wordpress');
            self::$logger->pushHandler($syslogHandler);
        }

        return self::$logger;
    }

    public static function get(): Logger
    {
        return self::$logger ?? self::init();
    }

    public static function info(string $message, array $context = []): void
    {
        self::get()->info($message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::get()->error($message, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        self::get()->warning($message, $context);
    }

    public static function debug(string $message, array $context = []): void
    {
        self::get()->debug($message, $context);
    }
}
```

### 14.8.3 Performance Monitoring

```php
<?php
// wp-content/mu-plugins/performance-monitoring.php

/**
 * Monitorar Performance
 */

function monitor_performance() {
    if (!defined('WP_DEBUG') || !WP_DEBUG) {
        return;
    }

    $start = microtime(true);
    
    add_action('shutdown', function () use ($start) {
        $elapsed = microtime(true) - $start;
        
        $stats = [
            'time' => $elapsed,
            'memory' => memory_get_peak_usage(true) / 1024 / 1024,
            'queries' => get_num_queries(),
        ];

        error_log(json_encode([
            'type' => 'performance',
            'page' => $_SERVER['REQUEST_URI'] ?? '',
            'method' => $_SERVER['REQUEST_METHOD'] ?? '',
            'stats' => $stats,
            'timestamp' => date('Y-m-d H:i:s'),
        ]));

        // Alertar se performance é ruim
        if ($elapsed > 1.0) {
            error_log("SLOW REQUEST: {$_SERVER['REQUEST_URI']} took {$elapsed}s");
        }

        if ($stats['memory'] > 128) {
            error_log("HIGH MEMORY: {$stats['memory']}MB for {$_SERVER['REQUEST_URI']}");
        }
    });
}

monitor_performance();
```

### 14.8.4 Uptime Monitoring

```bash
#!/bin/bash

# monitoring/health-check.sh

SITE_URL="https://example.com"
SLACK_WEBHOOK="${SLACK_WEBHOOK}"

check_health() {
    # Verificar status HTTP
    HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$SITE_URL")
    
    if [ "$HTTP_CODE" != "200" ]; then
        # Notificar
        curl -X POST "$SLACK_WEBHOOK" \
            -H 'Content-Type: application/json' \
            -d "{
                \"text\": \"⚠️ Site health check failed\",
                \"blocks\": [
                    {
                        \"type\": \"section\",
                        \"text\": {
                            \"type\": \"mrkdwn\",
                            \"text\": \"*Health Check Failed*\nURL: $SITE_URL\nStatus: $HTTP_CODE\nTime: $(date)\"
                        }
                    }
                ]
            }"
        
        return 1
    fi

    # Verificar database
    if ! wp db query "SELECT 1" &> /dev/null; then
        curl -X POST "$SLACK_WEBHOOK" \
            -H 'Content-Type: application/json' \
            -d "{
                \"text\": \"🚨 Database connection failed\"
            }"
        return 1
    fi

    # Verificar Redis
    if ! redis-cli ping &> /dev/null; then
        echo "Warning: Redis unavailable"
    fi

    return 0
}

check_health
exit $?
```

### 14.8.5 Crontab para Monitoring

```bash
# /etc/cron.d/wordpress-monitoring

# Executar health check a cada 5 minutos
*/5 * * * * root /var/scripts/health-check.sh

# Limpeza de logs antigos diariamente
0 3 * * * root find /var/log/wordpress -type f -mtime +30 -delete

# Backup incremental diariamente
0 2 * * * root /var/scripts/backup.sh

# Verificar espaço em disco
0 * * * * root /var/scripts/disk-check.sh

# Limpeza de cache Redis
0 4 * * * root redis-cli --rdb /var/backups/redis_backup.rdb
```

---

## 14.9 Backup Strategy

### 14.9.1 Backup Script Completo

```bash
#!/bin/bash

# backup.sh
# Script de backup completo de WordPress

set -euo pipefail

# Configurações
BACKUP_BASE_DIR="/var/backups/wordpress"
BACKUP_DIR="${BACKUP_BASE_DIR}/$(date +%Y-%m-%d)"
RETENTION_DAYS=30

# Database
DB_USER="wordpress_prod"
DB_NAME="wordpress_prod"
DB_HOST="localhost"

# Files
WP_PATH="/var/www/wordpress"
WP_CONTENT="${WP_PATH}/wp-content"

# Remote backup (opcional)
REMOTE_BACKUP_ENABLED=false
REMOTE_BACKUP_HOST="${REMOTE_BACKUP_HOST:-}"
REMOTE_BACKUP_PATH="${REMOTE_BACKUP_PATH:-}"

# Criar diretório de backup
mkdir -p "$BACKUP_DIR"

log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1"
}

log "Iniciando backup completo..."

# 1. Backup do Database
log "Backup do database..."
mysqldump \
    --single-transaction \
    --skip-lock-tables \
    --user="$DB_USER" \
    "$DB_NAME" | \
    gzip > "$BACKUP_DIR/database.sql.gz"

log "Database backup: $(du -h $BACKUP_DIR/database.sql.gz | cut -f1)"

# 2. Backup Incremental de Uploads
log "Backup incremental de uploads..."
rsync -av \
    --delete \
    --backup-dir="${BACKUP_DIR}/uploads-backup-$(date +%s)" \
    "${WP_CONTENT}/uploads/" \
    "${BACKUP_DIR}/uploads/" || true

# 3. Backup dos Plugins
log "Backup dos plugins..."
tar -czf "$BACKUP_DIR/plugins.tar.gz" \
    -C "$WP_CONTENT" plugins/

# 4. Backup dos Temas
log "Backup dos temas..."
tar -czf "$BACKUP_DIR/themes.tar.gz" \
    -C "$WP_CONTENT" themes/

# 5. Backup de Configurações
log "Backup de configurações..."
tar -czf "$BACKUP_DIR/config.tar.gz" \
    -C "$WP_PATH" wp-config.php .htaccess || true

# 6. Backup completo (comprimido)
log "Criando backup full..."
tar -czf "$BACKUP_DIR/full-backup.tar.gz" \
    --exclude="wp-content/uploads" \
    -C "$WP_PATH" . || true

log "Full backup: $(du -h $BACKUP_DIR/full-backup.tar.gz | cut -f1)"

# 7. Backup remoto (usando SFTP/S3/etc)
if [ "$REMOTE_BACKUP_ENABLED" = true ]; then
    log "Sincronizando com backup remoto..."
    
    # Exemplo com rsync
    if [ -n "$REMOTE_BACKUP_HOST" ]; then
        rsync -avz \
            --delete \
            "$BACKUP_DIR/" \
            "${REMOTE_BACKUP_HOST}:${REMOTE_BACKUP_PATH}/$(date +%Y-%m-%d)/" || true
    fi
    
    # Exemplo com AWS S3
    # aws s3 sync "$BACKUP_DIR" "s3://seu-bucket/wordpress/$(date +%Y-%m-%d)" || true
fi

# 8. Verificação de integridade
log "Verificando integridade..."
if [ -f "$BACKUP_DIR/database.sql.gz" ]; then
    if gunzip -t "$BACKUP_DIR/database.sql.gz" &> /dev/null; then
        log "✓ Database backup verificado"
    else
        log "✗ Database backup corrompido!"
        exit 1
    fi
fi

# 9. Limpar backups antigos
log "Limpando backups antigos (> ${RETENTION_DAYS} dias)..."
find "$BACKUP_BASE_DIR" -type d -mtime +${RETENTION_DAYS} -exec rm -rf {} + 2>/dev/null || true

# 10. Registrar no banco
log "Registrando backup..."
cat >> "${BACKUP_BASE_DIR}/backup.log" << EOF
Backup: $(date '+%Y-%m-%d %H:%M:%S')
Size: $(du -sh "$BACKUP_DIR" | cut -f1)
Status: SUCCESS
EOF

log "✓ Backup concluído com sucesso!"
log "  Localização: $BACKUP_DIR"
log "  Tamanho: $(du -sh $BACKUP_DIR | cut -f1)"
```

### 14.9.2 Backup Strategies com Diferentes Frequencies

```bash
#!/bin/bash

# Backup strategy com diferentes frequências

# Daily backup (completo)
0 1 * * * root /var/scripts/backup/daily-backup.sh

# Weekly full backup (archive)
0 0 * * 0 root /var/scripts/backup/weekly-backup.sh

# Monthly backup (para arquivamento longo prazo)
0 0 1 * * root /var/scripts/backup/monthly-backup.sh

# Incremental backup (pequeno, rápido)
*/6 * * * * root /var/scripts/backup/incremental-backup.sh
```

### 14.9.3 Restore Testing

```bash
#!/bin/bash

# restore-test.sh
# Testar restore em ambiente staging

set -e

BACKUP_FILE="${1}"
TEST_DB="wordpress_restore_test"
TEST_PATH="/tmp/wordpress_restore_test"

log() {
    echo "[$(date '+%H:%M:%S')] $1"
}

if [ -z "$BACKUP_FILE" ]; then
    echo "Uso: ./restore-test.sh <backup-file.tar.gz>"
    exit 1
fi

log "Iniciando teste de restore..."

# 1. Criar test database
log "Criando database de teste..."
mysql -e "DROP DATABASE IF EXISTS $TEST_DB;"
mysql -e "CREATE DATABASE $TEST_DB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 2. Restaurar arquivos
log "Extraindo arquivos..."
mkdir -p "$TEST_PATH"
tar -xzf "/var/backups/wordpress/$BACKUP_FILE" -C "$TEST_PATH"

# 3. Restaurar database
log "Restaurando database..."
gunzip < "/var/backups/wordpress/database.sql.gz" | \
    mysql "$TEST_DB"

# 4. Testes de integridade
log "Executando testes..."

# Verificar estrutura de diretórios
if [ ! -d "$TEST_PATH/wp-content" ]; then
    log "✗ wp-content não encontrado!"
    exit 1
fi

# Verificar database
TABLE_COUNT=$(mysql "$TEST_DB" -e "SHOW TABLES;" | wc -l)
if [ "$TABLE_COUNT" -lt 10 ]; then
    log "✗ Database corrupta!"
    exit 1
fi

# Verificar se WP pode ser inicializado
cd "$TEST_PATH"
if ! wp core is-installed --path="$TEST_PATH"; then
    log "⚠️  WordPress não foi instalado corretamente"
fi

log "✓ Teste de restore completado com sucesso!"

# 5. Limpeza
log "Limpando..."
rm -rf "$TEST_PATH"
mysql -e "DROP DATABASE $TEST_DB;"
```

---

## 14.10 Disaster Recovery

### 14.10.1 RTO/RPO Targets

```markdown
# Disaster Recovery Plan

## RTO (Recovery Time Objective) - Tempo para recuperação

- **Critical Systems**: < 1 hora
  - Database
  - Web server
  - Email

- **Important Services**: < 4 horas
  - Backup systems
  - Cache layer
  - CDN

- **Non-critical**: < 24 horas
  - Development environment
  - Testing servers

## RPO (Recovery Point Objective) - Perda máxima aceitável de dados

- **Database**: 1 hora (backups de hora em hora)
- **File uploads**: 6 horas (backups a cada 6h)
- **Configurations**: 1 dia (backups diários)
- **Logs**: 7 dias (retenção)
```

### 14.10.2 Recovery Procedures

```bash
#!/bin/bash

# recovery.sh
# Procedimento de recuperação de desastre

set -e

BACKUP_ID="${1:-latest}"
RECOVERY_LOG="/var/log/recovery_$(date +%s).log"

exec &> >(tee "$RECOVERY_LOG")

log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1"
}

alert() {
    # Notificar equipe
    curl -X POST "$SLACK_WEBHOOK" \
        -H 'Content-Type: application/json' \
        -d "{\"text\": \"🚨 Disaster Recovery initiated: $1\"}"
}

# ====== FASE 1: Assessment ======

log "FASE 1: Avaliação da situação"
alert "Disaster Recovery iniciado - Avaliação"

# Verificar status do servidor
log "Verificando status dos serviços..."
systemctl status nginx || true
systemctl status php8.2-fpm || true
systemctl status mysql || true

# ====== FASE 2: Preparartion ======

log "FASE 2: Preparação"

# Parar serviços que podem estar corrompidos
log "Parando serviços..."
sudo systemctl stop nginx
sudo systemctl stop php8.2-fpm

# Verificar espaço em disco
DISK_USAGE=$(df /var/www | tail -1 | awk '{print $5}' | sed 's/%//')
if [ "$DISK_USAGE" -gt 90 ]; then
    log "Espaço em disco crítico ($DISK_USAGE%)"
    # Limpar arquivos temporários
    rm -rf /var/www/wordpress/wp-content/cache/*
    rm -rf /tmp/*
fi

# ====== FASE 3: Restore ======

log "FASE 3: Restauração de dados"

# Determinar backup
if [ "$BACKUP_ID" = "latest" ]; then
    BACKUP_FILE=$(ls -t /var/backups/wordpress/*.tar.gz | head -1)
else
    BACKUP_FILE="/var/backups/wordpress/$BACKUP_ID"
fi

if [ ! -f "$BACKUP_FILE" ]; then
    log "❌ Arquivo de backup não encontrado!"
    alert "Recovery FAILED - Backup file not found"
    exit 1
fi

log "Usando backup: $BACKUP_FILE"

# Restore files
log "Restaurando arquivos..."
rm -rf /var/www/wordpress/*
tar -xzf "$BACKUP_FILE" -C /var/www/wordpress

# Restore database
log "Restaurando database..."
if [ -f "$(dirname $BACKUP_FILE)/database.sql.gz" ]; then
    # Backup database atual primeiro
    mysqldump wordpress_prod | gzip > /var/backups/corrupted_$(date +%s).sql.gz || true
    
    # Restaurar
    gunzip < "$(dirname $BACKUP_FILE)/database.sql.gz" | mysql wordpress_prod
fi

# ====== FASE 4: Verification ======

log "FASE 4: Verificação"

# Verificar database
log "Verificando database..."
if ! mysql wordpress_prod -e "SELECT 1;" &> /dev/null; then
    log "❌ Database verification failed!"
    alert "Recovery FAILED - Database verification"
    exit 1
fi

# Verificar WordPress installation
log "Verificando WordPress..."
if ! wp core is-installed; then
    log "❌ WordPress não está instalado corretamente!"
    alert "Recovery FAILED - WordPress not installed"
    exit 1
fi

# ====== FASE 5: Resumption ======

log "FASE 5: Retomada de operações"

# Corrigir permissões
log "Corrigindo permissões..."
sudo chown -R www-data:www-data /var/www/wordpress
sudo chmod -R 755 /var/www/wordpress

# Iniciar serviços
log "Iniciando serviços..."
sudo systemctl start php8.2-fpm
sudo systemctl start nginx
sudo systemctl start mysql

# Esperar serviços estarem prontos
sleep 5

# Health check
log "Verificando saúde da aplicação..."
if curl -f https://example.com/health > /dev/null; then
    log "✓ Aplicação online!"
    alert "Recovery SUCCESS - Sistema restaurado"
else
    log "⚠️  Health check falhou"
    alert "Recovery WARNING - Health check failed"
fi

log "Recovery completo!"
```

### 14.10.3 Documentation Template

```markdown
# Disaster Recovery - Documentação

## Informações Críticas

- **Website**: example.com
- **Admin Contact**: admin@example.com
- **Technical Lead**: tech-lead@example.com
- **Backup Location**: /var/backups/wordpress
- **Recovery Time Objective (RTO)**: 1 hora
- **Recovery Point Objective (RPO)**: 1 hora

## Localizações de Backups

| Type | Location | Frequency | Retention |
|------|----------|-----------|-----------|
| Database | /var/backups/wordpress | Hourly | 30 dias |
| Files | /var/backups/wordpress | Daily | 30 dias |
| Remote | S3 (AWS) | Daily | 90 dias |
| Archive | External HDD | Monthly | 1 ano |

## Contatos de Emergência

- **Database Admin**: dba@example.com
- **Infrastructure**: infra@example.com
- **On-call**: +55 11 XXXX-XXXX

## Recovery Checklists

### Database Recovery
- [ ] Localizar arquivo de backup
- [ ] Parar aplicação
- [ ] Restore database
- [ ] Verificar integridade
- [ ] Reiniciar aplicação

### Full System Recovery
- [ ] Provisionar novo servidor
- [ ] Instalar dependências
- [ ] Restaurar arquivos
- [ ] Restaurar database
- [ ] Testar conectividade
- [ ] DNS atualizar (se necessário)

### Test Procedure
- Executar mensal
- Usar ambiente staging
- Documentar tempo de recuperação
- Atualizar runbooks

## Scripts Críticos

- `backup.sh` - Backup automático
- `recovery.sh` - Procedimento de recovery
- `restore-test.sh` - Testar restore
- `health-check.sh` - Verificar saúde

## Lições Aprendidas

[Registrar descobertas de cada incident]
```

### 14.10.4 Testing Disaster Recovery

```bash
#!/bin/bash

# test-disaster-recovery.sh
# Teste completo do procedimento de DR

set -e

TEST_DATE=$(date +%Y-%m-%d_%H-%M-%S)
TEST_LOG="/var/log/dr-test-${TEST_DATE}.log"

exec &> >(tee "$TEST_LOG")

echo "═══════════════════════════════════════"
echo "  Disaster Recovery Test - ${TEST_DATE}"
echo "═══════════════════════════════════════"

# 1. Backup test database
echo "1. Criando cópia de teste do banco..."
TEST_DB="wordpress_dr_test_${RANDOM}"
mysql -e "CREATE DATABASE $TEST_DB LIKE wordpress_prod;"
mysql -e "INSERT INTO ${TEST_DB}.* SELECT * FROM wordpress_prod.*;"

# 2. Simulate corruption
echo "2. Simulando corrupção de dados..."
mysql "$TEST_DB" -e "DROP TABLE wp_posts;"

# 3. Test recovery
echo "3. Testando recovery..."
BACKUP_FILE=$(ls -t /var/backups/wordpress/database.sql.gz | head -1)
gunzip < "$BACKUP_FILE" | mysql "$TEST_DB"

# 4. Verify
echo "4. Verificando..."
if mysql "$TEST_DB" -e "SELECT COUNT(*) FROM wp_posts" &> /dev/null; then
    POSTS=$(mysql "$TEST_DB" -e "SELECT COUNT(*) FROM wp_posts;" 2>/dev/null)
    echo "✓ Recovery test passed! Found $POSTS posts"
else
    echo "✗ Recovery test FAILED!"
    exit 1
fi

# 5. Cleanup
echo "5. Limpando..."
mysql -e "DROP DATABASE $TEST_DB;"

# 6. Generate Report
echo ""
echo "═══════════════════════════════════════"
echo "  Test Complete"
echo "═══════════════════════════════════════"
echo "Log: $TEST_LOG"
echo ""
```

---

## Resumo - Checklist Fase 14

```markdown
# ✅ Checklist Fase 14 - Deployment e DevOps

## 14.1 - Development Environment
- [ ] Docker Compose configurado
- [ ] PHP-FPM rodando
- [ ] Nginx configurado
- [ ] Redis funcionando
- [ ] WP-CLI disponível
- [ ] Mailhog para testes

## 14.2 - Staging Environment
- [ ] Docker Compose para staging
- [ ] Scripts de sync de database
- [ ] Scripts de sync de assets
- [ ] Testes automatizados em staging
- [ ] Ambiente espelhando production

## 14.3 - Production Environment
- [ ] Servidor configurado
- [ ] PHP otimizado
- [ ] Nginx hardened
- [ ] MySQL otimizado
- [ ] SSL/TLS configurado
- [ ] Redis rodando

## 14.4 - Version Control
- [ ] .gitignore completo
- [ ] Conventional commits
- [ ] Git Flow implementado
- [ ] Pre-commit hooks
- [ ] Branch strategy

## 14.5 - CI/CD Pipeline
- [ ] GitHub Actions / GitLab CI configurado
- [ ] Tests automatizados
- [ ] Security scans
- [ ] Docker build
- [ ] Deploy automático

## 14.6 - Automated Testing
- [ ] PHPUnit configurado
- [ ] Tests unitários
- [ ] Tests de integração
- [ ] Code coverage
- [ ] PHPStan / Psalm

## 14.7 - Automated Deployment
- [ ] Deploy script funcional
- [ ] Database migrations
- [ ] Rollback strategy
- [ ] Health checks

## 14.8 - Monitoring & Logging
- [ ] Sentry configurado
- [ ] Application logs
- [ ] Performance monitoring
- [ ] Uptime monitoring
- [ ] Alertas configurados

## 14.9 - Backup Strategy
- [ ] Backup script
- [ ] Backups automáticos
- [ ] Backup remoto
- [ ] Retenção de dados
- [ ] Restore testing

## 14.10 - Disaster Recovery
- [ ] RTO/RPO definidos
- [ ] Recovery procedures
- [ ] Documentation
- [ ] Testing program
- [ ] Equipe treinada
```

---

## Próximos Passos

Após completar esta Fase 14:

1. **Implementar Monitoramento Avançado**
   - APM (Application Performance Monitoring)
   - Distributed tracing
   - Custom metrics

2. **Otimizações Adicionais**
   - Database query optimization
   - Caching strategies
   - CDN integration

3. **Security Hardening**
   - WAF (Web Application Firewall)
   - DDoS protection
   - Penetration testing

4. **Documentation**
   - Runbooks
   - Troubleshooting guides
   - Architecture diagrams

---

## Referências Úteis

- [WordPress DevOps Best Practices](https://developer.wordpress.org/)
- [Docker for WordPress](https://docs.docker.com/)
- [GitHub Actions Documentation](https://docs.github.com/en/actions)
- [Sentry Documentation](https://docs.sentry.io/)
- [Laravel Deployment Guide](https://laravel.com/docs/deployment)

