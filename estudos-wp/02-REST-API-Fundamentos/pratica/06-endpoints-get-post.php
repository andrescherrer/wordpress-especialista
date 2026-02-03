<?php
/**
 * REFERÊNCIA RÁPIDA – Endpoints GET (listar) e POST (criar)
 *
 * GET: methods => WP_REST_Server::READABLE; callback retorna array/objeto; permission_callback.
 * POST: methods => WP_REST_Server::CREATABLE; $request->get_json_params() ou get_body_params(); retornar 201 + corpo.
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

    // GET – listar itens
    register_rest_route($ns, '/itens', [
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => function ($request) {
            $per_page = $request->get_param('per_page') ?: 10;
            $page     = $request->get_param('page') ?: 1;
            $query    = new WP_Query([
                'post_type'      => 'post',
                'post_status'    => 'publish',
                'posts_per_page' => min(absint($per_page), 100),
                'paged'          => max(1, absint($page)),
            ]);
            $itens = [];
            foreach ($query->posts as $post) {
                $itens[] = [
                    'id'    => $post->ID,
                    'title' => get_the_title($post),
                    'date'  => get_the_date('c', $post),
                ];
            }
            return rest_ensure_response($itens);
        },
        'permission_callback' => '__return_true',
        'args'                => [
            'per_page' => ['type' => 'integer', 'default' => 10, 'minimum' => 1, 'maximum' => 100],
            'page'     => ['type' => 'integer', 'default' => 1, 'minimum' => 1],
        ],
    ]);

    // POST – criar item (exemplo: criar post)
    register_rest_route($ns, '/itens', [
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => function ($request) {
            $params = $request->get_json_params() ?: $request->get_body_params();
            $title  = isset($params['title']) ? sanitize_text_field($params['title']) : '';
            $content = isset($params['content']) ? wp_kses_post($params['content']) : '';
            if (empty($title)) {
                return new WP_Error('missing_title', 'Título é obrigatório', ['status' => 400]);
            }
            $post_id = wp_insert_post([
                'post_title'   => $title,
                'post_content' => $content,
                'post_status'  => 'draft',
                'post_type'    => 'post',
            ]);
            if (is_wp_error($post_id)) {
                return $post_id;
            }
            return new WP_REST_Response([
                'id'      => $post_id,
                'title'   => get_the_title($post_id),
                'status'  => get_post_status($post_id),
            ], 201);
        },
        'permission_callback' => function () {
            return current_user_can('edit_posts');
        },
        'args'                => [
            'title'   => ['type' => 'string', 'required' => true],
            'content' => ['type' => 'string'],
        ],
    ]);
});
