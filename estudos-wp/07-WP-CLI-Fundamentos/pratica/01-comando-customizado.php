<?php
/**
 * Exemplo 01: Comando WP-CLI customizado (cleanup, status, reset)
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   if (!defined('WP_CLI') || !WP_CLI) return;
 *   WP_CLI::add_command( 'meu-plugin', 'Nome_Classe' );
 *   Métodos públicos = subcomandos: cleanup → wp meu-plugin cleanup
 *   $args = posicionais, $assoc_args = opções (--days=30, --dry-run)
 *   PHPDoc: ## OPTIONS, ## EXAMPLES, @when after_wp_load
 *
 * Uso: wp meu-plugin cleanup | wp meu-plugin status | wp meu-plugin reset [--hard]
 *
 * @package EstudosWP
 * @subpackage 07-WP-CLI-Fundamentos
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

/**
 * Comando principal do plugin (cleanup, status, reset).
 */
class Estudos_WP_CLI_Comando_Principal {

	/**
	 * Limpar dados antigos do plugin.
	 *
	 * ## OPTIONS
	 *
	 * [--days=<days>]
	 * : Quantos dias para manter (padrão: 30)
	 * ---
	 * default: 30
	 * ---
	 *
	 * [--dry-run]
	 * : Mostrar o que será deletado sem executar
	 *
	 * ## EXAMPLES
	 *
	 *     wp meu-plugin cleanup
	 *     wp meu-plugin cleanup --days=60
	 *     wp meu-plugin cleanup --dry-run
	 *
	 * @when after_wp_load
	 */
	public function cleanup( $args, $assoc_args ) {
		global $wpdb;

		$days    = isset( $assoc_args['days'] ) ? (int) $assoc_args['days'] : 30;
		$dry_run = isset( $assoc_args['dry-run'] );
		$date    = gmdate( 'Y-m-d H:i:s', strtotime( "-{$days} days" ) );

		// Exemplo: tabela customizada (ajuste o prefixo/nome conforme seu plugin)
		$table   = $wpdb->prefix . 'meu_plugin_logs';
		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT id FROM {$table} WHERE created_at < %s",
				$date
			)
		);

		$count = $results ? count( $results ) : 0;

		if ( $dry_run ) {
			WP_CLI::log( "Encontrados {$count} registros para deletar" );
			WP_CLI::log( "Data limite: {$date}" );
			WP_CLI::log( 'Execute sem --dry-run para confirmar' );
			return;
		}

		if ( $count > 0 ) {
			$wpdb->query(
				$wpdb->prepare(
					"DELETE FROM {$table} WHERE created_at < %s",
					$date
				)
			);
			WP_CLI::success( "Deletados {$count} registros!" );
		} else {
			WP_CLI::log( 'Nenhum registro para deletar' );
		}
	}

	/**
	 * Ver status do plugin.
	 *
	 * ## EXAMPLES
	 *
	 *     wp meu-plugin status
	 *
	 * @when after_wp_load
	 */
	public function status( $args, $assoc_args ) {
		$version = get_option( 'meu_plugin_version', '1.0' );
		$enabled = get_option( 'meu_plugin_enabled', false );

		WP_CLI::log( '=== Status do Plugin ===' );
		WP_CLI::log( '' );
		WP_CLI::log( 'Versão: ' . $version );
		WP_CLI::log( 'Status: ' . ( $enabled ? '✓ Habilitado' : '✗ Desabilitado' ) );

		global $wpdb;
		$table = $wpdb->prefix . 'meu_plugin_data';
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) === $table ) {
			$count = $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" );
			WP_CLI::log( 'Registros: ' . $count );
		}
		WP_CLI::log( '' );
	}

	/**
	 * Reiniciar o plugin (cache ou hard reset).
	 *
	 * ## OPTIONS
	 *
	 * [--hard]
	 * : Reset completo (deletar todos os dados)
	 *
	 * ## EXAMPLES
	 *
	 *     wp meu-plugin reset
	 *     wp meu-plugin reset --hard
	 *
	 * @when after_wp_load
	 */
	public function reset( $args, $assoc_args ) {
		$hard = isset( $assoc_args['hard'] );

		if ( $hard ) {
			WP_CLI::confirm( 'Tem certeza que deseja DELETAR todos os dados?' );

			global $wpdb;
			$table = $wpdb->prefix . 'meu_plugin_data';
			if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) === $table ) {
				$wpdb->query( "TRUNCATE TABLE {$table}" );
			}
			WP_CLI::success( 'Plugin resetado completamente!' );
		} else {
			delete_option( 'meu_plugin_cache' );
			WP_CLI::success( 'Cache do plugin limpo!' );
		}
	}
}

WP_CLI::add_command( 'meu-plugin', 'Estudos_WP_CLI_Comando_Principal' );
