<?php
/**
 * Exemplo 01: Registrar rotas REST API
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   add_action( 'rest_api_init', callback );
 *   register_rest_route( namespace, route, [ methods, callback, permission_callback, args ] );
 *   Métodos: WP_REST_Server::READABLE (GET), CREATABLE (POST), EDITABLE (PUT/PATCH), DELETABLE (DELETE)
 *   Rota com ID: '/(?P<id>\d+)'  →  $request->get_param( 'id' );
 *   Callback recebe WP_REST_Request $request.
 *
 * @package EstudosWP
 * @subpackage 02-REST-API-Fundamentos
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'rest_api_init', 'estudos_wp_rest_registrar_rotas' );

function estudos_wp_rest_registrar_rotas() {
	$namespace = 'estudos-wp/v1';

	// GET /wp-json/estudos-wp/v1/hello
	register_rest_route(
		$namespace,
		'/hello',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'estudos_wp_rest_hello',
			'permission_callback' => '__return_true',
			'args'                => array(
				'nome' => array(
					'type'              => 'string',
					'default'           => 'Mundo',
					'sanitize_callback' => 'sanitize_text_field',
					'description'       => 'Nome para saudação',
				),
			),
		)
	);

	// GET /wp-json/estudos-wp/v1/posts-recentes
	register_rest_route(
		$namespace,
		'/posts-recentes',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'estudos_wp_rest_posts_recentes',
			'permission_callback' => '__return_true',
			'args'                => array(
				'per_page' => array(
					'type'              => 'integer',
					'default'           => 5,
					'minimum'           => 1,
					'maximum'           => 20,
					'sanitize_callback' => 'absint',
					'description'       => 'Quantidade de posts',
				),
				'page'     => array(
					'type'              => 'integer',
					'default'           => 1,
					'sanitize_callback' => 'absint',
					'description'       => 'Página',
				),
			),
		)
	);

	// GET /wp-json/estudos-wp/v1/post/(?P<id>\d+)
	register_rest_route(
		$namespace,
		'/post/(?P<id>\d+)',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'estudos_wp_rest_post_por_id',
			'permission_callback' => '__return_true',
			'args'                => array(
				'id' => array(
					'required'          => true,
					'type'              => 'integer',
					'validate_callback' => function( $param ) {
						return is_numeric( $param ) && $param > 0;
					},
					'description'       => 'ID do post',
				),
			),
		)
	);
}

/**
 * GET /hello – saudação simples.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response
 */
function estudos_wp_rest_hello( WP_REST_Request $request ) {
	$nome = $request->get_param( 'nome' );
	return new WP_REST_Response(
		array(
			'mensagem' => 'Olá, ' . esc_html( $nome ) . '!',
			'timestamp' => current_time( 'c' ),
		),
		200
	);
}

/**
 * GET /posts-recentes – lista posts publicados.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function estudos_wp_rest_posts_recentes( WP_REST_Request $request ) {
	$per_page = $request->get_param( 'per_page' );
	$page     = $request->get_param( 'page' );

	$query = new WP_Query(
		array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => $per_page,
			'paged'          => $page,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);

	$posts = array();
	foreach ( $query->posts as $post ) {
		$posts[] = array(
			'id'    => $post->ID,
			'title' => esc_html( $post->post_title ),
			'date'  => get_the_date( 'c', $post ),
			'link'  => get_permalink( $post ),
		);
	}

	$response = rest_ensure_response( $posts );
	$response->header( 'X-WP-Total', $query->found_posts );
	$response->header( 'X-WP-TotalPages', $query->max_num_pages );

	return $response;
}

/**
 * GET /post/{id} – um post por ID.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function estudos_wp_rest_post_por_id( WP_REST_Request $request ) {
	$id   = (int) $request->get_param( 'id' );
	$post = get_post( $id );

	if ( ! $post || $post->post_status !== 'publish' ) {
		return new WP_Error(
			'rest_post_not_found',
			'Post não encontrado',
			array( 'status' => 404 )
		);
	}

	return rest_ensure_response(
		array(
			'id'      => $post->ID,
			'title'   => esc_html( $post->post_title ),
			'content' => wp_kses_post( $post->post_content ),
			'date'    => get_the_date( 'c', $post ),
			'link'    => get_permalink( $post ),
		)
	);
}
