<?php
/**
 * Exemplo 04: Fragment caching – cache de HTML (sidebar, blocos)
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   Fluxo: $html = wp_cache_get( $key, $group ); if ( false !== $html ) { echo $html; return; }
 *   ob_start(); ... gerar HTML ... $html = ob_get_clean();
 *   wp_cache_set( $key, $html, $group, $expiration );
 *   Invalidar ao atualizar widgets/conteúdo que compõem o fragmento.
 *
 * Uso: render_sidebar() e get_related_posts_html() servem HTML do cache ou geram e armazenam.
 *
 * @package EstudosWP
 * @subpackage 08-Performance-Cache-Otimizacao
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fragment cache: cache de HTML gerado.
 */
class Estudos_WP_Fragment_Cache {

	const CACHE_GROUP = 'estudos_wp_fragments';

	/**
	 * Renderizar sidebar com cache.
	 */
	public function render_sidebar() {
		$cache_key = 'sidebar_primary';

		$html = wp_cache_get( $cache_key, self::CACHE_GROUP );

		if ( false === $html ) {
			ob_start();
			?>
			<aside class="sidebar estudos-wp-sidebar">
				<div class="widget-area">
					<?php
					if ( is_active_sidebar( 'primary' ) ) {
						dynamic_sidebar( 'primary' );
					}
					?>
				</div>
			</aside>
			<?php
			$html = ob_get_clean();
			wp_cache_set( $cache_key, $html, self::CACHE_GROUP, 6 * HOUR_IN_SECONDS );
		}

		echo wp_kses_post( $html );
	}

	/**
	 * HTML de posts relacionados (query pesada + markup).
	 *
	 * @param int $post_id ID do post.
	 * @param int $limit   Quantidade de relacionados.
	 * @return string HTML.
	 */
	public function get_related_posts_html( $post_id, $limit = 3 ) {
		$cache_key = 'related_posts_' . $post_id . '_' . $limit;

		$html = wp_cache_get( $cache_key, self::CACHE_GROUP );

		if ( false !== $html ) {
			return $html;
		}

		$categories = wp_get_post_categories( $post_id );
		$args       = array(
			'post_type'      => 'post',
			'posts_per_page' => $limit,
			'post__not_in'   => array( $post_id ),
			'orderby'        => 'rand',
			'no_found_rows'  => true,
		);
		if ( ! empty( $categories ) ) {
			$args['category__in'] = $categories;
		}

		$related = new WP_Query( $args );

		ob_start();
		if ( $related->have_posts() ) {
			echo '<div class="related-posts">';
			while ( $related->have_posts() ) {
				$related->the_post();
				echo '<article><h3>' . esc_html( get_the_title() ) . '</h3>';
				echo '<p>' . wp_kses_post( get_the_excerpt() ) . '</p></article>';
			}
			echo '</div>';
		}
		wp_reset_postdata();
		$html = ob_get_clean();

		wp_cache_set( $cache_key, $html, self::CACHE_GROUP, DAY_IN_SECONDS );

		return $html;
	}

	/**
	 * Invalidar cache da sidebar ao alterar widgets.
	 */
	public function __construct() {
		add_action( 'sidebar_admin_setup', array( $this, 'invalidate_sidebar' ) );
		add_action( 'save_post', array( $this, 'invalidate_related' ), 10, 1 );
	}

	public function invalidate_sidebar() {
		wp_cache_delete( 'sidebar_primary', self::CACHE_GROUP );
	}

	public function invalidate_related( $post_id ) {
		wp_cache_delete( 'related_posts_' . $post_id . '_3', self::CACHE_GROUP );
		wp_cache_delete( 'related_posts_' . $post_id . '_5', self::CACHE_GROUP );
	}
}

new Estudos_WP_Fragment_Cache();
