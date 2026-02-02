<?php
/**
 * Exemplo 04: Funções de termos (get_terms, get_term, wp_get_post_terms, wp_set_post_terms)
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   get_terms( $taxonomy, $args ) – lista; get_term( $id, $taxonomy ) – um termo.
 *   wp_get_post_terms( $post_id, $taxonomy ) – termos do post.
 *   wp_set_post_terms( $post_id, $term_ids, $taxonomy ) – define (sobrescreve).
 *   wp_add_object_terms( $post_id, $term_id, $taxonomy ) – adiciona; wp_remove_object_terms – remove.
 *   wp_insert_term( $name, $taxonomy, $args ) – cria termo.
 *
 * @package EstudosWP
 * @subpackage 05-CPT-Taxonomias
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Listar todos os gêneros (para select ou lista).
 *
 * @return WP_Term[]|int
 */
function estudos_wp_get_generos() {
	return get_terms( array(
		'taxonomy'   => 'genero_livro',
		'hide_empty' => false,
		'orderby'    => 'name',
		'order'      => 'ASC',
	) );
}

/**
 * Obter um termo por slug.
 *
 * @param string $slug Slug do termo.
 * @param string $tax  Taxonomia.
 * @return WP_Term|null
 */
function estudos_wp_get_term_by_slug( $slug, $tax = 'genero_livro' ) {
	$term = get_term_by( 'slug', $slug, $tax );
	return $term instanceof WP_Term ? $term : null;
}

/**
 * Atribuir gêneros a um livro (sobrescreve os atuais).
 *
 * @param int   $post_id   ID do post (livro).
 * @param int[] $term_ids  IDs dos termos em genero_livro.
 * @return bool|WP_Error
 */
function estudos_wp_set_livro_generos( $post_id, array $term_ids ) {
	return wp_set_post_terms( $post_id, $term_ids, 'genero_livro' );
}

/**
 * Adicionar uma tag a um livro (sem remover as existentes).
 *
 * @param int $post_id ID do post.
 * @param int $term_id ID do termo em livro_tag.
 * @return array|WP_Error
 */
function estudos_wp_add_livro_tag( $post_id, $term_id ) {
	return wp_add_object_terms( $post_id, $term_id, 'livro_tag' );
}

/**
 * Criar gênero se não existir e retornar term_id.
 *
 * @param string $name Nome do gênero.
 * @param int    $parent ID do termo pai (0 = raiz).
 * @return int|WP_Error
 */
function estudos_wp_create_genero( $name, $parent = 0 ) {
	$slug = sanitize_title( $name );
	$term = get_term_by( 'slug', $slug, 'genero_livro' );
	if ( $term ) {
		return $term->term_id;
	}
	$result = wp_insert_term( $name, 'genero_livro', array(
		'slug'        => $slug,
		'parent'      => $parent,
		'description' => '',
	) );
	if ( is_wp_error( $result ) ) {
		return $result;
	}
	return $result['term_id'];
}
