<?php
/**
 * Exemplo 04: Admin notices e enfileirar scripts no admin
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   admin_notices: echo '<div class="notice notice-success is-dismissible"><p>...</p></div>'; tipos: success, error, warning, info.
 *   admin_enqueue_scripts( $hook ): carregar só na página (ex: $hook === 'toplevel_page_meu-plugin').
 *   wp_enqueue_style/script; wp_enqueue_media; wp_color_picker; wp_localize_script( $handle, 'obj', [ 'ajaxUrl', 'nonce' ] ).
 *
 * @package EstudosWP
 * @subpackage 04-Configuracoes-Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ========== ADMIN NOTICES ==========

add_action( 'admin_notices', 'estudos_wp_admin_exemplo_notice' );

function estudos_wp_admin_exemplo_notice() {
	// Exibir apenas em determinada página (ex.: após salvar)
	if ( ! isset( $_GET['estudos_wp_notice'] ) ) {
		return;
	}

	$tipo = sanitize_text_field( wp_unslash( $_GET['estudos_wp_notice'] ) );
	if ( ! in_array( $tipo, array( 'success', 'error', 'warning', 'info' ), true ) ) {
		return;
	}

	$mensagens = array(
		'success' => 'Configurações salvas com sucesso.',
		'error'   => 'Ocorreu um erro ao salvar.',
		'warning' => 'Revise os dados antes de continuar.',
		'info'    => 'Nova versão disponível.',
	);
	$msg = isset( $mensagens[ $tipo ] ) ? $mensagens[ $tipo ] : '';
	if ( $msg === '' ) {
		return;
	}
	?>
	<div class="notice notice-<?php echo esc_attr( $tipo ); ?> is-dismissible">
		<p><?php echo esc_html( $msg ); ?></p>
	</div>
	<?php
}

/**
 * Helper: adicionar notice (ex.: após processar form).
 *
 * @param string $message Mensagem.
 * @param string $type    success|error|warning|info.
 */
function estudos_wp_admin_add_notice( $message, $type = 'success' ) {
	add_action( 'admin_notices', function() use ( $message, $type ) {
		$type = in_array( $type, array( 'success', 'error', 'warning', 'info' ), true ) ? $type : 'info';
		?>
		<div class="notice notice-<?php echo esc_attr( $type ); ?> is-dismissible">
			<p><?php echo wp_kses_post( $message ); ?></p>
		</div>
		<?php
	} );
}

// ========== ADMIN SCRIPTS (carregar só na página do plugin) ==========

add_action( 'admin_enqueue_scripts', 'estudos_wp_admin_enqueue_scripts' );

function estudos_wp_admin_enqueue_scripts( $hook ) {
	// Carregar apenas na página principal do plugin (ajuste o slug conforme seu menu)
	if ( $hook !== 'toplevel_page_estudos-wp-config' ) {
		return;
	}

	wp_enqueue_style(
		'estudos-wp-admin',
		plugins_url( 'admin.css', __FILE__ ),
		array(),
		'1.0'
	);

	wp_enqueue_script(
		'estudos-wp-admin',
		plugins_url( 'admin.js', __FILE__ ),
		array( 'jquery' ),
		'1.0',
		true
	);

	wp_localize_script(
		'estudos-wp-admin',
		'estudosWpAdmin',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'estudos_wp_admin' ),
		)
	);

	// Opcional: color picker
	// wp_enqueue_style( 'wp-color-picker' );
	// wp_enqueue_script( 'wp-color-picker' );
	// Opcional: media uploader
	// wp_enqueue_media();
}
