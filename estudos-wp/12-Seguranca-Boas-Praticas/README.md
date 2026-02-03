# 12 – Segurança Avançada e Boas Práticas

**Foco: prática.** Validação/sanitização, escape, nonces, capabilities, prepared statements, upload e headers.

---

## Comece pela prática

| # | Arquivo | O que você vai fazer |
|---|---------|----------------------|
| 1 | [01-validacao-sanitizacao.php](pratica/01-validacao-sanitizacao.php) | Validar e sanitizar formulário (email, URL, texto, número, whitelist) |
| 2 | [02-escape-output.php](pratica/02-escape-output.php) | esc_html, esc_attr, esc_url, wp_kses, wp_json_encode por contexto |
| 3 | [03-nonces-capabilities.php](pratica/03-nonces-capabilities.php) | wp_nonce_field/verify, current_user_can, permission_callback REST |
| 4 | [04-prepared-statements-upload.php](pratica/04-prepared-statements-upload.php) | $wpdb->prepare, insert/update seguros; upload (MIME, tamanho, nome) |
| 5 | [05-security-headers-rest.md](pratica/05-security-headers-rest.md) | Security headers (send_headers); REST args e rate limit |
| 6 | [06-checklist-code-review.md](pratica/06-checklist-code-review.md) | Checklist: input/output, nonces, capabilities, arquivos, acesso direto |
| 7 | [07-boas-praticas.md](pratica/07-boas-praticas.md) | Princípios, ferramentas e equívocos comuns |
| 8 | [08-capability-recurso.php](pratica/08-capability-recurso.php) | Capability por recurso (edit_post, $post_id) em REST e admin |
| 9 | [09-security-headers-cors.md](pratica/09-security-headers-cors.md) | Security headers (X-Frame-Options, CSP) e CORS |
| 10 | [10-upload-seguro.php](pratica/10-upload-seguro.php) | Upload seguro (MIME, tamanho, wp_handle_upload) |
| 11 | [11-checklist-seguranca.md](pratica/11-checklist-seguranca.md) | Checklist segurança (input, output, SQL, nonces, upload) |

**Como usar:** copie as classes/trechos para seu plugin. Sempre: validar entrada, sanitizar, escapar saída, nonce em formulários, capability antes de ações. Detalhes em [pratica/README.md](pratica/README.md).

---

## Teoria quando precisar

- **No código:** cada `.php` tem no topo um bloco **REFERÊNCIA RÁPIDA**.
- **Uma página:** [REFERENCIA-RAPIDA.md](REFERENCIA-RAPIDA.md) – validação, escape, nonces, capabilities, prepared, upload (Ctrl+F).
- **Fonte completa:** [012-WordPress-Fase-12-Seguranca-Boas-Praticas.md](../../012-WordPress-Fase-12-Seguranca-Boas-Praticas.md) na raiz do repositório.

---

## Objetivos da Fase 12

- Aplicar validação e sanitização (sanitize_email, sanitize_text_field, esc_url_raw, absint, whitelist)
- Escapar saída por contexto (esc_html, esc_attr, esc_url, wp_kses, wp_json_encode)
- Proteger com nonces (formulários e AJAX) e capabilities (current_user_can, permission_callback)
- Usar prepared statements em todas as queries; tratar upload com MIME real e nome seguro
- Conhecer security headers e checklist de revisão de código
