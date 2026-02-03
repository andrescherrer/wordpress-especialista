<?php
/**
 * REFERÊNCIA RÁPIDA – GraphQL (WPGraphQL), Headers e Rate Limiting
 *
 * GraphQL: show_in_graphql, graphql_single_name/plural_name no CPT; campos sensíveis com is_user_logged_in() no resolve.
 * Headers: ler HTTP_X_API_KEY; validar em permission_callback; retornar WP_Error 400/401 se inválido.
 * Rate limit: transient por identificador (IP ou API key); X-RateLimit-* na resposta; 429 se exceder.
 *
 * Fonte: 015-WordPress-Fase-15-Topicos-Complementares-Avancados.md (Advanced API Topics)
 */

// ========== 1. Registrar CPT para GraphQL (WPGraphQL ativo) ==========

add_action('init', function () {
    register_post_type('book', [
        'label'               => 'Books',
        'public'              => true,
        'show_in_rest'        => true,
        'show_in_graphql'     => true,
        'graphql_single_name' => 'book',
        'graphql_plural_name' => 'books',
        'supports'            => ['title', 'editor', 'excerpt'],
    ]);
});

// Campo customizado GraphQL (ex.: dado sensível) – verificar autenticação no resolve
add_filter('graphql_post_type_fields', function ($fields, $post_type) {
    if ($post_type !== 'book') {
        return $fields;
    }
    $fields['internalNote'] = [
        'type'    => 'String',
        'resolve' => function ($post) {
            if (! is_user_logged_in() || ! current_user_can('edit_posts')) {
                throw new \GraphQL\Error\UserError('Requer autenticação');
            }
            return get_post_meta($post->ID, '_internal_note', true) ?: '';
        },
    ];
    return $fields;
}, 10, 2);

// ========== 2. Validação de headers (REST API) ==========

class HeaderValidator
{
    const REQUIRED_HEADERS = ['X-API-Key'];

    public static function validate_headers(): bool|\WP_Error
    {
        foreach (self::REQUIRED_HEADERS as $header) {
            if (empty(self::get_header($header))) {
                return new \WP_Error(
                    'missing_header',
                    sprintf('Header obrigatório ausente: %s', $header),
                    ['status' => 400]
                );
            }
        }
        $api_key = self::get_header('X-API-Key');
        $valid_keys = get_option('api_valid_keys', []);
        if (! is_array($valid_keys) || ! in_array($api_key, $valid_keys, true)) {
            return new \WP_Error('invalid_api_key', 'X-API-Key inválida', ['status' => 401]);
        }
        return true;
    }

    private static function get_header(string $name): ?string
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        $val = $_SERVER[$key] ?? null;
        return is_string($val) ? $val : null;
    }
}

// ========== 3. Rate limiting com transients ==========

class RateLimiter
{
    const DEFAULT_LIMIT  = 100;
    const DEFAULT_WINDOW = 3600;

    public static function check(string $identifier = null, int $limit = self::DEFAULT_LIMIT, int $window = self::DEFAULT_WINDOW): array
    {
        $identifier = $identifier ?? self::get_identifier();
        $key        = 'rate_limit_' . md5($identifier);
        $current    = get_transient($key);

        if ($current === false) {
            set_transient($key, 1, $window);
            return ['allowed' => true, 'remaining' => $limit - 1, 'reset' => time() + $window];
        }

        if ($current >= $limit) {
            return ['allowed' => false, 'remaining' => 0, 'reset' => time() + (int) get_option('_transient_timeout_' . $key, time() + $window)];
        }

        set_transient($key, $current + 1, $window);
        return ['allowed' => true, 'remaining' => $limit - $current - 1, 'reset' => time() + $window];
    }

    public static function add_headers(\WP_REST_Response $response, string $identifier = null, int $limit = self::DEFAULT_LIMIT): \WP_REST_Response
    {
        $status = self::check($identifier, $limit);
        $response->header('X-RateLimit-Limit', (string) $limit);
        $response->header('X-RateLimit-Remaining', (string) $status['remaining']);
        $response->header('X-RateLimit-Reset', (string) $status['reset']);
        return $response;
    }

    private static function get_identifier(): string
    {
        $api_key = isset($_SERVER['HTTP_X_API_KEY']) ? $_SERVER['HTTP_X_API_KEY'] : null;
        if (is_string($api_key) && $api_key !== '') {
            return md5($api_key);
        }
        return md5($_SERVER['REMOTE_ADDR'] ?? '');
    }
}

// ========== 4. Registrar rota REST com permission_callback (headers + rate limit) ==========

add_action('rest_api_init', function () {
    register_rest_route('myapi/v1', '/protected', [
        'methods'             => 'GET',
        'callback'            => function (\WP_REST_Request $request) {
            $rate = RateLimiter::check(null, 100);
            $res  = new \WP_REST_Response(['message' => 'OK', 'user_id' => get_current_user_id()]);
            return RateLimiter::add_headers($res, null, 100);
        },
        'permission_callback' => function () {
            $valid = HeaderValidator::validate_headers();
            if (is_wp_error($valid)) {
                return $valid;
            }
            $rate = RateLimiter::check(null, 100);
            if (! $rate['allowed']) {
                return new \WP_Error('rate_limit_exceeded', 'Limite excedido', ['status' => 429]);
            }
            return true;
        },
    ]);
});
