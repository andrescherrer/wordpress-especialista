# Boas práticas – Shortcodes, Widgets e Gutenberg

**Referência rápida:** Sempre sanitizar entrada e escapar saída; nonce em formulários; wp_reset_postdata após WP_Query; verificar function_exists para blocos.

---

## Segurança

- **Shortcodes/Widgets:** sanitize_text_field, sanitize_email, esc_url, absint nos atributos e no update do widget.
- **Saída:** esc_html, esc_attr, esc_url ao montar HTML; wp_kses_post apenas quando precisar de HTML permitido.
- **Formulários:** wp_nonce_field + wp_verify_nonce ao processar POST.
- **Capabilities:** verificar current_user_can quando a ação for restrita.

---

## Performance

- **WP_Query em shortcode/widget/bloco:** usar posts_per_page limitado; chamar **wp_reset_postdata()** após o loop.
- **Assets:** enfileirar CSS/JS só quando o shortcode estiver presente (detectar no conteúdo com has_shortcode) ou sempre se for pouco peso.
- **Cache:** para listagens pesadas, considerar transients (get_transient/set_transient).

---

## Compatibilidade

- **Blocos:** if ( function_exists( 'register_block_type' ) ) antes de registrar.
- **Widgets:** register_widget no **widgets_init**.
- **Shortcodes:** add_shortcode em init ou plugins_loaded.

---

## Equívocos comuns

1. **“Shortcodes executam PHP direto”** – São callbacks registradas; o PHP roda no servidor, mas o conteúdo do shortcode vem do banco. Ainda assim é preciso escapar a saída.

2. **“Blocos substituem shortcodes”** – Não. Blocos são para edição visual; shortcodes para inserção dinâmica. Podem coexistir.

3. **“Widgets clássicos estão obsoletos”** – Ainda são suportados. Widgets em bloco são uma opção adicional.

4. **“Bloco dinâmico sempre renderiza no servidor”** – Em PHP, sim (render_callback). Também é possível bloco dinâmico que busca dados no cliente via JS.

---

## Checklist

- [ ] Shortcode: shortcode_atts + sanitize + escape
- [ ] Widget: form com get_field_id/name; update retornando array sanitizado
- [ ] Bloco: render_callback escapando atributos; attributes com default
- [ ] wp_reset_postdata() após qualquer WP_Query em callback
- [ ] Verificar register_block_type antes de registrar bloco

---

*Fonte: 006-WordPress-Fase-6-Shortcodes-Widgets-Gutenberg.md*
