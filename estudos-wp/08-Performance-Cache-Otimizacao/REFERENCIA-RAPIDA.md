# Referência rápida – Performance, Cache e Otimização

Uma página. Use Ctrl+F. Fonte: **008-WordPress-Fase-8-Performance-Cache-Otimizacao.md**.

---

## Object Cache (memória)

- **wp_cache_get( $key, $group = '', $force = false );** – obtém valor; retorna false se não existir.
- **wp_cache_set( $key, $data, $group = '', $expire = 0 );** – salva; $expire em segundos (0 = sem expiração).
- **wp_cache_delete( $key, $group = '' );** – remove uma chave.
- **wp_cache_flush();** – limpa todo o cache (evitar em produção com Redis/Memcached).
- **Grupos:** use um grupo por contexto (ex.: `'meu_plugin'`, `'author_stats'`) para invalidar por grupo.
- **Backends:** padrão = in-memory (por request); Redis/Memcached = persistente (plugins wp-redis, memcached).
- **wp_using_persistent_cache();** – verifica se há cache persistente ativo.

---

## Transients API (banco)

- **get_transient( $key );** – lê; retorna false se não existir ou expirado.
- **set_transient( $key, $value, $expiration );** – salva; $expiration em segundos (0 = sem expiração).
- **delete_transient( $key );** – remove.
- **Uso:** dados que precisam persistir entre requests (API externa, estatísticas); mais lento que object cache.
- **Limpeza:** transients expirados ficam na tabela options; pode agendar limpeza com wp_schedule_event.

---

## Invalidação de cache

- **Versionamento de chaves:** chave = `$key . '_v' . $version`; invalidar grupo = incrementar versão (uma escrita).
- **Cascata:** ao salvar post, invalidar cache de featured, author_posts, related_posts, etc. (mapear dependências).
- **save_post / publish_post:** hook para invalidar caches que dependem do post.
- **Stampede:** lock (wp_cache_set de `$key . '_lock'`) antes de regenerar; outras requests aguardam ou leem após.

---

## Fragment caching (HTML)

- **Fluxo:** ob_start() → gerar HTML → ob_get_clean() → wp_cache_set( $key, $html, $group, $expire ).
- **Servir:** $html = wp_cache_get( $key, $group ); if ( false !== $html ) { echo $html; return; } depois gerar e salvar.
- **Invalidar:** ao atualizar widgets/sidebar, wp_cache_delete( $key, $group ).

---

## Otimização de queries

- **N+1:** em loops de posts, usar **update_postmeta_cache( $post_ids )** e **update_post_term_cache( $post_ids )** antes do loop.
- **WP_Query:** **no_found_rows => true** quando não precisa de paginação total; **update_post_meta_cache => true**, **update_post_term_cache => true**.
- **Não usar query_posts()** – altera $wp_query global; use WP_Query em variável local.

---

## Page cache, CDN e assets

- **Page cache:** servir HTML estático (arquivo ou Redis) em template_redirect; não cachear admin nem usuário logado; invalidar ao salvar post.
- **CDN:** filter em wp_get_attachment_url, style_loader_src, script_loader_src (e srcset) para trocar domínio por CDN.
- **Assets:** defer em scripts; CSS crítico inline; remover emojis/RSD/wlwmanifest se não usar; minificar CSS/JS.

---

## Métricas (Core Web Vitals)

- **TTFB** &lt; 500 ms | **FCP** &lt; 1,5 s | **LCP** &lt; 2,5 s | **TTI** &lt; 3,8 s | **CLS** &lt; 0,1

---

## Boas práticas

- Cachear queries frequentes e dados de API; definir TTL adequado (ex.: listas 1h, preços 5 min).
- Sempre invalidar (ou versionar) quando dados mudarem; documentar dependências.
- Usar índices no banco para queries lentas; EXPLAIN para analisar; limpar revisões e transients expirados.
