# DevOps – Secrets, SSL/TLS, Database

Resumo: não hardcodar secrets, forçar HTTPS, DB não público. Fonte: **019-WordPress-Fase-19-Anti-padroes-Seguranca.md**.

---

## Secrets

- **Não versionar:** .env, wp-config com credenciais reais, docker-compose com senhas em claro.
- **Usar:** variáveis de ambiente (`getenv()`, `${VAR}` no docker-compose); .env.example como template; .env no .gitignore.
- **Docker:** `environment: MYSQL_ROOT_PASSWORD: ${MYSQL_ROOT_PASSWORD}`; Docker Secrets em Swarm.
- **Kubernetes:** Secret resource; não colar senha em YAML versionado.
- **CI/CD:** GitHub Actions `secrets.API_KEY`, nunca em linha de comando em log.

---

## SSL/TLS

- **HTTP → HTTPS:** redirect 301 em Nginx/Apache; no WordPress: `FORCE_SSL_ADMIN`, `FORCE_SSL_LOGIN`; em init, redirecionar para HTTPS se não for WP_DEBUG.
- **Nginx:** TLS 1.2/1.3; ciphers modernos; `add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;`; OCSP stapling quando possível.

---

## Database

- **Não expor porta 3306** na internet; em Docker não mapear `ports: 3306:3306` para host em produção.
- **Rede interna:** Docker network `internal: true` ou hostname interno (ex.: `db` no docker-compose) acessível só pelos containers.
- **Conexão WP:** `DB_HOST` via socket (`localhost:/var/run/mysqld/mysqld.sock`) ou hostname da rede interna.
- **Firewall:** permitir 3306 apenas de IPs de aplicação; usuário MySQL com privilégios mínimos (SELECT, INSERT, UPDATE, DELETE na DB do WP).
