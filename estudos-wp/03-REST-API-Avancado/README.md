# 03 – REST API Avançado

**Foco: prática.** Teoria só quando precisar, sempre à mão.

---

## Comece pela prática

| # | Arquivo | O que você vai fazer |
|---|---------|----------------------|
| 1 | [01-controller-completo.php](pratica/01-controller-completo.php) | Controller CRUD com prepare_item, validate, get_collection_params |
| 2 | [02-resposta-estruturada.php](pratica/02-resposta-estruturada.php) | Wrapper de resposta: success, error, validation_error, paginated |
| 3 | [03-validator-sanitizer.php](pratica/03-validator-sanitizer.php) | Validação avançada (Validator) e sanitização de saída |
| 4 | [04-tratamento-erros.php](pratica/04-tratamento-erros.php) | Error handler centralizado, validation_error, not_found |
| 5 | [05-testes-api.md](pratica/05-testes-api.md) | Testar com curl e PHPUnit (WP_REST_Request, dispatch) |
| 6 | [06-boas-praticas.md](pratica/06-boas-praticas.md) | Checklist e estrutura de projeto |

**Como usar:** copie trechos para um plugin; controllers usam `rest_api_init`. Detalhes em [pratica/README.md](pratica/README.md).

---

## Teoria quando precisar

- **No código:** cada arquivo `.php` tem no topo um bloco **Referência rápida** com a sintaxe daquele tópico.
- **Uma página:** [REFERENCIA-RAPIDA.md](REFERENCIA-RAPIDA.md) – controllers, resposta, validação, erros, testes (Ctrl+F).
- **Fonte completa:** [003-WordPress-Fase-3-REST-API-Avancado.md](../../003-WordPress-Fase-3-REST-API-Avancado.md) na raiz do repositório.

---

## Objetivos da Fase 3

- Construir controllers REST complexos com herança (WP_REST_Controller)
- Estruturar respostas de forma consistente (success/data/message/meta)
- Validação avançada e sanitização de saída (classes reutilizáveis)
- Tratamento de erros (handler centralizado, WP_Error com status)
- Testar endpoints (curl, PHPUnit com rest_get_server)
- Aplicar boas práticas (permission_callback, versionamento, rate limit, cache)
