<?php
/**
 * Exemplo 01: Controller REST completo (CRUD + prepare_item + validate)
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   Estender WP_REST_Controller; register_routes() com GET/POST e GET/PUT/DELETE por id.
 *   prepare_item_for_response( $object ) → array para JSON.
 *   validate_*_data( $params, $is_update ) → true ou WP_Error.
 *   get_collection_params() → page, per_page, orderby, order (enum).
 *   Permissions: *_permissions_check com is_user_logged_in() e current_user_can( 'edit_post', $id ).
 *
 * @package EstudosWP
 * @subpackage 03-REST-API-Avancado
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'rest_api_init', array( new Estudos_WP_REST_Artigos_Controller(), 'register_routes' ) );

/**
 * Controller CRUD para "artigos" (posts do tipo post).
 */
class Estudos_WP_REST_Artigos_Controller extends WP_REST_Controller {

	protected $namespace = 'estudos-wp/v1';
	protected $rest_base = 'artigos';

	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_item' ),
					'permission_callback' => array( $this, 'create_item_permissions_check' ),
					'args'                => array(
						'title'   => array(
							'required'          => true,
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_text_field',
						),
						'content' => array(
							'required'          => true,
							'type'              => 'string',
							'sanitize_callback' => 'wp_kses_post',
						),
					),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>\d+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => array(
						'id' => array(
							'required'          => true,
							'type'              => 'integer',
							'validate_callback' => function( $v ) {
								return is_numeric( $v ) && (int) $v > 0;
							},
						),
					),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_item' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
					'args'                => array(
						'id'      => array( 'required' => true, 'type' => 'integer' ),
						'title'   => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ),
						'content' => array( 'type' => 'string', 'sanitize_callback' => 'wp_kses_post' ),
					),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_item' ),
					'permission_callback' => array( $this, 'delete_item_permissions_check' ),
					'args'                => array(
						'id' => array( 'required' => true, 'type' => 'integer' ),
					),
				),
			)
		);
	}

	public function get_items( $request ) {
		$per_page = $request->get_param( 'per_page' );
		$page     = $request->get_param( 'page' );
		$orderby  = $request->get_param( 'orderby' );
		$order    = $request->get_param( 'order' );

		$query = new WP_Query(
			array(
				'post_type'      => 'post',
				'post_status'    => 'publish',
				'posts_per_page' => $per_page,
				'paged'          => $page,
				'orderby'        => $orderby,
				'order'          => $order,
			)
		);

		$items = array_map( array( $this, 'prepare_item_for_response' ), $query->posts );
		$response = rest_ensure_response( $items );
		$response->header( 'X-WP-Total', $query->found_posts );
		$response->header( 'X-WP-TotalPages', $query->max_num_pages );
		return $response;
	}

	public function get_item( $request ) {
		$id   = (int) $request->get_param( 'id' );
		$post = get_post( $id );

		if ( ! $post || $post->post_type !== 'post' ) {
			return new WP_Error(
				'rest_post_not_found',
				'Post não encontrado',
				array( 'status' => 404 )
			);
		}

		return rest_ensure_response( $this->prepare_item_for_response( $post ) );
	}

	public function create_item( $request ) {
		$params = array(
			'title'   => $request->get_param( 'title' ),
			'content' => $request->get_param( 'content' ),
		);

		$validation = $this->validate_artigo_data( $params, false );
		if ( is_wp_error( $validation ) ) {
			return $validation;
		}

		$post_id = wp_insert_post(
			array(
				'post_title'   => $params['title'],
				'post_content' => $params['content'],
				'post_type'    => 'post',
				'post_status'  => 'draft',
				'post_author'  => get_current_user_id(),
			),
			true
		);

		if ( is_wp_error( $post_id ) ) {
			return new WP_Error(
				'rest_create_failed',
				$post_id->get_error_message(),
				array( 'status' => 500 )
			);
		}

		$post = get_post( $post_id );
		return new WP_REST_Response(
			array(
				'success' => true,
				'data'    => $this->prepare_item_for_response( $post ),
				'message' => 'Artigo criado com sucesso',
			),
			201
		);
	}

	public function update_item( $request ) {
		$id   = (int) $request->get_param( 'id' );
		$post = get_post( $id );

		if ( ! $post || $post->post_type !== 'post' ) {
			return new WP_Error(
				'rest_post_not_found',
				'Post não encontrado',
				array( 'status' => 404 )
			);
		}

		$params = array(
			'title'   => $request->get_param( 'title' ),
			'content' => $request->get_param( 'content' ),
		);
		$params = array_filter( $params, function( $v ) {
			return $v !== null && $v !== '';
		} );

		if ( ! empty( $params ) ) {
			$validation = $this->validate_artigo_data( $params, true );
			if ( is_wp_error( $validation ) ) {
				return $validation;
			}

			$update = array( 'ID' => $id );
			if ( isset( $params['title'] ) ) {
				$update['post_title'] = $params['title'];
			}
			if ( isset( $params['content'] ) ) {
				$update['post_content'] = $params['content'];
			}
			wp_update_post( $update );
		}

		$post = get_post( $id );
		return rest_ensure_response(
			array(
				'success' => true,
				'data'    => $this->prepare_item_for_response( $post ),
				'message' => 'Artigo atualizado',
			)
		);
	}

	public function delete_item( $request ) {
		$id   = (int) $request->get_param( 'id' );
		$post = get_post( $id );

		if ( ! $post || $post->post_type !== 'post' ) {
			return new WP_Error(
				'rest_post_not_found',
				'Post não encontrado',
				array( 'status' => 404 )
			);
		}

		$deleted = wp_delete_post( $id, true );
		if ( ! $deleted ) {
			return new WP_Error(
				'rest_delete_failed',
				'Falha ao deletar',
				array( 'status' => 500 )
			);
		}

		return new WP_REST_Response(
			array( 'success' => true, 'message' => 'Artigo removido' ),
			200
		);
	}

	public function prepare_item_for_response( $post ) {
		if ( ! $post instanceof WP_Post ) {
			return array();
		}
		return array(
			'id'       => $post->ID,
			'title'    => esc_html( $post->post_title ),
			'content'  => wp_kses_post( $post->post_content ),
			'status'   => $post->post_status,
			'date'     => get_the_date( 'c', $post ),
			'modified' => get_the_modified_date( 'c', $post ),
			'link'     => get_permalink( $post ),
		);
	}

	public function get_collection_params() {
		return array(
			'page'     => array(
				'type'              => 'integer',
				'default'           => 1,
				'minimum'           => 1,
				'sanitize_callback' => 'absint',
			),
			'per_page' => array(
				'type'              => 'integer',
				'default'           => 10,
				'minimum'           => 1,
				'maximum'           => 100,
				'sanitize_callback' => 'absint',
			),
			'orderby'  => array(
				'type'              => 'string',
				'default'           => 'date',
				'enum'              => array( 'date', 'title', 'modified' ),
				'sanitize_callback' => 'sanitize_text_field',
			),
			'order'    => array(
				'type'              => 'string',
				'default'           => 'DESC',
				'enum'              => array( 'ASC', 'DESC' ),
				'sanitize_callback' => 'sanitize_text_field',
			),
		);
	}

	protected function validate_artigo_data( $data, $is_update = false ) {
		if ( ! $is_update && empty( $data['title'] ) ) {
			return new WP_Error( 'rest_missing_title', 'Título é obrigatório', array( 'status' => 400 ) );
		}
		if ( isset( $data['title'] ) && strlen( $data['title'] ) > 255 ) {
			return new WP_Error( 'rest_title_too_long', 'Título no máximo 255 caracteres', array( 'status' => 400 ) );
		}
		if ( ! $is_update && empty( $data['content'] ) ) {
			return new WP_Error( 'rest_missing_content', 'Conteúdo é obrigatório', array( 'status' => 400 ) );
		}
		return true;
	}

	public function get_items_permissions_check( $request ) {
		return true;
	}

	public function get_item_permissions_check( $request ) {
		return true;
	}

	public function create_item_permissions_check( $request ) {
		return is_user_logged_in() && current_user_can( 'edit_posts' );
	}

	public function update_item_permissions_check( $request ) {
		$id = (int) $request->get_param( 'id' );
		return is_user_logged_in() && current_user_can( 'edit_post', $id );
	}

	public function delete_item_permissions_check( $request ) {
		$id = (int) $request->get_param( 'id' );
		return is_user_logged_in() && current_user_can( 'delete_post', $id );
	}
}
