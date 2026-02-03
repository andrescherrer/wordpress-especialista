<?php
/**
 * Exemplo 06: Debug – report, performance, clear
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   report: montar string (WP, PHP, opções, plugins ativos); file_put_contents se --output.
 *   performance: microtime(true) antes/depois; logar tempo em segundos.
 *   clear: DELETE da tabela de logs; WP_CLI::success.
 *
 * Uso: wp meu-plugin debug report [--output=debug.txt] | wp meu-plugin debug performance | wp meu-plugin debug clear
 *
 * @package EstudosWP
 * @subpackage 09-WP-CLI-Ferramentas
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

/**
 * Ferramentas de debug: report, performance, clear.
 */
class Estudos_WP_CLI_Debug_Command {

	/**
	 * Gerar relatório de debug.
	 *
	 * ## OPTIONS
	 *
	 * [--output=<file>]
	 * : Salvar relatório em arquivo
	 *
	 * ## EXAMPLES
	 *
	 *     wp meu-plugin debug report
	 *     wp meu-plugin debug report --output=debug.log
	 *
	 * @when after_wp_load
	 */
	public function report( $args, $assoc_args ) {
		$report = $this->generate_report();
		$output = isset( $assoc_args['output'] ) ? $assoc_args['output'] : null;

		if ( $output ) {
			if ( false === file_put_contents( $output, $report ) ) {
				WP_CLI::error( 'Falha ao salvar arquivo.' );
			}
			WP_CLI::success( "Relatório salvo em {$output}" );
		} else {
			WP_CLI::log( $report );
		}
	}

	/**
	 * Testar performance (queries, cache).
	 *
	 * ## EXAMPLES
	 *
	 *     wp meu-plugin debug performance
	 *
	 * @when after_wp_load
	 */
	public function performance( $args, $assoc_args ) {
		WP_CLI::log( 'Analisando performance...' );

		$start = microtime( true );
		$this->test_database_performance();
		$this->test_cache_performance();
		$elapsed = microtime( true ) - $start;

		WP_CLI::log( '' );
		WP_CLI::log( 'Tempo total: ' . number_format( $elapsed, 4 ) . 's' );
	}

	/**
	 * Limpar logs de debug do plugin.
	 *
	 * ## EXAMPLES
	 *
	 *     wp meu-plugin debug clear
	 *
	 * @when after_wp_load
	 */
	public function clear( $args, $assoc_args ) {
		global $wpdb;
		$table = $wpdb->prefix . 'meu_plugin_debug_logs';
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) === $table ) {
			$wpdb->query( "TRUNCATE TABLE {$table}" );
		}
		WP_CLI::success( 'Logs de debug limpos!' );
	}

	private function generate_report() {
		$r  = "═══════════════════════════════════════════\n";
		$r .= "Relatório de Debug - Meu Plugin\n";
		$r .= "═══════════════════════════════════════════\n\n";

		$r .= "WordPress:\n";
		$r .= "  Versão: " . get_bloginfo( 'version' ) . "\n";
		$r .= "  URL: " . home_url() . "\n";
		$r .= "  Admin Email: " . get_bloginfo( 'admin_email' ) . "\n\n";

		$r .= "PHP:\n";
		$r .= "  Versão: " . PHP_VERSION . "\n";
		$r .= "  Memory Limit: " . ini_get( 'memory_limit' ) . "\n";
		$r .= "  Max Execution Time: " . ini_get( 'max_execution_time' ) . "s\n\n";

		$r .= "Plugins ativos:\n";
		foreach ( (array) get_option( 'active_plugins', array() ) as $plugin ) {
			$r .= "  • " . $plugin . "\n";
		}
		$r .= "\nTema: " . get_option( 'template' ) . "\n\n";

		$r .= "Opções do plugin:\n";
		$r .= "  Version: " . get_option( 'meu_plugin_version', '-' ) . "\n";
		$r .= "  Mode: " . get_option( 'meu_plugin_mode', '-' ) . "\n";
		$r .= "  Cache: " . ( get_option( 'meu_plugin_cache_enabled' ) ? 'Sim' : 'Não' ) . "\n";

		return $r;
	}

	private function test_database_performance() {
		global $wpdb;
		$start = microtime( true );
		for ( $i = 0; $i < 50; $i++ ) {
			$wpdb->get_results( "SELECT ID FROM {$wpdb->posts} LIMIT 1" );
		}
		$elapsed = microtime( true ) - $start;
		WP_CLI::log( sprintf( 'Database: %.4fs para 50 queries', $elapsed ) );
	}

	private function test_cache_performance() {
		$start = microtime( true );
		for ( $i = 0; $i < 500; $i++ ) {
			wp_cache_set( "debug_test_{$i}", "val_{$i}", 'debug', 60 );
			wp_cache_get( "debug_test_{$i}", 'debug' );
		}
		$elapsed = microtime( true ) - $start;
		WP_CLI::log( sprintf( 'Cache: %.4fs para 500 ops', $elapsed ) );
	}
}

WP_CLI::add_command( 'meu-plugin debug', 'Estudos_WP_CLI_Debug_Command' );
