# Git, .gitignore e CI/CD

Fonte: **014-WordPress-Fase-14-Implantacao-DevOps.md** (14.4, 14.5).

---

## .gitignore (WordPress)

```
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

# IDE / OS
.vscode/
.idea/
.DS_Store
*.swp
*.swo
*~

# Node / Composer
node_modules/
vendor/
# composer.lock – versionar em projetos aplicação

# Docker
docker-compose.override.yml
.docker/

# Logs e backups
*.log
logs/
debug.log
backups/
*.sql
*.sql.gz
*.zip

# Testes
coverage/
.phpunit.result.cache
.cache/
tmp/
```

Ajuste `seu-tema` e quais plugins versionar (`!wp-content/plugins/meu-plugin/`).

---

## Conventional Commits

- **feat:** nova funcionalidade  
- **fix:** correção de bug  
- **refactor:** mudança que não corrige bug nem adiciona feature  
- **docs:** documentação  
- **ci:** alteração de CI  
- **chore:** outras (deps, config)  
- **perf:** melhoria de performance  
- **test:** testes  

Formato: `type(scope): subject` (imperativo, ~50 chars). Ex.: `feat(plugin): add export to CSV`.

Configurar template: `git config commit.template .gitmessage`.

---

## Branching (Git Flow resumido)

- **main:** produção; só merges de release/hotfix; tag de versão.
- **develop:** staging; base para features; merges de feature/*.
- **feature/nome:** desenvolvimento; merge em develop.
- **release/1.2.0:** testes finais; merge em main e develop.
- **hotfix/nome:** correção urgente; merge em main e develop.

---

## CI/CD (GitHub Actions) – pontos principais

- **Eventos:** `push` e `pull_request` em `develop` e `main`.
- **Jobs:** test (PHP, MySQL, Redis, PHPUnit, PHPStan) → security (audit, phpcs) → build (Docker, se push) → deploy-staging (se develop) → deploy-production (se main, environment: production).
- **Secrets:** configurar em Settings → Secrets and variables → Actions; usar `${{ secrets.NOME }}`; nunca fazer echo de secrets.
- **Deploy:** SSH (appleboy/ssh-action ou similar) com script de backup → pull → composer → migrations → cache → permissões → reload → health check.
