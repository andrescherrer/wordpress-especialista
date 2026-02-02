<?php
/**
 * Exemplo 02: Registrar campos e sanitize (text, email, select, checkbox)
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   add_settings_field( $id, $title, $callback, $page, $section ).
 *   name="option_name[key]"; valor com get_option( 'option_name' )[key]; esc_attr(), checked(), selected().
 *   sanitize_callback: sanitize_text_field, sanitize_email, esc_url_raw; checkbox isset() ? 1 : 0.
 *
 * @package EstudosWP
 * @subpackage 04-Configuracoes-Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Supondo option_name = 'meu_plugin_opcoes', page_slug = 'meu-plugin-opcoes', section = 'meu_plugin_sec'

/**
 * Exemplo: registrar vários tipos de campo.
 */
function estudos_wp_admin_register_campos_exemplo() {
	$page    = 'meu-plugin-opcoes';
	$section = 'meu_plugin_sec';
	$option  = 'meu_plugin_opcoes';

	register_setting(
		'meu_plugin_group',
		$option,
		array(
			'type'              => 'array',
			'sanitize_callback' => 'estudos_wp_admin_sanitize_opcoes',
		)
	);

	add_settings_field(
		'meu_plugin_email',
		'Email',
		'estudos_wp_admin_render_email',
		$page,
		$section,
		array( 'label_for' => 'meu_plugin_email' )
	);

	add_settings_field(
		'meu_plugin_url',
		'URL',
		'estudos_wp_admin_render_url',
		$page,
		$section,
		array( 'label_for' => 'meu_plugin_url' )
	);

	add_settings_field(
		'meu_plugin_tipo',
		'Tipo',
		'estudos_wp_admin_render_select',
		$page,
		$section,
		array( 'label_for' => 'meu_plugin_tipo' )
	);

	add_settings_field(
		'meu_plugin_notificar',
		'Notificar',
		'estudos_wp_admin_render_checkbox',
		$page,
		$section
	);
}

function estudos_wp_admin_render_email() {
	$opts = get_option( 'meu_plugin_opcoes', array() );
	$val  = isset( $opts['email'] ) ? $opts['email'] : '';
	?>
	<input type="email"
	       id="meu_plugin_email"
	       name="meu_plugin_opcoes[email]"
	       value="<?php echo esc_attr( $val ); ?>"
	       class="regular-text">
	<?php
}

function estudos_wp_admin_render_url() {
	$opts = get_option( 'meu_plugin_opcoes', array() );
	$val  = isset( $opts['url'] ) ? $opts['url'] : '';
	?>
	<input type="url"
	       id="meu_plugin_url"
	       name="meu_plugin_opcoes[url]"
	       value="<?php echo esc_attr( $val ); ?>"
	       class="large-text">
	<?php
}

function estudos_wp_admin_render_select() {
	$opts  = get_option( 'meu_plugin_opcoes', array() );
	$val   = isset( $opts['tipo'] ) ? $opts['tipo'] : '';
	$list  = array( 'opcao_a' => 'Opção A', 'opcao_b' => 'Opção B', 'opcao_c' => 'Opção C' );
	?>
	<select id="meu_plugin_tipo" name="meu_plugin_opcoes[tipo]">
		<option value="">— Selecione —</option>
		<?php foreach ( $list as $k => $label ) : ?>
			<option value="<?php echo esc_attr( $k ); ?>" <?php selected( $val, $k ); ?>><?php echo esc_html( $label ); ?></option>
		<?php endforeach; ?>
	</select>
	<?php
}

function estudos_wp_admin_render_checkbox() {
	$opts = get_option( 'meu_plugin_opcoes', array() );
	$val  = isset( $opts['notificar'] ) ? (int) $opts['notificar'] : 0;
	?>
	<label for="meu_plugin_notificar">
		<input type="checkbox"
		       id="meu_plugin_notificar"
		       name="meu_plugin_opcoes[notificar]"
		       value="1"
		       <?php checked( 1, $val ); ?>>
		Enviar notificações
	</label>
	<?php
}

function estudos_wp_admin_sanitize_opcoes( $input ) {
	if ( ! is_array( $input ) ) {
		return get_option( 'meu_plugin_opcoes', array() );
	}
	$out = array();
	$out['email']     = isset( $input['email'] ) ? sanitize_email( $input['email'] ) : '';
	$out['url']       = isset( $input['url'] ) ? esc_url_raw( $input['url'] ) : '';
	$out['tipo']      = isset( $input['tipo'] ) ? sanitize_text_field( $input['tipo'] ) : '';
	$out['notificar'] = isset( $input['notificar'] ) ? 1 : 0;
	return $out;
}

// Para usar: chame estudos_wp_admin_register_campos_exemplo() dentro do callback de admin_init
// e garanta que add_settings_section( 'meu_plugin_sec', ... ) e add_menu_page( ..., 'meu-plugin-opcoes', ... ) existam.
