<?php
/**
 * Exemplo 02: Network options e admin de rede – get_site_option, update_site_option, network_admin_menu
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   get_site_option( $key, $default ); update_site_option( $key, $value );
 *   add_action( 'network_admin_menu', $callback ); is_network_admin();
 *   admin_post_* para salvar; wp_nonce_field e wp_verify_nonce.
 *
 * @package EstudosWP
 * @subpackage 11-Multisite-Internacionalizacao
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Menu e configurações no Network Admin.
 */
class Estudos_WP_Network_Options_Admin {

	const OPTION_KEY = 'meu_plugin_network_settings';
	const NONCE_ACTION = 'meu_plugin_network_save';

	public function __construct() {
		if ( ! is_multisite() ) {
			return;
		}
		add_action( 'network_admin_menu', array( $this, 'add_network_menu' ) );
		add_action( 'admin_post_meu_plugin_network_save', array( $this, 'save_settings' ) );
	}

	public function add_network_menu() {
		add_menu_page(
			__( 'Meu Plugin', 'meu-plugin' ),
			__( 'Meu Plugin', 'meu-plugin' ),
			'manage_network',
			'meu-plugin-network',
			array( $this, 'render_settings' ),
			'dashicons-admin-settings',
			30
		);
	}

	public function render_settings() {
		if ( ! current_user_can( 'manage_network' ) ) {
			wp_die( esc_html__( 'Sem permissão.', 'meu-plugin' ) );
		}

		$settings = get_site_option( self::OPTION_KEY, array() );
		$api_key  = isset( $settings['api_key'] ) ? $settings['api_key'] : '';
		$enabled  = ! empty( $settings['enable_feature'] );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Configurações da rede', 'meu-plugin' ); ?></h1>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( self::NONCE_ACTION ); ?>
				<input type="hidden" name="action" value="meu_plugin_network_save">

				<table class="form-table">
					<tr>
						<th scope="row"><label for="api_key"><?php esc_html_e( 'API Key', 'meu-plugin' ); ?></label></th>
						<td>
							<input type="text" name="api_key" id="api_key" value="<?php echo esc_attr( $api_key ); ?>" class="regular-text">
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="enable_feature"><?php esc_html_e( 'Habilitar recurso', 'meu-plugin' ); ?></label></th>
						<td>
							<input type="checkbox" name="enable_feature" id="enable_feature" value="1" <?php checked( $enabled ); ?>>
							<p class="description"><?php esc_html_e( 'Aplicar a todos os sites da rede.', 'meu-plugin' ); ?></p>
						</td>
					</tr>
				</table>

				<?php submit_button( __( 'Salvar', 'meu-plugin' ) ); ?>
			</form>

			<h2><?php esc_html_e( 'Sites', 'meu-plugin' ); ?></h2>
			<?php $this->render_sites_list(); ?>
		</div>
		<?php
	}

	private function render_sites_list() {
		$sites = get_sites( array( 'number' => 50 ) );
		if ( empty( $sites ) ) {
			echo '<p>' . esc_html__( 'Nenhum site.', 'meu-plugin' ) . '</p>';
			return;
		}
		echo '<table class="wp-list-table widefat"><thead><tr>';
		echo '<th>' . esc_html__( 'Site', 'meu-plugin' ) . '</th><th>' . esc_html__( 'URL', 'meu-plugin' ) . '</th></tr></thead><tbody>';
		foreach ( $sites as $site ) {
			$name = get_blog_option( $site->blog_id, 'blogname', '' );
			$url  = get_site_url( $site->blog_id );
			echo '<tr><td>' . esc_html( $name ) . '</td><td><a href="' . esc_url( $url ) . '" target="_blank">' . esc_html( $url ) . '</a></td></tr>';
		}
		echo '</tbody></table>';
	}

	public function save_settings() {
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), self::NONCE_ACTION ) ) {
			wp_die( esc_html__( 'Verificação de segurança falhou.', 'meu-plugin' ) );
		}
		if ( ! current_user_can( 'manage_network' ) ) {
			wp_die( esc_html__( 'Sem permissão.', 'meu-plugin' ) );
		}

		$settings = array(
			'api_key'        => isset( $_POST['api_key'] ) ? sanitize_text_field( wp_unslash( $_POST['api_key'] ) ) : '',
			'enable_feature' => isset( $_POST['enable_feature'] ) ? 1 : 0,
		);
		update_site_option( self::OPTION_KEY, $settings );

		wp_safe_redirect( add_query_arg( 'updated', 1, network_admin_url( 'admin.php?page=meu-plugin-network' ) ) );
		exit;
	}
}

new Estudos_WP_Network_Options_Admin();
