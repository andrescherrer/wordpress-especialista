# Backup e restore

Fonte: **014-WordPress-Fase-14-Implantacao-DevOps.md** (14.9, 14.10).

---

## Script de backup (resumo)

- **DB:** `mysqldump --single-transaction --skip-lock-tables -u $DB_USER $DB_NAME | gzip > database.sql.gz`
- **Arquivos:** tar de `wp-content/plugins`, `wp-content/themes`, `wp-config.php`, `.htaccess`; opcional: rsync incremental de `uploads`.
- **Retenção:** `find $BACKUP_BASE -mtime +30 -exec rm -rf {} +` (ex.: 30 dias).
- **Integridade:** `gunzip -t database.sql.gz` após gerar; falhar o script se corrompido.
- **Remoto:** rsync para outro servidor ou `aws s3 sync` para bucket; não depender só de backup local.
- **Cron:** diário (ex.: 1h) para backup completo; incremental ou PITR conforme RPO.

---

## Point-in-Time Recovery (PITR)

- **Objetivo:** restaurar o banco até um horário exato (não só o momento do dump).
- **Requisitos:** binary log habilitado no MySQL (`log-bin=mysql-bin`, `binlog_format=ROW`).
- **Backup:** após dump full com `--master-data=2`, anotar arquivo e posição do binlog; copiar binlogs desde o backup.
- **Restore:** aplicar dump full; depois aplicar binlogs até o timestamp desejado (`mysqlbinlog --stop-datetime="..."`).

Use PITR quando o RPO exigir perda de dados mínima (minutos).

---

## Teste de restore

- Executar restore em ambiente de teste com frequência definida (ex.: mensal).
- Passos: 1) Restaurar DB em instância de teste. 2) Restaurar arquivos. 3) Ajustar wp-config (DB_NAME, etc.) se necessário. 4) Validar site (login, páginas, uploads).
- Registrar resultado (sucesso/falha) e tempo; corrigir procedimentos ou scripts se falhar.
- Documentar onde estão os backups (local + remoto) e quem tem acesso.
