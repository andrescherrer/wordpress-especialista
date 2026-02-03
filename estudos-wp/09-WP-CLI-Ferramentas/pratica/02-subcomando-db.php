<?php
/**
 * Exemplo 02: Subcomando db – init, export, reset
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   init: require upgrade.php; dbDelta( $sql ) com CREATE TABLE IF NOT EXISTS.
 *   export: get_results( ..., ARRAY_A ); JSON (wp_json_encode) ou CSV (fputcsv); file_put_contents.
 *   reset: exigir --confirm; DROP TABLE; em seguida chamar init.
 *   format_items: \WP_CLI\Utils\format_items( 'table', $items, array( 'col1', 'col2' ) );
 *
 * Uso: wp meu-plugin db init | wp meu-plugin db export [--output=file.json] [--format=csv] | wp meu-plugin db reset --confirm
 *
 * @package EstudosWP
 * @subpackage 09-WP-CLI-Ferramentas
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

/**
 * Subcomando db: init, export, reset.
 */
class Estudos_WP_CLI_DB_Command {

	/**
	 * Inicializar tabelas do plugin (dbDelta).
	 *
	 * ## EXAMPLES
	 *
	 *     wp meu-plugin db init
	 *
	 * @when after_wp_load
	 */
	public function init( $args, $assoc_args ) {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();
		$table          = $wpdb->prefix . 'meu_plugin_data';

		$sql = "CREATE TABLE IF NOT EXISTS {$table} (
			id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			uuid VARCHAR(36) NOT NULL,
			data LONGTEXT NOT NULL,
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			INDEX idx_uuid (uuid),
			INDEX idx_created_at (created_at)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		WP_CLI::success( 'Banco de dados inicializado!' );
	}

	/**
	 * Exportar dados para arquivo.
	 *
	 * ## OPTIONS
	 *
	 * [--output=<file>]
	 * : Arquivo de saída
	 * ---
	 * default: export.json
	 * ---
	 *
	 * [--format=<format>]
	 * : json ou csv
	 * ---
	 * default: json
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     wp meu-plugin db export
	 *     wp meu-plugin db export --output=dados.csv --format=csv
	 *
	 * @when after_wp_load
	 */
	public function export( $args, $assoc_args ) {
		global $wpdb;

		$table  = $wpdb->prefix . 'meu_plugin_data';
		$output = isset( $assoc_args['output'] ) ? $assoc_args['output'] : 'export.json';
		$format = isset( $assoc_args['format'] ) ? $assoc_args['format'] : 'json';

		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) !== $table ) {
			WP_CLI::error( 'Tabela do plugin não existe. Execute wp meu-plugin db init.' );
		}

		$data = $wpdb->get_results( "SELECT * FROM {$table}", ARRAY_A );

		if ( 'csv' === $format ) {
			$content = $this->array_to_csv( $data );
		} else {
			$content = wp_json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
		}

		if ( false === file_put_contents( $output, $content ) ) {
			WP_CLI::error( 'Falha ao escrever arquivo.' );
		}

		WP_CLI::success( "Dados exportados para {$output}" );
	}

	/**
	 * Resetar banco (DROP + init). Exige --confirm.
	 *
	 * ## OPTIONS
	 *
	 * [--confirm]
	 * : Confirmar operação destrutiva
	 *
	 * ## EXAMPLES
	 *
	 *     wp meu-plugin db reset --confirm
	 *
	 * @when after_wp_load
	 */
	public function reset( $args, $assoc_args ) {
		if ( ! isset( $assoc_args['confirm'] ) ) {
			WP_CLI::error( 'Use --confirm para confirmar reset do banco.' );
		}

		global $wpdb;
		$tables = $wpdb->get_col( "SHOW TABLES LIKE '{$wpdb->prefix}meu_plugin_%'" );

		foreach ( $tables as $table ) {
			$wpdb->query( "DROP TABLE IF EXISTS {$table}" );
		}

		$this->init( array(), array() );
		WP_CLI::success( 'Banco resetado e reinicializado!' );
	}

	private function array_to_csv( array $data ) {
		if ( empty( $data ) ) {
			return '';
		}
		$out = fopen( 'php://memory', 'w' );
		fputcsv( $out, array_keys( $data[0] ) );
		foreach ( $data as $row ) {
			fputcsv( $out, $row );
		}
		rewind( $out );
		$content = stream_get_contents( $out );
		fclose( $out );
		return $content;
	}
}

WP_CLI::add_command( 'meu-plugin db', 'Estudos_WP_CLI_DB_Command' );
