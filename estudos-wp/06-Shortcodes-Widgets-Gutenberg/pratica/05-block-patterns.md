# Block Patterns

**Referência rápida:** **register_block_pattern( 'namespace/nome', $args )** – title, description, categories, content (string com HTML comentado dos blocos). Categorias: buttons, columns, gallery, header, text, featured, etc.

---

## Registrar pattern

```php
add_action( 'init', function() {
    if ( ! function_exists( 'register_block_pattern' ) ) {
        return;
    }

    register_block_pattern( 'meu-plugin/hero', array(
        'title'       => __( 'Hero Section', 'meu-plugin' ),
        'description' => __( 'Seção hero com título e botão.', 'meu-plugin' ),
        'categories'  => array( 'buttons', 'text' ),
        'content'     => '<!-- wp:heading {"textAlign":"center","level":1} -->
<h1 class="has-text-align-center">Bem-vindo</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Descrição do site.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link">Saiba mais</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->',
    ) );
} );
```

---

## Categorias comuns

- `text` – parágrafos, listas, citações  
- `buttons` – botões  
- `columns` – colunas  
- `gallery` – galerias  
- `header` – cabeçalhos  
- `featured` – destaque  

---

## Content

O **content** é uma string com o HTML que o editor de blocos usa. Cada bloco aparece entre comentários:

- `<!-- wp:block-name {"attr": "value"} -->` … `<!-- /wp:block-name -->`

Para obter o HTML correto, monte o layout no editor, copie o conteúdo em “Code editor” (ou use a ferramenta de exportação de padrões) e use como content do pattern.

---

## Inserir no editor

Após registrar, o pattern aparece no inseridor de blocos em “Padrões” (ou “Patterns”), dentro das categorias definidas.

---

*Fonte: 006-WordPress-Fase-6-Shortcodes-Widgets-Gutenberg.md*
