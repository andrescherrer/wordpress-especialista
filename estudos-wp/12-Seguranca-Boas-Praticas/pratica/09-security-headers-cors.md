# Security headers e CORS (resumo)

Resumo: headers de segurança e CORS. Fonte: **012-WordPress-Fase-12-Seguranca-Boas-Praticas.md**.

---

## Headers comuns

| Header | Descrição | Exemplo |
|--------|-----------|---------|
| **X-Frame-Options** | Evitar clickjacking | `DENY` ou `SAMEORIGIN` |
| **X-Content-Type-Options** | Evitar MIME sniffing | `nosniff` |
| **Strict-Transport-Security** | Forçar HTTPS (HSTS) | `max-age=31536000; includeSubDomains` |
| **Content-Security-Policy** | Restringir origens de script/style | Política restritiva (configurar conforme site) |
| **X-XSS-Protection** | (Legado) filtro XSS do browser | `1; mode=block` |

---

## Enviar no WordPress

- **send_headers:** `add_action('send_headers', function () { header('X-Frame-Options: SAMEORIGIN'); });`
- Ou configurar no servidor (Nginx/Apache) para aplicar a todo o site.

---

## CORS (REST API)

- Para API consumida por outro domínio: `Access-Control-Allow-Origin` (evitar `*` com credenciais).
- WordPress REST já envia CORS; customizar com filtro `rest_pre_serve_request` ou headers na resposta.
- Restringir origens permitidas quando possível.
