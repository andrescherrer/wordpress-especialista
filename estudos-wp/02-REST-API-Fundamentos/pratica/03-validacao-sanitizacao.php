<?php
/**
 * Exemplo 03: Validação e sanitização em args da REST API
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   type: string, integer, number, boolean, array, object
 *   required, default, minimum, maximum, enum (array de valores)
 *   sanitize_callback: sanitize_text_field, sanitize_email, absint, floatval, wp_kses_post
 *   validate_callback: function( $value ) { return true ou new WP_Error( 'code', 'msg' ); }
 *   Fluxo: validar (formato) → sanitizar (limpar) → processar. Saída: escapar (esc_html, esc_url).
 *
 * @package EstudosWP
 * @subpackage 02-REST-API-Fundamentos
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'rest_api_init', 'estudos_wp_rest_registrar_rota_validacao' );

function estudos_wp_rest_registrar_rota_validacao() {
	register_rest_route(
		'estudos-wp/v1',
		'/contato',
		array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'estudos_wp_rest_contato_callback',
			'permission_callback' => '__return_true',
			'args'                => array(
				'nome'    => array(
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
					'validate_callback' => function( $value ) {
						if ( strlen( $value ) < 2 ) {
							return new WP_Error(
								'invalid_nome',
								'Nome deve ter pelo menos 2 caracteres',
								array( 'status' => 400 )
							);
						}
						if ( strlen( $value ) > 100 ) {
							return new WP_Error(
								'invalid_nome',
								'Nome deve ter no máximo 100 caracteres',
								array( 'status' => 400 )
							);
						}
						return true;
					},
					'description'       => 'Nome do remetente',
				),
				'email'   => array(
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_email',
					'validate_callback' => function( $value ) {
						if ( ! is_email( $value ) ) {
							return new WP_Error(
								'invalid_email',
								'Email inválido',
								array( 'status' => 400 )
							);
						}
						return true;
					},
					'description'       => 'Email do remetente',
				),
				'assunto' => array(
					'required'          => false,
					'type'              => 'string',
					'default'           => 'Contato',
					'enum'              => array( 'Contato', 'Suporte', 'Comercial', 'Outro' ),
					'sanitize_callback' => 'sanitize_text_field',
					'description'       => 'Tipo de assunto',
				),
				'mensagem' => array(
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_textarea_field',
					'validate_callback' => function( $value ) {
						if ( strlen( $value ) < 10 ) {
							return new WP_Error(
								'invalid_mensagem',
								'Mensagem deve ter pelo menos 10 caracteres',
								array( 'status' => 400 )
							);
						}
						return true;
					},
					'description'       => 'Corpo da mensagem',
				),
				'idade'   => array(
					'required'          => false,
					'type'              => 'integer',
					'default'           => 0,
					'minimum'           => 0,
					'maximum'           => 150,
					'sanitize_callback' => 'absint',
					'validate_callback' => function( $value ) {
						if ( $value > 0 && $value < 18 ) {
							return new WP_Error(
								'underage',
								'Deve ter pelo menos 18 anos',
								array( 'status' => 422 )
							);
						}
						return true;
					},
					'description'       => 'Idade (opcional)',
				),
			),
		)
	);
}

/**
 * Callback: simula envio de contato (não envia email; só valida e retorna).
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response
 */
function estudos_wp_rest_contato_callback( WP_REST_Request $request ) {
	// Dados já sanitizados e validados pelos args
	$nome     = $request->get_param( 'nome' );
	$email    = $request->get_param( 'email' );
	$assunto  = $request->get_param( 'assunto' );
	$mensagem = $request->get_param( 'mensagem' );
	$idade    = $request->get_param( 'idade' );

	// Em produção: wp_mail(), salvar em DB, etc.
	return new WP_REST_Response(
		array(
			'mensagem' => 'Dados recebidos com sucesso (exemplo; email não enviado).',
			'dados'    => array(
				'nome'     => esc_html( $nome ),
				'email'    => esc_html( $email ),
				'assunto'  => esc_html( $assunto ),
				'mensagem' => esc_html( wp_trim_words( $mensagem, 20 ) ),
				'idade'    => $idade,
			),
		),
		201
	);
}
