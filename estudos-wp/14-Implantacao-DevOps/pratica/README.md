# Prática – Como usar (Implantação e DevOps)

1. **Docker:** Use o docker-compose como base; ajuste ports, volumes e env para seu projeto; sempre healthcheck em db e redis.
2. **Secrets:** Mantenha .env fora do Git; use .env.example com placeholders; no wp-config carregue via Dotenv e defina constantes a partir de $_ENV.
3. **CI/CD:** Configure GitHub Actions (ou GitLab CI) com job de testes, security (audit), build e deploy por branch (develop → staging, main → production); use secrets para SSH e DB.
4. **Deploy:** Script idempotente: backup → pull → composer → migrations → cache → permissões → reload → health check; tenha rollback testado.
5. **Backup:** Automatize backup de DB + arquivos; envie cópia para outro servidor ou S3; teste restore com frequência; para RPO curto, considere PITR com binlog.
6. **Monitoring:** Sentry (ou similar) para erros; logs estruturados; endpoint /health e uptime check; defina RTO/RPO e documente procedimentos de DR.

**Teoria rápida:** comentários no topo dos arquivos e [../REFERENCIA-RAPIDA.md](../REFERENCIA-RAPIDA.md).
