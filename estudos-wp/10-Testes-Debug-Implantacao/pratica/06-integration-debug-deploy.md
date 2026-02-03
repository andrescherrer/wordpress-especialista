# Integração, Query Monitor, Sentry e Deploy

Referência. Fonte: **010-WordPress-Fase-10-Testes-Debug-Implantacao.md**.

---

## Testes de integração (resumo)

- Usam WordPress real: `wp_insert_user`, `get_user_by`, `$wpdb`, etc.
- **Bootstrap:** carregar `wp-load.php` no `tests/bootstrap.php`.
- **setUp:** limpar dados (ex.: deletar usuários de teste) para não poluir.
- **assertNotWPError( $id )** para checar que `wp_insert_user` não retornou WP_Error.
- Opcional: usar **WP_UnitTestCase** do pacote de testes do Core (wordpress-develop).

---

## Query Monitor

- Plugin: [Query Monitor](https://wordpress.org/plugins/query-monitor/). Em desenvolvimento, ative e use a barra de debug.
- **Enviar mensagem para a barra:** `do_action( 'qm/debug', $mensagem );`  
  `$mensagem` pode ser string ou array (ex.: tipo, query, tempo).
- Exemplo no código:

```php
do_action( 'qm/debug', [
    'funcao' => 'processa_dados',
    'tempo'  => microtime( true ) - $inicio,
] );
```

---

## Sentry

- **Instalar:** `composer require sentry/sentry`
- **Inicializar** (em plugin ou wp-config):

```php
\Sentry\init( [
    'dsn'                  => 'https://...@sentry.io/...',
    'environment'          => wp_get_environment_type(),
    'release'               => '1.0.0',
    'traces_sample_rate'    => 0.1,
] );
```

- **Capturar exceção:** `\Sentry\captureException( $e );`
- **Capturar mensagem:** `\Sentry\captureMessage( 'Texto', \Sentry\Level::Info );`
- **Handler global:** `set_exception_handler( function ( $e ) { \Sentry\captureException( $e ); ... } );`

---

## Deploy – checklist resumido

**Pré:** testes passando, backup DB e arquivos, versão/changelog atualizados.  
**Durante:** modo manutenção, deploy (rsync/git), `composer install --no-dev`, migrations, `wp cache flush`, `wp transient delete --all`.  
**Pós:** modo manutenção off, smoke test, verificar logs e métricas, monitorar.

---

## Script de deploy (resumo)

```bash
set -e
# 1) Testes locais
./vendor/bin/phpunit || exit 1
# 2) Backup remoto (ssh + mysqldump / tar)
# 3) Modo manutenção (touch .maintenance)
# 4) rsync ou git pull no servidor
# 5) ssh: composer install --no-dev, wp cache flush, migrations
# 6) Remover .maintenance
# 7) Smoke test / health check
```

---

## CI/CD (GitHub Actions) – resumo

- **Job test:** checkout, setup PHP (shivammathur/setup-php), MySQL service, `composer install`, `./vendor/bin/phpunit`, opcional phpcs e npm build.
- **Job deploy:** `needs: test`, só em branch main; usar secrets (DEPLOY_KEY, DEPLOY_HOST, DEPLOY_USER); ssh + git pull ou rsync; composer e cache flush no servidor.
- Coverage: `--coverage-clover coverage.xml` e codecov-action ou similar.
