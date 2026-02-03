# Estrutura mínima de um tema

Arquivos **obrigatórios**: **style.css**, **index.php**; **functions.php**; hierarquia de templates (reforço com foco em tema).

---

## Arquivos obrigatórios

| Arquivo | Descrição |
|---------|------------|
| **style.css** | Cabeçalho do tema (Theme Name, Description, Version, etc.) e estilos; obrigatório. |
| **index.php** | Template fallback; usado quando não houver template mais específico; obrigatório. |
| **functions.php** | Carregado automaticamente; enqueue de scripts/estilos, suportes do tema (title-tag, post-thumbnails), menus, sidebars. |

---

## Cabeçalho style.css (mínimo)

```css
/*
Theme Name: Meu Tema
Theme URI: https://example.com
Description: Tema clássico mínimo.
Version: 1.0
Requires at least: 5.9
Tested up to: 6.4
Requires PHP: 7.4
License: GPL v2 or later
*/
```

---

## index.php (mínimo)

Loop básico ou include de template part:

```php
<?php get_header(); ?>
<?php
if (have_posts()) {
    while (have_posts()) {
        the_post();
        the_title('<h2>', '</h2>');
        the_content();
    }
}
?>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
```

Para isso funcionar, é preciso ter **header.php**, **sidebar.php**, **footer.php** (ou o tema não quebra se usar apenas index.php com HTML mínimo).

---

## Hierarquia de templates (reforço)

- **index.php** – fallback.
- **front-page.php** – página inicial (se configurada como estática).
- **home.php** – listagem de posts (blog).
- **single.php** – post único; **single-{post-type}.php** – single de CPT.
- **page.php** – página; **page-{slug}.php** – página por slug.
- **archive.php** – listagem; **archive-{post-type}.php** – archive de CPT.
- **404.php** – não encontrado.

O WordPress escolhe o primeiro arquivo que existir na hierarquia. Ver [Template Hierarchy](https://developer.wordpress.org/themes/basics/template-hierarchy/).

---

## Próximos passos

- **02-sidebar-widgets.php** – registrar sidebar e exibir widgets.
- **03-menus.php** – menus.
- **05-child-theme.md** – child theme.
- **07-plugin-vs-tema.md** – o que vai no tema vs no plugin.
