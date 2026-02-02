<?php
/**
 * Exemplo 04: Permissões (permission_callback) e resposta/erro
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   permission_callback: __return_true (público) ou function( $request ) { return bool; }
 *   Retornar WP_Error para 401/403: new WP_Error( 'code', 'msg', [ 'status' => 401 ] );
 *   Sucesso: rest_ensure_response( $data ) ou new WP_REST_Response( $data, 201 )
 *   Erro: return new WP_Error( 'codigo', 'Mensagem', [ 'status' => 404|422|500 ] );
 *   Paginação: $response->header( 'X-WP-Total', $total ); X-WP-TotalPages
 *
 * @package EstudosWP
 * @subpackage 02-REST-API-Fundamentos
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'rest_api_init', 'estudos_wp_rest_registrar_rotas_permissao' );

function estudos_wp_rest_registrar_rotas_permissao() {
	$namespace = 'estudos-wp/v1';

	// Público: qualquer um pode ler
	register_rest_route(
		$namespace,
		'/config-publica',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'estudos_wp_rest_config_publica',
			'permission_callback' => '__return_true',
		)
	);

	// Autenticado: só usuário logado
	register_rest_route(
		$namespace,
		'/meu-perfil',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'estudos_wp_rest_meu_perfil',
			'permission_callback' => 'estudos_wp_rest_permission_autenticado',
		)
	);

	// Capability: só quem pode editar posts
	register_rest_route(
		$namespace,
		'/rascunhos',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'estudos_wp_rest_rascunhos',
			'permission_callback' => 'estudos_wp_rest_permission_edit_posts',
		)
	);

	// Exemplo com retorno de WP_Error explícito no permission_callback
	register_rest_route(
		$namespace,
		'/admin-only',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'estudos_wp_rest_admin_only',
			'permission_callback' => 'estudos_wp_rest_permission_admin',
		)
	);
}

/**
 * Permission: exige usuário autenticado.
 *
 * @param WP_REST_Request $request Request.
 * @return bool|WP_Error
 */
function estudos_wp_rest_permission_autenticado( WP_REST_Request $request ) {
	if ( ! is_user_logged_in() ) {
		return new WP_Error(
			'rest_not_logged_in',
			'Você precisa estar autenticado.',
			array( 'status' => 401 )
		);
	}
	return true;
}

/**
 * Permission: exige capability edit_posts.
 *
 * @param WP_REST_Request $request Request.
 * @return bool|WP_Error
 */
function estudos_wp_rest_permission_edit_posts( WP_REST_Request $request ) {
	if ( ! current_user_can( 'edit_posts' ) ) {
		return new WP_Error(
			'rest_forbidden',
			'Você não tem permissão para acessar este recurso.',
			array( 'status' => 403 )
		);
	}
	return true;
}

/**
 * Permission: exige manage_options (admin).
 *
 * @param WP_REST_Request $request Request.
 * @return bool|WP_Error
 */
function estudos_wp_rest_permission_admin( WP_REST_Request $request ) {
	if ( ! current_user_can( 'manage_options' ) ) {
		return new WP_Error(
			'rest_forbidden',
			'Apenas administradores podem acessar.',
			array( 'status' => 403 )
		);
	}
	return true;
}

function estudos_wp_rest_config_publica( WP_REST_Request $request ) {
	return rest_ensure_response(
		array(
			'site_name' => get_bloginfo( 'name' ),
			'timezone'  => wp_timezone_string(),
		)
	);
}

function estudos_wp_rest_meu_perfil( WP_REST_Request $request ) {
	$user = wp_get_current_user();
	return rest_ensure_response(
		array(
			'id'       => $user->ID,
			'login'    => $user->user_login,
			'email'    => $user->user_email,
			'nome'     => $user->display_name,
		)
	);
}

function estudos_wp_rest_rascunhos( WP_REST_Request $request ) {
	$query = new WP_Query(
		array(
			'post_type'      => 'post',
			'post_status'    => 'draft',
			'author'         => get_current_user_id(),
			'posts_per_page' => 10,
		)
	);

	$posts = array();
	foreach ( $query->posts as $post ) {
		$posts[] = array(
			'id'    => $post->ID,
			'title' => esc_html( $post->post_title ),
		);
	}

	$response = rest_ensure_response( $posts );
	$response->header( 'X-WP-Total', $query->found_posts );
	return $response;
}

function estudos_wp_rest_admin_only( WP_REST_Request $request ) {
	return rest_ensure_response(
		array(
			'mensagem' => 'Área restrita a administradores.',
			'versao_wp' => get_bloginfo( 'version' ),
		)
	);
}
