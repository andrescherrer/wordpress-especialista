# Templates para CPT – Single e Archive

**Referência rápida:** Single: `single-{post_type}.php` → single.php → index.php. Archive: `archive-{post_type}.php` → archive.php → index.php. Conditional: `is_singular( 'livro' )`, `is_post_type_archive( 'livro' )`.

---

## Template Hierarchy (CPT)

| Tipo   | Ordem dos arquivos (no tema) |
|--------|------------------------------|
| Single | `single-livro.php` → `single.php` → `index.php` |
| Archive| `archive-livro.php` → `archive.php` → `index.php` |

Para o CPT **livro**, crie no tema ativo:

- **single-livro.php** – página de um livro
- **archive-livro.php** – listagem de livros (usa has_archive)

---

## Conditional tags

```php
is_singular( 'livro' );           // É single de livro?
is_singular( array( 'livro', 'page' ) );
is_post_type_archive( 'livro' );  // É archive de livro?
get_post_type();                  // 'livro', 'post', etc.
```

---

## Conteúdo mínimo de single-livro.php

```php
<?php
get_header();

if ( have_posts() ) {
    while ( have_posts() ) {
        the_post();
        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <h1><?php the_title(); ?></h1>
            <p class="meta">
                Gêneros: <?php echo wp_get_post_terms( get_the_ID(), 'genero_livro' ) ? wp_strip_all_tags( get_the_term_list( get_the_ID(), 'genero_livro', '', ', ' ) ) : '—'; ?>
            </p>
            <div class="content"><?php the_content(); ?></div>
        </article>
        <?php
    }
} else {
    get_template_part( 'content', 'none' );
}

get_footer();
```

---

## Conteúdo mínimo de archive-livro.php

```php
<?php
get_header();
?>

<h1>Catálogo de Livros</h1>

<?php
if ( have_posts() ) {
    echo '<ul>';
    while ( have_posts() ) {
        the_post();
        ?>
        <li>
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            <?php the_excerpt(); ?>
        </li>
        <?php
    }
    echo '</ul>';
    the_posts_pagination();
} else {
    echo '<p>Nenhum livro encontrado.</p>';
}

get_footer();
```

---

## URLs (rewrite)

Com `rewrite['slug'] => 'livros'` e `has_archive => 'livros'`:

- Listagem: `https://seusite.com/livros/`
- Single: `https://seusite.com/livros/slug-do-livro/`

Após alterar slug ou registrar CPT, salvar em **Configurações → Links permanentes** (ou `flush_rewrite_rules()` na ativação do plugin).

---

*Fonte: 005-WordPress-Fase-5-CPT-Taxonomias.md*
