# 🎯 Tópicos Complementares Avançados - WordPress

> Guia aprofundado de tópicos avançados para desenvolvimento WordPress em nível profissional

---

**Navegação:** [Índice](./000-WordPress-Indice-Topicos.md) | [← Fase 15](./016-WordPress-Fase-15-Jobs-Assincronos-Background.md) | [Fase 17 →](./017-WordPress-Fase-17-Testes-Em-Toda-Fase.md)

---

## 📑 Índice

1. [Advanced API Topics](#advanced-api-topics)
2. [Advanced Performance](#advanced-performance)
3. [WordPress Ecosystem](#wordpress-ecosystem)
4. [Headless WordPress](#headless-wordpress)
5. [Community e Best Practices](#community-e-best-practices)

---

## Advanced API Topics

### 1. GraphQL for WordPress

#### O que é GraphQL?

GraphQL é uma linguagem de query para APIs que permite clientes solicitarem apenas os dados que necessitam. Diferentemente do REST, onde endpoints retornam estruturas fixas, GraphQL oferece flexibilidade total.

#### WPGraphQL - Plugin Principal

O **WPGraphQL** é a solução mais popular para implementar GraphQL em WordPress.

**Instalação e Configuração Básica:**

```bash
# Via Composer (recomendado)
composer require wp-graphql/wp-graphql

# Ou através do WordPress Dashboard
# Plugins > Add New > Buscar "WPGraphQL"
```

**Estrutura do Schema GraphQL:**

```graphql
# Query básica
query {
  posts(first: 10) {
    edges {
      node {
        id
        title
        content
        author {
          node {
            name
            email
          }
        }
      }
    }
  }
}

# Query com argumentos
query {
  post(id: "cG9zdDoxMjM=") {
    title
    content
    featuredImage {
      node {
        sourceUrl
        altText
      }
    }
  }
}
```

**Registrar Custom Post Types em GraphQL:**

```php
<?php
// functions.php ou arquivo de configuração

add_action('init', function() {
    register_post_type('book', [
        'label' => 'Books',
        'public' => true,
        'show_in_graphql' => true,
        'graphql_single_name' => 'book',
        'graphql_plural_name' => 'books',
        'supports' => ['title', 'editor', 'excerpt'],
    ]);
});
```

**Criar Custom Fields GraphQL:**

```php
<?php
// Usando ACF com GraphQL

// Primeiro, instale: wp-graphql/wp-graphql-acf
add_filter('acf/settings/show_admin', '__return_false');

// Seus ACF fields já estarão disponíveis no GraphQL automaticamente
// {
//   post(id: "cG9zdDoxMjM=") {
//     acfFields {
//       fieldName
//     }
//   }
// }
```

**Mutations (Modificações de Dados):**

```graphql
# Criar um novo post
mutation {
  createPost(input: {
    clientMutationId: "CreateBook"
    title: "Novo Livro"
    content: "Descrição do livro"
    status: PUBLISH
  }) {
    post {
      id
      title
      content
    }
  }
}

# Atualizar post
mutation {
  updatePost(input: {
    id: "cG9zdDoxMjM="
    title: "Título Atualizado"
  }) {
    post {
      id
      title
    }
  }
}

# Deletar post
mutation {
  deletePost(input: {
    id: "cG9zdDoxMjM="
  }) {
    deletedId
  }
}
```

**Authentication em GraphQL:**

```php
<?php
// Registrar campo customizado que requer autenticação

add_filter('graphql_post_type_fields', function($fields, $post_type) {
    $fields['secretData'] = [
        'type' => 'String',
        'resolve' => function($post) {
            // Verificar autenticação
            if (!is_user_logged_in()) {
                throw new \GraphQL\Error\UserError('Você precisa estar autenticado');
            }
            return 'Dados secretos do usuário autenticado';
        },
    ];
    return $fields;
}, 10, 2);
```

#### Vantagens e Casos de Uso

**Vantagens:**
- Solicitações precisas (apenas dados necessários)
- Menos transferência de dados
- Melhor experiência em redes lentes
- Introspection automático (documentação)
- Tipagem forte

**Casos de Uso:**
- Aplicações mobile com dados limitados
- SPAs (React, Vue) que necessitam dados granulares
- Múltiplos frontends com diferentes necessidades de dados

---

### 2. Custom Header Validation

#### Validação de Headers Personalizados

Headers HTTP customizados são úteis para autenticação, versionamento e controle de requisições.

**Criar Middleware de Validação de Headers:**

```php
<?php
// Arquivo: inc/API/HeaderValidator.php

namespace MyPlugin\API;

class HeaderValidator {
    const REQUIRED_HEADERS = [
        'X-API-Key',
        'X-Request-ID',
        'X-Client-Version'
    ];

    public static function validate_headers() {
        foreach (self::REQUIRED_HEADERS as $header) {
            if (!self::has_header($header)) {
                return new \WP_Error(
                    'missing_header',
                    sprintf('Header obrigatório ausente: %s', $header),
                    ['status' => 400]
                );
            }
        }

        // Validar formato do X-API-Key
        $api_key = self::get_header('X-API-Key');
        if (!self::is_valid_api_key($api_key)) {
            return new \WP_Error(
                'invalid_api_key',
                'X-API-Key inválida',
                ['status' => 401]
            );
        }

        return true;
    }

    private static function get_header($name) {
        $name = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        return $_SERVER[$name] ?? null;
    }

    private static function has_header($name) {
        return !empty(self::get_header($name));
    }

    private static function is_valid_api_key($key) {
        // Comparar com chaves armazenadas em opção do WordPress
        $valid_keys = get_option('api_valid_keys', []);
        return in_array($key, $valid_keys);
    }
}
```

**Aplicar Validação em Rotas:**

```php
<?php
// Arquivo: inc/API/Routes.php

namespace MyPlugin\API;

add_action('rest_api_init', function() {
    register_rest_route('myapi/v1', '/protected-endpoint', [
        'methods' => 'GET',
        'callback' => [__CLASS__, 'handle_protected_request'],
        'permission_callback' => [HeaderValidator::class, 'validate_headers'],
    ]);
});

function handle_protected_endpoint(\WP_REST_Request $request) {
    return [
        'message' => 'Acesso permitido',
        'user_id' => get_current_user_id(),
        'timestamp' => time(),
    ];
}
```

**Validação Customizada por Endpoint:**

```php
<?php
// Validação específica por tipo de requisição

class APIEndpoint {
    public static function register_routes() {
        register_rest_route('api/v1', '/posts', [
            [
                'methods' => 'POST',
                'callback' => [__CLASS__, 'create_post'],
                'permission_callback' => [__CLASS__, 'validate_create_headers'],
            ],
            [
                'methods' => 'GET',
                'callback' => [__CLASS__, 'get_posts'],
                'permission_callback' => [__CLASS__, 'validate_read_headers'],
            ],
        ]);
    }

    public static function validate_create_headers() {
        // Criar posts requer headers mais rigorosos
        if (!current_user_can('edit_posts')) {
            return new \WP_Error('no_permission', 'Sem permissão', ['status' => 403]);
        }

        // Validar rate limit
        $rate_limit = self::check_rate_limit();
        if (!$rate_limit) {
            return new \WP_Error('rate_limit', 'Limite de requisições excedido', ['status' => 429]);
        }

        return true;
    }

    public static function validate_read_headers() {
        // Leitura pode ser mais permissiva
        return true;
    }

    private static function check_rate_limit() {
        $key = 'api_rate_limit_' . md5($_SERVER['REMOTE_ADDR']);
        $count = get_transient($key) ?? 0;

        if ($count >= 100) { // 100 requisições por hora
            return false;
        }

        set_transient($key, $count + 1, HOUR_IN_SECONDS);
        return true;
    }
}
```

---

### 3. Request Throttling (Rate Limiting)

#### Implementar Rate Limiting Eficaz

Rate limiting protege sua API contra abuso e garante disponibilidade para todos os clientes.

**Sistema de Rate Limiting Baseado em IP:**

```php
<?php
// Arquivo: inc/API/RateLimiter.php

namespace MyPlugin\API;

class RateLimiter {
    const DEFAULT_LIMIT = 100; // requisições por período
    const DEFAULT_WINDOW = 3600; // 1 hora em segundos

    public static function check_rate_limit($identifier = null, $limit = self::DEFAULT_LIMIT, $window = self::DEFAULT_WINDOW) {
        $identifier = $identifier ?? self::get_identifier();
        $key = "rate_limit_{$identifier}";

        $current = get_transient($key);

        if ($current === false) {
            // Primeira requisição no período
            set_transient($key, 1, $window);
            return [
                'allowed' => true,
                'remaining' => $limit - 1,
                'reset' => time() + $window,
            ];
        }

        if ($current >= $limit) {
            return [
                'allowed' => false,
                'remaining' => 0,
                'reset' => time() + $window,
            ];
        }

        set_transient($key, $current + 1, $window);

        return [
            'allowed' => true,
            'remaining' => $limit - $current - 1,
            'reset' => time() + $window,
        ];
    }

    public static function add_rate_limit_headers(\WP_REST_Response $response, $identifier = null, $limit = self::DEFAULT_LIMIT) {
        $status = self::check_rate_limit($identifier, $limit);

        $response->header('X-RateLimit-Limit', $limit);
        $response->header('X-RateLimit-Remaining', $status['remaining']);
        $response->header('X-RateLimit-Reset', $status['reset']);

        return $response;
    }

    private static function get_identifier() {
        // Usar API key se autenticado, senão usar IP
        if (isset($_SERVER['HTTP_X_API_KEY'])) {
            return md5($_SERVER['HTTP_X_API_KEY']);
        }

        return md5($_SERVER['REMOTE_ADDR']);
    }
}
```

**Middleware para Aplicar Rate Limiting:**

```php
<?php
// Arquivo: inc/API/Middleware.php

add_filter('rest_pre_dispatch', function($response, $server, $request) {
    $endpoint = $request->get_route();

    // Diferentes limites por endpoint
    $limits = [
        '/myapi/v1/posts' => ['limit' => 1000, 'window' => 3600],
        '/myapi/v1/search' => ['limit' => 50, 'window' => 3600],
        '/myapi/v1/auth' => ['limit' => 10, 'window' => 3600],
    ];

    $limit_config = $limits[$endpoint] ?? ['limit' => 100, 'window' => 3600];

    $rate_limit = RateLimiter::check_rate_limit(
        null,
        $limit_config['limit'],
        $limit_config['window']
    );

    if (!$rate_limit['allowed']) {
        $response = new \WP_REST_Response([
            'code' => 'rate_limit_exceeded',
            'message' => 'Limite de requisições excedido. Tente novamente mais tarde.',
        ], 429);

        $response->header('X-RateLimit-Reset', $rate_limit['reset']);
        return $response;
    }

    return $response;
}, 10, 3);
```

**Rate Limiting Dinâmico por Usuário:**

```php
<?php
class AdvancedRateLimiter {
    public static function get_user_limit($user_id) {
        $user = get_userdata($user_id);

        // Limites diferentes por role
        $tier_limits = [
            'administrator' => 10000,
            'editor' => 5000,
            'author' => 1000,
            'subscriber' => 100,
        ];

        foreach ($tier_limits as $role => $limit) {
            if (in_array($role, $user->roles)) {
                return $limit;
            }
        }

        return 50; // Padrão para usuários não autenticados
    }

    public static function check_user_rate_limit($user_id = null) {
        if (!$user_id && is_user_logged_in()) {
            $user_id = get_current_user_id();
        }

        $limit = $user_id ? self::get_user_limit($user_id) : 50;
        $identifier = $user_id ?? $_SERVER['REMOTE_ADDR'];

        return RateLimiter::check_rate_limit($identifier, $limit, 3600);
    }
}
```

---

### 4. API Versioning

#### Estratégias de Versionamento

Versioning permite manter compatibilidade enquanto evolui sua API.

**Versionamento por URL (Recomendado):**

```php
<?php
// Diferentes versões coexistem

add_action('rest_api_init', function() {
    // Versão 1
    register_rest_route('api/v1', '/posts', [
        'methods' => 'GET',
        'callback' => [PostController::class, 'get_posts_v1'],
    ]);

    // Versão 2
    register_rest_route('api/v2', '/posts', [
        'methods' => 'GET',
        'callback' => [PostController::class, 'get_posts_v2'],
    ]);
});

class PostController {
    public static function get_posts_v1() {
        // Formato antigo
        return [
            'posts' => get_posts(['numberposts' => 10]),
        ];
    }

    public static function get_posts_v2() {
        // Formato novo com mais informações
        $posts = get_posts(['numberposts' => 10]);

        return [
            'data' => array_map(function($post) {
                return [
                    'id' => $post->ID,
                    'title' => $post->post_title,
                    'content' => $post->post_content,
                    'author' => get_the_author_meta('display_name', $post->post_author),
                    'featured_image' => get_the_post_thumbnail_url($post->ID),
                    'metadata' => [
                        'created_at' => $post->post_date,
                        'updated_at' => $post->post_modified,
                    ],
                ];
            }, $posts),
            'meta' => [
                'version' => '2.0',
                'total' => count($posts),
            ],
        ];
    }
}
```

**Versionamento por Header:**

```php
<?php
// Usar header para indicar versão

add_filter('rest_pre_dispatch', function($response, $server, $request) {
    $version = $request->get_header('X-API-Version') ?? 'v1';

    if (version_compare($version, '1.0', '<')) {
        return new \WP_Error(
            'deprecated_version',
            'Versão de API não suportada',
            ['status' => 400]
        );
    }

    // Marcar versão na requisição para uso posterior
    $request->set_param('_api_version', $version);

    return $response;
}, 10, 3);
```

**Deprecation Strategy:**

```php
<?php
class VersionManager {
    const DEPRECATED_ENDPOINTS = [
        '/api/v1/old-endpoint' => [
            'deprecated_at' => '2024-01-01',
            'sunset_date' => '2024-12-31',
            'replacement' => '/api/v2/new-endpoint',
        ],
    ];

    public static function check_deprecation($route) {
        if (isset(self::DEPRECATED_ENDPOINTS[$route])) {
            $info = self::DEPRECATED_ENDPOINTS[$route];

            // Adicionar headers de deprecação
            header('Deprecation: true');
            header('Sunset: ' . date('r', strtotime($info['sunset_date'])));
            header('Link: <' . $info['replacement'] . '>; rel="successor-version"');
        }
    }
}
```

---

### 5. Deprecation Handling

#### Gerenciar Endpoints Descontinuados

Estratégia para deprecar endpoints sem quebrar clientes existentes.

**Sistema de Deprecação Completo:**

```php
<?php
// Arquivo: inc/API/DeprecationManager.php

namespace MyPlugin\API;

class DeprecationManager {
    private static $deprecations = [
        'api/v1/users/profile' => [
            'deprecated_version' => '2.0',
            'sunset_date' => '2025-01-01',
            'replacement_endpoint' => 'api/v2/users/{id}',
            'migration_guide' => 'https://docs.example.com/migration-v1-to-v2',
            'reason' => 'Endpoint consolidado na v2',
        ],
    ];

    public static function register_deprecation_route($route, $deprecated_config) {
        self::$deprecations[$route] = $deprecated_config;
    }

    public static function add_deprecation_middleware() {
        add_filter('rest_pre_dispatch', function($response, $server, $request) {
            $route = $request->get_route();

            if (isset(self::$deprecations[$route])) {
                return self::handle_deprecated_endpoint($route, $request);
            }

            return $response;
        }, 10, 3);
    }

    private static function handle_deprecated_endpoint($route, $request) {
        $deprecation = self::$deprecations[$route];
        $sunset = new \DateTime($deprecation['sunset_date']);
        $now = new \DateTime();

        if ($now > $sunset) {
            return new \WP_Error(
                'endpoint_sunset',
                sprintf(
                    'Este endpoint foi descontinuado em %s. Use: %s',
                    $deprecation['sunset_date'],
                    $deprecation['replacement_endpoint']
                ),
                ['status' => 410] // Gone
            );
        }

        // Adicionar headers de deprecação
        $response = new \WP_REST_Response([], 200);

        $response->header('Deprecation', 'true');
        $response->header('Deprecated-Since', $deprecation['deprecated_version']);
        $response->header(
            'Sunset',
            $sunset->format('D, d M Y H:i:s T')
        );
        $response->header(
            'Link',
            '<' . rest_url($deprecation['replacement_endpoint']) . '>; rel="successor-version"'
        );
        $response->header('Warning', '299 - "Endpoint descontinuado. Use: ' . $deprecation['replacement_endpoint'] . '"');

        // Log de uso do endpoint descontinuado
        self::log_deprecated_usage($route);

        return $response;
    }

    private static function log_deprecated_usage($route) {
        $log_key = 'deprecated_usage_' . md5($route);
        $count = get_transient($log_key) ?? 0;
        set_transient($log_key, $count + 1, DAY_IN_SECONDS);
    }

    public static function get_deprecation_report() {
        $report = [];

        foreach (self::$deprecations as $route => $config) {
            $log_key = 'deprecated_usage_' . md5($route);
            $usage_count = get_transient($log_key) ?? 0;

            $report[] = [
                'route' => $route,
                'deprecated_since' => $config['deprecated_version'],
                'sunset_date' => $config['sunset_date'],
                'replacement' => $config['replacement_endpoint'],
                'usage_today' => $usage_count,
                'migration_guide' => $config['migration_guide'],
            ];
        }

        return $report;
    }
}
```

**Feedback ao Cliente:**

```php
<?php
// Classe para retornar informações úteis ao cliente

class DeprecatedResponseBuilder {
    public static function wrap_response($data, $route) {
        $deprecation = DeprecationManager::$deprecations[$route] ?? null;

        $response = [
            'data' => $data,
            'deprecation_notice' => $deprecation ? [
                'message' => 'Este endpoint está descontinuado',
                'deprecated_since' => $deprecation['deprecated_version'],
                'sunset_date' => $deprecation['sunset_date'],
                'replacement_endpoint' => $deprecation['replacement_endpoint'],
                'days_until_sunset' => self::days_until($deprecation['sunset_date']),
                'migration_guide' => $deprecation['migration_guide'],
            ] : null,
        ];

        return $response;
    }

    private static function days_until($date) {
        $sunset = new \DateTime($date);
        $now = new \DateTime();
        $interval = $now->diff($sunset);
        return $interval->days;
    }
}
```

---

### 6. API Documentation (OpenAPI/Swagger)

#### Documentação Automática com OpenAPI

Gerar documentação interativa e testável.

**Setup de Swagger/OpenAPI em WordPress:**

```php
<?php
// Arquivo: inc/API/OpenAPIGenerator.php

namespace MyPlugin\API;

class OpenAPIGenerator {
    public static function generate_spec() {
        return [
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'My WordPress API',
                'version' => '2.0.0',
                'description' => 'API RESTful para gerenciamento de conteúdo',
                'contact' => [
                    'name' => 'Suporte',
                    'url' => home_url(),
                    'email' => get_option('admin_email'),
                ],
                'license' => [
                    'name' => 'GPL v2',
                    'url' => 'https://www.gnu.org/licenses/gpl-2.0.html',
                ],
            ],
            'servers' => [
                [
                    'url' => rest_url(),
                    'description' => 'API Principal',
                ],
            ],
            'paths' => self::generate_paths(),
            'components' => self::generate_components(),
            'security' => [
                ['ApiKeyAuth' => []],
                ['BearerAuth' => []],
            ],
        ];
    }

    private static function generate_paths() {
        return [
            '/api/v2/posts' => [
                'get' => [
                    'summary' => 'Listar posts',
                    'operationId' => 'listPosts',
                    'tags' => ['Posts'],
                    'parameters' => [
                        [
                            'name' => 'per_page',
                            'in' => 'query',
                            'description' => 'Posts por página',
                            'schema' => ['type' => 'integer', 'default' => 10],
                        ],
                        [
                            'name' => 'page',
                            'in' => 'query',
                            'description' => 'Número da página',
                            'schema' => ['type' => 'integer', 'default' => 1],
                        ],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Lista de posts',
                            'content' => [
                                'application/json' => [
                                    'schema' => ['$ref' => '#/components/schemas/PostList'],
                                ],
                            ],
                        ],
                        '401' => ['description' => 'Não autenticado'],
                        '429' => ['description' => 'Limite de requisições excedido'],
                    ],
                ],
                'post' => [
                    'summary' => 'Criar novo post',
                    'operationId' => 'createPost',
                    'tags' => ['Posts'],
                    'security' => [
                        ['ApiKeyAuth' => []],
                        ['BearerAuth' => []],
                    ],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => ['$ref' => '#/components/schemas/CreatePostRequest'],
                            ],
                        ],
                    ],
                    'responses' => [
                        '201' => [
                            'description' => 'Post criado com sucesso',
                            'content' => [
                                'application/json' => [
                                    'schema' => ['$ref' => '#/components/schemas/Post'],
                                ],
                            ],
                        ],
                        '400' => ['description' => 'Dados inválidos'],
                        '401' => ['description' => 'Não autenticado'],
                        '403' => ['description' => 'Sem permissão'],
                    ],
                ],
            ],
            '/api/v2/posts/{id}' => [
                'get' => [
                    'summary' => 'Obter post por ID',
                    'operationId' => 'getPost',
                    'tags' => ['Posts'],
                    'parameters' => [
                        [
                            'name' => 'id',
                            'in' => 'path',
                            'required' => true,
                            'description' => 'ID do post',
                            'schema' => ['type' => 'integer'],
                        ],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Post encontrado',
                            'content' => [
                                'application/json' => [
                                    'schema' => ['$ref' => '#/components/schemas/Post'],
                                ],
                            ],
                        ],
                        '404' => ['description' => 'Post não encontrado'],
                    ],
                ],
            ],
        ];
    }

    private static function generate_components() {
        return [
            'schemas' => [
                'Post' => [
                    'type' => 'object',
                    'required' => ['id', 'title', 'content'],
                    'properties' => [
                        'id' => [
                            'type' => 'integer',
                            'example' => 123,
                        ],
                        'title' => [
                            'type' => 'string',
                            'example' => 'Meu Post Incrível',
                        ],
                        'content' => [
                            'type' => 'string',
                            'example' => 'Conteúdo do post...',
                        ],
                        'author' => [
                            'type' => 'object',
                            'properties' => [
                                'id' => ['type' => 'integer'],
                                'name' => ['type' => 'string'],
                            ],
                        ],
                        'created_at' => [
                            'type' => 'string',
                            'format' => 'date-time',
                            'example' => '2024-01-15T10:30:00Z',
                        ],
                        'updated_at' => [
                            'type' => 'string',
                            'format' => 'date-time',
                            'example' => '2024-01-16T14:20:00Z',
                        ],
                    ],
                ],
                'PostList' => [
                    'type' => 'object',
                    'properties' => [
                        'data' => [
                            'type' => 'array',
                            'items' => ['$ref' => '#/components/schemas/Post'],
                        ],
                        'meta' => [
                            'type' => 'object',
                            'properties' => [
                                'total' => ['type' => 'integer'],
                                'per_page' => ['type' => 'integer'],
                                'current_page' => ['type' => 'integer'],
                                'last_page' => ['type' => 'integer'],
                            ],
                        ],
                    ],
                ],
                'CreatePostRequest' => [
                    'type' => 'object',
                    'required' => ['title', 'content'],
                    'properties' => [
                        'title' => [
                            'type' => 'string',
                            'minLength' => 3,
                            'maxLength' => 255,
                        ],
                        'content' => [
                            'type' => 'string',
                            'minLength' => 10,
                        ],
                        'excerpt' => [
                            'type' => 'string',
                            'maxLength' => 500,
                        ],
                        'status' => [
                            'type' => 'string',
                            'enum' => ['draft', 'publish', 'pending'],
                            'default' => 'draft',
                        ],
                    ],
                ],
            ],
            'securitySchemes' => [
                'ApiKeyAuth' => [
                    'type' => 'apiKey',
                    'in' => 'header',
                    'name' => 'X-API-Key',
                    'description' => 'Chave de API para autenticação',
                ],
                'BearerAuth' => [
                    'type' => 'http',
                    'scheme' => 'bearer',
                    'bearerFormat' => 'JWT',
                    'description' => 'Token JWT para autenticação',
                ],
            ],
        ];
    }
}

// Registrar endpoint para servir o spec
add_action('rest_api_init', function() {
    register_rest_route('api/v2', '/openapi.json', [
        'methods' => 'GET',
        'callback' => function() {
            return new \WP_REST_Response(OpenAPIGenerator::generate_spec());
        },
        'permission_callback' => '__return_true',
    ]);
});
```

**Integrar com Swagger UI:**

```php
<?php
// Arquivo: inc/API/SwaggerUI.php

class SwaggerUI {
    public static function register_swagger_page() {
        add_action('wp_head', function() {
            if (!isset($_GET['swagger'])) {
                return;
            }

            // Carregar recursos do Swagger UI
            wp_enqueue_style('swagger-ui-css', 'https://cdn.jsdelivr.net/npm/swagger-ui-dist@3/swagger-ui.css');
            wp_enqueue_script('swagger-ui-js', 'https://cdn.jsdelivr.net/npm/swagger-ui-dist@3/swagger-ui.js');
        });

        // Template para a página
        add_action('template_redirect', function() {
            if (!isset($_GET['swagger'])) {
                return;
            }

            http_response_code(200);
            ?>
            <!DOCTYPE html>
            <html>
            <head>
                <title>API Documentation</title>
                <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/swagger-ui-dist@3/swagger-ui.css">
            </head>
            <body>
                <div id="swagger-ui"></div>
                <script src="https://cdn.jsdelivr.net/npm/swagger-ui-dist@3/swagger-ui.js"></script>
                <script>
                    const ui = SwaggerUIBundle({
                        url: "<?php echo rest_url('api/v2/openapi.json'); ?>",
                        dom_id: '#swagger-ui',
                        presets: [
                            SwaggerUIBundle.presets.apis,
                            SwaggerUIBundle.SwaggerUIStandalonePreset
                        ],
                        layout: "BaseLayout"
                    });
                </script>
            </body>
            </html>
            <?php
            die;
        });
    }
}
```

---

## Advanced Performance

### 1. Page Speed Optimization

#### Estratégias de Otimização

**Lazy Loading de Conteúdo:**

```php
<?php
// Arquivo: inc/Performance/LazyLoading.php

namespace MyPlugin\Performance;

class LazyLoading {
    public static function init() {
        add_filter('the_content', [__CLASS__, 'add_lazy_loading_to_images'], 999);
        add_filter('post_thumbnail_html', [__CLASS__, 'add_lazy_loading_to_thumbnail']);
    }

    public static function add_lazy_loading_to_images($content) {
        // Regex para encontrar todas as tags img
        $content = preg_replace_callback(
            '/<img([^>]+)src="([^"]+)"([^>]*)>/i',
            function($matches) {
                $attributes = $matches[1];
                $src = $matches[2];
                $closing = $matches[3];

                // Não aplicar lazy loading a imagens acima da fold
                if (strpos($attributes, 'data-no-lazy') !== false) {
                    return "<img{$attributes}src=\"{$src}\"{$closing}>";
                }

                // Usar atributo loading nativo (suportado por navegadores modernos)
                return "<img{$attributes}src=\"{$src}\" loading=\"lazy\"{$closing}>";
            },
            $content
        );

        return $content;
    }

    public static function add_lazy_loading_to_thumbnail($html) {
        return str_replace('<img', '<img loading="lazy"', $html);
    }
}
```

**Deferring JavaScript:**

```php
<?php
// Arquivo: inc/Performance/ScriptOptimization.php

namespace MyPlugin\Performance;

class ScriptOptimization {
    public static function defer_non_critical_scripts() {
        add_filter('script_loader_tag', function($tag, $handle, $src) {
            // Scripts que devem ser defer
            $defer_scripts = [
                'jquery',
                'jquery-core',
                'bootstrap-js',
                'custom-script',
            ];

            // Scripts que devem ser async
            $async_scripts = [
                'analytics',
                'google-analytics',
            ];

            if (in_array($handle, $defer_scripts)) {
                return str_replace(' src', ' defer src', $tag);
            }

            if (in_array($handle, $async_scripts)) {
                return str_replace(' src', ' async src', $tag);
            }

            return $tag;
        }, 10, 3);
    }

    public static function inline_critical_css() {
        add_action('wp_head', function() {
            $critical_css = file_get_contents(get_stylesheet_directory() . '/critical.css');
            echo '<style>' . $critical_css . '</style>';
        }, 5);
    }

    public static function preload_fonts() {
        add_action('wp_head', function() {
            $fonts = [
                'https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap',
            ];

            foreach ($fonts as $font) {
                echo '<link rel="preload" as="style" href="' . esc_url($font) . '">' . "\n";
            }
        });
    }
}
```

**Inline Scripts Críticos:**

```php
<?php
class CriticalScripts {
    public static function inline_critical_scripts() {
        // Detectar e aplicar style a acima da fold
        $critical_js = <<<'JS'
        <script>
            // Código crítico inline para acelerar carregamento
            document.documentElement.classList.add('js-enabled');
            
            // Detectar viewport para aplicar estilos apropriados
            if (window.innerWidth < 768) {
                document.body.classList.add('mobile-viewport');
            }
        </script>
        JS;

        wp_enqueue_script_inline($critical_js);
    }
}
```

---

### 2. Image Optimization

#### Otimizar e Servir Imagens Eficientemente

**Sistema de Compressão Automática:**

```php
<?php
// Arquivo: inc/Performance/ImageOptimization.php

namespace MyPlugin\Performance;

class ImageOptimization {
    const SUPPORTED_FORMATS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    public static function optimize_on_upload() {
        add_filter('wp_handle_upload', [__CLASS__, 'compress_image_on_upload']);
    }

    public static function compress_image_on_upload($upload) {
        $file = $upload['file'];
        $file_ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        if (!in_array($file_ext, self::SUPPORTED_FORMATS)) {
            return $upload;
        }

        // Usar biblioteca como ImageMagick ou GD
        if (extension_loaded('imagick')) {
            self::compress_with_imagick($file);
        } elseif (extension_loaded('gd')) {
            self::compress_with_gd($file);
        }

        return $upload;
    }

    private static function compress_with_imagick($file_path) {
        try {
            $image = new \Imagick($file_path);

            // Definir qualidade
            $image->setImageCompression(\Imagick::COMPRESSION_JPEG);
            $image->setImageCompressionQuality(85);

            // Remover metadados
            $image->stripImage();

            // Escrever arquivo
            $image->writeImage($file_path);
            $image->destroy();
        } catch (\Exception $e) {
            error_log('Imagick compression error: ' . $e->getMessage());
        }
    }

    private static function compress_with_gd($file_path) {
        $file_ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));

        $image = null;
        switch ($file_ext) {
            case 'jpg':
            case 'jpeg':
                $image = imagecreatefromjpeg($file_path);
                imagejpeg($image, $file_path, 85);
                break;
            case 'png':
                $image = imagecreatefrompng($file_path);
                imagepng($image, $file_path, 9);
                break;
        }

        if ($image) {
            imagedestroy($image);
        }
    }
}
```

**Servir Imagens em Múltiplos Formatos:**

```php
<?php
class ResponsiveImages {
    public static function generate_responsive_images($attachment_id) {
        $metadata = wp_get_attachment_metadata($attachment_id);

        if (!$metadata || !isset($metadata['file'])) {
            return;
        }

        $upload_dir = wp_upload_dir();
        $base_dir = $upload_dir['basedir'];
        $file_path = $base_dir . '/' . $metadata['file'];
        $file_ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));

        // Gerar versão WebP
        if (in_array($file_ext, ['jpg', 'jpeg', 'png'])) {
            self::convert_to_webp($file_path);
        }

        // Gerar versões redimensionadas
        self::generate_thumbnails($attachment_id, $file_path);
    }

    private static function convert_to_webp($original_path) {
        $webp_path = preg_replace('/\.\w+$/', '.webp', $original_path);

        if (extension_loaded('imagick')) {
            $image = new \Imagick($original_path);
            $image->setImageFormat('webp');
            $image->setImageCompressionQuality(85);
            $image->writeImage($webp_path);
            $image->destroy();
        }
    }

    private static function generate_thumbnails($attachment_id, $original_path) {
        $sizes = [
            'thumbnail' => ['width' => 150, 'height' => 150],
            'medium' => ['width' => 300, 'height' => 300],
            'large' => ['width' => 1024, 'height' => 1024],
        ];

        foreach ($sizes as $size_name => $dimensions) {
            // Gerar variação redimensionada
            self::resize_image($original_path, $size_name, $dimensions);
        }
    }

    private static function resize_image($path, $name, $dimensions) {
        // Implementar redimensionamento usando GD ou ImageMagick
        // ...
    }
}
```

**Picture Element com Fallbacks:**

```php
<?php
class PictureElement {
    public static function render_responsive_image($attachment_id, $alt_text = '') {
        $upload_dir = wp_upload_dir();
        $base_url = $upload_dir['baseurl'];
        $metadata = wp_get_attachment_metadata($attachment_id);

        $original_file = get_attached_file($attachment_id);
        $file_ext = strtolower(pathinfo($original_file, PATHINFO_EXTENSION));
        $base_name = preg_replace('/\.\w+$/', '', $original_file);

        $webp_url = $base_url . '/' . basename($base_name) . '.webp';
        $original_url = wp_get_attachment_url($attachment_id);

        ob_start();
        ?>
        <picture>
            <source srcset="<?php echo esc_url($webp_url); ?>" type="image/webp">
            <source srcset="<?php echo esc_url($original_url); ?>" type="image/<?php echo esc_attr($file_ext); ?>">
            <img 
                src="<?php echo esc_url($original_url); ?>" 
                alt="<?php echo esc_attr($alt_text); ?>"
                loading="lazy"
            >
        </picture>
        <?php
        return ob_get_clean();
    }
}
```

---

### 3. Code Splitting

#### Dividir Código em Chunks Menores

**Dynamic Imports no Frontend:**

```php
<?php
// Arquivo: inc/Performance/CodeSplitting.php

namespace MyPlugin\Performance;

class CodeSplitting {
    public static function register_split_scripts() {
        add_action('wp_enqueue_scripts', function() {
            // Script principal (core)
            wp_enqueue_script(
                'app-main',
                get_stylesheet_directory_uri() . '/js/main.js',
                [],
                filemtime(get_stylesheet_directory() . '/js/main.js'),
                true
            );

            // Chunks específicos carregados dinamicamente
            wp_enqueue_script(
                'app-chunk-posts',
                get_stylesheet_directory_uri() . '/js/chunks/posts.js',
                ['app-main'],
                filemtime(get_stylesheet_directory() . '/js/chunks/posts.js'),
                true
            );

            // Localizar dados necessários
            wp_localize_script('app-main', 'appConfig', [
                'apiUrl' => rest_url('api/v2'),
                'nonce' => wp_create_nonce('wp_rest'),
                'chunkUrl' => get_stylesheet_directory_uri() . '/js/chunks/',
            ]);
        });
    }
}
```

**Webpack Configuration para Code Splitting:**

```javascript
// webpack.config.js
const path = require('path');

module.exports = {
    mode: 'production',
    entry: {
        main: './src/index.js',
    },
    output: {
        path: path.resolve(__dirname, 'dist'),
        filename: '[name].js',
        chunkFilename: 'chunks/[name].[contenthash].js',
        clean: true,
    },
    optimization: {
        runtimeChunk: 'single',
        splitChunks: {
            chunks: 'all',
            cacheGroups: {
                vendor: {
                    test: /[\\/]node_modules[\\/]/,
                    name: 'vendors',
                    priority: 10,
                    reuseExistingChunk: true,
                },
                common: {
                    minChunks: 2,
                    priority: 5,
                    reuseExistingChunk: true,
                },
            },
        },
    },
    module: {
        rules: [
            {
                test: /\.jsx?$/,
                exclude: /node_modules/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['@babel/preset-env', '@babel/preset-react'],
                    },
                },
            },
        ],
    },
};
```

---

### 4. Progressive Enhancement

#### Garantir Funcionalidade Sem JavaScript

**Fallback HTML sem JS:**

```php
<?php
// Arquivo: inc/Performance/ProgressiveEnhancement.php

namespace MyPlugin\Performance;

class ProgressiveEnhancement {
    public static function render_post_list($posts) {
        // HTML básico funcionando sem JavaScript
        ?>
        <div class="posts-list" id="posts-list">
            <?php foreach ($posts as $post) : ?>
                <article class="post-item" data-post-id="<?php echo esc_attr($post->ID); ?>">
                    <h3>
                        <a href="<?php echo esc_url(get_permalink($post->ID)); ?>">
                            <?php echo esc_html($post->post_title); ?>
                        </a>
                    </h3>
                    <p><?php echo esc_html(wp_trim_words($post->post_content, 20)); ?></p>
                </article>
            <?php endforeach; ?>
        </div>

        <script>
            // Melhorias com JavaScript
            document.addEventListener('DOMContentLoaded', function() {
                const postsList = document.getElementById('posts-list');
                
                // Carregar mais posts dinamicamente
                const observer = new IntersectionObserver(function(entries) {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            // Carregar próxima página
                            loadMorePosts();
                        }
                    });
                });

                const lastItem = postsList.lastElementChild;
                if (lastItem) observer.observe(lastItem);
            });
        </script>
        <?php
    }
}
```

**Forms com Validação Progressiva:**

```php
<?php
class ProgressiveFormValidation {
    public static function render_contact_form() {
        ?>
        <form method="POST" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" id="contact-form">
            <div class="form-group">
                <label for="name">Nome *</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    required 
                    minlength="3"
                    pattern="[A-Za-z\s]+"
                >
                <!-- Mensagem de erro sem JS -->
                <noscript>
                    <p class="error">Campo obrigatório com mínimo 3 caracteres</p>
                </noscript>
            </div>

            <div class="form-group">
                <label for="email">E-mail *</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    required
                >
            </div>

            <button type="submit" class="btn">Enviar</button>
        </form>

        <script>
            // Validação e feedback com JS
            document.getElementById('contact-form').addEventListener('submit', async (e) => {
                // Validação extra com JS
                const form = e.target;
                const email = form.email.value;

                // Verificar se email já existe (exemplo)
                const exists = await checkEmailExists(email);
                if (exists) {
                    e.preventDefault();
                    alert('Este e-mail já foi registrado');
                }
            });
        </script>
        <?php
    }
}
```

---

### 5. Core Web Vitals

#### Otimizar LCP, FID e CLS

**Monitoramento de Core Web Vitals:**

```php
<?php
// Arquivo: inc/Performance/CoreWebVitals.php

namespace MyPlugin\Performance;

class CoreWebVitals {
    public static function init() {
        add_action('wp_footer', [__CLASS__, 'inject_cwv_monitoring']);
    }

    public static function inject_cwv_monitoring() {
        ?>
        <script>
            // Biblioteca Web Vitals
            import {getCLS, getFID, getFCP, getLCP, getTTFB} from 'https://cdn.jsdelivr.net/npm/web-vitals@3/+esm';

            // Coletar métricas
            const metrics = {};

            getCLS(metric => {
                metrics.cls = metric.value;
                sendMetric('cls', metric.value);
            });

            getFID(metric => {
                metrics.fid = metric.value;
                sendMetric('fid', metric.value);
            });

            getLCP(metric => {
                metrics.lcp = metric.value;
                sendMetric('lcp', metric.value);
            });

            getTTFB(metric => {
                metrics.ttfb = metric.value;
                sendMetric('ttfb', metric.value);
            });

            // Enviar métricas para o servidor
            function sendMetric(name, value) {
                if (navigator.sendBeacon) {
                    const data = new FormData();
                    data.append('metric', name);
                    data.append('value', value);
                    data.append('url', window.location.href);
                    
                    navigator.sendBeacon('<?php echo esc_js(rest_url('api/v2/metrics')); ?>', data);
                }
            }
        </script>
        <?php
    }
}

// Endpoint para receber métricas
add_action('rest_api_init', function() {
    register_rest_route('api/v2', '/metrics', [
        'methods' => 'POST',
        'callback' => [CoreWebVitals::class, 'save_metrics'],
        'permission_callback' => '__return_true',
    ]);
});
```

**Otimizar LCP (Largest Contentful Paint):**

```php
<?php
class LCPOptimization {
    public static function optimize_lcp() {
        // 1. Preload recursos críticos
        add_action('wp_head', function() {
            echo '<link rel="preload" as="image" href="' . esc_url(get_the_post_thumbnail_url()) . '">' . "\n";
        });

        // 2. Otimizar imagens da hero
        add_filter('post_thumbnail_html', function($html) {
            // Adicionar fetchpriority="high"
            return str_replace('<img', '<img fetchpriority="high"', $html);
        });

        // 3. Inline styles críticos
        add_action('wp_head', function() {
            echo '<style>
                .hero-image { max-width: 100%; height: auto; }
                .hero-section { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
            </style>';
        }, 5);
    }
}
```

**Otimizar FID (First Input Delay):**

```php
<?php
class FIDOptimization {
    public static function optimize_fid() {
        // Remover JavaScript bloqueante
        add_filter('script_loader_tag', function($tag, $handle) {
            $non_blocking = ['jquery', 'analytics', 'ads'];

            if (in_array($handle, $non_blocking)) {
                return str_replace('>', ' async>', str_replace('<script', '<script async', $tag));
            }

            return $tag;
        }, 10, 2);

        // Usar Web Workers para tarefas pesadas
        add_action('wp_footer', function() {
            ?>
            <script>
                // Offload pesadas computações para worker
                if (window.Worker) {
                    const worker = new Worker('<?php echo esc_url(get_stylesheet_directory_uri() . '/js/worker.js'); ?>');
                    
                    worker.postMessage({
                        task: 'processData',
                        data: complexDataSet
                    });

                    worker.onmessage = (e) => {
                        console.log('Worker result:', e.data);
                    };
                }
            </script>
            <?php
        });
    }
}
```

**Otimizar CLS (Cumulative Layout Shift):**

```php
<?php
class CLSOptimization {
    public static function optimize_cls() {
        // 1. Reservar espaço para elementos que carregam tarde
        add_action('wp_head', function() {
            echo '<style>
                /* Reservar espaço para imagens */
                img {
                    min-height: 300px;
                    aspect-ratio: 16 / 9;
                }

                /* Espaço para ads */
                .ad-space {
                    min-height: 250px;
                }

                /* Espaço para font loading */
                @font-face {
                    font-family: "CustomFont";
                    src: url("/fonts/custom.woff2") format("woff2");
                    size-adjust: 100%;
                    ascent-override: 90%;
                    descent-override: 22%;
                    line-gap-override: 0%;
                }
            </style>';
        });

        // 2. Usar `content-visibility` para melhorar rendering
        add_filter('the_content', function($content) {
            // Aplicar content-visibility a posts longos
            if (strlen($content) > 5000) {
                $content = '<div style="content-visibility: auto;">' . $content . '</div>';
            }
            return $content;
        });
    }
}
```

---

### 6. Lighthouse Optimization

#### Atingir Scores Altos no Lighthouse

**Análise e Relatório Automatizado:**

```php
<?php
// Arquivo: inc/Performance/LighthouseAnalysis.php

namespace MyPlugin\Performance;

class LighthouseAnalysis {
    // Usar Google Lighthouse CI
    public static function setup_lighthouse_ci() {
        return [
            'ci' => [
                'collect' => [
                    'numberOfRuns' => 3,
                    'url' => [home_url()],
                ],
                'upload' => [
                    'target' => 'temporary-public-storage',
                ],
                'assert' => [
                    'preset' => 'lighthouse:recommended',
                    'assertions' => [
                        'categories:performance' => ['minScore' => 0.9],
                        'categories:accessibility' => ['minScore' => 0.9],
                        'categories:best-practices' => ['minScore' => 0.9],
                        'categories:seo' => ['minScore' => 0.9],
                    ],
                ],
            ],
        ];
    }

    public static function optimize_for_lighthouse() {
        // Performance
        self::optimize_performance();
        // Accessibility
        self::optimize_accessibility();
        // Best Practices
        self::optimize_best_practices();
        // SEO
        self::optimize_seo();
    }

    private static function optimize_performance() {
        add_action('init', function() {
            // Remover unused CSS
            // Usar minificação
            // Ativar caching
            // Otimizar imagens
            // Code splitting
        });
    }

    private static function optimize_accessibility() {
        add_filter('the_title', function($title) {
            // Garantir headings semanticamente corretos
            return $title;
        });

        add_filter('wp_get_attachment_image_attributes', function($atts) {
            // Garantir alt texts em imagens
            if (empty($atts['alt'])) {
                $atts['alt'] = 'Imagem do site';
            }
            return $atts;
        });
    }

    private static function optimize_best_practices() {
        // HTTPS
        // CSP Headers
        // Sem console errors
        // Imagens tamanho correto
    }

    private static function optimize_seo() {
        // Structured data
        // Meta tags
        // Mobile friendly
        // Page titles
    }
}
```

**Implementar Automated Testing:**

```bash
#!/bin/bash
# lighthouse-ci.sh

npm install -g @lhci/cli@*

lhci autorun \
  --config=lighthouserc.json \
  --upload.targetBaseUrl=https://seu-site.com
```

```json
{
  "ci": {
    "collect": {
      "numberOfRuns": 3,
      "url": [
        "https://seu-site.com/",
        "https://seu-site.com/sobre",
        "https://seu-site.com/blog"
      ],
      "settings": {
        "chromeFlags": "--no-sandbox"
      }
    },
    "upload": {
      "target": "temporary-public-storage"
    },
    "assert": {
      "preset": "lighthouse:recommended",
      "assertions": {
        "categories:performance": ["error", { "minScore": 0.9 }],
        "categories:accessibility": ["error", { "minScore": 0.9 }],
        "categories:best-practices": ["error", { "minScore": 0.9 }],
        "categories:seo": ["error", { "minScore": 0.9 }]
      }
    }
  }
}
```

---

## WordPress Ecosystem

### 1. WooCommerce Integration - Padrões Avançados

#### Estender Funcionalidade do WooCommerce

**Padrão 1: Custom Product Type Completo**

```php
<?php
/**
 * Custom Product Type com todas as funcionalidades
 */
class WC_Product_Subscription extends WC_Product {
    
    public function __construct($product) {
        $this->product_type = 'subscription';
        parent::__construct($product);
    }
    
    public function get_type() {
        return 'subscription';
    }
    
    public function is_purchasable() {
        return true;
    }
    
    public function is_virtual() {
        return true; // Produto virtual
    }
    
    public function needs_shipping() {
        return false;
    }
    
    public function get_price_html($price = '') {
        $price = $this->get_price();
        $billing_period = $this->get_meta('_billing_period', true) ?: 'month';
        
        return wc_price($price) . ' / ' . $billing_period;
    }
    
    public function add_to_cart_text() {
        return __('Subscribe', 'woocommerce');
    }
}

// Registrar product type
add_filter('product_type_selector', function($types) {
    $types['subscription'] = __('Subscription', 'woocommerce');
    return $types;
});

add_filter('woocommerce_product_class', function($classname, $product_type) {
    if ($product_type === 'subscription') {
        $classname = 'WC_Product_Subscription';
    }
    return $classname;
}, 10, 2);
```

**Padrão 2: WooCommerce Hooks Avançados**

```php
<?php
/**
 * Padrões avançados de hooks WooCommerce
 */
class WooCommerce_Hooks_Advanced {
    
    /**
     * Modificar cálculo de preço dinamicamente
     */
    public static function dynamic_pricing() {
        add_filter('woocommerce_product_get_price', function($price, $product) {
            // Desconto baseado em quantidade
            if (WC()->cart) {
                $cart_item = WC()->cart->find_product_in_cart($product->get_id());
                if ($cart_item && $cart_item['quantity'] >= 10) {
                    $price = $price * 0.9; // 10% desconto
                }
            }
            
            return $price;
        }, 10, 2);
        
        // Desconto para usuários específicos
        add_filter('woocommerce_product_get_sale_price', function($sale_price, $product) {
            if (current_user_can('wholesale_customer')) {
                $regular_price = $product->get_regular_price();
                return $regular_price * 0.8; // 20% desconto
            }
            return $sale_price;
        }, 10, 2);
    }
    
    /**
     * Customizar processo de checkout
     */
    public static function custom_checkout_process() {
        // Validar campos customizados
        add_action('woocommerce_checkout_process', function() {
            $custom_field = $_POST['billing_custom_field'] ?? '';
            
            if (empty($custom_field)) {
                wc_add_notice(__('Campo customizado é obrigatório', 'woocommerce'), 'error');
            }
        });
        
        // Processar dados após checkout
        add_action('woocommerce_checkout_order_processed', function($order_id, $posted_data) {
            // Salvar dados customizados
            if (!empty($posted_data['billing_custom_field'])) {
                update_post_meta(
                    $order_id,
                    '_billing_custom_field',
                    sanitize_text_field($posted_data['billing_custom_field'])
                );
            }
        }, 10, 2);
    }
    
    /**
     * Customizar emails de pedido
     */
    public static function custom_order_emails() {
        // Adicionar conteúdo customizado aos emails
        add_action('woocommerce_email_order_details', function($order, $sent_to_admin, $plain_text, $email) {
            if ($email->id === 'customer_completed_order') {
                $custom_message = get_post_meta($order->get_id(), '_custom_email_message', true);
                if ($custom_message) {
                    echo '<p>' . esc_html($custom_message) . '</p>';
                }
            }
        }, 10, 4);
        
        // Adicionar anexos aos emails
        add_filter('woocommerce_email_attachments', function($attachments, $email_id, $order) {
            if ($email_id === 'customer_invoice') {
                $invoice_pdf = generate_invoice_pdf($order);
                if ($invoice_pdf) {
                    $attachments[] = $invoice_pdf;
                }
            }
            return $attachments;
        }, 10, 3);
    }
    
    /**
     * Integração com REST API WooCommerce
     */
    public static function rest_api_integration() {
        // Adicionar campos customizados à REST API
        add_action('rest_api_init', function() {
            register_rest_field('shop_order', 'custom_fields', [
                'get_callback' => function($order) {
                    return [
                        'custom_field' => get_post_meta($order['id'], '_billing_custom_field', true),
                        'subscription_id' => get_post_meta($order['id'], '_subscription_id', true),
                    ];
                },
                'update_callback' => function($value, $order) {
                    update_post_meta($order->get_id(), '_billing_custom_field', sanitize_text_field($value['custom_field']));
                },
                'schema' => [
                    'type' => 'object',
                    'properties' => [
                        'custom_field' => ['type' => 'string'],
                        'subscription_id' => ['type' => 'integer'],
                    ],
                ],
            ]);
        });
    }
    
    /**
     * Customizar carrinho e checkout
     */
    public static function cart_customization() {
        // Adicionar fees customizados
        add_action('woocommerce_cart_calculate_fees', function() {
            if (is_admin() && !defined('DOING_AJAX')) {
                return;
            }
            
            $fee_amount = 0;
            
            // Fee baseado em método de pagamento
            $chosen_payment_method = WC()->session->get('chosen_payment_method');
            if ($chosen_payment_method === 'bacs') {
                $fee_amount = 5.00; // Taxa para transferência bancária
            }
            
            if ($fee_amount > 0) {
                WC()->cart->add_fee(__('Taxa de processamento', 'woocommerce'), $fee_amount);
            }
        });
        
        // Validar carrinho antes de checkout
        add_action('woocommerce_check_cart_items', function() {
            foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                $product = $cart_item['data'];
                
                // Verificar estoque mínimo
                if ($product->get_stock_quantity() < 5) {
                    wc_add_notice(
                        sprintf(__('%s está com estoque baixo', 'woocommerce'), $product->get_name()),
                        'error'
                    );
                }
            }
        });
    }
}

// Inicializar
WooCommerce_Hooks_Advanced::dynamic_pricing();
WooCommerce_Hooks_Advanced::custom_checkout_process();
WooCommerce_Hooks_Advanced::custom_order_emails();
WooCommerce_Hooks_Advanced::rest_api_integration();
WooCommerce_Hooks_Advanced::cart_customization();
```

**Padrão 3: Subscription Management**

```php
<?php
/**
 * Sistema de assinaturas WooCommerce
 */
class WooCommerce_Subscriptions {
    
    /**
     * Criar assinatura a partir de pedido
     */
    public static function create_subscription_from_order($order_id) {
        $order = wc_get_order($order_id);
        
        if (!$order) {
            return false;
        }
        
        // Verificar se pedido contém produto de assinatura
        $has_subscription = false;
        foreach ($order->get_items() as $item) {
            $product = $item->get_product();
            if ($product && $product->get_type() === 'subscription') {
                $has_subscription = true;
                break;
            }
        }
        
        if (!$has_subscription) {
            return false;
        }
        
        // Criar assinatura
        $subscription_data = [
            'post_type' => 'shop_subscription',
            'post_status' => 'wc-active',
            'post_author' => $order->get_customer_id(),
            'meta_input' => [
                '_order_id' => $order_id,
                '_customer_user' => $order->get_customer_id(),
                '_billing_period' => get_post_meta($order_id, '_billing_period', true) ?: 'month',
                '_billing_interval' => 1,
                '_next_payment' => date('Y-m-d H:i:s', strtotime('+1 month')),
            ],
        ];
        
        $subscription_id = wp_insert_post($subscription_data);
        
        if (is_wp_error($subscription_id)) {
            error_log('Failed to create subscription: ' . $subscription_id->get_error_message());
            return false;
        }
        
        // Agendar renovação
        wp_schedule_event(
            strtotime('+1 month'),
            'monthly',
            'woocommerce_subscription_renewal',
            [$subscription_id]
        );
        
        return $subscription_id;
    }
    
    /**
     * Processar renovação de assinatura
     */
    public static function process_renewal($subscription_id) {
        $subscription = get_post($subscription_id);
        
        if (!$subscription || $subscription->post_type !== 'shop_subscription') {
            return false;
        }
        
        $order_id = get_post_meta($subscription_id, '_order_id', true);
        $order = wc_get_order($order_id);
        
        if (!$order) {
            return false;
        }
        
        // Criar novo pedido de renovação
        $renewal_order = wc_create_order([
            'customer_id' => $order->get_customer_id(),
            'created_via' => 'subscription_renewal',
        ]);
        
        // Adicionar itens do pedido original
        foreach ($order->get_items() as $item) {
            $renewal_order->add_product(
                $item->get_product(),
                $item->get_quantity()
            );
        }
        
        // Processar pagamento
        $payment_method = $order->get_payment_method();
        $payment_result = WC()->payment_gateways()->payment_gateways()[$payment_method]->process_payment($renewal_order->get_id());
        
        if ($payment_result['result'] === 'success') {
            // Atualizar próxima renovação
            update_post_meta(
                $subscription_id,
                '_next_payment',
                date('Y-m-d H:i:s', strtotime('+1 month'))
            );
            
            // Agendar próxima renovação
            wp_schedule_single_event(
                strtotime('+1 month'),
                'woocommerce_subscription_renewal',
                [$subscription_id]
            );
            
            return true;
        }
        
        // Pagamento falhou - suspender assinatura
        wp_update_post([
            'ID' => $subscription_id,
            'post_status' => 'wc-on-hold',
        ]);
        
        return false;
    }
}

// Hook para criar assinatura após pedido completo
add_action('woocommerce_order_status_completed', [WooCommerce_Subscriptions::class, 'create_subscription_from_order']);

// Hook para processar renovação
add_action('woocommerce_subscription_renewal', [WooCommerce_Subscriptions::class, 'process_renewal']);
```

**Produtos Customizados:**

```php
<?php
// Arquivo: inc/WooCommerce/CustomProducts.php

namespace MyPlugin\WooCommerce;

class CustomProducts {
    public static function register_custom_product_type() {
        add_filter('woocommerce_product_type_query_class_mapping', function($classes) {
            $classes['custom_bundle'] = 'WC_Product_Custom_Bundle';
            return $classes;
        });
    }

    public static function add_product_attributes() {
        add_action('admin_init', function() {
            // Registrar atributo customizado
            if (taxonomy_exists('pa_custom_attr')) {
                return;
            }

            register_taxonomy('pa_custom_attr', ['product'], [
                'hierarchical' => false,
                'label' => 'Atributo Customizado',
                'show_in_rest' => true,
            ]);
        });
    }

    public static function add_product_custom_fields() {
        add_action('woocommerce_product_options_general_product_data', function() {
            woocommerce_wp_text_input([
                'id' => '_custom_field',
                'label' => 'Campo Customizado',
                'description' => 'Descrição do campo',
            ]);
        });

        add_action('woocommerce_process_product_meta', function($post_id) {
            update_post_meta(
                $post_id,
                '_custom_field',
                sanitize_text_field($_POST['_custom_field'] ?? '')
            );
        });
    }
}
```

**Customizar Checkout:**

```php
<?php
class CheckoutCustomization {
    public static function add_custom_checkout_fields() {
        add_filter('woocommerce_checkout_fields', function($fields) {
            // Adicionar campo customizado
            $fields['billing']['billing_custom_field'] = [
                'type' => 'text',
                'label' => __('Campo Customizado', 'woocommerce'),
                'placeholder' => __('Preencha este campo', 'woocommerce'),
                'required' => true,
            ];

            return $fields;
        });
    }

    public static function save_custom_checkout_data($order_id) {
        if (isset($_POST['post_data'])) {
            parse_str($_POST['post_data'], $post_data);
        } else {
            $post_data = $_POST;
        }

        if (!empty($post_data['post_data']['billing_custom_field'])) {
            update_post_meta(
                $order_id,
                '_billing_custom_field',
                sanitize_text_field($post_data['post_data']['billing_custom_field'])
            );
        }
    }
}
```

**Customizar Pedidos:**

```php
<?php
class OrderCustomization {
    public static function modify_order_items() {
        add_filter('woocommerce_order_item_name', function($name, $item, $show_qty) {
            // Adicionar informação customizada
            $custom_data = wc_get_order_item_meta($item->get_id(), '_custom_meta');
            if ($custom_data) {
                $name .= ' - ' . esc_html($custom_data);
            }

            return $name;
        }, 10, 3);
    }

    public static function send_custom_order_emails() {
        add_action('woocommerce_order_status_completed', function($order_id) {
            $order = wc_get_order($order_id);

            // Enviar email customizado
            wp_mail(
                $order->get_billing_email(),
                'Seu pedido foi completado',
                'Obrigado por sua compra!',
                ['Content-Type: text/html; charset=UTF-8']
            );
        });
    }
}
```

---

### 2. ACF (Advanced Custom Fields) - Padrões Avançados

#### Trabalhar com ACF em WordPress

**Padrão 1: ACF Fields com Validação Customizada**

```php
<?php
/**
 * ACF Fields com validação avançada
 */
class ACF_Advanced_Patterns {
    
    /**
     * Registrar fields com validação customizada
     */
    public static function register_validated_fields() {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }
        
        acf_add_local_field_group([
            'key' => 'group_product_details',
            'title' => 'Detalhes do Produto',
            'fields' => [
                [
                    'key' => 'field_product_sku',
                    'label' => 'SKU',
                    'name' => 'product_sku',
                    'type' => 'text',
                    'required' => true,
                    'custom_validation' => true, // Flag para validação customizada
                ],
                [
                    'key' => 'field_product_price',
                    'label' => 'Preço',
                    'name' => 'product_price',
                    'type' => 'number',
                    'required' => true,
                    'min' => 0,
                    'step' => 0.01,
                ],
            ],
            'location' => [
                [
                    [
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'product',
                    ],
                ],
            ],
        ]);
        
        // Validação customizada
        add_filter('acf/validate_value/name=product_sku', [self::class, 'validate_sku'], 10, 4);
    }
    
    /**
     * Validar SKU único
     */
    public static function validate_sku($valid, $value, $field, $input) {
        if (!$valid) {
            return $valid;
        }
        
        // Verificar se SKU já existe
        $existing = get_posts([
            'post_type' => 'product',
            'meta_query' => [
                [
                    'key' => 'product_sku',
                    'value' => $value,
                    'compare' => '=',
                ],
            ],
            'post__not_in' => [get_the_ID()],
        ]);
        
        if (!empty($existing)) {
            return 'SKU já existe. Escolha outro.';
        }
        
        return $valid;
    }
    
    /**
     * Conditional Fields (campos condicionais)
     */
    public static function conditional_fields() {
        acf_add_local_field_group([
            'key' => 'group_conditional_fields',
            'title' => 'Campos Condicionais',
            'fields' => [
                [
                    'key' => 'field_product_type',
                    'label' => 'Tipo de Produto',
                    'name' => 'product_type',
                    'type' => 'select',
                    'choices' => [
                        'physical' => 'Físico',
                        'digital' => 'Digital',
                        'service' => 'Serviço',
                    ],
                ],
                [
                    'key' => 'field_shipping_weight',
                    'label' => 'Peso (kg)',
                    'name' => 'shipping_weight',
                    'type' => 'number',
                    'conditional_logic' => [
                        [
                            [
                                'field' => 'field_product_type',
                                'operator' => '==',
                                'value' => 'physical',
                            ],
                        ],
                    ],
                ],
                [
                    'key' => 'field_download_link',
                    'label' => 'Link de Download',
                    'name' => 'download_link',
                    'type' => 'url',
                    'conditional_logic' => [
                        [
                            [
                                'field' => 'field_product_type',
                                'operator' => '==',
                                'value' => 'digital',
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }
    
    /**
     * ACF Fields com Options Page
     */
    public static function options_page_fields() {
        // Criar options page
        if (function_exists('acf_add_options_page')) {
            acf_add_options_page([
                'page_title' => 'Configurações do Site',
                'menu_title' => 'Configurações',
                'menu_slug' => 'site-settings',
                'capability' => 'manage_options',
            ]);
        }
        
        // Adicionar fields à options page
        acf_add_local_field_group([
            'key' => 'group_site_settings',
            'title' => 'Configurações do Site',
            'fields' => [
                [
                    'key' => 'field_site_logo',
                    'label' => 'Logo do Site',
                    'name' => 'site_logo',
                    'type' => 'image',
                ],
                [
                    'key' => 'field_contact_email',
                    'label' => 'Email de Contato',
                    'name' => 'contact_email',
                    'type' => 'email',
                ],
            ],
            'location' => [
                [
                    [
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'site-settings',
                    ],
                ],
            ],
        ]);
    }
    
    /**
     * ACF com REST API e GraphQL
     */
    public static function api_integration() {
        // Habilitar ACF em REST API
        add_filter('acf/rest_api_enabled/post', '__return_true');
        
        // Customizar campos expostos na REST API
        add_filter('acf/rest_api_format', function($format, $post_id, $field) {
            // Formato customizado para campo de imagem
            if ($field['type'] === 'image') {
                $image_id = get_field($field['name'], $post_id);
                if ($image_id) {
                    return [
                        'id' => $image_id,
                        'url' => wp_get_attachment_image_url($image_id, 'full'),
                        'thumbnail' => wp_get_attachment_image_url($image_id, 'thumbnail'),
                        'alt' => get_post_meta($image_id, '_wp_attachment_image_alt', true),
                    ];
                }
            }
            
            return $format;
        }, 10, 3);
        
        // GraphQL (requer wp-graphql-acf)
        if (function_exists('acf_add_options_page')) {
            // Fields de options page automaticamente disponíveis em GraphQL
        }
    }
    
    /**
     * ACF Fields com Local JSON
     */
    public static function local_json_setup() {
        // Salvar fields como JSON local (versionamento)
        add_filter('acf/settings/save_json', function($path) {
            return get_stylesheet_directory() . '/acf-json';
        });
        
        add_filter('acf/settings/load_json', function($paths) {
            unset($paths[0]);
            $paths[] = get_stylesheet_directory() . '/acf-json';
            return $paths;
        });
    }
}

// Inicializar
ACF_Advanced_Patterns::register_validated_fields();
ACF_Advanced_Patterns::conditional_fields();
ACF_Advanced_Patterns::options_page_fields();
ACF_Advanced_Patterns::api_integration();
ACF_Advanced_Patterns::local_json_setup();
```

**Registrar Fields Programaticamente:**

```php
<?php
// Arquivo: inc/ACF/RegisterFields.php

namespace MyPlugin\ACF;

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group([
        'key' => 'group_book_details',
        'title' => 'Detalhes do Livro',
        'fields' => [
            [
                'key' => 'field_book_title',
                'label' => 'Título do Livro',
                'name' => 'book_title',
                'type' => 'text',
                'required' => true,
            ],
            [
                'key' => 'field_book_author',
                'label' => 'Autor',
                'name' => 'book_author',
                'type' => 'text',
                'required' => true,
            ],
            [
                'key' => 'field_book_cover',
                'label' => 'Capa do Livro',
                'name' => 'book_cover',
                'type' => 'image',
                'return_format' => 'array',
            ],
            [
                'key' => 'field_book_chapters',
                'label' => 'Capítulos',
                'name' => 'book_chapters',
                'type' => 'repeater',
                'sub_fields' => [
                    [
                        'key' => 'field_chapter_title',
                        'label' => 'Título do Capítulo',
                        'name' => 'chapter_title',
                        'type' => 'text',
                    ],
                    [
                        'key' => 'field_chapter_content',
                        'label' => 'Conteúdo',
                        'name' => 'chapter_content',
                        'type' => 'textarea',
                    ],
                ],
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'book',
                ],
            ],
        ],
    ]);
}
```

**Trabalhar com ACF em Rest API:**

```php
<?php
class ACFToRest {
    public static function expose_acf_to_rest() {
        add_filter('acf/rest_api_enabled/post', '__return_true');
        add_filter('acf/rest_api_enabled/post/book', '__return_true');

        // Customizar campos expostos
        add_filter('acf/rest_api_enabled/post/book', function() {
            return [
                'show_in_rest' => true,
            ];
        });
    }

    public static function add_acf_to_graphql() {
        // Usar plugin: wp-graphql/wp-graphql-acf
        // Automaticamente expõe campos ACF em GraphQL
    }
}
```

---

### 3. Jetpack API Integration - Padrões Avançados

#### Integrar Jetpack para Funcionalidades Adicionais

**Padrão 1: Jetpack REST API Completo**

```php
<?php
/**
 * Integração completa com Jetpack API
 */
class Jetpack_API_Integration {
    
    /**
     * Configurar módulos Jetpack programaticamente
     */
    public static function configure_modules() {
        if (!class_exists('Jetpack')) {
            return;
        }
        
        // Módulos essenciais
        $modules = [
            'json-api',           // REST API
            'protect',            // Proteção brute force
            'photon',             // CDN de imagens
            'stats',              // Estatísticas
            'sitemaps',           // Sitemaps XML
            'verification-tools', // Verificação de propriedade
        ];
        
        foreach ($modules as $module) {
            if (Jetpack::is_module_active($module)) {
                continue;
            }
            
            Jetpack::activate_module($module, false, false);
        }
    }
    
    /**
     * Usar Jetpack REST API para autenticação
     */
    public static function rest_api_auth() {
        // Jetpack fornece autenticação via Application Passwords
        // Verificar se request vem do Jetpack
        add_filter('determine_current_user', function($user_id) {
            $jetpack_signature = $_SERVER['HTTP_X_JETPACK_SIGNATURE'] ?? null;
            
            if ($jetpack_signature) {
                // Validar assinatura Jetpack
                $is_valid = Jetpack::verify_xml_rpc_signature(
                    $_SERVER['REQUEST_METHOD'],
                    $_SERVER['REQUEST_URI'],
                    file_get_contents('php://input')
                );
                
                if ($is_valid) {
                    // Autenticar como usuário do Jetpack
                    $jetpack_user = Jetpack::get_connected_user_data();
                    if ($jetpack_user) {
                        return $jetpack_user['ID'];
                    }
                }
            }
            
            return $user_id;
        });
    }
    
    /**
     * Integração com Jetpack Stats API
     */
    public static function stats_integration() {
        // Obter estatísticas do site
        add_action('rest_api_init', function() {
            register_rest_route('jetpack/v1', '/stats', [
                'methods' => 'GET',
                'callback' => function() {
                    if (!class_exists('Jetpack')) {
                        return new WP_Error('jetpack_not_active', 'Jetpack não está ativo');
                    }
                    
                    $stats = Jetpack::get_module('stats');
                    
                    if (!$stats) {
                        return new WP_Error('stats_not_available', 'Stats não disponível');
                    }
                    
                    // Obter dados de estatísticas
                    $stats_data = [
                        'visitors' => get_option('jetpack_stats_visitors'),
                        'views' => get_option('jetpack_stats_views'),
                        'top_posts' => get_option('jetpack_stats_top_posts'),
                    ];
                    
                    return new WP_REST_Response($stats_data, 200);
                },
                'permission_callback' => function() {
                    return current_user_can('manage_options');
                },
            ]);
        });
    }
    
    /**
     * Jetpack Photon - CDN de Imagens
     */
    public static function photon_integration() {
        // Habilitar Photon para todas as imagens
        add_filter('jetpack_photon_url', '__return_true');
        
        // Excluir certas imagens do Photon
        add_filter('jetpack_photon_skip_image', function($skip, $image_src) {
            // Pular imagens de certos domínios
            $excluded_domains = ['example.com', 'cdn.example.com'];
            
            foreach ($excluded_domains as $domain) {
                if (strpos($image_src, $domain) !== false) {
                    return true;
                }
            }
            
            return $skip;
        }, 10, 2);
        
        // Customizar parâmetros do Photon
        add_filter('jetpack_photon_pre_args', function($args, $image_url) {
            // Adicionar parâmetros customizados
            $args['quality'] = 90;
            $args['strip'] = 'all';
            
            return $args;
        }, 10, 2);
    }
    
    /**
     * Jetpack Protect - Proteção contra Brute Force
     */
    public static function protect_integration() {
        // Verificar se IP está bloqueado
        add_action('wp_login_failed', function($username) {
            if (class_exists('Jetpack_Protect_Module')) {
                $ip = $_SERVER['REMOTE_ADDR'];
                
                // Jetpack Protect gerencia bloqueios automaticamente
                // Mas podemos adicionar lógica customizada
                $failed_attempts = get_transient("login_failed_{$ip}") ?: 0;
                $failed_attempts++;
                
                if ($failed_attempts >= 5) {
                    // Bloquear IP temporariamente
                    set_transient("ip_blocked_{$ip}", true, 3600); // 1 hora
                } else {
                    set_transient("login_failed_{$ip}", $failed_attempts, 900); // 15 minutos
                }
            }
        });
        
        // Verificar bloqueio antes de login
        add_filter('authenticate', function($user, $username, $password) {
            $ip = $_SERVER['REMOTE_ADDR'];
            
            if (get_transient("ip_blocked_{$ip}")) {
                return new WP_Error(
                    'ip_blocked',
                    'Seu IP foi temporariamente bloqueado devido a múltiplas tentativas de login falhadas.'
                );
            }
            
            return $user;
        }, 10, 3);
    }
    
    /**
     * Jetpack Sitemaps - Customização
     */
    public static function sitemaps_customization() {
        // Adicionar custom post types ao sitemap
        add_filter('jetpack_sitemap_post_types', function($post_types) {
            $post_types[] = 'product';
            $post_types[] = 'event';
            return $post_types;
        });
        
        // Excluir posts específicos do sitemap
        add_filter('jetpack_sitemap_skip_post', function($skip, $post) {
            // Excluir posts com meta específico
            if (get_post_meta($post->ID, '_exclude_from_sitemap', true)) {
                return true;
            }
            
            return $skip;
        }, 10, 2);
    }
}

// Inicializar
Jetpack_API_Integration::configure_modules();
Jetpack_API_Integration::rest_api_auth();
Jetpack_API_Integration::stats_integration();
Jetpack_API_Integration::photon_integration();
Jetpack_API_Integration::protect_integration();
Jetpack_API_Integration::sitemaps_customization();
```

**Padrão 2: Automattic Coding Standards vs PSR-12**

```php
<?php
/**
 * Comparação: WordPress Coding Standards vs PSR-12
 * 
 * WordPress usa suas próprias coding standards baseadas em PEAR,
 * enquanto PSR-12 é o padrão PHP-FIG mais comum.
 * 
 * Diferenças principais:
 */

// ========== INDENTAÇÃO ==========
// WordPress: Tabs
class MyClass {
	public function method() {
		// código
	}
}

// PSR-12: Spaces (4 espaços)
class MyClass
{
    public function method()
    {
        // código
    }
}

// ========== CHAVES ==========
// WordPress: Chaves na mesma linha
if (condition) {
	// código
}

// PSR-12: Chaves em linha separada para classes/métodos
class MyClass
{
    public function method()
    {
        if (condition) {
            // código
        }
    }
}

// ========== NAMESPACES ==========
// WordPress: Sem namespaces (ou namespaces simples)
// functions.php
function my_function() {}

// PSR-12: Namespaces obrigatórios
namespace App\Services;

class MyService {}

// ========== QUANDO USAR QUAL ==========
/**
 * Use WordPress Coding Standards quando:
 * - Desenvolvendo plugins/temas WordPress
 * - Contribuindo para WordPress core
 * - Trabalhando em projetos WordPress puros
 * 
 * Use PSR-12 quando:
 * - Desenvolvendo bibliotecas PHP independentes
 * - Trabalhando em projetos que não são WordPress
 * - Integrando WordPress com frameworks externos
 * 
 * Híbrido (Recomendado para WordPress moderno):
 * - Use WordPress standards para código que interage diretamente com WordPress
 * - Use PSR-12 para código de domínio/bibliotecas isoladas
 * - Use namespaces e autoloading (Composer)
 */

// Exemplo híbrido
namespace MyPlugin\Domain;

// Código de domínio - PSR-12
class Product
{
    private string $name;
    
    public function __construct(string $name)
    {
        $this->name = $name;
    }
}

// Código WordPress - WordPress standards
class Product_Manager {
	public static function register() {
		add_action('init', [self::class, 'register_post_type']);
	}
	
	public static function register_post_type() {
		register_post_type('product', [
			'public' => true,
			'show_in_rest' => true,
		]);
	}
}
```

**Otimizar com Jetpack:**

```php
<?php
class JetpackIntegration {
    public static function setup_jetpack() {
        if (class_exists('Jetpack')) {
            // Ativar módulos
            Jetpack::activate_module('json-api');
            Jetpack::activate_module('protect');
            Jetpack::activate_module('photon');
        }
    }

    public static function use_jetpack_protection() {
        // Proteção contra brute force
        if (function_exists('jetpack_has_protect')) {
            // Jetpack cuida automaticamente
        }
    }

    public static function optimize_images_with_photon() {
        // Jetpack Photon otimiza imagens automaticamente
        add_filter('jetpack_photon_skip_image', function($skip, $image_src) {
            // Pular otimização de certas imagens
            return $skip;
        }, 10, 2);
    }
}
```

---

### 4. Akismet

#### Spam Protection com Akismet

**Configurar Akismet:**

```php
<?php
class AkismetIntegration {
    public static function setup_akismet() {
        // Automaticamente protege comentários
        // Precisa de chave de API do Akismet
    }

    public static function check_user_comment_spam($comment_id) {
        // Verificar se comentário é spam
        if (class_exists('Akismet')) {
            // Usar API do Akismet
        }
    }

    public static function mark_as_spam($comment_id) {
        // Marcar comentário como spam manualmente
        wp_spam_comment($comment_id);
    }
}
```

---

### 5. WP Rocket

#### Cache e Otimização com WP Rocket

**Configurar WP Rocket Programaticamente:**

```php
<?php
class WPRocketIntegration {
    public static function configure_wp_rocket() {
        if (!function_exists('rocket_clean_domain')) {
            return;
        }

        // Limpar cache completo
        rocket_clean_domain();

        // Limpar cache de página específica
        rocket_clean_post(get_the_ID());

        // Excluir URLs do cache
        // Feito via dashboard do WP Rocket
    }

    public static function optimize_database() {
        if (function_exists('rocket_do_dbcleanup')) {
            rocket_do_dbcleanup();
        }
    }
}
```

---

### 6. Other Popular Plugins

#### Integrar com Plugins Populares

**Integração com Elementor:**

```php
<?php
class ElementorIntegration {
    public static function register_custom_widget() {
        add_action('elementor/widgets/widgets_registered', function() {
            \Elementor\Plugin::instance()->widgets_manager->register(new CustomWidget());
        });
    }
}

class CustomWidget extends \Elementor\Widget_Base {
    public function get_name() {
        return 'custom_widget';
    }

    public function get_title() {
        return 'Widget Customizado';
    }

    public function get_icon() {
        return 'eicon-check';
    }

    public function register_controls() {
        $this->start_controls_section('content_section', [
            'label' => 'Conteúdo',
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('title', [
            'label' => 'Título',
            'type' => \Elementor\Controls_Manager::TEXT,
        ]);

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        echo '<h3>' . $settings['title'] . '</h3>';
    }
}
```

**Integração com Yoast SEO:**

```php
<?php
class YoastIntegration {
    public static function add_custom_seo_analysis() {
        if (function_exists('YoastSEO')) {
            // Customizar análise SEO
            // Usar hooks do Yoast
        }
    }

    public static function expose_yoast_to_api() {
        add_filter('rest_prepare_post', function($response) {
            $seo_score = get_post_meta(
                $response->data['id'],
                '_yoast_wpseo_content_score',
                true
            );

            if ($seo_score) {
                $response->data['seo_score'] = $seo_score;
            }

            return $response;
        });
    }
}
```

---

## Headless WordPress

### 1. REST API as Primary Interface

#### WordPress como Headless CMS

**Estrutura de API Headless:**

```php
<?php
// Arquivo: inc/Headless/HeadlessCMS.php

namespace MyPlugin\Headless;

class HeadlessCMS {
    public static function setup_headless() {
        // Registrar post types com suporte REST
        add_action('init', function() {
            register_post_type('article', [
                'label' => 'Artigos',
                'public' => true,
                'show_in_rest' => true,
                'rest_base' => 'articles',
                'supports' => ['title', 'editor', 'excerpt', 'thumbnail'],
            ]);
        });

        // Expor apenas dados necessários
        add_filter('rest_prepare_post', [__CLASS__, 'prepare_post_for_api'], 10, 2);
    }

    public static function prepare_post_for_api($response, $post) {
        $data = $response->get_data();

        // Estrutura customizada para frontend
        return new \WP_REST_Response([
            'id' => $data['id'],
            'title' => $data['title'],
            'content' => $data['content'],
            'excerpt' => $data['excerpt'],
            'featured_image' => get_the_post_thumbnail_url($post->ID),
            'author' => [
                'id' => $post->post_author,
                'name' => get_the_author_meta('display_name', $post->post_author),
            ],
            'meta' => [
                'published_at' => $post->post_date,
                'updated_at' => $post->post_modified,
            ],
        ]);
    }
}
```

**Endpoints Customizados para Headless:**

```php
<?php
class HeadlessEndpoints {
    public static function register_endpoints() {
        add_action('rest_api_init', function() {
            // Endpoint para homepage
            register_rest_route('api/v2', '/homepage', [
                'methods' => 'GET',
                'callback' => [__CLASS__, 'get_homepage_data'],
                'permission_callback' => '__return_true',
            ]);

            // Endpoint para blog
            register_rest_route('api/v2', '/blog', [
                'methods' => 'GET',
                'callback' => [__CLASS__, 'get_blog_data'],
                'permission_callback' => '__return_true',
            ]);

            // Endpoint para categoria
            register_rest_route('api/v2', '/categories/(?P<id>\d+)/posts', [
                'methods' => 'GET',
                'callback' => [__CLASS__, 'get_category_posts'],
                'args' => [
                    'id' => [
                        'validate_callback' => function($param) {
                            return is_numeric($param);
                        },
                    ],
                ],
            ]);
        });
    }

    public static function get_homepage_data() {
        return [
            'hero' => [
                'title' => get_bloginfo('name'),
                'description' => get_bloginfo('description'),
                'featured_image' => get_theme_mod('homepage_image'),
            ],
            'featured_posts' => get_posts(['meta_key' => '_is_featured', 'numberposts' => 5]),
            'categories' => get_categories(['hide_empty' => true]),
        ];
    }

    public static function get_blog_data() {
        return [
            'posts' => get_posts(['numberposts' => 10]),
            'categories' => get_categories(['hide_empty' => true]),
        ];
    }

    public static function get_category_posts($request) {
        $category_id = $request['id'];
        $page = $request->get_param('page') ?? 1;
        $per_page = $request->get_param('per_page') ?? 10;

        return get_posts([
            'cat' => $category_id,
            'paged' => $page,
            'posts_per_page' => $per_page,
        ]);
    }
}
```

---

### 2. Decoupled Frontend

#### Separar Backend e Frontend

**Estrutura de Projeto Decoupled:**

```
projeto/
├── backend/
│   └── wordpress/
│       └── wp-content/plugins/meu-plugin/
└── frontend/
    ├── React/
    ├── Vue/
    ├── Next.js/
    └── Nuxt/
```

**React Frontend com WordPress API:**

```javascript
// frontend/src/services/api.js

import axios from 'axios';

const API_BASE_URL = process.env.REACT_APP_API_URL || 'http://localhost/wp-json/api/v2';

const apiClient = axios.create({
    baseURL: API_BASE_URL,
    headers: {
        'Content-Type': 'application/json',
        'X-API-Key': process.env.REACT_APP_API_KEY,
    },
});

export const postsAPI = {
    getAll: (page = 1, perPage = 10) =>
        apiClient.get('/posts', {
            params: { page, per_page: perPage },
        }),

    getById: (id) =>
        apiClient.get(`/posts/${id}`),

    create: (data) =>
        apiClient.post('/posts', data),

    update: (id, data) =>
        apiClient.put(`/posts/${id}`, data),

    delete: (id) =>
        apiClient.delete(`/posts/${id}`),
};

export const categoriesAPI = {
    getAll: () =>
        apiClient.get('/categories'),

    getPostsByCategory: (categoryId) =>
        apiClient.get(`/categories/${categoryId}/posts`),
};

export default apiClient;
```

**React Hook Customizado:**

```javascript
// frontend/src/hooks/usePosts.js

import { useState, useEffect } from 'react';
import { postsAPI } from '../services/api';

export function usePosts(page = 1, perPage = 10) {
    const [posts, setPosts] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        const fetchPosts = async () => {
            try {
                setLoading(true);
                const response = await postsAPI.getAll(page, perPage);
                setPosts(response.data.data);
            } catch (err) {
                setError(err.message);
            } finally {
                setLoading(false);
            }
        };

        fetchPosts();
    }, [page, perPage]);

    return { posts, loading, error };
}
```

---

### 3. Static Site Generation

#### Gerar Site Estático de WordPress

**Next.js com WordPress:**

```javascript
// frontend/next.config.js

module.exports = {
    async rewrites() {
        return {
            beforeFiles: [
                {
                    source: '/api/:path*',
                    destination: `${process.env.WORDPRESS_API_URL}/:path*`,
                },
            ],
        };
    },
};
```

**Static Generation com getStaticProps:**

```javascript
// frontend/pages/posts/[slug].js

import { useRouter } from 'next/router';
import apiClient from '../../services/api';

export default function Post({ post }) {
    const router = useRouter();

    if (router.isFallback) {
        return <div>Carregando...</div>;
    }

    return (
        <article>
            <h1>{post.title}</h1>
            <img src={post.featured_image} alt={post.title} />
            <div dangerouslySetInnerHTML={{ __html: post.content }} />
        </article>
    );
}

export async function getStaticPaths() {
    const response = await apiClient.get('/posts?per_page=100');
    const paths = response.data.data.map(post => ({
        params: { slug: post.slug },
    }));

    return {
        paths,
        fallback: 'blocking',
    };
}

export async function getStaticProps({ params }) {
    const response = await apiClient.get(`/posts?slug=${params.slug}`);
    const post = response.data.data[0];

    return {
        props: { post },
        revalidate: 3600, // Revalidar a cada hora
    };
}
```

---

### 4. Jamstack Architecture

#### Implementar Arquitetura Jamstack

**Fluxo CI/CD para Jamstack:**

```yaml
# .github/workflows/jamstack-deploy.yml

name: Build and Deploy Jamstack

on:
  push:
    branches: [main]
  webhook:
    - wordpress-content-update

jobs:
  build:
    runs-on: ubuntu-latest

    steps:
      - uses: actions/checkout@v3

      - name: Setup Node.js
        uses: actions/setup-node@v3
        with:
          node-version: '18'

      - name: Install dependencies
        run: npm ci

      - name: Build static site
        run: npm run build
        env:
          WORDPRESS_API_URL: ${{ secrets.WORDPRESS_API_URL }}

      - name: Deploy to Netlify
        uses: nwtgck/actions-netlify@v2.0
        with:
          publish-dir: './out'
          production-branch: main
          netlify-token: ${{ secrets.NETLIFY_TOKEN }}
```

**Webhook do WordPress para Trigger Build:**

```php
<?php
// Arquivo: inc/Jamstack/BuildTrigger.php

namespace MyPlugin\Jamstack;

class BuildTrigger {
    public static function setup_hooks() {
        // Trigger build quando post é atualizado
        add_action('publish_post', [__CLASS__, 'trigger_build']);
        add_action('post_updated', [__CLASS__, 'trigger_build']);
    }

    public static function trigger_build($post_id) {
        $webhook_url = get_option('jamstack_webhook_url');

        if (!$webhook_url) {
            return;
        }

        wp_remote_post($webhook_url, [
            'method' => 'POST',
            'timeout' => 5,
            'blocking' => false,
            'body' => json_encode([
                'event' => 'post_updated',
                'post_id' => $post_id,
                'timestamp' => current_time('mysql'),
            ]),
            'headers' => ['Content-Type' => 'application/json'],
        ]);
    }
}
```

---

## Community e Best Practices

### 1. Contributing to WordPress

#### Contribuir para o Núcleo do WordPress

**Setup de Desenvolvimento:**

```bash
# Clonar repositório
git clone https://github.com/WordPress/wordpress-develop.git
cd wordpress-develop

# Instalar dependências
npm install
composer install

# Preparar ambiente
npm run build

# Ou para desenvolvimento contínuo
npm run dev
```

**Criar Patch:**

```bash
# Criar branch
git checkout -b fix/meu-fix

# Fazer alterações
# ...

# Commit com mensagem descritiva
git commit -m "Fix: Descrição do problema e solução"

# Push para fork
git push origin fix/meu-fix
```

**Submeter para Trac:**

1. Criar conta em trac.wordpress.org
2. Abrir ticket descrevendo o problema
3. Anexar patch
4. Participar da discussão

**Testes Automatizados:**

```bash
# Rodar testes
npm test

# Testes específicos
npm test -- --testNamePattern="meu teste"

# Com cobertura
npm test -- --coverage
```

---

### 2. Plugin Repository Standards

#### Publicar Plugin no Repositório Oficial

**Estrutura Recomendada:**

```
meu-plugin/
├── meu-plugin.php (Main file)
├── includes/
│   ├── class-main.php
│   ├── class-admin.php
│   └── class-frontend.php
├── assets/
│   ├── js/
│   ├── css/
│   └── images/
├── languages/
│   └── meu-plugin.pot
├── readme.txt
├── LICENSE
└── .gitignore
```

**readme.txt Padrão:**

```
=== Meu Plugin ===
Contributors: seu_usuario
Tags: feature, wordpress
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 5.0
Requires PHP: 7.4
Tested up to: 6.4
Stable tag: 1.0.0

Um breve descritivo do plugin.

== Description ==

Descrição detalhada do que o plugin faz.

== Installation ==

1. Fazer upload do diretório `meu-plugin` para `/wp-content/plugins/`
2. Ativar o plugin através do menu 'Plugins' no WordPress
3. Configurar conforme necessário

== Frequently Asked Questions ==

= Como funciona? =

Explicação...

== Screenshots ==

1. Descrição da screenshot

== Changelog ==

= 1.0.0 =
* Versão inicial

== Security ==

- Usar `wp_verify_nonce()` para requisições AJAX
- Validar e sanitizar todas as entradas
- Usar prepared statements para queries
```

**Submeter para Repositório:**

```bash
# SVN setup (WordPress usa SVN)
svn co https://plugins.svn.wordpress.org/meu-plugin/ meu-plugin-svn
cd meu-plugin-svn

# Copiar arquivos
cp -r ../meu-plugin/* trunk/

# Commit
svn add trunk/*
svn commit -m "Initial commit"

# Tag de release
svn cp trunk/ tags/1.0.0
svn commit -m "Tag version 1.0.0"
```

---

### 3. Code Review Practices

#### Melhores Práticas de Revisão de Código

**Checklist de Review:**

```markdown
## Code Review Checklist

### Funcionalidade
- [ ] Código faz o que é proposto?
- [ ] Todos os casos de uso foram considerados?
- [ ] Edge cases foram tratados?
- [ ] Testes cobrem o novo código?

### Qualidade
- [ ] Código segue padrões do projeto?
- [ ] Código é legível e bem documentado?
- [ ] Não há duplicação de código?
- [ ] Performance é aceitável?

### Segurança
- [ ] Entradas são validadas e sanitizadas?
- [ ] Escaping é feito corretamente?
- [ ] Permissões são verificadas?
- [ ] Não há vulnerabilidades conhecidas?

### Testing
- [ ] Testes unitários passam?
- [ ] Testes de integração passam?
- [ ] Coverage é adequado?

### Documentation
- [ ] Código é comentado?
- [ ] README foi atualizado?
- [ ] Changelog foi atualizado?
```

**Ferramenta de Linting:**

```bash
# PHP CodeSniffer com padrão WordPress
composer require --dev squizlabs/php_codesniffer
./vendor/bin/phpcs --standard=WordPress plugin/

# PHPStan para análise estática
composer require --dev phpstan/phpstan
phpstan analyse plugin/

# PHPCBF para auto-correção
./vendor/bin/phpcbf --standard=WordPress plugin/
```

---

### 4. Documentation Standards

#### Documentação de Código

**PHP DocBlocks:**

```php
<?php
/**
 * Classe responsável por gerenciar posts.
 *
 * Fornece funcionalidades para CRUD de posts e manipulação
 * de metadados associados.
 *
 * @package MyPlugin\Posts
 * @since 1.0.0
 */
class PostManager {
    /**
     * Recupera um post pelo ID.
     *
     * @since 1.0.0
     *
     * @param int $post_id ID do post a recuperar.
     * @return \WP_Post|null Post encontrado ou null.
     *
     * @throws \Exception Se post_id não é válido.
     *
     * @example
     *     $post = PostManager::get_post(123);
     *     echo $post->post_title;
     */
    public static function get_post($post_id) {
        if (!is_numeric($post_id)) {
            throw new \Exception('ID inválido');
        }

        return get_post($post_id);
    }
}
```

**README.md Comprehensive:**

```markdown
# Meu Plugin

Descrição do plugin.

## Instalação

## Uso

### Exemplos Básicos

```php
// Exemplo 1
$resultado = meu_plugin_funcao();
```

## API Reference

### Functions

#### `meu_plugin_fazer_algo()`

Descrição.

**Parâmetros:**

- `$param1` (string) - Descrição
- `$param2` (array) - Descrição

**Retorna:**

(boolean) True se sucesso, false caso contrário.

**Exemplo:**

```php
meu_plugin_fazer_algo('valor', ['option' => 'value']);
```

## Contribuindo

## Licença

GPL v2 ou posterior
```

---

### 5. Community Guidelines

#### Melhores Práticas na Comunidade

**Código de Conduta:**

```markdown
# Código de Conduta

## Nossa Missão

Criar um espaço seguro e inclusivo para desenvolvedores.

## Nossos Valores

- **Respeito**: Tratar todos com cortesia
- **Inclusão**: Acolher pessoas de todas as origens
- **Transparência**: Comunicação clara e honesta
- **Colaboração**: Trabalhar juntos

## Comportamentos Esperados

- Ser respeitoso em todas as interações
- Acolher feedback construtivo
- Focar no que é melhor para a comunidade
- Ser paciente com novos membros

## Comportamentos Inaceitáveis

- Discurso de ódio ou discriminação
- Assédio ou bullying
- Spam ou conteúdo indesejado
- Comportamento prejudicial à comunidade

## Denúncias

Denúncias devem ser enviadas para: conduct@example.com
```

**Guia de Discussão:**

- Ser educado e respeitoso
- Pesquisar antes de perguntar
- Fornecer contexto e detalhes
- Agradecer quem ajuda
- Compartilhar soluções encontradas

---

## 📚 Resumo e Próximos Passos

Este guia cobriu tópicos avançados em WordPress, desde otimizações de API até integração com o ecossistema e contribuição à comunidade.

### Recomendações:

1. **Implementar gradualmente**: Não tente aplicar tudo de uma vez
2. **Medir impacto**: Use ferramentas como Lighthouse e WP CLI para medir melhorias
3. **Documentar**: Mantenha documentação atualizada
4. **Testar**: Sempre teste em staging antes de produção
5. **Comunidade**: Contribua e aprenda com a comunidade

### Recursos Úteis:

- [WordPress.org Documentation](https://developer.wordpress.org)
- [WordPress.org Plugin Development](https://developer.wordpress.org/plugins)
- [REST API Handbook](https://developer.wordpress.org/rest-api)
- [WP CLI Documentation](https://developer.wordpress.org/cli)
- [WordPress Slack Community](https://wordpress.slack.com)

---

**Navegação:** [Índice](./000-WordPress-Indice-Topicos.md) | [← Fase 15](./016-WordPress-Fase-15-Jobs-Assincronos-Background.md) | [Fase 17 →](./017-WordPress-Fase-17-Testes-Em-Toda-Fase.md)

**Último atualizado:** Janeiro 2026
