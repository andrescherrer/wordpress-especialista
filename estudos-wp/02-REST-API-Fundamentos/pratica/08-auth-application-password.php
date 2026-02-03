<?php
/**
 * REFERÊNCIA RÁPIDA – Autenticação com Application Password
 *
 * Usuário: Perfil → Aplicativos de Autenticação → Novo; usar user:password (Application Password).
 * REST aceita Basic Auth (user + Application Password) ou header Authorization: Basic base64(user:app_password).
 * permission_callback: is_user_logged_in() para rotas que exigem login.
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

    // Rota que exige autenticação (Application Password ou cookie de admin)
    register_rest_route($ns, '/meu-perfil', [
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => function ($request) {
            $user_id = get_current_user_id();
            if (!$user_id) {
                return new WP_Error('unauthorized', 'Faça login ou use Application Password', ['status' => 401]);
            }
            $user = get_userdata($user_id);
            return rest_ensure_response([
                'id'           => $user->ID,
                'login'        => $user->user_login,
                'display_name' => $user->display_name,
                'email'        => $user->user_email,
            ]);
        },
        'permission_callback' => function () {
            return is_user_logged_in();
        },
    ]);
});

// Dica: para testar com Application Password no Postman/Insomnia:
// 1. Usuário > Aplicativos de Autenticação > Adicionar novo aplicativo > nome "Postman"
// 2. Copiar a senha gerada (só aparece uma vez)
// 3. Auth type: Basic; username: seu_login; password: a Application Password (não a senha do WP)
