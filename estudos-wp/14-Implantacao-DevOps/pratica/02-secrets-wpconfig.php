<?php
/**
 * REFERÊNCIA RÁPIDA – Secrets no wp-config (variáveis de ambiente)
 *
 * Nunca commitar credenciais. Usar .env (fora do Git) e Dotenv para carregar.
 * .env.example: versionado com placeholders; .env: local, no .gitignore.
 * required(): garante que variáveis obrigatórias existem antes de iniciar.
 *
 * Instalação: composer require vlucas/phpdotenv
 * Fonte: 014-WordPress-Fase-14-Implantacao-DevOps.md (14.1.3)
 */

// Carregar .env (na raiz do WordPress, um nível acima de wp-content)
$env_path = defined('ABSPATH') ? dirname(ABSPATH) : __DIR__;
if (file_exists($env_path . '/.env')) {
    if (class_exists('Dotenv\Dotenv')) {
        $dotenv = \Dotenv\Dotenv::createImmutable($env_path);
        $dotenv->load();
        $dotenv->required(['DB_NAME', 'DB_USER', 'DB_PASSWORD']);
    }
}

// Usar variáveis de ambiente (fallback para compatibilidade)
if (getenv('DB_NAME')) {
    define('DB_NAME', getenv('DB_NAME'));
}
if (getenv('DB_USER')) {
    define('DB_USER', getenv('DB_USER'));
}
if (getenv('DB_PASSWORD')) {
    define('DB_PASSWORD', getenv('DB_PASSWORD'));
}
if (getenv('DB_HOST')) {
    define('DB_HOST', getenv('DB_HOST'));
}

// Chaves e salts: preferir env
if (getenv('AUTH_KEY')) {
    define('AUTH_KEY', getenv('AUTH_KEY'));
}
if (getenv('SECURE_AUTH_KEY')) {
    define('SECURE_AUTH_KEY', getenv('SECURE_AUTH_KEY'));
}
// ... demais keys; ou usar wp_salt('auth') como fallback

// Ambiente e debug (nunca WP_DEBUG true em produção)
define('WP_ENV', getenv('WP_ENV') ?: 'production');
define('WP_DEBUG', filter_var(getenv('WP_DEBUG'), FILTER_VALIDATE_BOOLEAN));
if (WP_DEBUG && getenv('WP_DEBUG_LOG')) {
    define('WP_DEBUG_LOG', true);
}

// API keys de plugins (exemplo)
if (getenv('SENTRY_DSN')) {
    define('SENTRY_DSN', getenv('SENTRY_DSN'));
}
if (getenv('JWT_SECRET')) {
    define('JWT_SECRET', getenv('JWT_SECRET'));
}

/*
 * .env.example (versionado):
 *
 * DB_NAME=wordpress_db
 * DB_USER=wordpress_user
 * DB_PASSWORD=change_me
 * DB_HOST=localhost
 * WP_ENV=development
 * WP_DEBUG=true
 * WP_DEBUG_LOG=true
 * SENTRY_DSN=
 * JWT_SECRET=
 *
 * .env (não versionado): copiar de .env.example e preencher valores reais.
 */
