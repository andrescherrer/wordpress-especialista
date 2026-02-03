<?php
/**
 * REFERÊNCIA RÁPIDA – Headless: rota REST que retorna lista de posts para front externo
 *
 * Endpoint público ou com auth (Application Password/JWT) que retorna JSON; front (Next, React) consome e renderiza.
 * Paginação (page, per_page), campos filtrados (id, title, excerpt, link, date), sem dados sensíveis.
 * Fonte: 015-WordPress-Fase-15-Topicos-Complementares-Avancados.md
 *
 * @package EstudosWP
 * @subpackage 15-Topicos-Complementares-Avancados
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('rest_api_init', function () {
    register_rest_route('estudos-wp/v1', '/headless/posts', [
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => function ($request) {
            $page     = max(1, (int) $request->get_param('page'));
            $per_page = min(100, max(1, (int) $request->get_param('per_page') ?: 10));
            $query    = new WP_Query([
                'post_type'      => 'post',
                'post_status'    => 'publish',
                'posts_per_page' => $per_page,
                'paged'          => $page,
            ]);
            $items = [];
            foreach ($query->posts as $post) {
                $items[] = [
                    'id'      => $post->ID,
                    'title'   => get_the_title($post),
                    'excerpt' => get_the_excerpt($post),
                    'link'    => get_permalink($post),
                    'date'    => get_the_date('c', $post),
                ];
            }
            $response = new WP_REST_Response($items, 200);
            $response->header('X-WP-Total', $query->found_posts);
            $response->header('X-WP-TotalPages', (int) ceil($query->found_posts / $per_page));
            return $response;
        },
        'permission_callback' => '__return_true',
        'args'                => [
            'page'     => ['type' => 'integer', 'default' => 1],
            'per_page' => ['type' => 'integer', 'default' => 10, 'maximum' => 100],
        ],
    ]);
});
