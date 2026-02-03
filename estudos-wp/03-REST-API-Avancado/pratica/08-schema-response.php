<?php
/**
 * REFERÊNCIA RÁPIDA – Schema em register_rest_route
 *
 * 'schema' => 'nome_schema' ou array com 'description', 'type', 'properties', 'context'.
 * get_item_schema(): retorna array com properties (id, title, etc.) e type (object).
 * Fonte: 003-WordPress-Fase-3-REST-API-Avancado.md
 *
 * @package EstudosWP
 * @subpackage 03-REST-API-Avancado
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('rest_api_init', function () {
    $ns = 'estudos-wp/v1';
    register_rest_route($ns, '/produtos', [
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => function ($request) {
            $query = new WP_Query([
                'post_type'      => 'post',
                'post_status'    => 'publish',
                'posts_per_page' => 10,
            ]);
            $itens = [];
            foreach ($query->posts as $post) {
                $itens[] = [
                    'id'          => $post->ID,
                    'title'       => get_the_title($post),
                    'description' => wp_trim_words(get_the_excerpt($post), 15),
                    'status'      => $post->post_status,
                ];
            }
            return rest_ensure_response($itens);
        },
        'permission_callback' => '__return_true',
        'args'                => [
            'per_page' => ['type' => 'integer', 'default' => 10, 'minimum' => 1, 'maximum' => 100],
        ],
        'schema' => [
            'title'      => 'produto',
            'type'       => 'object',
            'properties' => [
                'id'          => ['type' => 'integer', 'description' => 'ID do item'],
                'title'       => ['type' => 'string', 'description' => 'Título'],
                'description' => ['type' => 'string', 'description' => 'Resumo'],
                'status'      => ['type' => 'string', 'enum' => ['publish', 'draft', 'pending']],
            ],
        ],
    ]);
});
