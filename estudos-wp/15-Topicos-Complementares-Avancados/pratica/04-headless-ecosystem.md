# Headless WordPress e Ecossistema

Fonte: **015-WordPress-Fase-15-Topicos-Complementares-Avancados.md** (Headless, WordPress Ecosystem).

---

## Headless – REST como interface principal

- **CPT com REST:** `show_in_rest => true`, `rest_base => 'articles'`; `rest_prepare_post` (ou prepare para o CPT) para moldar resposta (id, title, content, excerpt, featured_image, author, meta).
- **Endpoints customizados:** ex.: `/api/v2/homepage` (hero, featured_posts, categories), `/api/v2/blog` (posts, categories), `/api/v2/categories/{id}/posts` com validação do `id`; `permission_callback => '__return_true'` para leitura pública quando apropriado.
- **Frontend decoupled:** React/Vue/Next/Nuxt consome REST (ou GraphQL); configurar CORS se domínio diferente; autenticação via Application Password ou JWT para operações que exigem login.
- **SSG/Jamstack:** gerar estático (Next.js getStaticProps, etc.) consumindo REST; revalidar ou rebuild quando conteúdo mudar (webhook ou cron).

---

## Ecossistema – WooCommerce e ACF

**WooCommerce:**

- Hooks (actions/filters) para checkout, produtos, pedidos; evitar alterar core; usar templates em tema filho; APIs REST de orders/products quando precisar de integração externa.
- Padrões: não salvar dados sensíveis em options públicas; validar e sanitizar inputs; capabilities (manage_woocommerce, etc.).

**ACF (Advanced Custom Fields):**

- Campos expostos na REST: `show_in_rest` no field group; ou usar plugin wp-graphql-acf para GraphQL.
- Acesso em código: `get_field('nome_campo', $post_id)`; retorno pode ser array, objeto ou valor; validar antes de usar.

**Outros:** Jetpack (API, módulos); Akismet (anti-spam); WP Rocket (cache). Integrar via APIs e hooks oficiais; documentação de cada plugin.
