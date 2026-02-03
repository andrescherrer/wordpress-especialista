<?php
/**
 * Exemplo 04: Prepared statements e upload seguro
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   $wpdb->prepare( "SELECT ... WHERE id = %d", $id ); %d int, %s string.
 *   $wpdb->insert( $table, $data, $format ); $wpdb->update( ..., $where, $format, $where_format ).
 *   Upload: MIME real (finfo_file), whitelist de tipos, tamanho, sanitize nome; chmod 0644.
 *
 * @package EstudosWP
 * @subpackage 12-Seguranca-Boas-Praticas
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Queries seguras com prepare.
 */
class Estudos_WP_Prepared_Statements {

	/**
	 * Buscar posts por autor (seguro).
	 *
	 * @param int $user_id ID do autor.
	 * @return array
	 */
	public function get_posts_by_author( $user_id ) {
		global $wpdb;
		$user_id = absint( $user_id );
		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT ID, post_title FROM {$wpdb->posts} WHERE post_author = %d AND post_status = 'publish'",
				$user_id
			)
		);
	}

	/**
	 * Inserir registro com tipos definidos.
	 *
	 * @param array $data name, email.
	 * @return int|false
	 */
	public function safe_insert( $data ) {
		global $wpdb;
		$table = $wpdb->prefix . 'meu_plugin_data';
		return $wpdb->insert(
			$table,
			array(
				'name'       => sanitize_text_field( $data['name'] ),
				'email'      => sanitize_email( $data['email'] ),
				'created_at' => current_time( 'mysql' ),
			),
			array( '%s', '%s', '%s' )
		);
	}

	/**
	 * Update seguro com where.
	 */
	public function safe_update( $id, $data ) {
		global $wpdb;
		$table = $wpdb->prefix . 'meu_plugin_data';
		return $wpdb->update(
			$table,
			array(
				'name'       => sanitize_text_field( $data['name'] ),
				'updated_at' => current_time( 'mysql' ),
			),
			array( 'id' => absint( $id ) ),
			array( '%s', '%s' ),
			array( '%d' )
		);
	}
}

/**
 * Upload de arquivo com validação de MIME, tamanho e nome.
 */
class Estudos_WP_File_Upload_Secure {

	private $allowed_mimes = array( 'image/jpeg', 'image/png', 'image/webp' );
	private $max_size      = 5 * 1024 * 1024; // 5MB

	/**
	 * Processar upload com validações.
	 *
	 * @param array $file Elemento de $_FILES['campo'].
	 * @return array|WP_Error
	 */
	public function handle_upload( $file ) {
		if ( empty( $file['tmp_name'] ) || ! is_uploaded_file( $file['tmp_name'] ) ) {
			return new WP_Error( 'no_file', __( 'Nenhum arquivo enviado.', 'meu-plugin' ) );
		}

		$mime = $this->get_real_mime_type( $file['tmp_name'] );
		if ( ! in_array( $mime, $this->allowed_mimes, true ) ) {
			return new WP_Error( 'invalid_type', __( 'Tipo de arquivo não permitido.', 'meu-plugin' ) );
		}

		if ( $file['size'] > $this->max_size ) {
			return new WP_Error( 'file_too_large', __( 'Arquivo muito grande.', 'meu-plugin' ) );
		}

		$name = sanitize_file_name( $file['name'] );
		$name = wp_unique_filename( wp_upload_dir()['path'], $name );
		$dir  = wp_upload_dir();
		$path = $dir['path'] . '/' . $name;

		if ( ! move_uploaded_file( $file['tmp_name'], $path ) ) {
			return new WP_Error( 'move_failed', __( 'Erro ao salvar arquivo.', 'meu-plugin' ) );
		}
		chmod( $path, 0644 );

		return array(
			'path' => $path,
			'url'  => $dir['url'] . '/' . $name,
		);
	}

	private function get_real_mime_type( $path ) {
		if ( function_exists( 'finfo_file' ) ) {
			$finfo = finfo_open( FILEINFO_MIME_TYPE );
			$mime  = finfo_file( $finfo, $path );
			finfo_close( $finfo );
			return $mime ?: 'application/octet-stream';
		}
		if ( function_exists( 'getimagesize' ) ) {
			$info = @getimagesize( $path );
			return isset( $info['mime'] ) ? $info['mime'] : 'application/octet-stream';
		}
		return 'application/octet-stream';
	}
}
