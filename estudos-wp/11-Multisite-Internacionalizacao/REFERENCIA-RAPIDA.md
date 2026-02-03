# Referência rápida – Multisite e Internacionalização

Uma página. Use Ctrl+F. Fonte: **011-WordPress-Fase-11-Multisite-Internacionalizacao.md**.

---

## Multisite

- **is_multisite();** – retorna true se for rede Multisite.
- **get_current_blog_id();** – ID do site atual.
- **switch_to_blog( $blog_id );** – trocar contexto para outro site; **restore_current_blog();** – voltar.
- **get_sites( array( 'fields' => 'ids', 'number' => -1 ) );** – lista de sites (objetos ou IDs).
- **get_blog_details( $blog_id );** – objeto com domain, path, etc.
- **get_blog_option( $blog_id, 'option_name' );** – opção de um site específico (sem switch).
- **wp_insert_site** – hook quando novo site é criado; **wp_delete_site** – quando site é deletado.
- **Plugin header:** `Network: true` – permite ativação em toda a rede.
- **muplugins_loaded** – hook que dispara antes de plugins_loaded; usar para código que deve rodar só em rede.

---

## Site vs Network Options

- **get_option( $key )** – opção do **site atual** (wp_X_options).
- **get_site_option( $key )** – opção da **rede** (wp_sitemeta).
- **update_site_option( $key, $value );** – salvar na rede.
- **delete_site_option( $key );**
- Network admin: **is_network_admin();** – true no painel “Rede”. **add_action( 'network_admin_menu', ... );** – menu no admin da rede.

---

## i18n – Funções de tradução

- **Text domain:** segundo parâmetro em todas as funções (ex.: `'meu-plugin'`).
- **__( 'Texto', 'meu-plugin' );** – retorna traduzido.
- **_e( 'Texto', 'meu-plugin' );** – echo da tradução.
- **esc_html__( 'Texto', 'meu-plugin' );** – retorna escapado para HTML; **esc_attr__( ... );** para atributos.
- **_x( 'Texto', 'contexto', 'meu-plugin' );** – com contexto para tradutores.
- **_n( '1 item', '%d items', $count, 'meu-plugin' );** – singular/plural; usar com **sprintf( _n(...), $count )** ou **number_format_i18n( $count )**.
- **_nx( '1 item', '%d items', $count, 'contexto', 'meu-plugin' );** – plural com contexto.
- **load_plugin_textdomain( $domain, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );** – em **plugins_loaded**.

---

## POT, PO, MO

- **POT** – template (strings a traduzir). **PO** – tradução por locale. **MO** – compilado (WordPress usa).
- **wp i18n make-pot . languages/meu-plugin.pot --domain=meu-plugin** – gerar POT (WP-CLI).
- Estrutura: `languages/meu-plugin.pot`, `meu-plugin-pt_BR.po`, `meu-plugin-pt_BR.mo`.

---

## JavaScript e RTL

- **wp_localize_script( 'handle', 'varJs', array( 'chave' => __( 'Texto', 'meu-plugin' ) ) );** – passar strings traduzidas para JS.
- **wp_set_script_translations( 'handle', 'meu-plugin', path_to_languages );** – traduções para scripts que usam wp.i18n.
- **wp_style_add_data( 'meu-plugin-style', 'rtl', 'replace' );** – WordPress pode carregar -rtl.css automaticamente.
- **is_rtl();** – true para idiomas direita-para-esquerda; usar para condicionar markup ou classes.
