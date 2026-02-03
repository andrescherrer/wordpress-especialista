<?php
/**
 * REFERÊNCIA RÁPIDA – Endpoint de login que emite JWT
 *
 * Rota POST recebe user/pass; valida com wp_authenticate; gera JWT (firebase/php-jwt);
 * retorna { "token": "..." }; secret: AUTH_KEY ou constante.
 * Requer: composer require firebase/php-jwt
 *
 * @package EstudosWP
 * @subpackage 02-REST-API-Fundamentos
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('rest_api_init', function () {
    $ns = 'estudos-wp/v1';

    register_rest_route($ns, '/auth/jwt', [
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => function ($request) {
            $username = $request->get_param('username');
            $password = $request->get_param('password');

            if (empty($username) || empty($password)) {
                return new WP_Error('missing_credentials', 'username e password são obrigatórios', ['status' => 400]);
            }

            $user = wp_authenticate($username, $password);
            if (is_wp_error($user)) {
                return new WP_Error('invalid_credentials', 'Usuário ou senha inválidos', ['status' => 401]);
            }

            // Gerar JWT (requer firebase/php-jwt: composer require firebase/php-jwt)
            if (!class_exists('JWT')) {
                return new WP_Error('jwt_not_available', 'Biblioteca JWT não instalada. Execute: composer require firebase/php-jwt', ['status' => 500]);
            }

            $secret = defined('JWT_AUTH_SECRET') ? JWT_AUTH_SECRET : (defined('AUTH_KEY') ? AUTH_KEY : 'change-me-in-production');
            $issued_at = time();
            $expire = $issued_at + (7 * DAY_IN_SECONDS); // 7 dias

            $payload = [
                'iss' => get_bloginfo('url'),
                'iat' => $issued_at,
                'exp' => $expire,
                'user_id' => $user->ID,
                'login' => $user->user_login,
            ];

            try {
                $token = \Firebase\JWT\JWT::encode($payload, $secret, 'HS256');
            } catch (Exception $e) {
                return new WP_Error('jwt_encode_failed', $e->getMessage(), ['status' => 500]);
            }

            return rest_ensure_response([
                'token' => $token,
                'expires_in' => 7 * DAY_IN_SECONDS,
            ]);
        },
        'permission_callback' => '__return_true',
        'args' => [
            'username' => ['required' => true, 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field'],
            'password' => ['required' => true, 'type' => 'string'],
        ],
    ]);
});
