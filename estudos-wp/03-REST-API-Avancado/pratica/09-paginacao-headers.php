<?php
/**
 * REFERÊNCIA RÁPIDA – Paginação e headers em REST
 *
 * Parâmetros: page, per_page (máx. 100).
 * Headers: X-WP-Total (total de itens), X-WP-TotalPages (total de páginas).
 * WP_REST_Response permite set_header('X-WP-Total', $total).
 *
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
    register_rest_route($ns, '/itens-paginados', [
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => function ($request) {
            $per_page = min(100, max(1, (int) $request->get_param('per_page') ?: 10));
            $page    = max(1, (int) $request->get_param('page') ?: 1);
            $offset  = ($page - 1) * $per_page;

            $query = new WP_Query([
                'post_type'      => 'post',
                'post_status'    => 'publish',
                'posts_per_page' => $per_page,
                'paged'          => $page,
                'fields'         => 'ids',
            ]);
            $total = $query->found_posts;
            $ids   = $query->posts;

            $itens = [];
            foreach ($ids as $id) {
                $itens[] = ['id' => $id, 'title' => get_the_title($id)];
            }

            $response = new WP_REST_Response($itens, 200);
            $response->header('X-WP-Total', $total);
            $response->header('X-WP-TotalPages', (int) ceil($total / $per_page));
            return $response;
        },
        'permission_callback' => '__return_true',
        'args'                => [
            'per_page' => ['type' => 'integer', 'default' => 10, 'minimum' => 1, 'maximum' => 100],
            'page'     => ['type' => 'integer', 'default' => 1, 'minimum' => 1],
        ],
    ]);
});
