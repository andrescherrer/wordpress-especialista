# 14 – Implantação e DevOps (Deployment e DevOps)

**Foco: prática.** Docker (dev), secrets, health checks, Git, CI/CD, deploy, backup, monitoring e disaster recovery.

---

## Comece pela prática

| # | Arquivo | O que você vai fazer |
|---|---------|----------------------|
| 1 | [01-docker-compose-dev.yml](pratica/01-docker-compose-dev.yml) | Docker Compose para desenvolvimento (MySQL, PHP, Nginx, Redis, WP-CLI) |
| 2 | [02-secrets-wpconfig.php](pratica/02-secrets-wpconfig.php) | wp-config usando .env (Dotenv); nunca commitar credenciais |
| 3 | [03-health-checks-cicd.md](pratica/03-health-checks-cicd.md) | Health checks (Docker e produção); resumo de pipeline CI/CD |
| 4 | [04-git-gitignore-cicd.md](pratica/04-git-gitignore-cicd.md) | .gitignore WordPress, conventional commits, branching, CI (GitHub Actions) |
| 5 | [05-deploy-rollback.md](pratica/05-deploy-rollback.md) | Script de deploy, migrations com WP-CLI, estratégia de rollback |
| 6 | [06-backup-restore.md](pratica/06-backup-restore.md) | Script de backup, PITR (binlog), teste de restore |
| 7 | [07-boas-praticas.md](pratica/07-boas-praticas.md) | Monitoring (Sentry, logs), RTO/RPO, checklist e equívocos |
| 8 | [08-docker-worker-cron.md](pratica/08-docker-worker-cron.md) | Docker: worker e cron (fila e agendamento) |
| 9 | [09-github-actions-teste-deploy.yml](pratica/09-github-actions-teste-deploy.yml) | GitHub Actions: teste (PHPUnit) + deploy (exemplo) |
| 10 | [10-backup-restore-passos.md](pratica/10-backup-restore-passos.md) | Backup (DB + arquivos) e restore (passos) |

**Como usar:** adapte os YAML/shell/PHP ao seu projeto. Nunca commitar `.env` ou credenciais; usar secrets no CI e .env local. Detalhes em [pratica/README.md](pratica/README.md).

---

## Teoria quando precisar

- **No código:** arquivos `.php` e `.yml` têm no topo um bloco **REFERÊNCIA RÁPIDA** (comentário).
- **Uma página:** [REFERENCIA-RAPIDA.md](REFERENCIA-RAPIDA.md) – Docker, secrets, health, Git, CI/CD, deploy, backup, monitoring, DR (Ctrl+F).
- **Fonte completa:** [014-WordPress-Fase-14-Implantacao-DevOps.md](../../014-WordPress-Fase-14-Implantacao-DevOps.md) na raiz do repositório.

---

## Objetivos da Fase 14

- Configurar ambiente de desenvolvimento com Docker (Compose, Dockerfile PHP-FPM)
- Gerenciar secrets com .env e secrets de CI/CD (nunca hardcode)
- Implementar health checks (Docker e produção)
- Configurar pipeline CI/CD (GitHub Actions ou GitLab CI) com testes e deploy
- Automatizar deploy (script, migrations, rollback) e backup (com PITR quando necessário)
- Aplicar monitoring (Sentry, logs, uptime) e disaster recovery (RTO/RPO, procedimentos, testes)
