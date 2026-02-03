# 19 – Anti-padrões e Segurança (Security Anti-patterns)

**Foco: prática.** Identificar e corrigir padrões inseguros por fase: Core (XSS, SQLi, capabilities, dados sensíveis), REST API (validação, permissões, IDs, logs), Settings, CPT, Arquitetura, DevOps e checklist de code review.

---

## Comece pela prática

| # | Arquivo | O que você vai fazer |
|---|---------|----------------------|
| 1 | [01-core-xss-sqli-capabilities.php](pratica/01-core-xss-sqli-capabilities.php) | XSS (escape), SQL Injection (prepare), capabilities e nonces, dados sensíveis (criptografia) |
| 2 | [02-rest-api-validacao-permissoes.php](pratica/02-rest-api-validacao-permissoes.php) | Validação/sanitização em REST, permission_callback, IDs públicos vs hash, não logar senha/token |
| 3 | [03-settings-validacao-credentials.php](pratica/03-settings-validacao-credentials.php) | sanitize_callback/validate_callback em settings, wp_kses para HTML, credenciais via env/wp-config |
| 4 | [04-cpt-capability-drafts-meta-box.php](pratica/04-cpt-capability-drafts-meta-box.php) | save_post com nonce e capability, post_status publish, meta box com escape (esc_attr, wp_kses_post) |
| 5 | [05-arquitetura-di-events-repository.php](pratica/05-arquitetura-di-events-repository.php) | DI com type hint/interface, eventos com logging seguro, repository com sanitização |
| 6 | [06-devops-secrets-ssl-database.md](pratica/06-devops-secrets-ssl-database.md) | Secrets (env, .gitignore), SSL/TLS e HSTS, DB em rede interna/socket |
| 7 | [07-checklist-code-review.md](pratica/07-checklist-code-review.md) | Checklist de revisão: input, output, SQL, auth, dados sensíveis, uploads, REST, erros, secrets, infra |
| 8 | [08-upload-errado-vs-correto.php](pratica/08-upload-errado-vs-correto.php) | Upload inseguro vs seguro (tipo, tamanho, wp_handle_upload) |
| 9 | [09-rest-permission-errado-vs-correto.php](pratica/09-rest-permission-errado-vs-correto.php) | REST sem permission vs com capability por recurso |
| 10 | [10-anti-padroes-por-fase.md](pratica/10-anti-padroes-por-fase.md) | Resumo anti-padrões por fase (Core, REST, Settings, CPT, Arquitetura, DevOps) |

**Como usar:** os `.php` mostram anti-padrão vs solução correta; use os `.md` para revisão e checklist. Detalhes em [pratica/README.md](pratica/README.md).

---

## Teoria quando precisar

- **No código:** cada `.php` tem no topo um bloco **REFERÊNCIA RÁPIDA**.
- **Uma página:** [REFERENCIA-RAPIDA.md](REFERENCIA-RAPIDA.md) – Core, REST, Settings, CPT, Arquitetura, DevOps, checklist (Ctrl+F).
- **Fonte completa:** [019-WordPress-Fase-19-Anti-padroes-Seguranca.md](../../019-WordPress-Fase-19-Anti-padroes-Seguranca.md) na raiz do repositório.

---

## Objetivos da Fase 19

- Reconhecer anti-padrões de segurança em Core, REST, Settings, CPT, Arquitetura e DevOps
- Aplicar soluções: escape, prepared statements, capabilities, nonces, validação, sanitização
- Evitar expor IDs/dados sensíveis e não logar senhas/tokens
- Usar o checklist em code reviews e consultar Fase 12 para segurança avançada
