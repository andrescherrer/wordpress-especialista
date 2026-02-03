# Object cache persistente (Redis/Memcached)

Instalar extensão PHP (**redis** ou **memcached**); configurar **wp-config.php** (WP_REDIS_HOST, etc.); plugin **Redis Object Cache** ou equivalente; verificar que transients e wp_cache usam Redis.

---

## Extensão PHP

- **Redis:** `pecl install redis` ou via pacote do SO (ex.: `apt install php-redis`).
- **Memcached:** `php-memcached` (não confundir com php-memcache, versão antiga).
- Conferir no PHP: `php -m | grep redis` ou `php -m | grep memcached`.

---

## wp-config.php

### Redis (com plugin Redis Object Cache)

Antes de `require_once(ABSPATH . 'wp-settings.php');`:

```php
define('WP_REDIS_HOST', '127.0.0.1');
define('WP_REDIS_PORT', 6379);
define('WP_REDIS_PREFIX', 'wp_');
// define('WP_REDIS_PASSWORD', 'senha'); // se exigir auth
```

Ou via constante/ENV que o plugin Redis Object Cache reconheça (conferir documentação do plugin).

### Memcached

Depende do plugin ou do drop-in usado. Alguns usam `Memcached` com servidores definidos em código ou constante; não há padrão único no core. Ex.: definir lista de servidores em `object-cache.php` (drop-in).

---

## Plugin Redis Object Cache

- Instalar **Redis Object Cache** (ou similar) no WordPress.
- Ativar o plugin; em **Configurações** ou **Ferramentas** conectar ao Redis.
- Quando ativo, o WordPress usa Redis para **object cache** (wp_cache_*, transients que usam object cache); as chamadas a `wp_cache_get/set` e transients passam a ir ao Redis.

---

## Verificar

- No plugin: status “Connected” ou equivalente.
- Inserir transient e conferir no Redis (ex.: `redis-cli KEYS wp_*`) se as chaves aparecem.
- Reduzir carga no MySQL em páginas com muitas leituras de options/transients.

---

## Recursos

- Documentação do plugin Redis Object Cache; [Redis PHP extension](https://github.com/phpredis/phpredis).
- Ver **01-object-cache.php** (object cache em memória) e **14-load-balancer-sessoes.md**, **15-cdn-assets-paginas.md**; estudos-wp/14: **11-fila-externa-sqs-rabbitmq.md**, **12-banco-read-replica.md**, **13-escalabilidade-checklist.md**.
