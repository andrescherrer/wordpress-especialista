<?php
/**
 * Exemplo 02: Subcomandos WP-CLI (meu-plugin db check|repair|optimize)
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   WP_CLI::add_command( 'meu-plugin db', 'Classe_DB' );
 *   Gera: wp meu-plugin db check | wp meu-plugin db repair | wp meu-plugin db optimize
 *   Mesma assinatura: function nome( $args, $assoc_args )
 *
 * Uso: wp meu-plugin db check | wp meu-plugin db repair | wp meu-plugin db optimize
 *
 * @package EstudosWP
 * @subpackage 07-WP-CLI-Fundamentos
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

/**
 * Subcomando "db": check, repair, optimize.
 */
class Estudos_WP_CLI_DB_Command {

	/**
	 * Verificar integridade das tabelas do plugin.
	 *
	 * ## EXAMPLES
	 *
	 *     wp meu-plugin db check
	 *
	 * @when after_wp_load
	 */
	public function check( $args, $assoc_args ) {
		global $wpdb;

		$tables = array(
			$wpdb->prefix . 'meu_plugin_data',
			$wpdb->prefix . 'meu_plugin_logs',
		);

		WP_CLI::log( 'Checando integridade das tabelas...' );
		WP_CLI::log( '' );

		foreach ( $tables as $table ) {
			if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) !== $table ) {
				WP_CLI::warning( "Tabela não existe: {$table}" );
				continue;
			}
			$result = $wpdb->get_row( "CHECK TABLE {$table}" );
			if ( $result && isset( $result->Msg_type ) && $result->Msg_type === 'status' ) {
				WP_CLI::success( "✓ {$table}" );
			} else {
				WP_CLI::warning( "⚠ {$table}" );
			}
		}

		WP_CLI::log( '' );
		WP_CLI::success( 'Check completo!' );
	}

	/**
	 * Reparar tabelas do plugin.
	 *
	 * ## EXAMPLES
	 *
	 *     wp meu-plugin db repair
	 *
	 * @when after_wp_load
	 */
	public function repair( $args, $assoc_args ) {
		global $wpdb;

		$tables = array(
			$wpdb->prefix . 'meu_plugin_data',
			$wpdb->prefix . 'meu_plugin_logs',
		);

		foreach ( $tables as $table ) {
			if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) !== $table ) {
				continue;
			}
			WP_CLI::log( "Reparando {$table}..." );
			$wpdb->query( "REPAIR TABLE {$table}" );
		}

		WP_CLI::success( 'Tabelas reparadas!' );
	}

	/**
	 * Otimizar tabelas do plugin.
	 *
	 * ## EXAMPLES
	 *
	 *     wp meu-plugin db optimize
	 *
	 * @when after_wp_load
	 */
	public function optimize( $args, $assoc_args ) {
		global $wpdb;

		$tables = array(
			$wpdb->prefix . 'meu_plugin_data',
			$wpdb->prefix . 'meu_plugin_logs',
		);

		WP_CLI::log( 'Otimizando tabelas...' );

		foreach ( $tables as $table ) {
			if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) !== $table ) {
				continue;
			}
			$wpdb->query( "OPTIMIZE TABLE {$table}" );
			WP_CLI::log( "  ✓ {$table}" );
		}

		WP_CLI::success( 'Otimização completa!' );
	}
}

WP_CLI::add_command( 'meu-plugin db', 'Estudos_WP_CLI_DB_Command' );
