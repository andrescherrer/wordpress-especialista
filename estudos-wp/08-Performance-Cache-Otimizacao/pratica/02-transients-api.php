<?php
/**
 * Exemplo 02: Transients API – get_transient, set_transient, delete_transient
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   get_transient( $key ); set_transient( $key, $value, $expiration ); delete_transient( $key );
 *   Transients são salvos no banco (wp_options); persistem entre requests e restarts.
 *   Uso típico: API externa, estatísticas, dados que não mudam a cada request.
 *
 * Uso: get_widget_data() e get_feed_data() usam transient; clear ao publicar/deletar post.
 *
 * @package EstudosWP
 * @subpackage 08-Performance-Cache-Otimizacao
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Transients: cache em banco para widget e API externa.
 */
class Estudos_WP_Transients {

	const TRANSIENT_WIDGET = 'estudos_wp_widget_data';
	const TRANSIENT_STATS  = 'estudos_wp_site_stats';

	/**
	 * Dados de widget com cache (transient).
	 *
	 * @return array
	 */
	public function get_widget_data() {
		$data = get_transient( self::TRANSIENT_WIDGET );

		if ( false === $data ) {
			$data = array(
				'title' => 'Widget Title',
				'items' => $this->fetch_widget_items(),
				'count' => 0,
			);
			set_transient( self::TRANSIENT_WIDGET, $data, 12 * HOUR_IN_SECONDS );
		}

		return $data;
	}

	/**
	 * Cache de API externa (ex.: feed).
	 *
	 * @param string $feed_url URL do feed.
	 * @return string|WP_Error
	 */
	public function get_feed_data( $feed_url ) {
		$transient_key = 'estudos_wp_feed_' . md5( $feed_url );

		$data = get_transient( $transient_key );

		if ( false === $data ) {
			$response = wp_remote_get( $feed_url, array( 'timeout' => 10 ) );

			if ( is_wp_error( $response ) ) {
				return $response;
			}

			$data = wp_remote_retrieve_body( $response );
			set_transient( $transient_key, $data, 6 * HOUR_IN_SECONDS );
		}

		return $data;
	}

	/**
	 * Estatísticas do site (várias fontes); cache longo.
	 *
	 * @return array
	 */
	public function get_site_stats() {
		$stats = get_transient( self::TRANSIENT_STATS );

		if ( false === $stats ) {
			$counts = wp_count_posts( 'post' );
			$stats  = array(
				'total_posts'    => isset( $counts->publish ) ? (int) $counts->publish : 0,
				'total_users'    => is_callable( 'count_users' ) ? count_users()['total_users'] : 0,
				'generated_at'   => current_time( 'mysql' ),
			);
			set_transient( self::TRANSIENT_STATS, $stats, DAY_IN_SECONDS );
		}

		return $stats;
	}

	/**
	 * Limpar cache ao publicar ou deletar post.
	 */
	public function clear_stats_cache() {
		delete_transient( self::TRANSIENT_WIDGET );
		delete_transient( self::TRANSIENT_STATS );
	}

	/**
	 * Limpar cache de um feed específico.
	 *
	 * @param string $feed_url URL do feed.
	 */
	public function clear_feed_cache( $feed_url ) {
		delete_transient( 'estudos_wp_feed_' . md5( $feed_url ) );
	}

	private function fetch_widget_items() {
		return array();
	}

	public function __construct() {
		add_action( 'publish_post', array( $this, 'clear_stats_cache' ) );
		add_action( 'delete_post', array( $this, 'clear_stats_cache' ) );
	}
}

new Estudos_WP_Transients();
