<?php
/**
 * Exemplo 03: Query CPT com tax_query e wp_get_post_terms
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   tax_query: taxonomy, field (slug|id|name), terms, operator (IN|NOT IN|AND).
 *   relation AND/OR entre múltiplas taxonomias.
 *   wp_get_post_terms( $post_id, $taxonomy ) → array de WP_Term.
 *
 * @package EstudosWP
 * @subpackage 05-CPT-Taxonomias
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Listar livros por slug de gênero.
 *
 * @param string $genero_slug Slug do termo em genero_livro.
 * @param int    $per_page    Quantidade.
 * @return WP_Post[]
 */
function estudos_wp_get_livros_por_genero( $genero_slug, $per_page = 10 ) {
	$args = array(
		'post_type'      => 'livro',
		'post_status'    => 'publish',
		'posts_per_page' => $per_page,
		'tax_query'      => array(
			array(
				'taxonomy' => 'genero_livro',
				'field'    => 'slug',
				'terms'    => $genero_slug,
			),
		),
	);
	$query = new WP_Query( $args );
	return $query->posts;
}

/**
 * Listar livros que tenham um gênero OU uma tag.
 *
 * @param string $genero_slug Slug do gênero.
 * @param string $tag_slug    Slug da tag.
 * @return WP_Post[]
 */
function estudos_wp_get_livros_genero_ou_tag( $genero_slug, $tag_slug ) {
	$args = array(
		'post_type'      => 'livro',
		'post_status'    => 'publish',
		'posts_per_page' => 10,
		'tax_query'      => array(
			'relation' => 'OR',
			array(
				'taxonomy' => 'genero_livro',
				'field'    => 'slug',
				'terms'    => $genero_slug,
			),
			array(
				'taxonomy' => 'livro_tag',
				'field'    => 'slug',
				'terms'    => $tag_slug,
			),
		),
	);
	$query = new WP_Query( $args );
	return $query->posts;
}

/**
 * Exibir termos de um post (exemplo para uso em loop).
 *
 * @param int    $post_id   ID do post.
 * @param string $taxonomy  Nome da taxonomia.
 * @param string $separator Separador entre termos.
 * @return string
 */
function estudos_wp_post_terms_list( $post_id, $taxonomy, $separator = ', ' ) {
	$terms = wp_get_post_terms( $post_id, $taxonomy );
	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return '';
	}
	$links = array();
	foreach ( $terms as $term ) {
		$url   = get_term_link( $term );
		$links[] = '<a href="' . esc_url( $url ) . '">' . esc_html( $term->name ) . '</a>';
	}
	return implode( $separator, $links );
}

// Exemplo no template: livros do gênero "ficcao"
// $livros = estudos_wp_get_livros_por_genero( 'ficcao', 5 );
// foreach ( $livros as $livro ) {
//     echo esc_html( $livro->post_title );
//     echo estudos_wp_post_terms_list( $livro->ID, 'genero_livro' );
// }
