<?php
/**
 * REFERÊNCIA RÁPIDA – REST: sem permission_callback vs com capability por recurso
 *
 * ERRADO: permission_callback => __return_true em DELETE/POST; qualquer um pode deletar.
 * CORRETO: permission_callback com is_user_logged_in e current_user_can; no callback current_user_can(delete_post, post_id) e 403.
 * Fonte: 019-WordPress-Fase-19-Anti-padroes-Seguranca.md
 *
 * @package EstudosWP
 * @subpackage 19-Anti-padroes-Seguranca
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('rest_api_init', function () {
    register_rest_route('estudos-wp/v1', '/posts/(?P<id>\d+)', [
        'methods'             => 'DELETE',
        'callback'            => function ($request) {
            $post_id = (int) $request->get_param('id');
            $post = get_post($post_id);
            if (!$post) {
                return new WP_Error('not_found', 'Post não encontrado', ['status' => 404]);
            }
            if (!current_user_can('delete_post', $post_id)) {
                return new WP_Error('forbidden', 'Sem permissão', ['status' => 403]);
            }
            wp_delete_post($post_id, true);
            return new WP_REST_Response(['success' => true], 200);
        },
        'permission_callback' => function () {
            return is_user_logged_in() && current_user_can('delete_posts');
        },
    ]);
});
