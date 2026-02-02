# 02 – REST API Fundamentos

**Foco: prática.** Teoria só quando precisar, sempre à mão.

---

## Comece pela prática

| # | Arquivo | O que você vai fazer |
|---|---------|----------------------|
| 1 | [01-registrar-rotas.php](pratica/01-registrar-rotas.php) | `register_rest_route`, GET/POST/PUT/DELETE, args |
| 2 | [02-controller-oop.php](pratica/02-controller-oop.php) | `WP_REST_Controller`, get_items, create_item, permissions |
| 3 | [03-validacao-sanitizacao.php](pratica/03-validacao-sanitizacao.php) | sanitize_callback, validate_callback, enum |
| 4 | [04-permissoes-response.php](pratica/04-permissoes-response.php) | permission_callback, WP_Error, rest_ensure_response |
| 5 | [05-autenticacao-seguranca.md](pratica/05-autenticacao-seguranca.md) | Cookie, Application Passwords, nonce, capability |

**Como usar:** copie trechos para um plugin (REST API usa `rest_api_init`). Detalhes em [pratica/README.md](pratica/README.md).

---

## Teoria quando precisar

- **No código:** cada arquivo `.php` tem no topo um bloco **Referência rápida** com a sintaxe daquele tópico.
- **Uma página:** [REFERENCIA-RAPIDA.md](REFERENCIA-RAPIDA.md) – rotas, métodos, status, validação, segurança (Ctrl+F).
- **Fonte completa:** [002-WordPress-Fase-2-REST-API-Fundamentos.md](../../002-WordPress-Fase-2-REST-API-Fundamentos.md) na raiz do repositório.

---

## Objetivos da Fase 2

- Registrar rotas e endpoints com `register_rest_route()` no hook `rest_api_init`
- Implementar controllers com `WP_REST_Controller` (get_items, get_item, create_item, etc.)
- Validar e sanitizar com `args` (sanitize_callback, validate_callback)
- Usar permission_callback e retornar WP_Error com status HTTP correto
- Conhecer autenticação (Cookie, Application Passwords) e segurança (nonce, capability)
