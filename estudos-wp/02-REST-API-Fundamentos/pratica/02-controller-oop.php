<?php
/**
 * Exemplo 02: REST Controller (OOP) com WP_REST_Controller
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   Estender WP_REST_Controller; definir $namespace e $rest_base.
 *   add_action( 'rest_api_init', [ $this, 'register_routes' ] );
 *   register_routes(): register_rest_route para coleção (GET/POST) e item (GET/PUT/DELETE por id).
 *   get_items, get_item, create_item, update_item, delete_item.
 *   get_items_permissions_check, create_item_permissions_check, etc.
 *   get_collection_params() para per_page, page, orderby, order.
 *
 * @package EstudosWP
 * @subpackage 02-REST-API-Fundamentos
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'rest_api_init', array( new Estudos_WP_REST_Items_Controller(), 'register_routes' ) );

/**
 * Controller de exemplo: CRUD de "itens" (dados em memória para demonstração).
 */
class Estudos_WP_REST_Items_Controller extends WP_REST_Controller {

	protected $namespace = 'estudos-wp/v1';
	protected $rest_base = 'items';

	/**
	 * Registrar rotas.
	 */
	public function register_routes() {
		// GET/POST /items
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
						'titulo' => array(
							'required'          => true,
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_text_field',
							'description'       => 'Título do item',
						),
					),
				),
			)
		);

		// GET/PUT/DELETE /items/(?P<id>\d+)
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
							'validate_callback' => function( $param ) {
								return is_numeric( $param ) && $param > 0;
							},
						),
					),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_item' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
					'args'                => array(
						'id'     => array( 'required' => true, 'type' => 'integer' ),
						'titulo' => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ),
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

	/**
	 * GET /items
	 */
	public function get_items( $request ) {
		$itens = $this->get_itens_store();
		$per_page = $request->get_param( 'per_page' );
		$page     = $request->get_param( 'page' );
		$offset   = ( $page - 1 ) * $per_page;
		$sliced   = array_slice( $itens, $offset, $per_page );

		$response = rest_ensure_response( $sliced );
		$response->header( 'X-WP-Total', count( $itens ) );
		$response->header( 'X-WP-TotalPages', (int) ceil( count( $itens ) / $per_page ) );

		return $response;
	}

	/**
	 * POST /items
	 */
	public function create_item( $request ) {
		$itens   = $this->get_itens_store();
		$novo_id = empty( $itens ) ? 1 : max( array_keys( $itens ) ) + 1;
		$titulo  = $request->get_param( 'titulo' );

		$itens[ $novo_id ] = array(
			'id'        => $novo_id,
			'titulo'    => $titulo,
			'criado_em' => current_time( 'c' ),
		);
		$this->set_itens_store( $itens );

		return new WP_REST_Response( $itens[ $novo_id ], 201 );
	}

	/**
	 * GET /items/{id}
	 */
	public function get_item( $request ) {
		$id   = (int) $request->get_param( 'id' );
		$itens = $this->get_itens_store();

		if ( ! isset( $itens[ $id ] ) ) {
			return new WP_Error(
				'rest_item_not_found',
				'Item não encontrado',
				array( 'status' => 404 )
			);
		}

		return rest_ensure_response( $itens[ $id ] );
	}

	/**
	 * PUT/PATCH /items/{id}
	 */
	public function update_item( $request ) {
		$id     = (int) $request->get_param( 'id' );
		$itens  = $this->get_itens_store();

		if ( ! isset( $itens[ $id ] ) ) {
			return new WP_Error(
				'rest_item_not_found',
				'Item não encontrado',
				array( 'status' => 404 )
			);
		}

		if ( $request->has_param( 'titulo' ) ) {
			$itens[ $id ]['titulo'] = $request->get_param( 'titulo' );
			$itens[ $id ]['atualizado_em'] = current_time( 'c' );
			$this->set_itens_store( $itens );
		}

		return rest_ensure_response( $itens[ $id ] );
	}

	/**
	 * DELETE /items/{id}
	 */
	public function delete_item( $request ) {
		$id    = (int) $request->get_param( 'id' );
		$itens = $this->get_itens_store();

		if ( ! isset( $itens[ $id ] ) ) {
			return new WP_Error(
				'rest_item_not_found',
				'Item não encontrado',
				array( 'status' => 404 )
			);
		}

		unset( $itens[ $id ] );
		$this->set_itens_store( $itens );

		return new WP_REST_Response( array( 'deleted' => true ), 200 );
	}

	public function get_items_permissions_check( $request ) {
		return true;
	}

	public function get_item_permissions_check( $request ) {
		return true;
	}

	public function create_item_permissions_check( $request ) {
		return current_user_can( 'edit_posts' );
	}

	public function update_item_permissions_check( $request ) {
		return current_user_can( 'edit_posts' );
	}

	public function delete_item_permissions_check( $request ) {
		return current_user_can( 'delete_posts' );
	}

	public function get_collection_params() {
		return array(
			'per_page' => array(
				'type'              => 'integer',
				'default'           => 10,
				'minimum'           => 1,
				'maximum'           => 100,
				'sanitize_callback' => 'absint',
			),
			'page'     => array(
				'type'              => 'integer',
				'default'           => 1,
				'sanitize_callback' => 'absint',
			),
		);
	}

	/** Store em option (exemplo; em produção use tabela ou CPT). */
	private function get_itens_store() {
		$stored = get_option( 'estudos_wp_rest_items', array() );
		return is_array( $stored ) ? $stored : array();
	}

	private function set_itens_store( $itens ) {
		update_option( 'estudos_wp_rest_items', $itens );
	}
}
