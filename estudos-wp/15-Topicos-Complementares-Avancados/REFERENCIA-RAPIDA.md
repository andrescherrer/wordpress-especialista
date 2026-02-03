# Referência rápida – Tópicos Complementares Avançados

Uma página. Use Ctrl+F. Fonte: **015-WordPress-Fase-15-Topicos-Complementares-Avancados.md**.

---

## GraphQL (WPGraphQL)

- **Plugin:** wp-graphql/wp-graphql; opcional: wp-graphql-acf para ACF.
- **CPT no GraphQL:** register_post_type com `show_in_graphql => true`, `graphql_single_name`, `graphql_plural_name`.
- **Queries:** posts, post(id), author; mutations: createPost, updatePost, deletePost (input com clientMutationId).
- **Autenticação:** em campos customizados, verificar `is_user_logged_in()` no resolve; lançar UserError se não autenticado.
- **Vantagens:** cliente pede só os campos que precisa; menos over-fetch; introspection.

---

## Headers customizados e validação

- **Ler header:** `$_SERVER['HTTP_X_API_KEY']` (traços viram underscore, maiúsculas).
- **permission_callback:** pode chamar validador que checa X-API-Key, X-Request-ID, etc.; retornar WP_Error com status 400/401 se inválido.
- **Validar API key:** comparar com opção (get_option('api_valid_keys')) ou constante; nunca expor em resposta.

---

## Rate limiting

- **Identificador:** IP (REMOTE_ADDR) ou API key (header); key = "rate_limit_" . md5(identifier).
- **Transient:** get_transient(key); se false, set_transient(key, 1, window); senão incrementar até limit; se >= limit, retornar 429.
- **Headers na resposta:** X-RateLimit-Limit, X-RateLimit-Remaining, X-RateLimit-Reset.
- **Por rota:** configurar limites diferentes (ex.: /auth 10/hora, /posts 1000/hora); usar rest_pre_dispatch ou permission_callback.

---

## API versioning

- **Por URL (recomendado):** register_rest_route('api/v1', ...) e register_rest_route('api/v2', ...); callbacks diferentes por versão.
- **Por header:** X-API-Version; em rest_pre_dispatch ler header e set_param('_api_version'); rotear internamente.
- **Deprecation:** headers Deprecation: true, Sunset: (data), Link: <url>; rel="successor-version"; Warning: 299 - "mensagem". Após sunset retornar 410 Gone.

---

## Deprecation handling

- **Registro:** array rota => [deprecated_version, sunset_date, replacement_endpoint, migration_guide].
- **Middleware:** rest_pre_dispatch; se rota deprecada e data > sunset → WP_Error 410; senão adicionar headers e continuar; opcional: log de uso (transient).
- **Resposta:** incluir deprecation_notice no JSON (message, replacement_endpoint, days_until_sunset) para clientes migrarem.

---

## OpenAPI / Swagger

- **Spec:** openapi 3.0.0; info, servers, paths (get/post com parameters, responses, security), components/schemas e securitySchemes (ApiKeyAuth, BearerAuth).
- **Endpoint:** register_rest_route('api/v2', '/openapi.json', callback que retorna WP_REST_Response com spec).
- **Swagger UI:** página HTML que carrega swagger-ui.js e aponta url para rest_url('api/v2/openapi.json').

---

## Performance

- **Lazy load:** loading="lazy" em img; filtrar the_content e post_thumbnail_html; excluir imagens above-the-fold (data-no-lazy).
- **Defer/async:** script_loader_tag: adicionar defer ou async conforme handle (lista de handles).
- **Critical CSS:** inline no wp_head; preload para fonts.
- **Imagens:** otimizar no upload (Imagick/GD: compressão, strip); WebP quando possível; srcset.
- **Core Web Vitals:** LCP, FID/INP, CLS; medir com Lighthouse; reduzir JS bloqueante e tamanho de recursos.

---

## Headless WordPress

- **REST como interface:** show_in_rest em CPT; rest_base; rest_prepare_post para moldar resposta (featured_image, author, meta).
- **Endpoints customizados:** /api/v2/homepage, /api/v2/blog, /api/v2/categories/{id}/posts; permission_callback __return_true para leitura pública quando apropriado.
- **Frontend:** React/Vue/Next/Nuxt consome REST ou GraphQL; CORS se domínio diferente; autenticação (JWT ou Application Password).

---

## Code review e documentação

- **Checklist:** funcionalidade, edge cases, testes; qualidade (padrões, legibilidade, duplicação, performance); segurança (validação, escape, permissões); testing (unit, integration, coverage); documentação (comentários, README, changelog).
- **Ferramentas:** phpcs --standard=WordPress; phpstan analyse; phpcbf para auto-fix.
- **PHPDoc:** descrição da classe/método; @param, @return, @throws; @since; exemplo quando útil.
- **README:** instalação, uso, API reference (funções e parâmetros), contribuindo, licença.

---

## Comunidade

- **Plugin repo:** seguir guidelines do WordPress.org (segurança, licença, slugs).
- **Código de conduta:** missão, valores, comportamentos esperados/inaceitáveis, canal de denúncias.
- **Contribuir:** pesquisar antes de perguntar; dar contexto; agradecer e compartilhar soluções.
