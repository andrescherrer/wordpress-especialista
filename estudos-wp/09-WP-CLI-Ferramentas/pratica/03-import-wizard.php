<?php
/**
 * Exemplo 03: Wizard de importação interativo (CSV/JSON)
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   WP_CLI::prompt( 'Pergunta', $default ); WP_CLI::confirm( 'Continuar?' );
 *   Validar: loop enquanto inválido ou callable no prompt.
 *   Progress: make_progress_bar com total; tick a cada registro; finish().
 *   Arquivo: file_exists; CSV com fgetcsv; JSON com json_decode.
 *
 * Uso: wp meu-plugin import (prompts para tipo e caminho do arquivo)
 *
 * @package EstudosWP
 * @subpackage 09-WP-CLI-Ferramentas
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

/**
 * Wizard de importação: prompts e processamento CSV/JSON.
 */
class Estudos_WP_CLI_Import_Command {

	/**
	 * Importar dados interativamente.
	 *
	 * ## EXAMPLES
	 *
	 *     wp meu-plugin import
	 *
	 * @when after_wp_load
	 */
	public function __invoke( $args, $assoc_args ) {
		WP_CLI::log( '' );
		WP_CLI::log( '=== Wizard de Importação ===' );
		WP_CLI::log( '' );

		WP_CLI::log( 'Tipo de arquivo: 1) CSV  2) JSON' );
		$type_choice = WP_CLI::prompt( 'Escolha (1 ou 2)', '1' );
		$format      = ( '2' === $type_choice ) ? 'json' : 'csv';

		$file_path = WP_CLI::prompt( 'Caminho do arquivo' );
		$file_path = trim( $file_path );

		if ( ! file_exists( $file_path ) ) {
			WP_CLI::error( 'Arquivo não encontrado.' );
		}

		WP_CLI::log( '' );
		WP_CLI::log( 'Arquivo: ' . $file_path );
		WP_CLI::log( 'Tamanho: ' . size_format( filesize( $file_path ) ) );
		WP_CLI::log( '' );

		WP_CLI::confirm( 'Continuar com a importação?' );

		if ( 'json' === $format ) {
			$this->import_json( $file_path );
		} else {
			$this->import_csv( $file_path );
		}
	}

	private function import_csv( $file_path ) {
		global $wpdb;
		$table = $wpdb->prefix . 'meu_plugin_data';

		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) !== $table ) {
			WP_CLI::error( 'Tabela não existe. Execute wp meu-plugin db init.' );
		}

		$file = fopen( $file_path, 'r' );
		if ( ! $file ) {
			WP_CLI::error( 'Erro ao abrir arquivo.' );
		}

		$headers = fgetcsv( $file );
		if ( ! $headers ) {
			fclose( $file );
			WP_CLI::error( 'Arquivo CSV vazio ou inválido.' );
		}

		$count   = 0;
		$total   = $this->count_lines( $file_path ) - 1;
		$total   = max( 1, $total );
		$progress = \WP_CLI\Utils\make_progress_bar( 'Importando', $total );

		while ( ( $row = fgetcsv( $file ) ) !== false ) {
			$data = array_combine( $headers, $row );
			$wpdb->insert(
				$table,
				array(
					'uuid' => wp_generate_uuid4(),
					'data' => wp_json_encode( $data ),
				),
				array( '%s', '%s' )
			);
			$count++;
			$progress->tick();
		}

		$progress->finish();
		fclose( $file );
		WP_CLI::success( "{$count} registros importados!" );
	}

	private function import_json( $file_path ) {
		global $wpdb;
		$table = $wpdb->prefix . 'meu_plugin_data';

		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) !== $table ) {
			WP_CLI::error( 'Tabela não existe. Execute wp meu-plugin db init.' );
		}

		$content = file_get_contents( $file_path );
		$data    = json_decode( $content, true );

		if ( json_last_error() !== JSON_ERROR_NONE ) {
			WP_CLI::error( 'JSON inválido: ' . json_last_error_msg() );
		}

		if ( ! is_array( $data ) ) {
			WP_CLI::error( 'JSON deve ser um array de objetos.' );
		}

		$total   = count( $data );
		$progress = \WP_CLI\Utils\make_progress_bar( 'Importando', $total );

		foreach ( $data as $record ) {
			$wpdb->insert(
				$table,
				array(
					'uuid' => wp_generate_uuid4(),
					'data' => wp_json_encode( $record ),
				),
				array( '%s', '%s' )
			);
			$progress->tick();
		}

		$progress->finish();
		WP_CLI::success( count( $data ) . ' registros importados!' );
	}

	private function count_lines( $file_path ) {
		$count = 0;
		$f     = fopen( $file_path, 'r' );
		while ( fgets( $f ) ) {
			$count++;
		}
		fclose( $f );
		return $count;
	}
}

WP_CLI::add_command( 'meu-plugin import', 'Estudos_WP_CLI_Import_Command' );
