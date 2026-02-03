# Referência rápida – Shortcodes, Widgets e Gutenberg

Uma página. Use Ctrl+F. Fonte: **006-WordPress-Fase-6-Shortcodes-Widgets-Gutenberg.md**.

---

## Shortcodes

- **add_shortcode( 'tag', $callback );** – callback( $atts, $content = '' ). $content só existe em [tag]conteúdo[/tag].
- **shortcode_atts( $defaults, $atts, 'tag' )** – mescla atributos com padrões; terceiro parâmetro = nome do shortcode.
- **Sempre:** sanitizar atributos (sanitize_text_field, esc_url, absint); escapar saída (esc_html, esc_attr, esc_url).
- **Conteúdo interno:** wp_kses_post( $content ) se permitir HTML; do_shortcode( $content ) para processar shortcodes aninhados.
- Uso: `[meu_botao texto="Clique" url="https://..."]` ou `[meu_alert tipo="info"]Texto[/meu_alert]`.

---

## Widgets (clássicos)

- Estender **WP_Widget**; **__construct()** com parent::__construct( $id, $name, $widget_ops ).
- **widget( $args, $instance )** – front-end: echo $args['before_widget'], título com $args['before_title']/after_title, conteúdo.
- **form( $instance )** – formulário no admin: get_field_id(), get_field_name(), valores de $instance.
- **update( $new_instance, $old_instance )** – sanitizar e retornar array para salvar.
- **register_widget( 'Nome_Classe' );** no hook **widgets_init**.

---

## Blocos Gutenberg (PHP)

- **register_block_type( 'namespace/nome', $args )** – no **init**; verificar function_exists('register_block_type').
- **Bloco dinâmico:** 'render_callback' => callable – recebe $attributes, $content; retorna HTML (escapar).
- **Args:** editor_script, editor_style, style (front); attributes (schema); para estático: save em JS ou save => null para dinâmico.
- Blocos estáticos: definidos em JS (registerBlockType, edit, save). Dinâmicos: save retorna null no JS; PHP render_callback gera o HTML no front.

---

## Block Patterns

- **register_block_pattern( 'namespace/nome', $args )** – title, description, categories, content (string HTML dos blocos).
- content = HTML comentado dos blocos (ex: `<!-- wp:paragraph -->...<!-- /wp:paragraph -->`).
- Categorias: 'buttons', 'columns', 'gallery', 'header', 'text', etc.

---

## Checklist

- [ ] Shortcode: shortcode_atts + sanitize atributos + escape na saída
- [ ] Widget: form com get_field_id/get_field_name; update sanitizando; widget com $args
- [ ] Bloco dinâmico: render_callback escapando atributos e conteúdo
- [ ] Nonce em formulários dentro de shortcode
- [ ] wp_reset_postdata() após WP_Query em shortcode/widget/bloco
