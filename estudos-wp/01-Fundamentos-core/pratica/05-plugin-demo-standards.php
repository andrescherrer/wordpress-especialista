<?php
/**
 * Plugin Name: Estudos WP – Demo Fundamentos Core
 * Description: Plugin de exemplo aplicando WordPress Coding Standards e conceitos da Fase 1.
 * Version: 1.0.0
 * Author: Estudos WordPress
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: estudos-wp-fundamentos
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 *
 * REFERÊNCIA RÁPIDA (Coding Standards):
 *   Classes: My_Plugin_Class  |  Funções: meu_plugin_funcao  |  Constantes: MEU_PLUGIN_VER
 *   Header: Plugin Name, Version, Text Domain; if (!defined('ABSPATH')) exit;
 *   Hooks no plugins_loaded ou init; activation/deactivation com register_*_hook.
 *
 * @package EstudosWP
 * @subpackage 01-Fundamentos-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Constantes no padrão UPPER_SNAKE_CASE.
define( 'ESTUDOS_WP_FUNDAMENTOS_VERSION', '1.0.0' );
define( 'ESTUDOS_WP_FUNDAMENTOS_PATH', plugin_dir_path( __FILE__ ) );
define( 'ESTUDOS_WP_FUNDAMENTOS_URL', plugin_dir_url( __FILE__ ) );

/**
 * Classe principal do plugin (PascalCase com underscores).
 */
class Estudos_WP_Fundamentos_Plugin {

	/**
	 * Instância única.
	 *
	 * @var Estudos_WP_Fundamentos_Plugin
	 */
	private static $instance = null;

	/**
	 * Retorna a instância.
	 *
	 * @since 1.0.0
	 * @return Estudos_WP_Fundamentos_Plugin
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Construtor: registra hooks no init.
	 */
	private function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'wp_footer', array( $this, 'footer_demo' ), 10 );
		add_filter( 'the_title', array( $this, 'filter_title_demo' ), 10, 2 );
	}

	/**
	 * Inicialização no hook init.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		load_plugin_textdomain( 'estudos-wp-fundamentos', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		// Registrar CPT, taxonomies, etc.
	}

	/**
	 * Action de exemplo no rodapé.
	 *
	 * @since 1.0.0
	 */
	public function footer_demo() {
		if ( current_user_can( 'manage_options' ) ) {
			echo '<!-- Estudos WP Fundamentos v' . esc_attr( ESTUDOS_WP_FUNDAMENTOS_VERSION ) . ' -->';
		}
	}

	/**
	 * Filter de exemplo no título.
	 *
	 * @since 1.0.0
	 * @param string $title   Título atual.
	 * @param int    $post_id ID do post (opcional).
	 * @return string
	 */
	public function filter_title_demo( $title, $post_id = null ) {
		if ( is_admin() || empty( $title ) ) {
			return $title;
		}
		return $title . ' ';
	}
}

/**
 * Ativação: criar tabelas ou opções se necessário.
 */
function estudos_wp_fundamentos_activate() {
	// flush_rewrite_rules() se registrar CPT na ativação
}
register_activation_hook( __FILE__, 'estudos_wp_fundamentos_activate' );

/**
 * Desativação: limpar transientes, etc.
 */
function estudos_wp_fundamentos_deactivate() {
	// delete_option( 'estudos_wp_fundamentos_config' );
}
register_deactivation_hook( __FILE__, 'estudos_wp_fundamentos_deactivate' );

/**
 * Inicializar plugin no plugins_loaded (seguro para usar funções WP).
 */
add_action( 'plugins_loaded', function() {
	Estudos_WP_Fundamentos_Plugin::get_instance();
} );
