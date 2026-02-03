# Tabela de hooks de bootstrap – Quando usar cada um

Resumo: o que já está disponível em cada hook. Fonte: **001-WordPress-Fase-1-Fundamentos-Core.md**.

---

## Ordem e uso típico

| Hook | Quando executa | O que já está disponível |
|------|----------------|---------------------------|
| **muplugins_loaded** | Após must-use plugins | PHP básico; WP ainda não carregado |
| **plugins_loaded** | Após todos os plugins | Funções WP; não use get_post() etc. |
| **after_setup_theme** | Após tema carregar | Suporte a tema (add_theme_support) |
| **init** | Após WP carregar (posts, users, taxonomias) | `get_post()`, `get_userdata()`, `register_post_type()` |
| **wp_loaded** | Após query de URL (antes da resposta) | Query principal definida |
| **wp** | Após query executada | `$wp_query` pronto; bom para lógica por tipo de página |
| **template_redirect** | Antes de carregar template | Última chance para redirect (headers não enviados) |
| **wp_footer** / **wp_head** | No tema | Output no front; scripts/styles já enfileirados |

---

## Regra prática

- **Registrar CPT, taxonomias, settings:** `init`
- **Registrar rotas REST:** `rest_api_init`
- **Lógica que depende da URL/query:** `wp` ou `template_redirect`
- **Carregar algo antes de qualquer WP:** `plugins_loaded` (evitar para acessar posts/users)
- **Must-use (sempre ativo):** colocar plugin em `wp-content/mu-plugins/`; carrega antes de `plugins_loaded`
