<?php
/**
 * REFERÊNCIA RÁPIDA – REST API: validação, permissões, IDs, logs
 *
 * Validação: args com validate_callback e sanitize_callback; WP_Error 400 com errors.
 * Permissões: permission_callback com is_user_logged_in() e current_user_can(); 403 para recurso.
 * IDs: hash/UUID em vez de ID sequencial; filtrar email/dados sensíveis por capability.
 * Logs: nunca senha/token; apenas username e código de erro; secure_log redacta campos.
 *
 * Fonte: 019-WordPress-Fase-19-Anti-padroes-Seguranca.md
 */

// ========== 1. Validação em REST ==========

// ❌ ERRADO: aceitar qualquer coisa
// $data = $request->get_json_params();
// wp_insert_user(['user_login' => $data['username'], ...]);

// ✅ CORRETO: args + validação no callback
add_action('rest_api_init', function () {
    register_rest_route('myapp/v1', '/users', [
        'methods'             => 'POST',
        'callback'            => function ($request) {
            $data = $request->get_json_params();
            $errors = [];
            if (empty($data['username']) || !validate_username($data['username']) || strlen($data['username']) < 3) {
                $errors['username'] = 'Username inválido ou muito curto';
            }
            if (empty($data['email']) || !is_email($data['email'])) {
                $errors['email'] = 'Email inválido';
            }
            if (empty($data['password']) || strlen($data['password']) < 8) {
                $errors['password'] = 'Senha deve ter pelo menos 8 caracteres';
            }
            if (!empty($errors)) {
                return new WP_Error('validation_error', 'Dados inválidos', ['status' => 400, 'errors' => $errors]);
            }
            $user_id = wp_insert_user([
                'user_login' => sanitize_user($data['username']),
                'user_email' => sanitize_email($data['email']),
                'user_pass'  => $data['password'],
            ]);
            if (is_wp_error($user_id)) {
                return new WP_Error('user_creation_failed', $user_id->get_error_message(), ['status' => 500]);
            }
            return new WP_REST_Response(['id' => $user_id], 201);
        },
        'permission_callback' => '__return_true',
        'args'                => [
            'username' => ['required' => true, 'type' => 'string', 'validate_callback' => function ($p) {
                return validate_username($p) && strlen($p) >= 3;
            }, 'sanitize_callback' => 'sanitize_user'],
            'email'    => ['required' => true, 'type' => 'string', 'validate_callback' => 'is_email', 'sanitize_callback' => 'sanitize_email'],
            'password' => ['required' => true, 'type' => 'string', 'validate_callback' => function ($p) {
                return strlen($p) >= 8;
            }],
        ],
    ]);
});

// ========== 2. Permission callback ==========

// ❌ ERRADO: permission_callback => '__return_true' em DELETE

// ✅ CORRETO
register_rest_route('myapp/v1', '/posts/(?P<id>\d+)', [
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

// ========== 3. Não expor IDs sequenciais ==========

function myapp_user_public_id($user_id) {
    return hash_hmac('sha256', (string) $user_id . wp_salt('auth'), wp_salt('logged_in'));
}

// Na resposta: 'id' => myapp_user_public_id($user->ID)
// Email só se current_user_can('list_users')

// ========== 4. Não logar dados sensíveis ==========

// ❌ ERRADO: error_log('Login: ' . $data['username'] . ' / ' . $data['password']);

// ✅ CORRETO
// error_log('Login attempt for user: ' . sanitize_user($data['username']));
// error_log('Login failed: ' . $user->get_error_code());  // não serialize($user)

function myapp_secure_log($message, $context = []) {
    $redact = ['password', 'token', 'api_key', 'secret', 'credit_card'];
    foreach ($context as $key => $value) {
        if (in_array(strtolower($key), $redact, true)) {
            $context[$key] = '[REDACTED]';
        }
    }
    error_log($message . ' - ' . wp_json_encode($context));
}
