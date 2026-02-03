# Deploy e rollback

Fonte: **014-WordPress-Fase-14-Implantacao-DevOps.md** (14.7).

---

## Ordem típica do script de deploy

1. **Backup** – Código (tar) e banco (mysqldump | gzip); guardar com timestamp.
2. **Atualizar código** – `git fetch` → `git checkout $BRANCH` → `git pull`.
3. **Dependências** – `composer install --no-dev --optimize-autoloader`.
4. **Migrations** – `wp db optimize`; scripts custom (option delete, post meta, etc.).
5. **Cache** – `wp cache flush`; `redis-cli FLUSHALL` se usar Redis.
6. **Permissões** – `chown -R www-data:www-data` no path; `chmod 600 wp-config.php`.
7. **Reload** – `systemctl reload php8.2-fpm` (ou equivalente).
8. **Health check** – `curl -sf https://example.com/health`; se falhar, considerar falha e preparar rollback.

Use `set -euo pipefail` no bash; nunca expor senhas em echo ou logs.

---

## Exemplo de trecho de deploy (SSH)

```bash
cd /var/www/wordpress
BACKUP_PATH="/var/backups"
TS=$(date +%Y%m%d_%H%M%S)

# Backup
tar -czf "$BACKUP_PATH/wordpress_${TS}.tar.gz" .
mysqldump -u wordpress_prod -p"$PROD_DB_PASSWORD" wordpress_prod | gzip > "$BACKUP_PATH/database_${TS}.sql.gz"

# Deploy
git fetch origin && git checkout main && git pull origin main
composer install --no-dev --optimize-autoloader --no-interaction
wp db optimize --allow-root
wp cache flush --allow-root
redis-cli FLUSHALL || true
sudo chown -R www-data:www-data /var/www/wordpress
sudo systemctl reload php8.2-fpm

# Health
curl -sf https://example.com/health || exit 1
```

---

## Migrations com WP-CLI

- Fazer backup do DB antes de qualquer migração.
- Exemplos: `wp option delete old_option_name`, `wp post meta set $id new_key value`, `wp db query "DELETE FROM wp_postmeta WHERE ..."`.
- Migrações custom em PHP: versionar em arquivos (ex.: `migrations/20260202_add_meta.php`) e rodar via WP-CLI ou script único que checa versão.

---

## Rollback

- **Entrada:** caminho/nome do backup (tar + SQL) a restaurar.
- **Passos:** 1) Backup da versão atual (tar + dump). 2) Restaurar arquivos do backup escolhido (tar -xzf). 3) Restaurar DB (gunzip < backup.sql.gz | mysql ...). 4) Reload PHP-FPM e flush cache. 5) Health check.
- Sempre confirmar com o usuário (ou aprovação no pipeline) antes de restaurar produção.
- Manter lista de backups disponíveis (ex.: `ls -lh /var/backups/wordpress_*.tar.gz`) para escolher o ponto de restauração.
