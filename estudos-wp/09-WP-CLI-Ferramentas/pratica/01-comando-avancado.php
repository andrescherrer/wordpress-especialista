<?php
/**
 * Exemplo 01: Comando WP-CLI avançado – cleanup, process-queue, check-integrity, repair
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   Progress bar: \WP_CLI\Utils\make_progress_bar( 'Label', $total ); tick(); finish();
 *   --dry-run: isset( $assoc_args['dry-run'] ); simular sem alterar.
 *   Try/catch em lote: capturar exceção e WP_CLI::warning() ou continuar.
 *   check-integrity: SHOW TABLES, get_option, file_exists; success ou warning por item.
 *
 * Uso: wp meu-plugin cleanup [--days=30] [--dry-run] | wp meu-plugin check-integrity | wp meu-plugin repair
 *
 * @package EstudosWP
 * @subpackage 09-WP-CLI-Ferramentas
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

/**
 * Comandos avançados: cleanup, process-queue, check-integrity, repair.
 */
class Estudos_WP_CLI_Comando_Avancado {

	/**
	 * Limpar dados antigos.
	 *
	 * ## OPTIONS
	 *
	 * [--days=<days>]
	 * : Dias para manter (padrão: 30)
	 * ---
	 * default: 30
	 * ---
	 *
	 * [--dry-run]
	 * : Simular sem deletar
	 *
	 * ## EXAMPLES
	 *
	 *     wp meu-plugin cleanup
	 *     wp meu-plugin cleanup --days=60 --dry-run
	 *
	 * @when after_wp_load
	 */
	public function cleanup( $args, $assoc_args ) {
		$days    = isset( $assoc_args['days'] ) ? absint( $assoc_args['days'] ) : 30;
		$dry_run = isset( $assoc_args['dry-run'] );

		global $wpdb;
		$table = $wpdb->prefix . 'meu_plugin_logs';
		$date  = gmdate( 'Y-m-d H:i:s', strtotime( "-{$days} days" ) );

		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) !== $table ) {
			WP_CLI::log( 'Tabela de logs não existe.' );
			return;
		}

		$count = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$table} WHERE created_at < %s",
				$date
			)
		);

		if ( 0 === (int) $count ) {
			WP_CLI::log( "Nenhum dado para limpar (anterior a {$days} dias)." );
			return;
		}

		if ( ! $dry_run ) {
			$wpdb->query(
				$wpdb->prepare(
					"DELETE FROM {$table} WHERE created_at < %s",
					$date
				)
			);
		}

		$mode = $dry_run ? '(DRY RUN) ' : '';
		WP_CLI::success( "{$mode}{$count} registros deletados!" );
	}

	/**
	 * Verificar integridade do plugin.
	 *
	 * ## EXAMPLES
	 *
	 *     wp meu-plugin check-integrity
	 *
	 * @when after_wp_load
	 */
	public function check_integrity( $args, $assoc_args ) {
		WP_CLI::log( 'Verificando integridade...' );

		$issues = array();

		global $wpdb;
		$tables = $wpdb->get_col( "SHOW TABLES LIKE '{$wpdb->prefix}meu_plugin_%'" );

		if ( empty( $tables ) ) {
			$issues[] = 'Nenhuma tabela do plugin encontrada.';
		}

		$required_options = array( 'meu_plugin_version', 'meu_plugin_settings' );
		foreach ( $required_options as $option ) {
			if ( ! get_option( $option ) ) {
				$issues[] = "Opção ausente: {$option}";
			}
		}

		if ( empty( $issues ) ) {
			WP_CLI::success( '✓ Plugin íntegro!' );
		} else {
			foreach ( $issues as $issue ) {
				WP_CLI::warning( '✗ ' . $issue );
			}
		}
	}

	/**
	 * Reparar tabelas do plugin.
	 *
	 * ## EXAMPLES
	 *
	 *     wp meu-plugin repair
	 *
	 * @when after_wp_load
	 */
	public function repair( $args, $assoc_args ) {
		global $wpdb;

		$tables = array(
			$wpdb->prefix . 'meu_plugin_data',
			$wpdb->prefix . 'meu_plugin_logs',
		);

		$to_repair = array();
		foreach ( $tables as $table ) {
			if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) === $table ) {
				$to_repair[] = $table;
			}
		}

		if ( empty( $to_repair ) ) {
			WP_CLI::warning( 'Nenhuma tabela do plugin para reparar.' );
			return;
		}

		$progress = \WP_CLI\Utils\make_progress_bar( 'Reparando', count( $to_repair ) );
		foreach ( $to_repair as $table ) {
			$wpdb->query( "REPAIR TABLE {$table}" );
			$progress->tick();
		}
		$progress->finish();
		WP_CLI::success( 'Tabelas reparadas!' );
	}
}

WP_CLI::add_command( 'meu-plugin', 'Estudos_WP_CLI_Comando_Avancado' );
