<?php
/**
 * REFERÊNCIA RÁPIDA – API Versioning e Deprecation
 *
 * Versioning: register_rest_route('api/v1', ...) e ('api/v2', ...); callbacks diferentes por versão.
 * Deprecation: headers Deprecation, Sunset, Link (successor-version); após sunset retornar 410 Gone.
 *
 * Fonte: 015-WordPress-Fase-15-Topicos-Complementares-Avancados.md (API Versioning, Deprecation Handling)
 */

// ========== Versionamento por URL (v1 e v2) ==========

add_action('rest_api_init', function () {
    register_rest_route('api/v1', '/posts', [
        'methods'  => 'GET',
        'callback' => function () {
            $posts = get_posts(['numberposts' => 10]);
            return ['posts' => $posts];
        },
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('api/v2', '/posts', [
        'methods'  => 'GET',
        'callback' => function () {
            $posts = get_posts(['numberposts' => 10]);
            return [
                'data' => array_map(function ($post) {
                    return [
                        'id'       => $post->ID,
                        'title'    => $post->post_title,
                        'content'  => $post->post_content,
                        'author'   => get_the_author_meta('display_name', $post->post_author),
                        'metadata' => [
                            'created_at' => $post->post_date,
                            'updated_at' => $post->post_modified,
                        ],
                    ];
                }, $posts),
                'meta' => ['version' => '2.0', 'total' => count($posts)],
            ];
        },
        'permission_callback' => '__return_true',
    ]);
});

// ========== Deprecation Manager (rotas deprecadas) ==========

class DeprecationManager
{
    private static array $deprecations = [
        'api/v1/old-endpoint' => [  // rota sem barra inicial (ltrim no filter)
            'deprecated_version'   => '2.0',
            'sunset_date'          => '2025-12-31',
            'replacement_endpoint' => 'api/v2/new-endpoint',
            'migration_guide'      => 'https://docs.example.com/migration',
        ],
    ];

    public static function register_deprecation(string $route, array $config): void
    {
        self::$deprecations[$route] = $config;
    }

    public static function handle_deprecated_request(string $route, \WP_REST_Request $request): \WP_REST_Response|\WP_Error
    {
        $config = self::$deprecations[$route] ?? null;
        if (! $config) {
            return new \WP_REST_Response([]);
        }

        $sunset = new \DateTime($config['sunset_date']);
        $now    = new \DateTime();

        if ($now > $sunset) {
            return new \WP_Error(
                'endpoint_sunset',
                sprintf(
                    'Endpoint descontinuado em %s. Use: %s',
                    $config['sunset_date'],
                    $config['replacement_endpoint']
                ),
                ['status' => 410]
            );
        }

        $response = new \WP_REST_Response(['data' => 'legacy response']);
        $response->header('Deprecation', 'true');
        $response->header('Deprecated-Since', $config['deprecated_version']);
        $response->header('Sunset', $sunset->format('D, d M Y H:i:s T'));
        $response->header(
            'Link',
            '<' . rest_url($config['replacement_endpoint']) . '>; rel="successor-version"'
        );
        $response->header(
            'Warning',
            '299 - "Endpoint descontinuado. Use: ' . $config['replacement_endpoint'] . '"'
        );

        return $response;
    }

    public static function get_deprecations(): array
    {
        return self::$deprecations;
    }
}

// Exemplo: rota v1 antiga que retorna deprecation headers ou 410
add_action('rest_api_init', function () {
    register_rest_route('api/v1', '/old-endpoint', [
        'methods'             => 'GET',
        'callback'            => function (\WP_REST_Request $request) {
            return DeprecationManager::handle_deprecated_request('api/v1/old-endpoint', $request);
        },
        'permission_callback' => '__return_true',
    ]);
});

// ========== Middleware: adicionar headers de depreciação em qualquer resposta deprecada ==========

add_filter('rest_pre_dispatch', function ($result, $server, $request) {
    $route  = ltrim($request->get_route(), '/');
    $config = DeprecationManager::get_deprecations()[$route] ?? null;

    if (! $config) {
        return $result;
    }

    $sunset = new \DateTime($config['sunset_date']);
    if (new \DateTime() > $sunset) {
        return new \WP_Error(
            'endpoint_sunset',
            'Endpoint descontinuado. Use: ' . $config['replacement_endpoint'],
            ['status' => 410]
        );
    }

    // Se o callback já retornou resposta, adicionar headers via rest_post_dispatch
    return $result;
}, 10, 3);

add_filter('rest_post_dispatch', function ($response, $server, $request) {
    if (! $response instanceof \WP_REST_Response) {
        return $response;
    }
    $route  = ltrim($request->get_route(), '/');
    $config = DeprecationManager::get_deprecations()[$route] ?? null;
    if (! $config) {
        return $response;
    }
    $sunset = new \DateTime($config['sunset_date']);
    $response->header('Deprecation', 'true');
    $response->header('Sunset', $sunset->format('D, d M Y H:i:s T'));
    $response->header('Link', '<' . rest_url($config['replacement_endpoint']) . '>; rel="successor-version"');
    return $response;
}, 10, 3);
