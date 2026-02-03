# Testando CPT, Settings e Shortcodes

Fonte: **017-WordPress-Fase-17-Testes-Em-Toda-Fase.md** (Fases 4, 5, 6).

---

## Custom Post Types

- **Registro:** Após init e carregar plugin, `get_post_types()`; `$this->assertArrayHasKey('product', get_post_types())`.
- **Criação e meta:** `$post_id = $this->factory->post->create(['post_type' => 'product', 'post_title' => 'Test']);`; simular `$_POST['product_price']`; `do_action('save_post_product', $post_id, get_post($post_id))`; `$this->assertEquals('99.99', get_post_meta($post_id, '_product_price', true))`.
- **Permissão:** Usuário com role subscriber; disparar save_post; assert que meta não foi salva (ou valor vazio).
- **Exibição:** `update_post_meta($post_id, '_product_price', '99.99');`; setar `global $post`; `apply_filters('the_content', $content)`; `$this->assertStringContainsString('Price: $99.99', $content)`.

---

## Settings API

- **Registro:** Registrar settings no init; obter array de settings registrados (ex.: global $wp_registered_settings ou função do plugin) e assert que a opção existe.
- **Sanitização:** `update_option('minha_opcao', 'valor_invalido');` chamar o callback de sanitize (ou submeter form); `$this->assertEquals('valor_esperado_sanitizado', get_option('minha_opcao'))`.
- **Meta boxes:** Simular POST; `do_action('save_post', $post_id)`; assert post meta ou options conforme o caso.

---

## Shortcodes

- **Saída:** `$output = do_shortcode('[meu_shortcode attr="valor"]');` ou `apply_filters('the_content', '[meu_shortcode]');`; `$this->assertStringContainsString('texto esperado', $output)`.
- **Atributos:** `do_shortcode('[meu_shortcode count="5"]');` e assert que o número aparece ou que a lógica foi aplicada (ex.: 5 itens).
- **Blocks (Gutenberg):** Testar renderização do block (render_callback) passando atributos; assert que o HTML ou dados retornados estão corretos.
