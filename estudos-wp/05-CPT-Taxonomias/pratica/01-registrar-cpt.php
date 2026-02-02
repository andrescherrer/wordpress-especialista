<?php
/**
 * Exemplo 01: Registrar Custom Post Type
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   add_action( 'init', callback ); register_post_type( $post_type, $args ).
 *   Args: label, labels, public, show_ui, show_in_rest, rewrite (slug), has_archive, hierarchical, menu_position, menu_icon, supports, capabilities, map_meta_cap.
 *   hierarchical true = tipo página (pai/filho); false = tipo post.
 *
 * @package EstudosWP
 * @subpackage 05-CPT-Taxonomias
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'estudos_wp_register_cpt_livro' );

function estudos_wp_register_cpt_livro() {
	$labels = array(
		'name'               => 'Livros',
		'singular_name'      => 'Livro',
		'menu_name'          => 'Livros',
		'add_new'            => 'Adicionar novo',
		'add_new_item'       => 'Adicionar novo Livro',
		'edit_item'          => 'Editar Livro',
		'new_item'           => 'Novo Livro',
		'view_item'          => 'Ver Livro',
		'search_items'       => 'Buscar Livros',
		'not_found'          => 'Nenhum livro encontrado',
		'not_found_in_trash' => 'Nenhum livro na lixeira',
		'all_items'          => 'Todos os Livros',
	);

	$args = array(
		'label'               => 'Livros',
		'labels'              => $labels,
		'description'         => 'Catálogo de livros',
		'public'              => true,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_rest'        => true,
		'query_var'           => true,
		'rewrite'             => array(
			'slug'       => 'livros',
			'with_front' => true,
		),
		'has_archive'         => 'livros',
		'hierarchical'        => false,
		'menu_position'       => 20,
		'menu_icon'           => 'dashicons-book',
		'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields' ),
		'capability_type'     => 'post',
		'map_meta_cap'        => true,
	);

	register_post_type( 'livro', $args );
}

// Flush rewrite rules na ativação (se for plugin)
// register_activation_hook( __FILE__, function() { estudos_wp_register_cpt_livro(); flush_rewrite_rules(); } );
