<?php
/**
 * Exemplo 01: Object Cache – wp_cache_get/set/delete e grupos
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   wp_cache_get( $key, $group = '', $force = false );
 *   wp_cache_set( $key, $data, $group = '', $expire = 0 );
 *   wp_cache_delete( $key, $group = '' );
 *   Grupos: organizam chaves; invalidação por contexto. TTL em segundos (HOUR_IN_SECONDS, DAY_IN_SECONDS).
 *
 * Uso: instanciar a classe no plugin; get_featured_posts() e get_post_views() usam cache; save_post invalida.
 *
 * @package EstudosWP
 * @subpackage 08-Performance-Cache-Otimizacao
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Object Cache: queries em cache + invalidação em save_post.
 */
class Estudos_WP_Object_Cache {

	const CACHE_GROUP       = 'estudos_wp';
	const CACHE_GROUP_VIEWS = 'estudos_wp_views';

	/**
	 * Cache simples de query (posts em destaque).
	 *
	 * @return array
	 */
	public function get_featured_posts() {
		$cache_key = 'featured_posts';

		$posts = wp_cache_get( $cache_key, self::CACHE_GROUP );

		if ( false === $posts ) {
			$query = new WP_Query(
				array(
					'post_type'      => 'post',
					'posts_per_page' => 10,
					'meta_key'       => '_featured',
					'meta_value'     => '1',
					'no_found_rows'  => true,
					'update_post_meta_cache' => true,
					'update_post_term_cache' => true,
				)
			);
			$posts = $query->posts;
			wp_cache_set( $cache_key, $posts, self::CACHE_GROUP, HOUR_IN_SECONDS );
		}

		return $posts;
	}

	/**
	 * Cache de contagem (ex.: views do post).
	 *
	 * @param int $post_id ID do post.
	 * @return int
	 */
	public function get_post_views( $post_id ) {
		$cache_key = 'post_views_' . $post_id;

		$views = wp_cache_get( $cache_key, self::CACHE_GROUP_VIEWS );

		if ( false === $views ) {
			$views = get_post_meta( $post_id, '_post_views', true );
			$views = $views ? absint( $views ) : 0;
			wp_cache_set( $cache_key, $views, self::CACHE_GROUP_VIEWS, HOUR_IN_SECONDS );
		}

		return $views;
	}

	/**
	 * Incrementar views e atualizar cache.
	 *
	 * @param int $post_id ID do post.
	 */
	public function increment_post_views( $post_id ) {
		$views = get_post_meta( $post_id, '_post_views', true );
		$views = $views ? absint( $views ) + 1 : 1;
		update_post_meta( $post_id, '_post_views', $views );

		$cache_key = 'post_views_' . $post_id;
		wp_cache_set( $cache_key, $views, self::CACHE_GROUP_VIEWS, HOUR_IN_SECONDS );
	}

	/**
	 * Invalidar cache ao salvar post.
	 *
	 * @param int $post_id ID do post.
	 */
	public function invalidate_cache( $post_id ) {
		wp_cache_delete( 'featured_posts', self::CACHE_GROUP );
		wp_cache_delete( 'post_views_' . $post_id, self::CACHE_GROUP_VIEWS );
	}

	public function __construct() {
		add_action( 'save_post', array( $this, 'invalidate_cache' ) );
	}
}

new Estudos_WP_Object_Cache();
