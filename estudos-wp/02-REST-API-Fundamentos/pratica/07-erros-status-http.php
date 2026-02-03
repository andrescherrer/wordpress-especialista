<?php
/**
 * REFERÊNCIA RÁPIDA – WP_Error e status HTTP em REST
 *
 * new WP_Error('code', 'message', ['status' => 400|401|403|404|500]);
 * return $error;  →  REST API envia status e corpo JSON com code/message.
 * rest_ensure_response($data) para sucesso; status 200 por padrão; 201 para criação.
 *
 * Fonte: 002-WordPress-Fase-2-REST-API-Fundamentos.md
 *
 * @package EstudosWP
 * @subpackage 02-REST-API-Fundamentos
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('rest_api_init', function () {
    $ns = 'estudos-wp/v1';

    register_rest_route($ns, '/recurso/(?P<id>\d+)', [
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => function ($request) {
            $id   = (int) $request->get_param('id');
            $post = get_post($id);

            if (!$post) {
                return new WP_Error('not_found', 'Recurso não encontrado', ['status' => 404]);
            }

            if ($post->post_status !== 'publish' && !current_user_can('edit_post', $id)) {
                return new WP_Error('forbidden', 'Sem permissão para ver este recurso', ['status' => 403]);
            }

            return rest_ensure_response([
                'id'    => $post->ID,
                'title' => $post->post_title,
            ]);
        },
        'permission_callback' => '__return_true',
    ]);

    register_rest_route($ns, '/recurso-criar', [
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => function ($request) {
            if (!is_user_logged_in()) {
                return new WP_Error('unauthorized', 'Autenticação necessária', ['status' => 401]);
            }
            $params = $request->get_json_params();
            if (empty($params['nome'])) {
                return new WP_Error('validation_error', 'Campo nome é obrigatório', [
                    'status' => 400,
                    'data'   => ['errors' => ['nome' => 'Obrigatório']],
                ]);
            }
            return new WP_REST_Response(['id' => 1, 'nome' => sanitize_text_field($params['nome'])], 201);
        },
        'permission_callback' => '__return_true',
    ]);
});
