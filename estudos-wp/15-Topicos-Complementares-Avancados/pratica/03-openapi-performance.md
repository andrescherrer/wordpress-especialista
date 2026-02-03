# OpenAPI/Swagger e Performance

Fonte: **015-WordPress-Fase-15-Topicos-Complementares-Avancados.md** (API Documentation, Advanced Performance).

---

## OpenAPI (spec JSON)

- **Estrutura:** openapi 3.0.0; info (title, version, description); servers (url = rest_url()); paths (cada rota com get/post, parameters, requestBody, responses); components/schemas e securitySchemes (ApiKeyAuth, BearerAuth).
- **Endpoint:** `register_rest_route('api/v2', '/openapi.json', ['methods' => 'GET', 'callback' => fn() => new WP_REST_Response(OpenAPIGenerator::generate_spec()), 'permission_callback' => '__return_true']);`
- **Swagger UI:** Página HTML com script SwaggerUIBundle({ url: rest_url('api/v2/openapi.json'), dom_id: '#swagger-ui' }); servir apenas para usuários autorizados (ex.: current_user_can('manage_options')) ou em ambiente de desenvolvimento.

---

## Performance – Lazy loading e scripts

**Lazy load em imagens:**

- Filtro `the_content`: adicionar `loading="lazy"` em `<img>` (exceto se tiver `data-no-lazy`).
- Filtro `post_thumbnail_html`: `str_replace('<img', '<img loading="lazy"', $html)`.

**Defer/async em scripts:**

- Filtro `script_loader_tag`: para handles em lista (ex.: jquery-core, custom-script), adicionar `defer` ao tag; para analytics, `async`.
- Exemplo: `str_replace(' src', ' defer src', $tag)` quando handle está na lista de defer.

**Critical CSS e preload:**

- Inline no `wp_head`: ler arquivo critical.css e imprimir em `<style>`.
- Fonts: `<link rel="preload" as="style" href="...">` para URLs de fontes.

---

## Core Web Vitals

- **LCP (Largest Contentful Paint):** otimizar recurso maior (imagem/hero); preload; reduzir bloqueios.
- **FID/INP (First Input Delay):** reduzir JS longo na main thread; code splitting; defer.
- **CLS (Cumulative Layout Shift):** dimensões explícitas em img/video; reservar espaço para ads/fonts.
- Medir com Lighthouse e PageSpeed Insights; monitorar em produção.
