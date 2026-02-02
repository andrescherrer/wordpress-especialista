# Referência rápida – CPT e Taxonomias

Uma página. Use Ctrl+F. Fonte: **005-WordPress-Fase-5-CPT-Taxonomias.md**.

---

## register_post_type( $post_type, $args )

- **Hook:** `init`.
- **Args principais:** label, labels (array), public, publicly_queryable, show_ui, show_in_menu, show_in_rest, query_var, rewrite (slug, with_front), has_archive, hierarchical, menu_position, menu_icon, supports (title, editor, thumbnail, excerpt, custom-fields, page-attributes…), capabilities, map_meta_cap.
- **hierarchical true** = tipo página (pai/filho); **false** = tipo post.
- **show_in_rest true** = expõe na REST API.
- Slug em **rewrite['slug']** define a URL (ex: /livros/).

---

## register_taxonomy( $taxonomy, $object_type, $args )

- **Hook:** `init` (após o CPT se a taxonomia for do CPT).
- **$object_type:** 'post', 'page' ou slug do CPT (ou array de vários).
- **hierarchical true** = tipo categoria (pai/filho); **false** = tipo tag.
- **Args:** labels, description, public, show_ui, show_in_rest, rewrite (slug, hierarchical), query_var, rest_base, show_admin_column.

---

## WP_Query com tax_query

```php
$args = [
    'post_type'      => 'product',
    'posts_per_page' => 10,
    'tax_query'      => [
        [
            'taxonomy' => 'product_cat',
            'field'    => 'slug',   // slug, id, name
            'terms'    => 'eletronicos',
            'operator' => 'IN'      // IN, NOT IN, AND
        ]
    ]
];
$q = new WP_Query( $args );
```

Várias taxonomias: **'relation' => 'AND'** ou **'OR'** no primeiro elemento do tax_query.

---

## Funções de termos

- **get_terms( $taxonomy, $args )** – lista de termos (hide_empty, number, parent, etc.).
- **get_term( $id, $taxonomy )** – um termo.
- **wp_get_post_terms( $post_id, $taxonomy )** – termos do post.
- **wp_set_post_terms( $post_id, $term_ids, $taxonomy )** – define termos (sobrescreve).
- **wp_add_object_terms( $post_id, $term_id, $taxonomy )** – adiciona um termo.
- **wp_remove_object_terms( $post_id, $term_id, $taxonomy )** – remove.
- **wp_insert_term( $name, $taxonomy, $args )** – cria termo (slug, parent, description).

---

## Template Hierarchy (CPT)

- **Single:** single-{post_type}.php → single.php → index.php.
- **Archive:** archive-{post_type}.php → archive.php → index.php.
- Conditional: **is_singular( 'livro' )**, **is_post_type_archive( 'livro' )**.

---

## Checklist

- [ ] CPT e taxonomia registrados no **init**
- [ ] Nome do CPT/taxonomia com prefixo (evitar conflito)
- [ ] show_in_rest true se for usar REST API
- [ ] map_meta_cap true no CPT quando usar capabilities customizadas
- [ ] supports com title, editor, thumbnail conforme necessidade
- [ ] rewrite slug amigável; has_archive se quiser listagem
