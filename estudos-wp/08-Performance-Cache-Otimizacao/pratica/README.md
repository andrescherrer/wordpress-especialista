# Prática – Como usar (Performance e Cache)

1. **Object Cache:** funciona com o WordPress padrão (in-memory por request). Em produção, use Redis ou Memcached para cache persistente entre requests.
2. **Transients:** salvos no banco (`wp_options`); ideais para API externa e dados que devem persistir após restart.
3. **Fragment cache:** use chaves únicas por contexto (ex.: `sidebar_primary`, `related_123`); invalide ao atualizar widgets ou conteúdo relacionado.
4. **Queries:** em listas de posts, chame `update_postmeta_cache()` e `update_post_term_cache()` com os IDs antes do loop para evitar N+1.

**Arquivos 09–12:** object cache grupo/TTL (09), transients expiração (10), enqueue condicional (11), checklist performance (12).

**Teoria rápida:** no topo de cada `.php` há um bloco **REFERÊNCIA RÁPIDA**. Tudo em uma página: [../REFERENCIA-RAPIDA.md](../REFERENCIA-RAPIDA.md).
