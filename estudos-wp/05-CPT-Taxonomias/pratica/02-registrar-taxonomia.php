<?php
/**
 * Exemplo 02: Registrar taxonomias (hierárquica e não-hierárquica)
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   register_taxonomy( $taxonomy, $object_type, $args ). Hook: init (após CPT).
 *   object_type: 'post', 'page' ou slug do CPT (ou array).
 *   hierarchical true = tipo categoria; false = tipo tag.
 *   rewrite slug; show_in_rest; show_admin_column.
 *
 * @package EstudosWP
 * @subpackage 05-CPT-Taxonomias
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'estudos_wp_register_taxonomias', 15 ); // 15 = depois do CPT 'livro'

function estudos_wp_register_taxonomias() {
	// Taxonomia hierárquica: Gênero (como categoria)
	$labels_cat = array(
		'name'              => 'Gêneros',
		'singular_name'     => 'Gênero',
		'menu_name'         => 'Gêneros',
		'search_items'      => 'Buscar gêneros',
		'all_items'         => 'Todos os gêneros',
		'parent_item'       => 'Gênero pai',
		'parent_item_colon' => 'Gênero pai:',
		'edit_item'         => 'Editar gênero',
		'update_item'       => 'Atualizar gênero',
		'add_new_item'      => 'Adicionar novo gênero',
		'new_item_name'     => 'Nome do novo gênero',
	);

	register_taxonomy(
		'genero_livro',
		'livro',
		array(
			'labels'            => $labels_cat,
			'description'       => 'Gênero literário do livro',
			'public'            => true,
			'show_ui'           => true,
			'show_in_menu'      => true,
			'show_in_rest'      => true,
			'hierarchical'      => true,
			'rewrite'           => array(
				'slug'         => 'genero',
				'with_front'   => true,
				'hierarchical' => true,
			),
			'query_var'         => true,
			'show_admin_column' => true,
		)
	);

	// Taxonomia não-hierárquica: Tags
	$labels_tag = array(
		'name'          => 'Tags de Livro',
		'singular_name' => 'Tag',
		'menu_name'     => 'Tags',
		'search_items'  => 'Buscar tags',
		'all_items'     => 'Todas as tags',
		'edit_item'     => 'Editar tag',
		'update_item'   => 'Atualizar tag',
		'add_new_item'  => 'Adicionar nova tag',
	);

	register_taxonomy(
		'livro_tag',
		'livro',
		array(
			'labels'            => $labels_tag,
			'public'            => true,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'hierarchical'      => false,
			'rewrite'           => array( 'slug' => 'livro-tag' ),
			'query_var'         => true,
			'show_admin_column' => true,
		)
	);
}
