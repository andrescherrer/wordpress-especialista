<?php
/**
 * Exemplo 03: i18n – load_plugin_textdomain, __(), _e(), _x(), _n(), esc_attr_e
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   load_plugin_textdomain( $domain, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
 *   __( 'Texto', 'domain' ) retorna; _e() echo; esc_attr_e / esc_html_e para atributo/HTML.
 *   _x( 'Texto', 'contexto', 'domain' ); _n( '1 item', '%d items', $count, 'domain' ); _nx() plural com contexto.
 *   sprintf( __( 'Olá %s', 'domain' ), $nome ); number_format_i18n( $n ).
 *
 * @package EstudosWP
 * @subpackage 11-Multisite-Internacionalizacao
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Carregar text domain e exemplos de uso das funções de tradução.
 */
class Estudos_WP_I18n_Funcoes {

	private $domain = 'meu-plugin';

	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
	}

	public function load_textdomain() {
		load_plugin_textdomain(
			$this->domain,
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/languages/'
		);
	}

	/**
	 * Exemplo: página de configurações totalmente traduzida.
	 */
	public function render_settings_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Configurações do Plugin', 'meu-plugin' ); ?></h1>

			<form method="post">
				<p>
					<label for="nome"><?php esc_html_e( 'Nome:', 'meu-plugin' ); ?></label>
					<input type="text" name="nome" id="nome" placeholder="<?php esc_attr_e( 'Digite seu nome', 'meu-plugin' ); ?>">
				</p>
				<p>
					<label for="email"><?php esc_html_e( 'E-mail:', 'meu-plugin' ); ?></label>
					<input type="email" name="email" id="email">
				</p>
				<?php submit_button( __( 'Salvar alterações', 'meu-plugin' ) ); ?>
			</form>

			<p class="description">
				<?php
				printf(
					/* translators: %s: link para documentação */
					esc_html__( 'Visite nossa %s para mais informações.', 'meu-plugin' ),
					'<a href="' . esc_url( 'https://exemplo.com' ) . '" target="_blank" rel="noopener">' . esc_html__( 'documentação', 'meu-plugin' ) . '</a>'
				);
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * Exemplo: string com contexto (evita ambiguidade para tradutores).
	 */
	public function get_label_post() {
		return _x( 'Post', 'tipo de conteúdo', 'meu-plugin' );
	}

	/**
	 * Exemplo: plural (1 item vs N items).
	 *
	 * @param int $count Quantidade.
	 * @return string
	 */
	public function get_stock_message( $count ) {
		return sprintf(
			_n( '%d item em estoque', '%d itens em estoque', $count, 'meu-plugin' ),
			number_format_i18n( $count )
		);
	}

	/**
	 * Exemplo: plural com contexto.
	 *
	 * @param int $count Quantidade.
	 * @return string
	 */
	public function get_notification_message( $count ) {
		return sprintf(
			_nx(
				'Você tem 1 notificação',
				'Você tem %d notificações',
				$count,
				'mensagem de notificação',
				'meu-plugin'
			),
			number_format_i18n( $count )
		);
	}
}
