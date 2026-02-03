<?php
/**
 * REFERÊNCIA RÁPIDA – Capability por recurso (edit_post, delete_post com $post_id)
 *
 * current_user_can('edit_post', $post_id); current_user_can('delete_post', $post_id).
 * Em REST: permission_callback que chama current_user_can('edit_post', $post_id) com ID da rota.
 * Fonte: 012-WordPress-Fase-12-Seguranca-Boas-Praticas.md
 *
 * @package EstudosWP
 * @subpackage 12-Seguranca-Boas-Praticas
 */

if (!defined('ABSPATH')) {
    exit;
}

// Admin: antes de salvar/editar um post
add_action('save_post', function ($post_id) {
    if (!current_user_can('edit_post', $post_id)) {
        wp_die(__('Você não tem permissão para editar este post.'));
    }
}, 1);

// REST: permission por recurso
add_action('rest_api_init', function () {
    register_rest_route('estudos-wp/v1', '/posts/(?P<id>\d+)', [
        'methods'             => 'GET',
        'callback'            => function ($request) {
            $post = get_post((int) $request->get_param('id'));
            if (!$post) {
                return new WP_Error('not_found', 'Post não encontrado', ['status' => 404]);
            }
            return rest_ensure_response(['id' => $post->ID, 'title' => $post->post_title]);
        },
        'permission_callback' => function ($request) {
            $post_id = (int) $request->get_param('id');
            return current_user_can('read_post', $post_id);
        },
    ]);
});
