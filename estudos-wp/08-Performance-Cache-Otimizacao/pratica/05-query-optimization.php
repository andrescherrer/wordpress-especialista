<?php
/**
 * Exemplo 05: Otimização de queries – evitar N+1, no_found_rows, update_post_meta_cache
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   N+1: em loop de posts, antes use update_postmeta_cache( $post_ids ) e update_post_term_cache( $post_ids ).
 *   WP_Query: no_found_rows => true (não contar total quando não precisa de paginação);
 *   update_post_meta_cache => true, update_post_term_cache => true para eager load.
 *   Não usar query_posts() – altera $wp_query global; use new WP_Query() em variável.
 *
 * Uso: get_featured_posts_bad() = N+1; get_featured_posts_good() = com cache de meta/terms.
 *
 * @package EstudosWP
 * @subpackage 08-Performance-Cache-Otimizacao
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Evitar N+1 e usar WP_Query otimizado.
 */
class Estudos_WP_Query_Optimization {

	/**
	 * Ruim: cada get_post_meta no loop = 1 query (N+1).
	 */
	public function get_featured_posts_bad() {
		$posts = get_posts(
			array(
				'meta_key'   => '_featured',
				'meta_value' => '1',
				'numberposts' => 10,
			)
		);

		foreach ( $posts as $post ) {
			// Cada chamada = 1 query se meta não estiver em cache
			$views  = get_post_meta( $post->ID, '_views', true );
			$rating = get_post_meta( $post->ID, '_rating', true );
		}

		return $posts;
	}

	/**
	 * Bom: carregar todas as metas de uma vez antes do loop.
	 */
	public function get_featured_posts_good() {
		$posts = get_posts(
			array(
				'meta_key'    => '_featured',
				'meta_value'  => '1',
				'numberposts' => 10,
			)
		);

		$post_ids = wp_list_pluck( $posts, 'ID' );
		update_post_meta_cache( $post_ids );
		update_post_term_cache( $post_ids );

		foreach ( $posts as $post ) {
			$views  = get_post_meta( $post->ID, '_views', true );
			$rating = get_post_meta( $post->ID, '_rating', true );
		}

		return $posts;
	}

	/**
	 * WP_Query otimizada: no_found_rows, update caches, resultado em object cache.
	 *
	 * @return array
	 */
	public function get_featured_posts_cached() {
		$cache_key = 'estudos_wp_featured_posts';
		$posts     = wp_cache_get( $cache_key, 'estudos_wp' );

		if ( false !== $posts ) {
			return $posts;
		}

		$query = new WP_Query(
			array(
				'post_type'                  => 'post',
				'posts_per_page'            => 10,
				'meta_key'                  => '_featured',
				'meta_value'                => '1',
				'ignore_sticky_posts'       => true,
				'no_found_rows'             => true,
				'update_post_term_cache'    => true,
				'update_post_meta_cache'    => true,
			)
		);

		$posts = $query->posts;
		wp_cache_set( $cache_key, $posts, 'estudos_wp', HOUR_IN_SECONDS );

		return $posts;
	}
}
