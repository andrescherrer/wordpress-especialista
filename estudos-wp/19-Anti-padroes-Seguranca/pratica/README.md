# Prática – Como usar (Anti-padrões e Segurança)

1. **Core:** Use escape em todo output (esc_html, esc_attr, esc_url); sempre $wpdb->prepare; current_user_can com capability específica; nonce em formulários; dados sensíveis criptografados ou em hash.
2. **REST:** Defina args com validate_callback/sanitize_callback; permission_callback que verifica login e capability; evite expor IDs sequenciais; não logue senha/token.
3. **Settings:** sanitize_callback e validate_callback em register_setting; wp_kses para HTML permitido; credenciais via getenv/wp-config/.env.
4. **CPT:** save_post: nonce + capability + sanitize; meta box: capability + nonce + esc_attr/wp_kses_post; listagens: post_status publish para público.
5. **Arquitetura:** Interfaces para DI; logging em eventos (contexto seguro); repository valida e sanitiza antes de persistir.
6. **DevOps:** Secrets em env; .env no .gitignore; HTTPS e HSTS; DB em rede interna ou socket.
7. **Checklist:** Use o checklist em 07-checklist-code-review.md em todo code review de segurança.

**Teoria rápida:** [../REFERENCIA-RAPIDA.md](../REFERENCIA-RAPIDA.md). **Segurança avançada:** Fase 12.
