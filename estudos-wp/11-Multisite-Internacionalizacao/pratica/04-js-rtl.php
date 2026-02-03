<?php
/**
 * Exemplo 04: JavaScript i18n e suporte RTL
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   wp_localize_script( 'handle', 'varJs', array( 'chave' => __( 'Texto', 'domain' ) ) );
 *   wp_set_script_translations( 'handle', 'domain', path_to_languages ) para wp.i18n.
 *   wp_style_add_data( 'handle', 'rtl', 'replace' ); is_rtl() para condicionar.
 *
 * @package EstudosWP
 * @subpackage 11-Multisite-Internacionalizacao
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enfileirar script com strings traduzidas e suporte RTL.
 */
class Estudos_WP_JS_RTL {

	private $domain = 'meu-plugin';

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 11 );
	}

	public function enqueue_scripts() {
		$handle = 'meu-plugin-script';
		$url    = defined( 'MEU_PLUGIN_URL' ) ? MEU_PLUGIN_URL : plugin_dir_url( __FILE__ ) . '../';
		$ver    = defined( 'MEU_PLUGIN_VERSION' ) ? MEU_PLUGIN_VERSION : '1.0.0';

		wp_enqueue_script(
			$handle,
			$url . 'assets/js/script.js',
			array( 'jquery' ),
			$ver,
			true
		);

		wp_localize_script(
			$handle,
			'meuPluginI18n',
			array(
				'confirmDelete' => __( 'Tem certeza que deseja excluir?', 'meu-plugin' ),
				'saving'        => __( 'Salvando...', 'meu-plugin' ),
				'saved'         => __( 'Salvo!', 'meu-plugin' ),
				'error'         => __( 'Ocorreu um erro. Tente novamente.', 'meu-plugin' ),
				'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
			)
		);

		// Alternativa: traduções para scripts que usam wp.i18n (Gutenberg, etc.)
		// wp_set_script_translations( $handle, $this->domain, dirname( __FILE__ ) . '../languages' );
	}

	public function enqueue_styles() {
		$handle = 'meu-plugin-style';
		$url    = defined( 'MEU_PLUGIN_URL' ) ? MEU_PLUGIN_URL : plugin_dir_url( __FILE__ ) . '../';
		$ver    = defined( 'MEU_PLUGIN_VERSION' ) ? MEU_PLUGIN_VERSION : '1.0.0';

		wp_enqueue_style(
			$handle,
			$url . 'assets/css/style.css',
			array(),
			$ver
		);

		// WordPress pode carregar automaticamente style-rtl.css se existir
		wp_style_add_data( $handle, 'rtl', 'replace' );

		// Ou carregar RTL manualmente
		if ( is_rtl() ) {
			wp_enqueue_style(
				'meu-plugin-style-rtl',
				$url . 'assets/css/style-rtl.css',
				array( $handle ),
				$ver
			);
		}
	}
}

new Estudos_WP_JS_RTL();
