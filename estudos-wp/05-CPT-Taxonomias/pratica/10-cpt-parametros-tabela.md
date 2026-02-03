# Tabela – register_post_type (parâmetros resumidos)

Resumo dos parâmetros mais usados. Fonte: **005-WordPress-Fase-5-CPT-Taxonomias.md**.

---

## Parâmetros principais

| Parâmetro | Descrição | Exemplo |
|----------|-----------|---------|
| **labels** | Nomes no admin (name, singular_name, add_new_item, edit_item, etc.) | array |
| **public** | Visível no front e no admin | true / false |
| **has_archive** | Ter archive (lista) no front | true |
| **supports** | title, editor, thumbnail, excerpt, custom-fields, revisions | array |
| **show_in_rest** | Habilitar Gutenberg e REST API | true |
| **rest_base** | Slug na REST (ex.: /wp/v2/produtos) | 'produtos' |
| **capability_type** | post ou nome custom (define edit_* capabilities) | 'post' |
| **menu_icon** | Ícone do menu (dashicons-*) | 'dashicons-cart' |
| **rewrite** | slug na URL; ['slug' => 'produtos'] | array |
| **taxonomies** | Taxonomias associadas (ex.: category, post_tag ou custom) | ['categoria'] |

---

## supports e editor

- **editor** → suporte a blocos (Gutenberg) quando show_in_rest é true.
- **custom-fields** → meta boxes e campos customizados no rest.

---

## register_taxonomy (resumo)

| Parâmetro | Descrição |
|-----------|-----------|
| **hierarchical** | true = como categorias; false = como tags |
| **show_in_rest** | Incluir na REST API |
| **rewrite** | slug na URL |
| **object_type** | Array de post types (ex.: ['produto']) |
