# Code review – o que revisores olham

Checklist: **segurança** (escape, nonce, capability); **performance**; **i18n**; **a11y**; **documentação**; link para Plugin Handbook – Best Practices.

---

- [ ] **Segurança**
  - **Escape:** todo output com `esc_html`, `esc_attr`, `esc_url`, etc., conforme o contexto.
  - **Nonce:** ações que alteram dados verificam nonce.
  - **Capability:** menus e ações restritas checam `current_user_can(...)`.
  - **Prepared statements:** queries SQL com `$wpdb->prepare()`.
  - **Upload:** validação de tipo e extensão; não executar arquivos enviados.

- [ ] **Performance**
  - Evitar queries em loops; usar transients/cache quando fizer sentido.
  - Enfileirar scripts/styles apenas onde forem usados; não carregar tudo no admin inteiro.

- [ ] **Internacionalização (i18n)**
  - Textos para o usuário com `__()`, `_e()`, `esc_html__()`, etc., e text domain.
  - readme.txt e strings do plugin traduzíveis.

- [ ] **Acessibilidade (a11y)**
  - Labels em formulários; contraste; teclado; ARIA quando necessário (ver estudos-wp/12b-Acessibilidade).

- [ ] **Documentação**
  - Código comentado onde a intenção não for óbvia; docblocks em funções públicas.
  - readme.txt completo (descrição, instalação, FAQ, changelog).

- [ ] **Boas práticas**
  - Prefixos em funções/opções para evitar conflito.
  - Não usar funções depreciadas sem necessidade; checar compatibilidade de versão mínima do PHP/WP.

---

## Recursos

- [Plugin Handbook – Best Practices](https://developer.wordpress.org/plugins/plugin-basics/best-practices/)
- [Plugin Handbook – Security](https://developer.wordpress.org/plugins/security/)
- Ver **22-contribuir-core.md**, **23-publicar-plugin-org.md**, **24-phpcs-wordpress-core.md**, **26-comunidade-slack-make.md**.
