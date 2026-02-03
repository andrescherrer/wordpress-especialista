# Boas práticas – Performance e cache

Checklist, métricas e ferramentas. Fonte: **008-WordPress-Fase-8-Performance-Cache-Otimizacao.md**.

---

## Checklist de performance

- [ ] **Object Cache** para queries e dados frequentes; usar grupos e TTL adequado
- [ ] **Transients** para API externa e dados que precisam persistir entre requests
- [ ] **Invalidar** cache ao salvar/deletar conteúdo (save_post, etc.); usar versionamento ou cascata quando fizer sentido
- [ ] **Fragment cache** para HTML pesado (sidebar, listas); invalidar ao atualizar widgets/conteúdo
- [ ] **Evitar N+1:** update_post_meta_cache() e update_post_term_cache() antes de loops
- [ ] **WP_Query:** no_found_rows => true quando não precisar de total; update_post_meta_cache e update_post_term_cache
- [ ] **Não usar query_posts();** usar new WP_Query() em variável local
- [ ] **Page cache** para anônimos; não cachear admin nem logados
- [ ] **CDN** para assets; defer em scripts; CSS crítico inline
- [ ] **Limpar** revisões antigas, transients expirados; índices no banco para queries lentas

---

## Métricas (Core Web Vitals)

| Métrica | Significado        | Alvo aproximado |
|--------|--------------------|------------------|
| TTFB   | Time to First Byte | < 500 ms        |
| FCP    | First Contentful Paint | < 1,5 s   |
| LCP    | Largest Contentful Paint | < 2,5 s  |
| TTI    | Time to Interactive | < 3,8 s     |
| CLS    | Cumulative Layout Shift | < 0,1   |

---

## Ferramentas

- **Debug/performance:** Query Monitor (queries, hooks), Xdebug, New Relic (APM)
- **Cache persistente:** Redis (wp-redis), Memcached
- **Page cache:** WP Super Cache, W3 Total Cache, cache em servidor (Nginx, Varnish)
- **Análise:** Google PageSpeed Insights, WebPageTest, GTmetrix, Lighthouse

---

## Equívocos comuns

1. **“Mais cache é sempre melhor”** – Cache em excesso pode gerar dados desatualizados e dificultar debug. Cachear com critério e invalidar corretamente.
2. **“Object Cache e Transients são iguais”** – Object Cache = memória (por request ou Redis/Memcached). Transients = banco; mais lentos, persistentes.
3. **“Invalidação é simples”** – Invalidação é um problema difícil; use versionamento de chaves e invalidação em cascata.
4. **“Page cache dispensa otimizar queries”** – Queries lentas ainda afetam geração do cache, admin e usuários logados. Otimize queries também.
