# Template Hierarchy – Prática

**Referência rápida:** Single → `single-{type}-{slug}.php` → `single-{type}.php` → `single.php` → `index.php`. Page → `page-{slug}.php` → `page.php`. Archive → `archive-{type}.php` → `archive.php`. Tags: `is_home()`, `is_singular()`, `is_page()`, `is_archive()`, `is_404()`.

---

Conceito: o WordPress escolhe o arquivo de template pela **ordem de especificidade**.  
Arquivos devem ficar na pasta do **tema ativo** (ex: `wp-content/themes/seu-tema/`).

## Ordem (resumo)

| Tipo de página      | Ordem dos arquivos |
|---------------------|--------------------|
| Single post         | `single-{post_type}-{slug}.php` → `single-{post_type}.php` → `single.php` → `index.php` |
| Página              | `page-{slug}.php` → `page-{id}.php` → `page.php` → `index.php` |
| Archive (post type) | `archive-{post_type}.php` → `archive.php` → `index.php` |
| Categoria           | `category-{slug}.php` → `category.php` → `archive.php` → `index.php` |
| Tag                 | `tag-{slug}.php` → `tag.php` → `archive.php` → `index.php` |
| Busca               | `search.php` → `index.php` |
| 404                 | `404.php` → `index.php` |

## Identificar template no código

```php
// Caminho do template atual
global $template;
echo $template; // ex: /var/www/wp-content/themes/tema/single.php

// Conditional tags
is_home();           // Página de blog
is_front_page();     // Home estática
is_singular();       // Post ou page único
is_singular( 'post' );
is_page();           // É página
is_page( 123 );      // É a página com ID 123
is_archive();        // Archive (categoria, tag, CPT)
is_category();       // Archive de categoria
is_tag();            // Archive de tag
is_search();         // Resultados de busca
is_404();            // Página não encontrada
```

## Exemplo: single customizado

Para o post type `livro`, crie no tema:

1. **single-livro.php** – usado para qualquer single de `livro`.
2. **single-livro-php-para-iniciantes.php** – usado só quando o slug do livro for `php-para-iniciantes`.

Conteúdo mínimo de `single-livro.php`:

```php
<?php
get_header();

if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		?>
		<article>
			<h1><?php the_title(); ?></h1>
			<div class="meta"><?php the_date(); ?> | <?php the_author(); ?></div>
			<div class="content"><?php the_content(); ?></div>
		</article>
		<?php
	}
}

get_footer();
```

## Referência

- Teoria: `../teoria.md` (seção Template Hierarchy)
- Fonte: `001-WordPress-Fase-1-Fundamentos-Core.md`
