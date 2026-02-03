# Backup e restore – Passos resumidos

Resumo: fluxo de backup (DB + arquivos) e restore. Fonte: **014-WordPress-Fase-14-Implantacao-DevOps.md**.

---

## Backup de banco (WP-CLI)

```bash
wp db export backup-$(date +%Y%m%d-%H%M).sql
# Ou com compressão: wp db export - | gzip > backup.sql.gz
```

- Agendar (cron) para rodar diariamente; manter últimos N backups; enviar para storage externo (S3, etc.) se possível.

---

## Backup de arquivos

- `wp-content/uploads`, `wp-content/plugins`, `wp-content/themes` (e custom); opcionalmente raiz do WP.
- Ferramentas: `rsync`, `tar`, ou script que compacta diretórios e envia para storage.
- Excluir cache e logs grandes quando fizer sentido.

---

## Restore de banco

```bash
wp db import backup-20260202.sql
# Se precisar: wp db reset (cuidado: apaga tudo) e depois import
```

- Verificar se o backup é do mesmo ambiente ou migrar URLs com `wp search-replace` se necessário.

---

## Restore de arquivos

- Extrair tarball/zip no diretório do site; ajustar permissões (www-data, etc.).
- Restaurar apenas uploads ou apenas plugins conforme o cenário de desastre.

---

## Teste de restore

- Periodicamente testar restore em ambiente de staging para garantir que o backup é utilizável e o procedimento está documentado.
