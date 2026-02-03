<?php
/**
 * Exemplo 01: Multisite – is_multisite, switch_to_blog, get_sites, ativação por site
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   is_multisite(); get_current_blog_id(); get_blog_details( $blog_id );
 *   switch_to_blog( $blog_id ); ... restore_current_blog();
 *   get_sites( [ 'fields' => 'ids', 'number' => -1 ] );
 *   wp_insert_site (novo site); wp_delete_site (site deletado).
 *
 * @package EstudosWP
 * @subpackage 11-Multisite-Internacionalizacao
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Classe Multisite: ativação por site, loop em sites, hooks de novo/deletar site.
 */
class Estudos_WP_Multisite_Basico {

	/**
	 * Ativar em um único site (criar tabela, opções).
	 */
	public static function activate_single_site() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();
		$table_name     = $wpdb->prefix . 'meu_plugin_data';

		$sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			blog_id BIGINT UNSIGNED NOT NULL,
			data LONGTEXT,
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			KEY blog_id (blog_id)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		update_option( 'meu_plugin_version', '1.0.0' );
	}

	/**
	 * Ativar em todos os sites da rede.
	 */
	public static function activate_all_sites() {
		if ( ! is_multisite() ) {
			self::activate_single_site();
			return;
		}

		$blog_ids = get_sites( array( 'fields' => 'ids', 'number' => -1 ) );

		foreach ( $blog_ids as $blog_id ) {
			switch_to_blog( $blog_id );
			self::activate_single_site();
			restore_current_blog();
		}
	}

	/**
	 * Obter todos os sites (objetos).
	 *
	 * @return array
	 */
	public static function get_all_sites() {
		if ( ! is_multisite() ) {
			return array();
		}
		return get_sites( array( 'number' => -1, 'orderby' => 'name' ) );
	}

	/**
	 * Executar callback em cada site (switch_to_blog / restore_current_blog).
	 *
	 * @param callable $callback Recebe ( $site ) onde $site é objeto do site.
	 */
	public static function loop_blogs( $callback ) {
		if ( ! is_multisite() ) {
			return;
		}

		$sites = self::get_all_sites();
		foreach ( $sites as $site ) {
			switch_to_blog( $site->blog_id );
			call_user_func( $callback, $site );
			restore_current_blog();
		}
	}

	/**
	 * Hook: novo site criado na rede.
	 *
	 * @param WP_Site $new_site Objeto do novo site.
	 */
	public static function on_new_site( $new_site ) {
		switch_to_blog( $new_site->blog_id );
		self::activate_single_site();
		restore_current_blog();
	}

	/**
	 * Hook: site deletado (limpar tabelas do plugin).
	 *
	 * @param WP_Site $old_site Site sendo deletado.
	 */
	public static function on_delete_site( $old_site ) {
		switch_to_blog( $old_site->blog_id );
		global $wpdb;
		$table = $wpdb->prefix . 'meu_plugin_data';
		$wpdb->query( "DROP TABLE IF EXISTS {$table}" );
		delete_option( 'meu_plugin_version' );
		restore_current_blog();
	}

	/**
	 * Registrar hooks de Multisite.
	 */
	public static function register_hooks() {
		if ( is_multisite() ) {
			add_action( 'wp_initialize_site', array( __CLASS__, 'on_new_site' ), 10, 1 );
			add_action( 'wp_delete_site', array( __CLASS__, 'on_delete_site' ), 10, 1 );
		}
	}
}

Estudos_WP_Multisite_Basico::register_hooks();
