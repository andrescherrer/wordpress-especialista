# Referência rápida – Fundamentos Core

Uma página. Use Ctrl+F para achar o que precisa. Fonte: **001-WordPress-Fase-1-Fundamentos-Core.md**.

---

## Bootstrap: quando usar cada hook

| Hook | Quando usar |
|------|-------------|
| `muplugins_loaded` | Logo após mu-plugins |
| `plugins_loaded` | Após todos os plugins; dependências |
| `after_setup_theme` | Suporte do tema (thumbnails, menus) |
| `init` | **Registrar CPT, taxonomias**, post status |
| `wp_loaded` | Query de URL pronta, usuário autenticado |
| `wp` | Query executada; `get_queried_object()` |

**Regra:** Nunca funções WP em `wp-config.php`. Sempre hooks.

---

## Hooks

| | Actions | Filters |
|---|--------|--------|
| Objetivo | Executar em um ponto | Modificar e **retornar** valor |
| Uso | `add_action('hook', 'callback', 10, 2)` | `add_filter('hook', 'callback', 10, 1)` |
| Remover | `remove_action('hook', 'callback', 10)` | `remove_filter('hook', 'callback', 10)` |

Prioridade: padrão 10; menor = antes. **Não** usar `remove_all_filters()` em hooks core.

---

## $wpdb

- `global $wpdb;` → `$wpdb->posts`, `$wpdb->postmeta` (já com prefixo)
- Consultas: `get_results()`, `get_row()`, `get_var()`, `get_col()`
- **Sempre** `$wpdb->prepare("... WHERE ID = %d", $id)` – placeholders `%d`, `%s`, `%f`
- Insert: `$wpdb->insert($tabela, $dados, ['%s','%d']);` → `$wpdb->insert_id`
- Update/Delete: `$wpdb->update($tabela, $dados, $where, $format, $where_format);`
- Transação: `START TRANSACTION` / `COMMIT` / `ROLLBACK`; não misturar com `wp_insert_post()` dentro

---

## Posts e meta

- `get_post($id)` | `wp_insert_post([...])` | `wp_update_post([...])` | `wp_delete_post($id)` ou `wp_delete_post($id, true)`
- Meta: `get_post_meta($id, 'key', true)` | `update_post_meta()` | `delete_post_meta()`
- Status: `publish`, `draft`, `pending`, `private`, `trash`

---

## The Loop

```php
if (have_posts()) {
    while (have_posts()) {
        the_post();
        the_title(); the_content(); get_the_ID();
    }
}
```

**Loop aninhado:** usar `WP_Query` próprio e **`wp_reset_postdata()`** ao terminar.

---

## Template Hierarchy (ordem)

Single: `single-{type}-{slug}.php` → `single-{type}.php` → `single.php` → `index.php`  
Page: `page-{slug}.php` → `page-{id}.php` → `page.php` → `index.php`  
Archive: `archive-{type}.php` → `archive.php` → `index.php`

Tags: `is_home()`, `is_front_page()`, `is_singular()`, `is_page()`, `is_archive()`, `is_category()`, `is_search()`, `is_404()`.

---

## Coding Standards

- Classes: `My_Plugin_Class` | Funções: `meu_plugin_funcao` | Constantes: `MEU_PLUGIN_VER`
- Indentação 4 espaços; chaves na mesma linha do `if`
- Plugin: header (Plugin Name, Version, Text Domain); `if (!defined('ABSPATH')) exit;`

---

## Checklist

- [ ] Código que usa funções WP → dentro de hooks (`init`, etc.)
- [ ] Action = fazer algo; Filter = retornar valor alterado
- [ ] Queries dinâmicas → `$wpdb->prepare()`
- [ ] Loop aninhado → `wp_reset_postdata()`
- [ ] Remover hook → `remove_action`/`remove_filter` específico, nunca `remove_all_*` em core
- [ ] Tabelas → `$wpdb->posts` (nunca `wp_posts` hardcoded)
