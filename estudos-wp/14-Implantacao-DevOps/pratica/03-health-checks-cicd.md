# Health checks e CI/CD

Fonte: **014-WordPress-Fase-14-Implantacao-DevOps.md** (14.1.4, 14.5).

---

## Health checks no Docker

No `docker-compose.yml`, use `healthcheck` em serviços que outros dependem (ex.: MySQL e Redis):

```yaml
db:
  image: mysql:8.0
  healthcheck:
    test: ["CMD", "mysqladmin", "ping", "-h", "localhost"]
    interval: 10s
    timeout: 5s
    retries: 5

redis:
  image: redis:7-alpine
  healthcheck:
    test: ["CMD", "redis-cli", "ping"]
    interval: 10s
    timeout: 5s
    retries: 5

php:
  depends_on:
    db:
      condition: service_healthy
```

Assim o PHP só sobe quando o MySQL estiver aceitando conexões.

---

## Health check em produção (endpoint)

Criar um endpoint que retorna 200 e status (ex.: JSON) para o load balancer ou pipeline:

- **URL:** `/health` ou `/wp-json/my-plugin/v1/health`
- **Resposta:** `{"status":"ok","db":"ok"}` (opcional: checar conexão DB e Redis)
- **Nginx/LB:** verificar esse URL; considerar como “unhealthy” se 5xx ou timeout

No pipeline de deploy, após o script:

```yaml
- name: Health Check
  run: curl -f https://example.com/health || exit 1
```

Se falhar, o deploy pode ser considerado falho e disparar rollback ou alerta.

---

## Resumo do pipeline CI/CD (GitHub Actions)

1. **test:** checkout → Setup PHP → composer install → MySQL/Redis services (health) → PHPUnit → PHPStan.
2. **security:** composer audit, phpcs (opcional).
3. **build:** (se push) build e push da imagem Docker para registry.
4. **deploy-staging:** se branch `develop`; SSH no servidor → backup → git pull → composer --no-dev → wp db optimize → cache flush → permissões → health check → notificação (ex.: Slack).
5. **deploy-production:** se branch `main`, environment `production`; mesmo fluxo com secrets de produção; criar release/tag.

Secrets: `STAGING_HOST`, `STAGING_USER`, `STAGING_SSH_KEY`, `STAGING_DB_PASSWORD`, `PROD_*`, `SLACK_WEBHOOK`. Nunca logar valores de secrets.
