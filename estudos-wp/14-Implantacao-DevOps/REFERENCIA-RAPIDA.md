# Referência rápida – Implantação e DevOps

Uma página. Use Ctrl+F. Fonte: **014-WordPress-Fase-14-Implantacao-DevOps.md**.

---

## Docker (desenvolvimento)

- **docker-compose.yml:** serviços db (MySQL), php (PHP-FPM), nginx, redis, wp-cli; volumes para código; networks.
- **healthcheck:** db e redis com `mysqladmin ping` / `redis-cli ping`; `depends_on: condition: service_healthy` no php.
- **Dockerfile PHP-FPM:** FROM php:8.2-fpm-alpine; extensões (pdo_mysql, gd, opcache, redis); Composer; usuário não-root.
- **.dockerignore:** node_modules, .git, vendor, .env, *.log para acelerar build e não vazar secrets.

---

## Secrets

- **Nunca** commitar senhas ou chaves; usar variáveis de ambiente ou arquivos fora do repo.
- **.env:** na raiz (ou onde o app carrega); .env.example versionado (placeholders); .env no .gitignore.
- **wp-config.php:** carregar com Dotenv (`Dotenv::createImmutable(__DIR__)->load()`); `define('DB_PASSWORD', $_ENV['DB_PASSWORD']);`; `$dotenv->required(['DB_NAME','DB_USER','DB_PASSWORD']);`.
- **CI/CD:** secrets no GitHub/GitLab (Settings → Secrets); usar `${{ secrets.PROD_DB_PASSWORD }}` no workflow; nunca echo de secrets em logs.

---

## Health checks

- **Docker:** `healthcheck:` com test (CMD mysqladmin ping / redis-cli ping), interval, timeout, retries.
- **Produção:** endpoint `/health` (PHP que retorna 200 + JSON com status); Nginx/LB checam esse endpoint; pipeline faz `curl -f URL/health` após deploy.
- **PHP-FPM:** script que checa se FPM responde ou se arquivo de “live” existe.

---

## Git e CI/CD

- **.gitignore:** wp-config.php, .env, wp-content/uploads, wp-content/plugins (exceto os que versiona), vendor, node_modules, *.log, backups, coverage.
- **Conventional Commits:** feat:, fix:, refactor:, docs:, ci:, chore:; scope opcional; subject imperativo, ~50 chars.
- **Branching:** main (produção), develop (staging), feature/*, release/*, hotfix/*; merge de develop → main com tag.
- **GitHub Actions:** jobs test (PHPUnit, PHPStan), security (composer audit, phpcs), build (Docker), deploy-staging (push develop), deploy-production (push main, environment: production); usar secrets para SSH e DB.

---

## Deploy

- **Script:** backup (tar do código, mysqldump) → git pull → composer install --no-dev --optimize-autoloader → wp db optimize → cache flush → permissões (www-data) → reload PHP-FPM → health check.
- **Migrations:** backup do DB antes; wp option delete, wp post meta, wp db query; ou scripts PHP versionados; registrar em log.
- **Rollback:** script que restaura tar + SQL do backup escolhido; sempre fazer backup da versão atual antes de rollback; testar restauração periodicamente.

---

## Backup

- **Completo:** mysqldump (--single-transaction, gzip) + tar de wp-content (plugins, themes) e wp-config; retenção (ex.: 30 dias) com find -mtime.
- **Remoto:** rsync ou S3/sync para outro servidor ou bucket; não depender só de backup local.
- **PITR (Point-in-Time Recovery):** habilitar binary log no MySQL (log-bin, binlog_format=ROW); backup full + --master-data=2; guardar posição do binlog; na restauração: aplicar full + binlogs até o horário desejado.
- **Restore testing:** rodar restore em ambiente de teste periodicamente; validar integridade (gunzip -t, import em DB de teste).

---

## Monitoring e DR

- **Sentry:** DSN por ambiente; release = APP_VERSION; tracesSampleRate menor em produção; set_error_handler/set_exception_handler para enviar exceções.
- **Logs:** aplicação em arquivo (ex.: wp-content/logs/app.log) com rotação; produção: Syslog ou serviço gerenciado; nunca logar senhas ou tokens.
- **Uptime:** cron ou serviço externo que chama /health; alerta se falhar.
- **RTO (Recovery Time Objective):** tempo máximo aceitável para voltar ao ar; define prioridade e procedimentos.
- **RPO (Recovery Point Objective):** perda máxima aceitável de dados; define frequência de backup e PITR.
- **Disaster recovery:** documentar procedimentos (restore DB, restore arquivos, DNS); manter backups em localização separada; treinar equipe e testar restore.
