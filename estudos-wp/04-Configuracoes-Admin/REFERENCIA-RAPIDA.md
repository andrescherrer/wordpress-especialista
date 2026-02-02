# Referência rápida – Configurações Admin (Settings API)

Uma página. Use Ctrl+F. Fonte: **004-WordPress-Fase-4-Configuracoes-Admin.md**.

---

## Settings API – fluxo

1. **register_setting( $option_group, $option_name, $args )** – option_group = mesmo de settings_fields(); args: type, sanitize_callback, show_in_rest.
2. **add_settings_section( $id, $title, $callback, $page )** – página = slug da página.
3. **add_settings_field( $id, $title, $callback, $page, $section, $args )** – callback renderiza o input; name="option_name[field_key]".
4. No form: **settings_fields( $option_group );** + **do_settings_sections( $page_slug );** + **submit_button();**.
5. Form **action="options.php"** – o WordPress processa e salva; redirect de volta à página.

---

## Menus admin

- **add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $callback, $icon, $position )** – menu principal.
- **add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $callback )** – submenu.
- **add_options_page( $page_title, $menu_title, $capability, $menu_slug, $callback )** – submenu em Configurações.
- Capability típica: **manage_options**. Sempre **current_user_can( 'manage_options' )** antes de renderizar.

---

## Campos no form

- **name:** para option array use `meu_plugin_settings[api_key]`; valor com **get_option( 'meu_plugin_settings' )['api_key']**.
- **Sempre esc_attr( $value )** em value=; **checked( $value, 1 )** em checkbox; **selected( $saved, $option_value )** em select.
- Sanitize no **sanitize_callback** do register_setting: sanitize_text_field, sanitize_email, esc_url_raw; checkbox: isset( $input['key'] ) ? 1 : 0.

---

## Meta boxes

- **add_action( 'add_meta_boxes', callback );** → **add_meta_box( $id, $title, $callback, $screen, $context, $priority )** – screen = 'post', 'page' ou CPT.
- No callback: **wp_nonce_field( 'action_name', 'field_name' );** e **get_post_meta( $post->ID, '_meta_key', true );**.
- **add_action( 'save_post', callback );** – verificar **wp_verify_nonce( $_POST['field_name'], 'action_name' )**, **! wp_is_post_autosave/revision**, **current_user_can( 'edit_post', $post_id )**, depois **update_post_meta( $post_id, '_meta_key', sanitize_*( $value ) );**.

---

## Admin notices

- **add_action( 'admin_notices', function() { echo '<div class="notice notice-success is-dismissible"><p>...</p></div>'; } );**
- Tipos: **notice-success**, **notice-error**, **notice-warning**, **notice-info**.
- Usar **wp_kses_post()** ou **esc_html()** no conteúdo da mensagem.

---

## Scripts e estilos no admin

- **add_action( 'admin_enqueue_scripts', function( $hook ) { ... } );** – carregar só na página desejada (ex: `$hook === 'toplevel_page_meu-plugin'`).
- **wp_enqueue_style( $handle, $src, $deps, $ver );** e **wp_enqueue_script( $handle, $src, $deps, $ver, $in_footer );**.
- **wp_enqueue_media();** para upload; **wp_enqueue_style( 'wp-color-picker' ); wp_enqueue_script( 'wp-color-picker' );** para cor.
- **wp_localize_script( $handle, 'objetoJs', [ 'ajaxUrl' => admin_url( 'admin-ajax.php' ), 'nonce' => wp_create_nonce( 'acao' ) ] );**

---

## Validação e erros

- No **sanitize_callback**: se inválido, **add_settings_error( $setting, $code, $message, $type )** e retornar **get_option( $option_name )** (valor anterior).
- **settings_errors( $option_name )** na página exibe as mensagens.

---

## Checklist

- [ ] register_setting com sanitize_callback
- [ ] Form com settings_fields() e do_settings_sections(); action="options.php"
- [ ] Verificar current_user_can antes de renderizar página
- [ ] Meta box: nonce + verificação em save_post + update_post_meta com sanitize
- [ ] admin_enqueue_scripts apenas no hook da página (evitar carregar em todo admin)
- [ ] name dos campos no formato option_name[key] quando for array
