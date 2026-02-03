# 15 – Tópicos Complementares Avançados

**Foco: prática.** API avançada (GraphQL, headers, rate limit, versioning, deprecation, OpenAPI), performance, ecossistema (WooCommerce, ACF), Headless, code review e comunidade.

---

## Comece pela prática

| # | Arquivo | O que você vai fazer |
|---|---------|----------------------|
| 1 | [01-graphql-headers-rate-limit.php](pratica/01-graphql-headers-rate-limit.php) | WPGraphQL (CPT), validação de headers, rate limiting na REST API |
| 2 | [02-api-versioning-deprecation.php](pratica/02-api-versioning-deprecation.php) | Versionamento por URL (v1/v2), depreciação com headers Sunset/Link |
| 3 | [03-openapi-performance.md](pratica/03-openapi-performance.md) | OpenAPI/Swagger, lazy loading e defer de scripts |
| 4 | [04-headless-ecosystem.md](pratica/04-headless-ecosystem.md) | Headless (REST como interface), WooCommerce/ACF em resumo |
| 5 | [05-code-review-docs.md](pratica/05-code-review-docs.md) | Checklist de code review, PHPDoc, README e ferramentas (PHPCS, PHPStan) |
| 6 | [06-community.md](pratica/06-community.md) | Contribuição, repositório de plugins, código de conduta |
| 7 | [07-boas-praticas.md](pratica/07-boas-praticas.md) | Resumo, recomendações e recursos úteis |
| 8 | [08-woocommerce-hooks.php](pratica/08-woocommerce-hooks.php) | WooCommerce: hooks em pedido (woocommerce_new_order) |
| 9 | [09-acf-campos.php](pratica/09-acf-campos.php) | ACF: get_field, the_field, have_rows em template |
| 10 | [10-headless-endpoint-exemplo.php](pratica/10-headless-endpoint-exemplo.php) | Headless: endpoint REST para front externo |

**Como usar:** copie classes/trechos para seu plugin ou tema; adapte rate limits, headers e rotas ao seu namespace. Detalhes em [pratica/README.md](pratica/README.md).

---

## Teoria quando precisar

- **No código:** cada `.php` tem no topo um bloco **REFERÊNCIA RÁPIDA**.
- **Uma página:** [REFERENCIA-RAPIDA.md](REFERENCIA-RAPIDA.md) – GraphQL, headers, rate limit, versioning, deprecation, OpenAPI, performance, Headless, code review (Ctrl+F).
- **Fonte completa:** [015-WordPress-Fase-15-Topicos-Complementares-Avancados.md](../../015-WordPress-Fase-15-Topicos-Complementares-Avancados.md) na raiz do repositório.

---

## Objetivos da Fase 15

- Usar GraphQL (WPGraphQL) e expor CPT/campos; validar headers e aplicar rate limiting na REST API
- Versionar API (URL ou header) e tratar depreciação (Sunset, Link, Warning)
- Gerar documentação OpenAPI e servir Swagger UI
- Aplicar otimizações de performance (lazy load, defer, imagens, Core Web Vitals)
- Configurar WordPress como Headless (REST como interface principal; endpoints customizados)
- Conhecer padrões do ecossistema (WooCommerce, ACF, Jetpack) e checklist de code review
- Seguir boas práticas de documentação e comunidade
