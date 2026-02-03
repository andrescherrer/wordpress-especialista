# Prática – Como usar (Tópicos Complementares Avançados)

1. **GraphQL:** Instale WPGraphQL; use show_in_graphql e graphql_single_name/plural_name no CPT; em campos sensíveis verifique is_user_logged_in() no resolve.
2. **Headers e rate limit:** Valide X-API-Key (e opcionalmente X-Request-ID) no permission_callback; use transients por identificador (IP ou key) com limite e janela; retorne 429 e headers X-RateLimit-* quando exceder.
3. **Versioning:** Prefira api/v1 e api/v2 na URL; mantenha v1 estável até sunset; use Deprecation e Sunset headers e, após a data, 410 Gone.
4. **OpenAPI:** Gere spec (paths, schemas, securitySchemes) e exponha em /openapi.json; use Swagger UI para documentação interativa.
5. **Performance:** Lazy load em imagens; defer em scripts não críticos; otimizar imagens no upload; medir com Lighthouse.
6. **Headless:** Exponha CPT e endpoints customizados (homepage, blog, categories); formate resposta em rest_prepare_*; frontend consome REST ou GraphQL.
7. **Code review:** Use o checklist (funcionalidade, qualidade, segurança, testes, documentação); PHPCS e PHPStan no pipeline; documente com PHPDoc e README.

**Arquivos 08–10:** WooCommerce hooks (08), ACF campos (09), headless endpoint (10).

**Arquivos 11–16 (WooCommerce):** hooks tabela (11), checkout custom (12), gateway esqueleto (13), produto variável (14), relatórios REST (15), checklist WooCommerce (16).

**Arquivos 17–21 (GraphQL):** WPGraphQL instalação (17), query básica (18), CPT e mutations (19), headless consumir GraphQL (20), REST vs GraphQL (21).

**Arquivos 22–26 (contribuição):** contribuir core (22), publicar plugin .org (23), PHPCS WordPress (24), code review checklist (25), comunidade Slack/Make (26).

**Teoria rápida:** no topo de cada `.php` há **REFERÊNCIA RÁPIDA**; uma página: [../REFERENCIA-RAPIDA.md](../REFERENCIA-RAPIDA.md).
