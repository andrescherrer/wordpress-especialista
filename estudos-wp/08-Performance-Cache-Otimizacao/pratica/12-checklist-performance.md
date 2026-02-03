# Checklist – Performance e cache

Use em revisão de performance. Fonte: **008-WordPress-Fase-8-Performance-Cache-Otimizacao.md**.

---

## Cache

- [ ] Object cache (wp_cache_get/set) para dados frequentes e pesados
- [ ] Transients para dados que podem expirar (feeds, listas)
- [ ] Invalidação ao atualizar dados (save_post, update_option)
- [ ] Grupo e TTL definidos para evitar crescimento indefinido

## Queries

- [ ] Evitar N+1 (preload com WP_Query ou meta_query)
- [ ] Usar fields => 'ids' quando só precisar de IDs
- [ ] Índices em meta_keys usados em meta_query
- [ ] Paginação (posts_per_page) em listagens

## Assets

- [ ] Scripts/styles enfileirados só onde necessário (condicional)
- [ ] defer/async em scripts não críticos
- [ ] Minificação/concatenção em produção quando fizer sentido
- [ ] Imagens com lazy load quando apropriado

## Transients e opções

- [ ] Transients com expiração para dados temporários
- [ ] delete_transient ao atualizar fonte dos dados
- [ ] Opções sensíveis com autoload = false
