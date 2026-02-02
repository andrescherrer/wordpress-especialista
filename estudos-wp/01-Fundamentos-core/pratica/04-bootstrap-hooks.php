<?php
/**
 * Exemplo 04: Ordem de Bootstrap e Hooks Corretos
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   plugins_loaded  → dependências de outros plugins
 *   after_setup_theme → add_theme_support (thumbnails, menus)
 *   init            → register_post_type, register_taxonomy (CPT, taxonomias)
 *   wp_loaded       → query/usuário prontos
 *   wp              → get_queried_object(); lógica por tipo de página
 *   Nunca: get_post(), get_users() etc. em wp-config.php ou no nível raiz do arquivo.
 *
 * @package EstudosWP
 * @subpackage 01-Fundamentos-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// =============================================================================
// ERRADO (não fazer): código no nível raiz usando get_post(), get_users(), etc.
// =============================================================================
// $post = get_post( 1 ); // Pode falhar dependendo de quando o arquivo é carregado!

// =============================================================================
// CERTO: usar hooks para garantir que o WordPress já carregou
// =============================================================================

// muplugins_loaded – muito cedo; apenas constantes ou coisas que não dependem de WP
// add_action( 'muplugins_loaded', function() {
//     define( 'ESTUDOS_WP_VER', '1.0' );
// } );

// plugins_loaded – todos os plugins carregados; ideal para checar dependências
add_action( 'plugins_loaded', 'estudos_wp_plugins_loaded' );

function estudos_wp_plugins_loaded() {
	// Exemplo: carregar tradução ou checar se outro plugin está ativo
	// if ( ! class_exists( 'WooCommerce' ) ) return;
	load_plugin_textdomain( 'estudos-wp', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

// after_setup_theme – suporte de tema (thumbnails, menus); tema já carregado
add_action( 'after_setup_theme', 'estudos_wp_after_setup_theme' );

function estudos_wp_after_setup_theme() {
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form' ) );
}

// init – registrar CPT, taxonomias, post status; WordPress inicializado
add_action( 'init', 'estudos_wp_init' );

function estudos_wp_init() {
	// Aqui get_post(), get_users(), register_post_type() etc. estão disponíveis
	// register_post_type( 'livro', array( 'public' => true, 'label' => 'Livros' ) );
	// register_taxonomy( 'genero', 'livro', array( 'label' => 'Gênero' ) );
}

// wp_loaded – após query de URL; usuário autenticado disponível
add_action( 'wp_loaded', 'estudos_wp_wp_loaded' );

function estudos_wp_wp_loaded() {
	// Bom para lógica que depende de estar logado ou da URL
	// if ( is_user_logged_in() ) { ... }
}

// wp – após a query principal ter sido executada; get_queried_object() disponível
add_action( 'wp', 'estudos_wp_wp' );

function estudos_wp_wp() {
	if ( is_single() ) {
		$post = get_queried_object();
		// Fazer algo apenas em single post
	}
}

// =============================================================================
// ENFILEIRAR SCRIPTS/ESTILOS (frontend e admin)
// =============================================================================

add_action( 'wp_enqueue_scripts', 'estudos_wp_enqueue_front' );

function estudos_wp_enqueue_front() {
	// Só no front; não em admin
	wp_enqueue_style(
		'estudos-wp-front',
		get_stylesheet_directory_uri() . '/estudos-wp.css',
		array(),
		'1.0'
	);
}

add_action( 'admin_enqueue_scripts', 'estudos_wp_enqueue_admin', 10, 1 );

function estudos_wp_enqueue_admin( $hook ) {
	// Carregar apenas na página que precisar
	if ( $hook === 'index.php' ) {
		wp_enqueue_script( 'estudos-wp-admin', get_stylesheet_directory_uri() . '/estudos-wp-admin.js', array( 'jquery' ), '1.0', true );
	}
}

// =============================================================================
// RESUMO: quando usar cada hook
// =============================================================================
/*
muplugins_loaded  → constante, versão; NÃO use get_post()
plugins_loaded   → dependência de outros plugins, i18n
after_setup_theme → add_theme_support
init             → register_post_type, register_taxonomy, rewrite
wp_loaded        → usuário, query de URL pronta
wp               → get_queried_object(), lógica por tipo de página
wp_enqueue_scripts → CSS/JS no front
admin_enqueue_scripts → CSS/JS no admin
*/
