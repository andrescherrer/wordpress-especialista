# 08 – Performance, Cache e Otimização

**Foco: prática.** Teoria só quando precisar, sempre à mão.

---

## Comece pela prática

| # | Arquivo | O que você vai fazer |
|---|---------|----------------------|
| 1 | [01-object-cache.php](pratica/01-object-cache.php) | wp_cache_get/set/delete, grupos, invalidação em save_post |
| 2 | [02-transients-api.php](pratica/02-transients-api.php) | get_transient, set_transient, cache de API externa, limpeza |
| 3 | [03-invalidacao-cache.php](pratica/03-invalidacao-cache.php) | Versionamento de chaves e invalidação em cascata |
| 4 | [04-fragment-cache.php](pratica/04-fragment-cache.php) | Cache de HTML (sidebar, blocos) com ob_start + wp_cache |
| 5 | [05-query-optimization.php](pratica/05-query-optimization.php) | Evitar N+1 (update_postmeta_cache), no_found_rows, update_post_term_cache |
| 6 | [06-page-cache-cdn-assets.md](pratica/06-page-cache-cdn-assets.md) | Referência: page cache, CDN, otimização de assets |
| 7 | [07-boas-praticas.md](pratica/07-boas-praticas.md) | Checklist, métricas (Core Web Vitals), ferramentas |

**Como usar:** copie as classes/funções para seu plugin ou tema. Object cache e transients exigem apenas o WordPress; cache persistente (Redis/Memcached) melhora em produção. Detalhes em [pratica/README.md](pratica/README.md).

---

## Teoria quando precisar

- **No código:** cada arquivo `.php` tem no topo um bloco **REFERÊNCIA RÁPIDA** com a sintaxe daquele tópico.
- **Uma página:** [REFERENCIA-RAPIDA.md](REFERENCIA-RAPIDA.md) – Object Cache, Transients, invalidação, fragment, queries (Ctrl+F).
- **Fonte completa:** [008-WordPress-Fase-8-Performance-Cache-Otimizacao.md](../../008-WordPress-Fase-8-Performance-Cache-Otimizacao.md) na raiz do repositório.

---

## Objetivos da Fase 8

- Usar Object Cache (wp_cache_get/set/delete) com grupos e TTL; invalidar em save_post
- Usar Transients API para dados persistentes e APIs externas; limpar ao publicar
- Aplicar padrões de invalidação: versionamento de chaves e invalidação em cascata
- Implementar fragment caching (cache de HTML) para sidebars e blocos pesados
- Otimizar queries: update_postmeta_cache, no_found_rows, evitar N+1
- Conhecer page cache, CDN e otimização de assets (defer, inline crítico)
- Aplicar checklist de performance e métricas (TTFB, LCP, etc.)
