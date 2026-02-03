<?php
/**
 * REFERÊNCIA RÁPIDA – Middleware / permission_callback que valida JWT
 *
 * Extrai header Authorization: Bearer <token>; decodifica e valida assinatura e expiração;
 * obtém user_id do payload; wp_set_current_user; retorna true/false no permission_callback.
 * Requer: firebase/php-jwt (mesmo secret do endpoint de login).
 *
 * @package EstudosWP
 * @subpackage 02-REST-API-Fundamentos
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Extrai e valida o JWT do request; define o usuário atual em caso de sucesso.
 *
 * @param WP_REST_Request $request
 * @return int|false User ID se válido, false caso contrário.
 */
function estudos_wp_rest_validate_jwt($request) {
    $auth_header = $request->get_header('Authorization');
    if (empty($auth_header) || !preg_match('/^Bearer\s+(.+)$/i', $auth_header, $m)) {
        return false;
    }
    $token = $m[1];

    if (!class_exists('JWT')) {
        return false;
    }

    $secret = defined('JWT_AUTH_SECRET') ? JWT_AUTH_SECRET : (defined('AUTH_KEY') ? AUTH_KEY : 'change-me-in-production');

    try {
        $decoded = \Firebase\JWT\JWT::decode($token, new \Firebase\JWT\Key($secret, 'HS256'));
        $user_id = (int) $decoded->user_id;
        if ($user_id && get_userdata($user_id)) {
            wp_set_current_user($user_id);
            return $user_id;
        }
    } catch (Exception $e) {
        return false;
    }
    return false;
}

/**
 * permission_callback que exige JWT válido.
 */
function estudos_wp_rest_permission_jwt($request) {
    return estudios_wp_rest_validate_jwt($request) !== false;
}

// Exemplo: rota protegida por JWT
add_action('rest_api_init', function () {
    $ns = 'estudos-wp/v1';

    register_rest_route($ns, '/meu-perfil-jwt', [
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => function ($request) {
            $user_id = get_current_user_id();
            $user = get_userdata($user_id);
            return rest_ensure_response([
                'id' => $user->ID,
                'login' => $user->user_login,
                'display_name' => $user->display_name,
            ]);
        },
        'permission_callback' => 'estudos_wp_rest_permission_jwt',
    ]);
});
