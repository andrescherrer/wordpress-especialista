<?php
/**
 * Exemplo 05: Subcomando CRUD (meu-plugin data list|create|update|delete)
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   $args[0], $args[1] = argumentos posicionais
 *   $assoc_args['format'], $assoc_args['limit'] = opções
 *   Saída: table (WP_CLI::table), json (WP_CLI::line), csv (echo)
 *   WP_CLI::confirm() antes de delete; --force para pular confirmação
 *
 * Uso: wp meu-plugin data list | wp meu-plugin data create "Nome" "Valor" | wp meu-plugin data update 1 "N" "V" | wp meu-plugin data delete 1
 *
 * @package EstudosWP
 * @subpackage 07-WP-CLI-Fundamentos
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

/**
 * Subcomando "data": list, create, update, delete.
 */
class Estudos_WP_CLI_Data_Command {

	/**
	 * Listar registros.
	 *
	 * ## OPTIONS
	 *
	 * [--format=<format>]
	 * : table, json, csv
	 * ---
	 * default: table
	 * ---
	 *
	 * [--limit=<limit>]
	 * : Quantidade
	 * ---
	 * default: 20
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     wp meu-plugin data list
	 *     wp meu-plugin data list --format=json --limit=50
	 *
	 * @when after_wp_load
	 */
	public function list( $args, $assoc_args ) {
		global $wpdb;

		$table  = $wpdb->prefix . 'meu_plugin_data';
		$limit  = isset( $assoc_args['limit'] ) ? (int) $assoc_args['limit'] : 20;
		$format = isset( $assoc_args['format'] ) ? $assoc_args['format'] : 'table';

		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) !== $table ) {
			WP_CLI::error( 'Tabela do plugin não existe' );
		}

		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT id, name, value, created_at FROM {$table} LIMIT %d",
				$limit
			)
		);

		if ( empty( $results ) ) {
			WP_CLI::warning( 'Nenhum registro encontrado' );
			return;
		}

		if ( 'json' === $format ) {
			WP_CLI::line( wp_json_encode( $results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) );
		} elseif ( 'csv' === $format ) {
			echo "id,name,value,created_at\n";
			foreach ( $results as $row ) {
				echo (int) $row->id . ',' . esc_html( $row->name ) . ',' . esc_html( $row->value ) . ',' . esc_html( $row->created_at ) . "\n";
			}
		} else {
			WP_CLI::table(
				array( 'ID', 'Name', 'Value', 'Created' ),
				array_map(
					function ( $row ) {
						return array( $row->id, $row->name, $row->value, $row->created_at );
					},
					$results
				)
			);
		}
	}

	/**
	 * Criar registro.
	 *
	 * ## OPTIONS
	 *
	 * <name>
	 * : Nome do registro
	 *
	 * <value>
	 * : Valor do registro
	 *
	 * ## EXAMPLES
	 *
	 *     wp meu-plugin data create "Chave 1" "Valor 1"
	 *
	 * @when after_wp_load
	 */
	public function create( $args, $assoc_args ) {
		global $wpdb;

		$table = $wpdb->prefix . 'meu_plugin_data';
		$name  = isset( $args[0] ) ? $args[0] : '';
		$value = isset( $args[1] ) ? $args[1] : '';

		if ( '' === $name ) {
			WP_CLI::error( 'Informe o nome' );
		}

		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) !== $table ) {
			WP_CLI::error( 'Tabela do plugin não existe' );
		}

		$result = $wpdb->insert(
			$table,
			array(
				'name'       => sanitize_text_field( $name ),
				'value'      => sanitize_text_field( $value ),
				'created_at' => current_time( 'mysql' ),
			),
			array( '%s', '%s', '%s' )
		);

		if ( $result ) {
			WP_CLI::success( 'Registro criado com ID: ' . $wpdb->insert_id );
		} else {
			WP_CLI::error( 'Falha ao criar registro' );
		}
	}

	/**
	 * Atualizar registro.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : ID do registro
	 *
	 * <name>
	 * : Novo nome
	 *
	 * <value>
	 * : Novo valor
	 *
	 * ## EXAMPLES
	 *
	 *     wp meu-plugin data update 1 "Novo Nome" "Novo Valor"
	 *
	 * @when after_wp_load
	 */
	public function update( $args, $assoc_args ) {
		global $wpdb;

		$table = $wpdb->prefix . 'meu_plugin_data';
		$id    = isset( $args[0] ) ? (int) $args[0] : 0;
		$name  = isset( $args[1] ) ? $args[1] : '';
		$value = isset( $args[2] ) ? $args[2] : '';

		if ( $id <= 0 ) {
			WP_CLI::error( 'ID inválido' );
		}

		$result = $wpdb->update(
			$table,
			array(
				'name'  => sanitize_text_field( $name ),
				'value' => sanitize_text_field( $value ),
			),
			array( 'id' => $id ),
			array( '%s', '%s' ),
			array( '%d' )
		);

		if ( false !== $result ) {
			WP_CLI::success( "Registro {$id} atualizado!" );
		} else {
			WP_CLI::error( 'Falha ao atualizar registro' );
		}
	}

	/**
	 * Deletar registro.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : ID do registro
	 *
	 * [--force]
	 * : Não pedir confirmação
	 *
	 * ## EXAMPLES
	 *
	 *     wp meu-plugin data delete 1
	 *     wp meu-plugin data delete 1 --force
	 *
	 * @when after_wp_load
	 */
	public function delete( $args, $assoc_args ) {
		global $wpdb;

		$table = $wpdb->prefix . 'meu_plugin_data';
		$id    = isset( $args[0] ) ? (int) $args[0] : 0;

		if ( $id <= 0 ) {
			WP_CLI::error( 'ID inválido' );
		}

		if ( ! isset( $assoc_args['force'] ) ) {
			WP_CLI::confirm( "Deletar registro {$id}?" );
		}

		$result = $wpdb->delete( $table, array( 'id' => $id ), array( '%d' ) );

		if ( $result ) {
			WP_CLI::success( "Registro {$id} deletado!" );
		} else {
			WP_CLI::error( 'Falha ao deletar registro' );
		}
	}
}

WP_CLI::add_command( 'meu-plugin data', 'Estudos_WP_CLI_Data_Command' );
