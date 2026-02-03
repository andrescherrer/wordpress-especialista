<?php
/**
 * Exemplo 04: Comando WP-CLI interativo (prompt, confirm, validação)
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   WP_CLI::prompt( 'Pergunta' )
 *   WP_CLI::prompt( 'Pergunta', $default )  — default pode ser string ou array (menu)
 *   WP_CLI::prompt( 'Pergunta', $default, $validator )  — callable que retorna bool
 *   WP_CLI::confirm( 'Continuar?' )  — Y/n; sai com erro se não confirmar
 *
 * Uso: wp meu-plugin setup
 *
 * @package EstudosWP
 * @subpackage 07-WP-CLI-Fundamentos
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

/**
 * Comando interativo de configuração.
 */
class Estudos_WP_CLI_Setup_Command {

	/**
	 * Configurar plugin interativamente.
	 *
	 * ## EXAMPLES
	 *
	 *     wp meu-plugin setup
	 *
	 * @when after_wp_load
	 */
	public function setup( $args, $assoc_args ) {
		WP_CLI::log( '' );
		WP_CLI::log( '%G=== Configuração do Meu Plugin ===%n' );
		WP_CLI::log( '' );

		$api_key = WP_CLI::prompt( 'Sua API Key' );
		update_option( 'meu_plugin_api_key', sanitize_text_field( $api_key ) );

		$site_name = WP_CLI::prompt( 'Nome do site', get_bloginfo( 'name' ) );
		update_option( 'meu_plugin_site_name', sanitize_text_field( $site_name ) );

		$mode = WP_CLI::prompt(
			'Modo de operação',
			array( 'development', 'staging', 'production' )
		);
		update_option( 'meu_plugin_mode', $mode );

		if ( WP_CLI::confirm( 'Habilitar cache?' ) ) {
			update_option( 'meu_plugin_cache_enabled', true );
			$duration = WP_CLI::prompt(
				'Duração do cache (minutos)',
				'60',
				function ( $response ) {
					return is_numeric( $response ) && (int) $response > 0;
				}
			);
			update_option( 'meu_plugin_cache_duration', absint( $duration ) );
		}

		$email = WP_CLI::prompt(
			'Email para notificações',
			get_option( 'admin_email' ),
			function ( $response ) {
				return is_email( $response );
			}
		);
		update_option( 'meu_plugin_notification_email', sanitize_email( $email ) );

		WP_CLI::log( '' );
		WP_CLI::success( 'Configuração concluída!' );
		WP_CLI::log( '' );
		WP_CLI::log( '%G=== Resumo ===%n' );
		WP_CLI::log( 'API Key: ' . str_repeat( '*', 20 ) );
		WP_CLI::log( 'Site: ' . $site_name );
		WP_CLI::log( 'Modo: ' . $mode );
		WP_CLI::log( 'Cache: ' . ( get_option( 'meu_plugin_cache_enabled' ) ? 'Habilitado' : 'Desabilitado' ) );
		WP_CLI::log( 'Email: ' . $email );
		WP_CLI::log( '' );
	}
}

WP_CLI::add_command( 'meu-plugin setup', 'Estudos_WP_CLI_Setup_Command' );
