<?php
/**
 * Exemplo 04: Tratamento de erros (handler centralizado + helpers)
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   rest_pre_dispatch: se $result é WP_Error, logar e/ou formatar resposta.
 *   Helpers: validation_error( $errors ) 422, not_found( $resource, $id ) 404, forbidden( $action ) 403.
 *   No controller: try/catch ou if ( is_wp_error( $x ) ) return $x; com status adequado.
 *
 * @package EstudosWP
 * @subpackage 03-REST-API-Avancado
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Helpers de erro para REST API.
 */
class Estudos_WP_REST_Errors {

	/**
	 * Erro de validação (422).
	 *
	 * @param array $errors [ 'campo' => 'mensagem' ].
	 * @return WP_Error
	 */
	public static function validation_error( array $errors ) {
		return new WP_Error(
			'rest_validation_failed',
			'Dados de entrada inválidos',
			array(
				'status' => 422,
				'errors' => $errors,
			)
		);
	}

	/**
	 * Recurso não encontrado (404).
	 *
	 * @param string   $resource Tipo do recurso (ex: 'Post').
	 * @param int|null $id       ID opcional.
	 * @return WP_Error
	 */
	public static function not_found( $resource, $id = null ) {
		$message = $id
			? sprintf( '%s com ID %s não encontrado', $resource, $id )
			: sprintf( '%s não encontrado', $resource );
		return new WP_Error(
			'rest_not_found',
			$message,
			array(
				'status'        => 404,
				'resource_type' => $resource,
				'id'            => $id,
			)
		);
	}

	/**
	 * Sem permissão (403).
	 *
	 * @param string|null $action Ação negada (opcional).
	 * @return WP_Error
	 */
	public static function forbidden( $action = null ) {
		$message = $action
			? sprintf( 'Você não tem permissão para %s', $action )
			: 'Você não tem permissão para esta ação';
		return new WP_Error(
			'rest_forbidden',
			$message,
			array( 'status' => 403 )
		);
	}

	/**
	 * Não autenticado (401).
	 *
	 * @return WP_Error
	 */
	public static function unauthorized() {
		return new WP_Error(
			'rest_unauthorized',
			'Autenticação necessária',
			array( 'status' => 401 )
		);
	}
}

/**
 * Error handler centralizado: loga erros REST e opcionalmente formata resposta.
 */
class Estudos_WP_REST_Error_Handler {

	/**
	 * Registrar no rest_pre_dispatch.
	 */
	public static function register() {
		add_filter( 'rest_pre_dispatch', array( __CLASS__, 'handle_errors' ), 10, 3 );
	}

	/**
	 * Se o resultado for WP_Error, logar (e opcionalmente formatar).
	 *
	 * @param mixed            $result  Resultado do dispatch.
	 * @param WP_REST_Server   $server  Server.
	 * @param WP_REST_Request  $request Request.
	 * @return mixed
	 */
	public static function handle_errors( $result, $server, $request ) {
		if ( ! is_wp_error( $result ) ) {
			return $result;
		}

		self::log_error( $result, $request );
		// Deixar o WP converter WP_Error em resposta; ou retornar self::format_response( $result ) para formato customizado.
		return $result;
	}

	/**
	 * Logar erro com contexto.
	 *
	 * @param WP_Error         $error   Erro.
	 * @param WP_REST_Request  $request Request.
	 */
	protected static function log_error( WP_Error $error, WP_REST_Request $request ) {
		if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
			return;
		}
		error_log(
			sprintf(
				'[REST API] %s: %s | Route: %s | Method: %s | User: %d',
				$error->get_error_code(),
				$error->get_error_message(),
				$request->get_route(),
				$request->get_method(),
				get_current_user_id()
			)
		);
	}
}

// Registrar handler (opcional; descomente no plugin):
// Estudos_WP_REST_Error_Handler::register();

// Uso no controller:
/*
if ( ! $post ) {
    return Estudos_WP_REST_Errors::not_found( 'Post', $id );
}
if ( ! current_user_can( 'edit_post', $id ) ) {
    return Estudos_WP_REST_Errors::forbidden( 'editar este post' );
}
if ( ! is_user_logged_in() ) {
    return Estudos_WP_REST_Errors::unauthorized();
}
return Estudos_WP_REST_Errors::validation_error( [ 'email' => 'Inválido' ] );
*/
