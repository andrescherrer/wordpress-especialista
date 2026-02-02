<?php
/**
 * Exemplo 02: Resposta estruturada (wrapper success / error / paginated)
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   Sucesso: success( $data, $message, $status ) → WP_REST_Response com success/data/message/meta.
 *   Erro: error( $code, $message, $status ) → WP_Error.
 *   Validação: validation_error( $errors ) → WP_Error 422 com lista de erros.
 *   Lista paginada: paginated_list( $items, $total, $page, $per_page ).
 *
 * @package EstudosWP
 * @subpackage 03-REST-API-Avancado
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Wrapper para respostas REST consistentes.
 */
class Estudos_WP_REST_Response {

	/**
	 * Resposta de sucesso.
	 *
	 * @param mixed  $data    Dados a retornar.
	 * @param string $message Mensagem opcional.
	 * @param int    $status  Código HTTP (default 200).
	 * @return WP_REST_Response
	 */
	public static function success( $data, $message = '', $status = 200 ) {
		$body = array(
			'success' => true,
			'data'    => $data,
			'message' => $message,
			'code'    => 'rest_success',
			'meta'    => array(
				'timestamp' => current_time( 'mysql' ),
				'status'    => $status,
			),
		);
		return new WP_REST_Response( $body, $status );
	}

	/**
	 * Resposta de erro.
	 *
	 * @param string $code    Código do erro.
	 * @param string $message Mensagem.
	 * @param int    $status  Código HTTP (default 400).
	 * @return WP_Error
	 */
	public static function error( $code, $message, $status = 400 ) {
		return new WP_Error(
			$code,
			$message,
			array(
				'status'    => $status,
				'timestamp' => current_time( 'mysql' ),
			)
		);
	}

	/**
	 * Erro de validação (422) com lista de erros por campo.
	 *
	 * @param array $errors Ex.: [ 'email' => 'Email inválido', 'nome' => 'Obrigatório' ].
	 * @return WP_Error
	 */
	public static function validation_error( array $errors ) {
		return new WP_Error(
			'rest_validation_failed',
			'Validação falhou',
			array(
				'status'    => 422,
				'errors'    => $errors,
				'timestamp' => current_time( 'mysql' ),
			)
		);
	}

	/**
	 * Lista paginada padronizada.
	 *
	 * @param array $items    Itens da página.
	 * @param int   $total    Total de itens.
	 * @param int   $page     Página atual.
	 * @param int   $per_page Itens por página.
	 * @return WP_REST_Response
	 */
	public static function paginated_list( $items, $total, $page, $per_page ) {
		$total_pages = $per_page > 0 ? (int) ceil( $total / $per_page ) : 0;
		$body = array(
			'success'     => true,
			'data'        => $items,
			'pagination'  => array(
				'total'        => $total,
				'current_page' => $page,
				'per_page'     => $per_page,
				'total_pages'  => $total_pages,
			),
			'meta' => array(
				'timestamp' => current_time( 'mysql' ),
			),
		);
		return new WP_REST_Response( $body, 200 );
	}
}

// Exemplo de uso no controller:
/*
// Sucesso simples
return Estudos_WP_REST_Response::success( $post, 'Post criado', 201 );

// Erro
return Estudos_WP_REST_Response::error( 'rest_not_found', 'Recurso não encontrado', 404 );

// Validação
return Estudos_WP_REST_Response::validation_error( [
    'email' => 'Email inválido',
    'nome'  => 'Nome é obrigatório',
] );

// Lista paginada
return Estudos_WP_REST_Response::paginated_list( $items, $total, $page, $per_page );
*/
