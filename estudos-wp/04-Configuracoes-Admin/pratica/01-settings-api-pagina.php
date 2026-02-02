<?php
/**
 * Exemplo 01: Settings API – página de configuração completa
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   admin_menu → add_menu_page, add_submenu_page ou add_options_page.
 *   admin_init → register_setting, add_settings_section, add_settings_field.
 *   Form: action="options.php", settings_fields( $option_group ), do_settings_sections( $page_slug ), submit_button().
 *   option_group = mesmo em register_setting e settings_fields; option_name = get_option( '...' ).
 *
 * @package EstudosWP
 * @subpackage 04-Configuracoes-Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'estudos_wp_admin_add_menu' );
add_action( 'admin_init', 'estudos_wp_admin_register_settings' );

define( 'ESTUDOS_WP_ADMIN_OPTION_GROUP', 'estudos_wp_settings_group' );
define( 'ESTUDOS_WP_ADMIN_OPTION_NAME', 'estudos_wp_settings' );
define( 'ESTUDOS_WP_ADMIN_PAGE_SLUG', 'estudos-wp-config' );

function estudos_wp_admin_add_menu() {
	add_menu_page(
		'Estudos WP Config',
		'Estudos WP',
		'manage_options',
		ESTUDOS_WP_ADMIN_PAGE_SLUG,
		'estudos_wp_admin_render_page',
		'dashicons-admin-generic',
		99
	);

	add_submenu_page(
		ESTUDOS_WP_ADMIN_PAGE_SLUG,
		'Configurações',
		'Configurações',
		'manage_options',
		ESTUDOS_WP_ADMIN_PAGE_SLUG,
		'estudos_wp_admin_render_page'
	);
}

function estudos_wp_admin_register_settings() {
	register_setting(
		ESTUDOS_WP_ADMIN_OPTION_GROUP,
		ESTUDOS_WP_ADMIN_OPTION_NAME,
		array(
			'type'              => 'array',
			'sanitize_callback' => 'estudos_wp_admin_sanitize_settings',
		)
	);

	add_settings_section(
		'estudos_wp_section_geral',
		'Configurações gerais',
		function() {
			echo '<p>Opções gerais do plugin.</p>';
		},
		ESTUDOS_WP_ADMIN_PAGE_SLUG
	);

	add_settings_field(
		'estudos_wp_titulo',
		'Título',
		'estudos_wp_admin_render_field_titulo',
		ESTUDOS_WP_ADMIN_PAGE_SLUG,
		'estudos_wp_section_geral'
	);

	add_settings_field(
		'estudos_wp_ativo',
		'Ativar',
		'estudos_wp_admin_render_field_ativo',
		ESTUDOS_WP_ADMIN_PAGE_SLUG,
		'estudos_wp_section_geral'
	);
}

function estudos_wp_admin_render_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<?php settings_errors( ESTUDOS_WP_ADMIN_OPTION_NAME ); ?>

		<form method="post" action="options.php">
			<?php
			settings_fields( ESTUDOS_WP_ADMIN_OPTION_GROUP );
			do_settings_sections( ESTUDOS_WP_ADMIN_PAGE_SLUG );
			submit_button( 'Salvar' );
			?>
		</form>
	</div>
	<?php
}

function estudos_wp_admin_render_field_titulo() {
	$options = get_option( ESTUDOS_WP_ADMIN_OPTION_NAME, array() );
	$valor   = isset( $options['titulo'] ) ? $options['titulo'] : '';
	?>
	<input type="text"
	       id="estudos_wp_titulo"
	       name="<?php echo esc_attr( ESTUDOS_WP_ADMIN_OPTION_NAME . '[titulo]' ); ?>"
	       value="<?php echo esc_attr( $valor ); ?>"
	       class="regular-text">
	<p class="description">Título exibido no front.</p>
	<?php
}

function estudos_wp_admin_render_field_ativo() {
	$options = get_option( ESTUDOS_WP_ADMIN_OPTION_NAME, array() );
	$ativo   = isset( $options['ativo'] ) ? (int) $options['ativo'] : 0;
	?>
	<label for="estudos_wp_ativo">
		<input type="checkbox"
		       id="estudos_wp_ativo"
		       name="<?php echo esc_attr( ESTUDOS_WP_ADMIN_OPTION_NAME . '[ativo]' ); ?>"
		       value="1"
		       <?php checked( 1, $ativo ); ?>>
		Ativar funcionalidade
	</label>
	<?php
}

function estudos_wp_admin_sanitize_settings( $input ) {
	if ( ! is_array( $input ) ) {
		return array();
	}
	$out = array();
	$out['titulo'] = isset( $input['titulo'] ) ? sanitize_text_field( $input['titulo'] ) : '';
	$out['ativo']  = isset( $input['ativo'] ) ? 1 : 0;
	return $out;
}
