<?php
/**
 * Exemplo 03: Nonces e capabilities
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   wp_nonce_field( 'action', 'name' ); wp_verify_nonce( $_POST['name'], 'action' );
 *   wp_nonce_url( $url, 'action', 'param' ); wp_create_nonce( 'action' ) para AJAX.
 *   current_user_can( 'capability' ); current_user_can( 'edit_post', $post_id );
 *   REST: permission_callback => function() { return current_user_can( '...' ); }
 *
 * @package EstudosWP
 * @subpackage 12-Seguranca-Boas-Praticas
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Formulário com nonce e verificação de capability.
 */
class Estudos_WP_Nonces_Capabilities {

	const NONCE_ACTION = 'meu_plugin_save';
	const NONCE_NAME   = 'meu_plugin_nonce';

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_post_meu_plugin_save_settings', array( $this, 'process_form' ) );
		add_action( 'rest_api_init', array( $this, 'register_rest' ) );
	}

	public function add_menu() {
		add_options_page(
			__( 'Meu Plugin', 'meu-plugin' ),
			__( 'Meu Plugin', 'meu-plugin' ),
			'manage_options',
			'meu-plugin-settings',
			array( $this, 'render_form' )
		);
	}

	/**
	 * Formulário com nonce.
	 */
	public function render_form() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Sem permissão.', 'meu-plugin' ) );
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Configurações', 'meu-plugin' ); ?></h1>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( self::NONCE_ACTION, self::NONCE_NAME ); ?>
				<input type="hidden" name="action" value="meu_plugin_save_settings">
				<p>
					<label for="nome"><?php esc_html_e( 'Nome', 'meu-plugin' ); ?></label>
					<input type="text" name="nome" id="nome" value="<?php echo esc_attr( get_option( 'meu_plugin_nome', '' ) ); ?>">
				</p>
				<?php submit_button( __( 'Salvar', 'meu-plugin' ) ); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Processar formulário: verificar nonce e capability.
	 */
	public function process_form() {
		if ( ! isset( $_POST[ self::NONCE_NAME ] ) ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ self::NONCE_NAME ] ) ), self::NONCE_ACTION ) ) {
			wp_die( esc_html__( 'Verificação de segurança falhou.', 'meu-plugin' ) );
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Sem permissão.', 'meu-plugin' ) );
		}
		$nome = isset( $_POST['nome'] ) ? sanitize_text_field( wp_unslash( $_POST['nome'] ) ) : '';
		update_option( 'meu_plugin_nome', $nome );
		wp_safe_redirect( add_query_arg( 'updated', 1, admin_url( 'options-general.php?page=meu-plugin-settings' ) ) );
		exit;
	}

	/**
	 * REST endpoint com permission_callback.
	 */
	public function register_rest() {
		register_rest_route(
			'meu-plugin/v1',
			'/config',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_get_config' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);
	}

	public function rest_get_config() {
		return new WP_REST_Response(
			array(
				'nome' => get_option( 'meu_plugin_nome', '' ),
			),
			200
		);
	}
}

new Estudos_WP_Nonces_Capabilities();
