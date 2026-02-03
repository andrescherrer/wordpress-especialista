<?php
/**
 * Exemplo 02: Escape de saída por contexto
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   esc_html() para texto em HTML; esc_attr() para atributos; esc_url() para URLs.
 *   wp_kses( $content, $allowed_html ) para HTML com tags permitidas.
 *   wp_json_encode( $data ) para dados em JavaScript (não usar json_encode direto).
 *
 * @package EstudosWP
 * @subpackage 12-Seguranca-Boas-Praticas
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Exemplos de escape em diferentes contextos.
 */
class Estudos_WP_Escape_Output {

	/**
	 * Contexto: texto dentro de HTML.
	 */
	public function output_title() {
		$title = get_the_title();
		echo esc_html( $title );
	}

	/**
	 * Contexto: atributo HTML (value, title, data-*).
	 */
	public function output_input_value() {
		$value = get_option( 'meu_plugin_data', '' );
		echo '<input type="text" value="' . esc_attr( $value ) . '" name="campo">';
	}

	/**
	 * Contexto: URL em href ou src.
	 */
	public function output_link() {
		$url = get_option( 'meu_plugin_url', '#' );
		echo '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Link', 'meu-plugin' ) . '</a>';
	}

	/**
	 * Contexto: dados para JavaScript (evita XSS e quebra de string).
	 */
	public function output_js_data() {
		$data = array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'meu_plugin_ajax' ),
		);
		echo '<script>var meuPluginData = ' . wp_json_encode( $data ) . ';</script>';
	}

	/**
	 * Contexto: HTML com tags permitidas (conteúdo rico).
	 */
	public function output_allowed_html() {
		$content = get_option( 'meu_plugin_content', '' );
		$allowed = array(
			'a'      => array(
				'href'  => true,
				'title' => true,
			),
			'strong' => array(),
			'em'     => array(),
			'p'      => array(),
			'br'     => array(),
		);
		echo wp_kses( $content, $allowed );
	}
}
