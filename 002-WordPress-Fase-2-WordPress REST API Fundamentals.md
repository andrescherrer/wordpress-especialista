# 🔌 FASE 2: WordPress REST API Fundamentals

**Versão:** 1.0  
**Data:** Janeiro 2026  
**Nível:** Especialista em PHP  
**Objetivo:** Dominar a criação e gerenciamento de APIs REST no WordPress

---

**Navegação:** [📚 Índice](000-WordPress-Topicos-Index.md) | [← Fase 1](001-WordPress-Fase-1-Fundamentals%20of%20WordPress%20Core.md) | [Fase 3 →](003-WordPress-Fase-3-REST-API-Advanced.md)

---

## 📑 Índice

1. [Conceitos Básicos da REST API](#conceitos-básicos-da-rest-api)
2. [Registrar Rotas Customizadas](#registrar-rotas-customizadas)
3. [REST Controllers (OOP)](#rest-controllers-oop)
4. [Validação e Sanitização](#validação-e-sanitização)
5. [REST Authentication](#rest-authentication)
6. [REST Permissions](#rest-permissions)
7. [REST Response e Error Handling](#rest-response-e-error-handling)
8. [Documentação e Schema](#documentação-e-schema)
9. [REST Filters Avançados](#rest-filters-avançados)

---

## 📚 Conceitos Básicos da REST API

### 2.1 O que é REST?

**REST** = Representational State Transfer

REST é um padrão arquitetural para APIs baseado em princípios:

- **Stateless**: Cada requisição contém todas as informações necessárias
- **Resource-oriented**: Recursos são identificados por URIs
- **HTTP methods**: GET (ler), POST (criar), PUT/PATCH (atualizar), DELETE (deletar)
- **Representation**: Dados em JSON, XML, etc
- **Hypermedia**: Links entre recursos (HATEOAS)

### 2.2 WordPress REST API Fundamentos

A REST API foi introduzida na **versão 4.7** do WordPress (Dezembro 2016).

```
URL Base: https://seusite.com/wp-json/
```

**Namespace**: `wp/v2/` (versão 2 é a atual)

**Exemplo de Endpoints Nativos:**
```
GET    /wp-json/wp/v2/posts              - Listar posts
POST   /wp-json/wp/v2/posts              - Criar post
GET    /wp-json/wp/v2/posts/{id}         - Obter post específico
PUT    /wp-json/wp/v2/posts/{id}         - Atualizar post
PATCH  /wp-json/wp/v2/posts/{id}         - Atualizar parcialmente
DELETE /wp-json/wp/v2/posts/{id}         - Deletar post

GET    /wp-json/wp/v2/pages              - Listar páginas
GET    /wp-json/wp/v2/categories         - Listar categorias
GET    /wp-json/wp/v2/tags               - Listar tags
GET    /wp-json/wp/v2/users              - Listar usuários
GET    /wp-json/wp/v2/comments           - Listar comentários
```

### 2.3 Verbos HTTP e Status Codes

#### **Verbos HTTP (Methods)**

```
GET     - Recuperar recurso (seguro, idempotente)
POST    - Criar novo recurso
PUT     - Atualizar recurso completamente (idempotente)
PATCH   - Atualizar recurso parcialmente
DELETE  - Deletar recurso (idempotente)
HEAD    - Como GET mas sem body
OPTIONS - Descrever opções de comunicação
```

#### **Status Codes Padrão**

```
2xx - Sucesso
  200 OK                    - Requisição bem-sucedida (GET, PUT, PATCH)
  201 Created              - Recurso criado (POST)
  204 No Content           - Sucesso sem retorno (DELETE)

3xx - Redirecionamento
  301 Moved Permanently    - Recurso movido permanentemente
  302 Found                - Redirecionamento temporário
  304 Not Modified         - Recurso não modificado (cache)

4xx - Erro do Cliente
  400 Bad Request          - Requisição malformada
  401 Unauthorized         - Não autenticado
  403 Forbidden            - Autenticado mas sem permissão
  404 Not Found            - Recurso não existe
  409 Conflict             - Conflito (ex: duplicado)
  422 Unprocessable Entity - Dados inválidos

5xx - Erro do Servidor
  500 Internal Server Error - Erro geral do servidor
  503 Service Unavailable   - Serviço indisponível
```

### 2.4 JSON Request/Response

**Exemplo de Request:**
```bash
curl -X POST https://seusite.com/wp-json/wp/v2/posts \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Novo Post",
    "content": "Conteúdo do post",
    "status": "publish"
  }'
```

**Exemplo de Response:**
```json
{
  "id": 123,
  "date": "2026-01-29T10:30:00",
  "date_gmt": "2026-01-29T13:30:00",
  "guid": {
    "rendered": "https://seusite.com/?p=123"
  },
  "modified": "2026-01-29T10:30:00",
  "modified_gmt": "2026-01-29T13:30:00",
  "slug": "novo-post",
  "status": "publish",
  "type": "post",
  "link": "https://seusite.com/novo-post/",
  "title": {
    "rendered": "Novo Post"
  },
  "content": {
    "rendered": "<p>Conteúdo do post</p>",
    "protected": false
  },
  "author": 1,
  "featured_media": 456,
  "comment_status": "open",
  "ping_status": "open"
}
```

---

## 🛣️ Registrar Rotas Customizadas

### 2.5 Função `register_rest_route()`

```php
<?php
// Hook para registrar rotas (OBRIGATÓRIO!)
add_action('rest_api_init', 'registrar_rotas_meu_plugin');

function registrar_rotas_meu_plugin() {
    // Estrutura básica
    register_rest_route(
        $namespace,        // 'meu-plugin/v1'
        $route,            // '/produtos'
        $args              // Array com configurações
    );
}
?>
```

### 2.6 Exemplo Prático Completo

```php
<?php
/**
 * Registrar rotas da API customizada
 */
add_action('rest_api_init', 'meu_plugin_registrar_rotas');

function meu_plugin_registrar_rotas() {
    // ========== GET /wp-json/meu-plugin/v1/produtos ==========
    register_rest_route(
        'meu-plugin/v1',           // Namespace
        '/produtos',               // Rota
        [
            'methods'              => WP_REST_Server::READABLE,  // GET
            'callback'             => 'listar_produtos',         // Função callback
            'permission_callback'  => '__return_true',           // Público
            
            // Argumentos/parâmetros aceitos
            'args'                 => [
                'per_page' => [
                    'type'              => 'integer',
                    'default'           => 10,
                    'minimum'           => 1,
                    'maximum'           => 100,
                    'sanitize_callback' => 'absint',
                    'description'       => 'Itens por página'
                ],
                'paged' => [
                    'type'              => 'integer',
                    'default'           => 1,
                    'sanitize_callback' => 'absint',
                    'description'       => 'Número da página'
                ],
                'categoria' => [
                    'type'              => 'string',
                    'required'          => false,
                    'sanitize_callback' => 'sanitize_text_field',
                    'description'       => 'Filtrar por categoria'
                ],
                'orderby' => [
                    'type'              => 'string',
                    'default'           => 'date',
                    'enum'              => ['date', 'title', 'price'],
                    'sanitize_callback' => 'sanitize_text_field',
                    'description'       => 'Campo para ordenação'
                ],
                'order' => [
                    'type'              => 'string',
                    'default'           => 'DESC',
                    'enum'              => ['ASC', 'DESC'],
                    'sanitize_callback' => 'sanitize_text_field',
                    'description'       => 'Ordem (ASC/DESC)'
                ]
            ]
        ]
    );

    // ========== POST /wp-json/meu-plugin/v1/produtos ==========
    register_rest_route(
        'meu-plugin/v1',
        '/produtos',
        [
            'methods'              => WP_REST_Server::CREATABLE,  // POST
            'callback'             => 'criar_produto',
            'permission_callback'  => 'verificar_permissao_criar',
            
            'args'                 => [
                'nome' => [
                    'required'          => true,
                    'type'              => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                    'validate_callback' => function($value) {
                        return strlen($value) >= 3;
                    },
                    'description'       => 'Nome do produto'
                ],
                'descricao' => [
                    'required'          => false,
                    'type'              => 'string',
                    'sanitize_callback' => 'sanitize_textarea_field',
                    'description'       => 'Descrição'
                ],
                'preco' => [
                    'required'          => true,
                    'type'              => 'number',
                    'minimum'           => 0,
                    'sanitize_callback' => 'floatval',
                    'description'       => 'Preço'
                ],
                'categoria' => [
                    'required'          => false,
                    'type'              => 'string',
                    'enum'              => ['eletrônicos', 'roupas', 'alimentos'],
                    'sanitize_callback' => 'sanitize_text_field',
                    'description'       => 'Categoria'
                ]
            ]
        ]
    );

    // ========== GET /wp-json/meu-plugin/v1/produtos/{id} ==========
    register_rest_route(
        'meu-plugin/v1',
        '/produtos/(?P<id>\d+)',   // Regex para ID numérico
        [
            'methods'              => WP_REST_Server::READABLE,
            'callback'             => 'obter_produto',
            'permission_callback'  => '__return_true',
            
            'args'                 => [
                'id' => [
                    'description'       => 'ID único do produto',
                    'type'              => 'integer',
                    'required'          => true,
                    'validate_callback' => function($id) {
                        return is_numeric($id) && $id > 0;
                    }
                ]
            ]
        ]
    );

    // ========== PUT /wp-json/meu-plugin/v1/produtos/{id} ==========
    register_rest_route(
        'meu-plugin/v1',
        '/produtos/(?P<id>\d+)',
        [
            'methods'              => WP_REST_Server::EDITABLE,  // PUT, PATCH
            'callback'             => 'atualizar_produto',
            'permission_callback'  => 'verificar_permissao_editar',
            
            'args'                 => [
                'id' => [
                    'required'          => true,
                    'type'              => 'integer',
                    'validate_callback' => function($id) {
                        return is_numeric($id) && $id > 0;
                    }
                ],
                'nome' => [
                    'type'              => 'string',
                    'sanitize_callback' => 'sanitize_text_field'
                ],
                'preco' => [
                    'type'              => 'number',
                    'minimum'           => 0,
                    'sanitize_callback' => 'floatval'
                ]
            ]
        ]
    );

    // ========== DELETE /wp-json/meu-plugin/v1/produtos/{id} ==========
    register_rest_route(
        'meu-plugin/v1',
        '/produtos/(?P<id>\d+)',
        [
            'methods'              => WP_REST_Server::DELETABLE,
            'callback'             => 'deletar_produto',
            'permission_callback'  => 'verificar_permissao_deletar',
            
            'args'                 => [
                'id' => [
                    'required'          => true,
                    'type'              => 'integer'
                ]
            ]
        ]
    );
}

// ==================== CALLBACKS ====================

/**
 * Listar produtos
 */
function listar_produtos(WP_REST_Request $request) {
    global $wpdb;
    
    // Obter parâmetros
    $per_page = $request->get_param('per_page') ?? 10;
    $paged = $request->get_param('paged') ?? 1;
    $categoria = $request->get_param('categoria');
    $orderby = $request->get_param('orderby') ?? 'date';
    $order = $request->get_param('order') ?? 'DESC';
    
    // Calcular offset
    $offset = ($paged - 1) * $per_page;
    
    // Construir query
    $query = "SELECT * FROM {$wpdb->prefix}meu_plugin_produtos";
    $where = [];
    $params = [];
    
    if ($categoria) {
        $where[] = "categoria = %s";
        $params[] = $categoria;
    }
    
    if ($where) {
        $query .= " WHERE " . implode(" AND ", $where);
    }
    
    // Validar orderby
    $allowed_orderby = ['date', 'title', 'price'];
    if (!in_array($orderby, $allowed_orderby)) {
        $orderby = 'date';
    }
    
    // Validar order
    $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';
    
    $query .= " ORDER BY {$orderby} {$order}";
    $query .= " LIMIT %d OFFSET %d";
    $params[] = $per_page;
    $params[] = $offset;
    
    // Executar query com prepared statement
    $prepared_query = $wpdb->prepare($query, ...$params);
    $produtos = $wpdb->get_results($prepared_query);
    
    // Contar total
    $count_query = "SELECT COUNT(*) FROM {$wpdb->prefix}meu_plugin_produtos";
    if ($where) {
        $count_query .= " WHERE " . implode(" AND ", $where);
        $count_prepared = $wpdb->prepare($count_query, ...array_slice($params, 0, -2));
    } else {
        $count_prepared = $count_query;
    }
    $total = $wpdb->get_var($count_prepared);
    
    // Preparar response
    $response = rest_ensure_response($produtos);
    
    // Adicionar headers de paginação
    $response->header('X-WP-Total', $total);
    $response->header('X-WP-TotalPages', ceil($total / $per_page));
    
    return $response;
}

/**
 * Criar produto
 */
function criar_produto(WP_REST_Request $request) {
    global $wpdb;
    
    $nome = $request->get_param('nome');
    $descricao = $request->get_param('descricao') ?? '';
    $preco = $request->get_param('preco');
    $categoria = $request->get_param('categoria') ?? 'geral';
    
    // Inserir no banco
    $resultado = $wpdb->insert(
        "{$wpdb->prefix}meu_plugin_produtos",
        [
            'nome' => $nome,
            'descricao' => $descricao,
            'preco' => $preco,
            'categoria' => $categoria,
            'data_criacao' => current_time('mysql')
        ],
        ['%s', '%s', '%f', '%s', '%s']
    );
    
    if ($resultado === false) {
        return new WP_Error(
            'db_insert_error',
            'Erro ao inserir produto no banco de dados',
            ['status' => 500]
        );
    }
    
    $id = $wpdb->insert_id;
    
    // Retornar produto criado
    $produto = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}meu_plugin_produtos WHERE id = %d",
        $id
    ));
    
    return new WP_REST_Response($produto, 201);  // 201 Created
}

/**
 * Obter produto único
 */
function obter_produto(WP_REST_Request $request) {
    global $wpdb;
    
    $id = $request->get_param('id');
    
    $produto = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}meu_plugin_produtos WHERE id = %d",
        $id
    ));
    
    if (!$produto) {
        return new WP_Error(
            'product_not_found',
            'Produto não encontrado',
            ['status' => 404]
        );
    }
    
    return rest_ensure_response($produto);
}

/**
 * Atualizar produto
 */
function atualizar_produto(WP_REST_Request $request) {
    global $wpdb;
    
    $id = $request->get_param('id');
    
    // Verificar se existe
    $existe = $wpdb->get_row($wpdb->prepare(
        "SELECT id FROM {$wpdb->prefix}meu_plugin_produtos WHERE id = %d",
        $id
    ));
    
    if (!$existe) {
        return new WP_Error(
            'product_not_found',
            'Produto não encontrado',
            ['status' => 404]
        );
    }
    
    // Preparar dados de atualização
    $updates = [];
    $types = [];
    
    if ($request->has_param('nome')) {
        $updates['nome'] = $request->get_param('nome');
        $types[] = '%s';
    }
    
    if ($request->has_param('preco')) {
        $updates['preco'] = $request->get_param('preco');
        $types[] = '%f';
    }
    
    if (empty($updates)) {
        return new WP_Error(
            'no_updates',
            'Nenhum campo para atualizar',
            ['status' => 400]
        );
    }
    
    // Atualizar
    $resultado = $wpdb->update(
        "{$wpdb->prefix}meu_plugin_produtos",
        $updates,
        ['id' => $id],
        $types,
        ['%d']
    );
    
    if ($resultado === false) {
        return new WP_Error(
            'update_failed',
            'Erro ao atualizar produto',
            ['status' => 500]
        );
    }
    
    // Retornar produto atualizado
    $produto = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}meu_plugin_produtos WHERE id = %d",
        $id
    ));
    
    return rest_ensure_response($produto);
}

/**
 * Deletar produto
 */
function deletar_produto(WP_REST_Request $request) {
    global $wpdb;
    
    $id = $request->get_param('id');
    
    // Verificar se existe
    $produto = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}meu_plugin_produtos WHERE id = %d",
        $id
    ));
    
    if (!$produto) {
        return new WP_Error(
            'product_not_found',
            'Produto não encontrado',
            ['status' => 404]
        );
    }
    
    // Deletar
    $resultado = $wpdb->delete(
        "{$wpdb->prefix}meu_plugin_produtos",
        ['id' => $id],
        ['%d']
    );
    
    if ($resultado === false) {
        return new WP_Error(
            'delete_failed',
            'Erro ao deletar produto',
            ['status' => 500]
        );
    }
    
    return new WP_REST_Response(['mensagem' => 'Produto deletado com sucesso'], 200);
}

// ==================== PERMISSION CALLBACKS ====================

function verificar_permissao_criar() {
    return current_user_can('publish_posts');
}

function verificar_permissao_editar(WP_REST_Request $request) {
    return current_user_can('edit_posts');
}

function verificar_permissao_deletar(WP_REST_Request $request) {
    return current_user_can('delete_posts');
}
?>
```

---

## 🏗️ REST Controllers (OOP)

### 2.7 Classe Base WP_REST_Controller

Para APIs mais robustas e profissionais, use o padrão **Controller** estendendo `WP_REST_Controller`:

```php
<?php
/**
 * REST Controller base para APIs customizadas
 */
namespace MeuPlugin\API;

abstract class Base_REST_Controller extends \WP_REST_Controller {
    
    protected $namespace = 'meu-plugin/v1';
    protected $rest_base = '';
    
    /**
     * Registrar rotas
     */
    public function register_routes() {
        // GET /items
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            [
                [
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => [$this, 'get_items'],
                    'permission_callback' => [$this, 'get_items_permissions_check'],
                    'args'                => $this->get_collection_params()
                ],
                [
                    'methods'             => \WP_REST_Server::CREATABLE,
                    'callback'            => [$this, 'create_item'],
                    'permission_callback' => [$this, 'create_item_permissions_check'],
                    'args'                => $this->get_endpoint_args_for_item_schema(\WP_REST_Server::CREATABLE)
                ]
            ]
        );
        
        // GET /items/{id}
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/(?P<id>\d+)',
            [
                [
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => [$this, 'get_item'],
                    'permission_callback' => [$this, 'get_item_permissions_check'],
                    'args'                => [
                        'id' => [
                            'required'    => true,
                            'type'        => 'integer',
                            'description' => 'ID único do item'
                        ]
                    ]
                ],
                [
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => [$this, 'update_item'],
                    'permission_callback' => [$this, 'update_item_permissions_check'],
                    'args'                => $this->get_endpoint_args_for_item_schema(\WP_REST_Server::EDITABLE)
                ],
                [
                    'methods'             => \WP_REST_Server::DELETABLE,
                    'callback'            => [$this, 'delete_item'],
                    'permission_callback' => [$this, 'delete_item_permissions_check'],
                    'args'                => [
                        'id' => [
                            'required' => true,
                            'type'     => 'integer'
                        ]
                    ]
                ]
            ]
        );
    }
    
    /**
     * GET /items
     */
    public function get_items($request) {
        $items = $this->get_items_from_database($request);
        
        $response = rest_ensure_response($items);
        $response->header('X-WP-Total', count($items));
        
        return $response;
    }
    
    /**
     * POST /items
     */
    public function create_item($request) {
        $item = $this->create_item_in_database($request);
        
        return new \WP_REST_Response($item, 201);
    }
    
    /**
     * GET /items/{id}
     */
    public function get_item($request) {
        $id = $request->get_param('id');
        $item = $this->get_item_from_database($id);
        
        if (!$item) {
            return new \WP_Error(
                'rest_not_found',
                'Item não encontrado',
                ['status' => 404]
            );
        }
        
        return rest_ensure_response($item);
    }
    
    /**
     * PUT/PATCH /items/{id}
     */
    public function update_item($request) {
        $id = $request->get_param('id');
        
        if (!$this->get_item_from_database($id)) {
            return new \WP_Error(
                'rest_not_found',
                'Item não encontrado',
                ['status' => 404]
            );
        }
        
        $item = $this->update_item_in_database($id, $request);
        
        return rest_ensure_response($item);
    }
    
    /**
     * DELETE /items/{id}
     */
    public function delete_item($request) {
        $id = $request->get_param('id');
        
        if (!$this->get_item_from_database($id)) {
            return new \WP_Error(
                'rest_not_found',
                'Item não encontrado',
                ['status' => 404]
            );
        }
        
        $this->delete_item_from_database($id);
        
        return new \WP_REST_Response(['deleted' => true], 200);
    }
    
    // ==================== PERMISSION METHODS ====================
    
    public function get_items_permissions_check($request) {
        return true;  // Público
    }
    
    public function create_item_permissions_check($request) {
        return current_user_can('publish_posts');
    }
    
    public function get_item_permissions_check($request) {
        return true;  // Público
    }
    
    public function update_item_permissions_check($request) {
        return current_user_can('edit_posts');
    }
    
    public function delete_item_permissions_check($request) {
        return current_user_can('delete_posts');
    }
    
    // ==================== DATABASE METHODS (Abstract) ====================
    
    abstract protected function get_items_from_database($request);
    abstract protected function create_item_in_database($request);
    abstract protected function get_item_from_database($id);
    abstract protected function update_item_in_database($id, $request);
    abstract protected function delete_item_from_database($id);
}

/**
 * Exemplo de Controller implementado
 */
class Product_Controller extends Base_REST_Controller {
    
    protected $rest_base = 'products';
    
    public function __construct() {
        add_action('rest_api_init', [$this, 'register_routes']);
    }
    
    protected function get_items_from_database($request) {
        global $wpdb;
        
        $per_page = $request->get_param('per_page') ?? 10;
        $paged = $request->get_param('paged') ?? 1;
        $offset = ($paged - 1) * $per_page;
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}meu_plugin_products 
             LIMIT %d OFFSET %d",
            $per_page,
            $offset
        ));
    }
    
    protected function create_item_in_database($request) {
        global $wpdb;
        
        $wpdb->insert(
            "{$wpdb->prefix}meu_plugin_products",
            [
                'name' => $request->get_param('name'),
                'price' => $request->get_param('price'),
                'created_at' => current_time('mysql')
            ],
            ['%s', '%f', '%s']
        );
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}meu_plugin_products WHERE id = %d",
            $wpdb->insert_id
        ));
    }
    
    protected function get_item_from_database($id) {
        global $wpdb;
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}meu_plugin_products WHERE id = %d",
            $id
        ));
    }
    
    protected function update_item_in_database($id, $request) {
        global $wpdb;
        
        $updates = [];
        
        if ($request->has_param('name')) {
            $updates['name'] = $request->get_param('name');
        }
        if ($request->has_param('price')) {
            $updates['price'] = $request->get_param('price');
        }
        
        if (!empty($updates)) {
            $wpdb->update(
                "{$wpdb->prefix}meu_plugin_products",
                $updates,
                ['id' => $id],
                null,
                ['%d']
            );
        }
        
        return $this->get_item_from_database($id);
    }
    
    protected function delete_item_from_database($id) {
        global $wpdb;
        
        $wpdb->delete(
            "{$wpdb->prefix}meu_plugin_products",
            ['id' => $id],
            ['%d']
        );
    }
    
    public function get_collection_params() {
        return [
            'per_page' => [
                'type'              => 'integer',
                'default'           => 10,
                'minimum'           => 1,
                'maximum'           => 100,
                'sanitize_callback' => 'absint'
            ],
            'paged' => [
                'type'              => 'integer',
                'default'           => 1,
                'sanitize_callback' => 'absint'
            ]
        ];
    }
}

// Instanciar controller
new Product_Controller();
?>
```

---

## ✅ Validação e Sanitização

### 2.8 Conceitos Essenciais

- **Validação**: Verifica se o dado está no formato correto
- **Sanitização**: Remove/escapa dados perigosos
- **Escaping**: Prepara dados para output seguro

```php
<?php
// ========== VALIDAÇÃO ==========

$args = [
    'email' => [
        'required'          => true,
        'type'              => 'string',
        'format'            => 'email',
        'validate_callback' => function($value) {
            return is_email($value) || new WP_Error(
                'invalid_email',
                'Email inválido'
            );
        }
    ],
    'idade' => [
        'type'              => 'integer',
        'minimum'           => 0,
        'maximum'           => 150,
        'validate_callback' => function($value) {
            if ($value < 18) {
                return new WP_Error(
                    'underage',
                    'Deve ter no mínimo 18 anos'
                );
            }
            return true;
        }
    ],
    'categoria' => [
        'type'              => 'string',
        'enum'              => ['categoria1', 'categoria2', 'categoria3'],
        'validate_callback' => function($value) {
            $allowed = ['categoria1', 'categoria2', 'categoria3'];
            if (!in_array($value, $allowed)) {
                return new WP_Error(
                    'invalid_category',
                    'Categoria inválida'
                );
            }
            return true;
        }
    ],
    'data' => [
        'type'              => 'string',
        'format'            => 'date',
        'validate_callback' => function($value) {
            $timestamp = strtotime($value);
            if ($timestamp === false) {
                return new WP_Error(
                    'invalid_date',
                    'Data inválida'
                );
            }
            return true;
        }
    ],
    'telefone' => [
        'type'              => 'string',
        'pattern'           => '^\d{10,11}$',  // Regex
        'validate_callback' => function($value) {
            if (!preg_match('/^\d{10,11}$/', $value)) {
                return new WP_Error(
                    'invalid_phone',
                    'Telefone deve ter 10 ou 11 dígitos'
                );
            }
            return true;
        }
    ]
];

// ========== SANITIZAÇÃO ==========

$args = [
    'nome' => [
        'required'          => true,
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_text_field'
    ],
    'email' => [
        'required'          => true,
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_email'
    ],
    'url' => [
        'type'              => 'string',
        'sanitize_callback' => 'esc_url_raw'
    ],
    'conteudo' => [
        'type'              => 'string',
        'sanitize_callback' => 'wp_kses_post'  // Permite HTML permitido
    ],
    'numero' => [
        'type'              => 'integer',
        'sanitize_callback' => 'absint'
    ],
    'preco' => [
        'type'              => 'number',
        'sanitize_callback' => 'floatval'
    ],
    'lista' => [
        'type'              => 'array',
        'items'             => ['type' => 'string'],
        'sanitize_callback' => function($values) {
            return array_map('sanitize_text_field', (array) $values);
        }
    ]
];

// ========== VALIDAÇÃO CUSTOMIZADA ==========

function validar_cpf($cpf) {
    // Remove caracteres não numéricos
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    
    // Verifica se tem 11 dígitos
    if (strlen($cpf) != 11) {
        return false;
    }
    
    // Implementar validação do CPF
    // ...
    
    return true;
}

$args = [
    'cpf' => [
        'required'          => true,
        'type'              => 'string',
        'sanitize_callback' => function($value) {
            return preg_replace('/[^0-9]/', '', $value);
        },
        'validate_callback' => function($value) {
            if (!validar_cpf($value)) {
                return new WP_Error(
                    'invalid_cpf',
                    'CPF inválido'
                );
            }
            return true;
        }
    ]
];
?>
```

---

## 🔐 REST Authentication

### 2.9 Métodos de Autenticação

```php
<?php
// ========== 1. AUTENTICAÇÃO BÁSICA (HTTP Basic Auth) ==========

// Cliente (curl)
curl -u usuario:senha https://seusite.com/wp-json/meu-plugin/v1/items

// No callback
function autenticar_basico(WP_REST_Request $request) {
    $auth_header = $request->get_header('authorization');
    
    if (empty($auth_header)) {
        return new WP_Error(
            'rest_unauthorized',
            'Autenticação necessária',
            ['status' => 401]
        );
    }
    
    // Decodificar Basic Auth
    list($username, $password) = explode(':', base64_decode(
        substr($auth_header, 6)
    ));
    
    $user = wp_authenticate($username, $password);
    
    if (is_wp_error($user)) {
        return new WP_Error(
            'rest_forbidden',
            'Usuário ou senha inválidos',
            ['status' => 403]
        );
    }
    
    wp_set_current_user($user->ID);
    
    return true;
}

// ========== 2. NONCES (WordPress padrão) ==========

// Gerar nonce (no frontend/admin)
$nonce = wp_create_nonce('wp_rest');

// JavaScript
wp_localize_script('meu-js', 'wpRest', [
    'nonce' => wp_create_nonce('wp_rest')
]);

// jQuery AJAX
jQuery.ajax({
    url: '/wp-json/meu-plugin/v1/items',
    type: 'POST',
    beforeSend: function(xhr) {
        xhr.setRequestHeader('X-WP-Nonce', wpRest.nonce);
    },
    data: { nome: 'Novo Item' }
});

// Verificar nonce no callback
function verificar_nonce(WP_REST_Request $request) {
    $nonce = $request->get_header('X-WP-Nonce');
    
    if (!wp_verify_nonce($nonce, 'wp_rest')) {
        return new WP_Error(
            'rest_nonce_invalid',
            'Nonce inválido',
            ['status' => 403]
        );
    }
    
    return true;
}

// ========== 3. APPLICATION PASSWORDS (WordPress 5.6+) ==========

// Usuário cria senha de aplicação em /wp-admin/profile.php

// Cliente (curl)
curl -u usuario:aplicacao_senha https://seusite.com/wp-json/meu-plugin/v1/items

// Verificar no callback
function verificar_auth(WP_REST_Request $request) {
    if (!is_user_logged_in()) {
        return new WP_Error(
            'rest_unauthorized',
            'Não autenticado',
            ['status' => 401]
        );
    }
    
    return true;
}

// ========== 4. JWT TOKENS CUSTOMIZADOS ==========

// Endpoint de login
function login_jwt(WP_REST_Request $request) {
    $username = $request->get_param('username');
    $password = $request->get_param('password');
    
    $user = wp_authenticate($username, $password);
    
    if (is_wp_error($user)) {
        return new WP_Error(
            'rest_unauthorized',
            'Credenciais inválidas',
            ['status' => 401]
        );
    }
    
    // Gerar JWT (usando biblioteca jwt)
    $token = [
        'iss' => get_bloginfo('url'),
        'sub' => $user->ID,
        'iat' => time(),
        'exp' => time() + (7 * DAY_IN_SECONDS)  // Válido por 7 dias
    ];
    
    $secret = defined('JWT_SECRET') ? JWT_SECRET : 'seu-secret-aqui';
    $jwt = JWT::encode($token, $secret);
    
    return new WP_REST_Response([
        'success' => true,
        'token' => $jwt,
        'user' => [
            'id' => $user->ID,
            'name' => $user->display_name
        ]
    ], 200);
}

// Middleware para verificar JWT
function verificar_jwt(WP_REST_Request $request) {
    $auth_header = $request->get_header('authorization');
    
    if (empty($auth_header)) {
        return new WP_Error(
            'rest_unauthorized',
            'Token não fornecido',
            ['status' => 401]
        );
    }
    
    list($bearer, $token) = explode(' ', $auth_header);
    
    if ($bearer !== 'Bearer') {
        return new WP_Error(
            'rest_unauthorized',
            'Formato de token inválido',
            ['status' => 401]
        );
    }
    
    try {
        $secret = defined('JWT_SECRET') ? JWT_SECRET : 'seu-secret-aqui';
        $decoded = JWT::decode($token, $secret, ['HS256']);
        
        // Setar usuário atual
        wp_set_current_user($decoded->sub);
        
        return true;
    } catch (Exception $e) {
        return new WP_Error(
            'rest_unauthorized',
            'Token inválido: ' . $e->getMessage(),
            ['status' => 401]
        );
    }
}

// ========== 5. OAUTH2 (para integração com apps terceiros) ==========

// Requer implementação mais complexa
// Considerar usar plugins como "WP OAuth"
?>
```

---

## 👮 REST Permissions

### 2.10 Verificação de Permissões

```php
<?php
// ========== PERMISSION CALLBACKS ==========

// 1. Sempre público
function permissao_publica() {
    return true;
}

// 2. Apenas autenticado
function permissao_autenticado() {
    return is_user_logged_in();
}

// 3. Apenas admin
function permissao_admin() {
    return current_user_can('manage_options');
}

// 4. Baseado em capability
function permissao_editor() {
    return current_user_can('edit_posts');
}

// 5. Complexo - verificar se é autor do post
function permissao_autor_post(WP_REST_Request $request) {
    $post_id = $request->get_param('id');
    $post = get_post($post_id);
    
    if (!$post) {
        return new WP_Error(
            'rest_not_found',
            'Post não encontrado',
            ['status' => 404]
        );
    }
    
    // Apenas autor pode editar seu próprio post
    if (get_current_user_id() !== (int) $post->post_author) {
        return new WP_Error(
            'rest_forbidden',
            'Você não pode editar este post',
            ['status' => 403]
        );
    }
    
    return true;
}

// 6. Diferentes permissões por método HTTP
function permissao_por_metodo(WP_REST_Request $request) {
    $method = $request->get_method();
    
    switch ($method) {
        case 'GET':
            return true;  // Qualquer um pode ler
            
        case 'POST':
        case 'PUT':
        case 'PATCH':
            return current_user_can('edit_posts');
            
        case 'DELETE':
            return current_user_can('delete_posts');
            
        default:
            return false;
    }
}

// 7. Verificação granular de capabilities
function permissao_granular(WP_REST_Request $request) {
    $method = $request->get_method();
    $post_type = $request->get_param('post_type') ?? 'post';
    
    // Verificar capability específica do post type
    $capability = match($method) {
        'GET' => "read_{$post_type}",
        'POST' => "create_{$post_type}s",
        'PUT', 'PATCH' => "edit_{$post_type}",
        'DELETE' => "delete_{$post_type}",
        default => false
    };
    
    if (!$capability) {
        return false;
    }
    
    return current_user_can($capability);
}

// ========== USANDO EM ROTAS ==========

add_action('rest_api_init', function() {
    register_rest_route('meu-plugin/v1', '/items', [
        [
            'methods' => 'GET',
            'callback' => 'get_items',
            'permission_callback' => 'permissao_publica'
        ],
        [
            'methods' => 'POST',
            'callback' => 'criar_item',
            'permission_callback' => 'permissao_autenticado'
        ]
    ]);
    
    register_rest_route('meu-plugin/v1', '/items/(?P<id>\d+)', [
        [
            'methods' => 'DELETE',
            'callback' => 'deletar_item',
            'permission_callback' => 'permissao_admin'
        ]
    ]);
});

// ========== CAPABILITIES CUSTOMIZADAS ==========

// Registrar capability customizada
add_filter('user_has_cap', function($allcaps, $caps, $args) {
    $cap = $caps[0] ?? '';
    
    if ($cap === 'manage_preco') {
        // Apenas usuários com role 'editor' podem gerenciar preços
        if (current_user_can('editor')) {
            $allcaps['manage_preco'] = true;
        }
    }
    
    return $allcaps;
}, 10, 3);

// Uso
if (current_user_can('manage_preco')) {
    // Pode gerenciar preços
}
?>
```

---

## 📦 REST Response e Error Handling

### 2.11 WP_REST_Response e WP_Error

```php
<?php
// ========== WP_REST_Response ==========

// Response simples
$response = new WP_REST_Response([
    'id' => 1,
    'nome' => 'Produto',
    'preco' => 99.90
], 200);

// Adicionar headers
$response->header('X-Custom-Header', 'valor');
$response->header('X-Total-Count', '100');

// Retornar
return $response;

// ========== WP_Error ==========

// Erro simples
return new WP_Error(
    'erro_codigo',
    'Mensagem de erro',
    ['status' => 400]
);

// Erro com detalhes
return new WP_Error(
    'validacao_falhou',
    'Campos obrigatórios inválidos',
    [
        'status' => 422,
        'details' => [
            'nome' => 'Campo obrigatório',
            'email' => 'Email inválido'
        ]
    ]
);

// ========== TRATAMENTO DE ERROS ==========

function criar_produto(WP_REST_Request $request) {
    global $wpdb;
    
    // Validação
    $nome = $request->get_param('nome');
    if (empty($nome)) {
        return new WP_Error(
            'nome_vazio',
            'O campo nome é obrigatório',
            ['status' => 400]
        );
    }
    
    // Tentar inserir
    $resultado = $wpdb->insert(
        "{$wpdb->prefix}produtos",
        ['nome' => $nome],
        ['%s']
    );
    
    // Tratar erro de BD
    if ($resultado === false) {
        return new WP_Error(
            'db_error',
            'Erro ao inserir no banco de dados: ' . $wpdb->last_error,
            ['status' => 500]
        );
    }
    
    // Sucesso
    $produto = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}produtos WHERE id = %d",
        $wpdb->insert_id
    ));
    
    return new WP_REST_Response($produto, 201);
}

// ========== STATUS CODES APROPRIADOS ==========

// 200 OK - GET, PUT bem-sucedido
return new WP_REST_Response($data, 200);

// 201 Created - POST bem-sucedido
return new WP_REST_Response($created_data, 201);

// 204 No Content - DELETE bem-sucedido
return new WP_REST_Response(null, 204);

// 400 Bad Request - Requisição malformada
return new WP_Error('bad_request', 'Requisição inválida', ['status' => 400]);

// 401 Unauthorized - Não autenticado
return new WP_Error('unauthenticated', 'Autenticação necessária', ['status' => 401]);

// 403 Forbidden - Sem permissão
return new WP_Error('forbidden', 'Sem permissão', ['status' => 403]);

// 404 Not Found - Recurso não existe
return new WP_Error('not_found', 'Recurso não encontrado', ['status' => 404]);

// 422 Unprocessable Entity - Validação falhou
return new WP_Error('validation_failed', 'Validação falhou', ['status' => 422]);

// 500 Internal Server Error - Erro do servidor
return new WP_Error('server_error', 'Erro do servidor', ['status' => 500]);
?>
```

---

## 📖 Documentação e Schema

### 2.12 Schema JSON e Documentação

```php
<?php
// ========== DEFINIR SCHEMA ==========

function obter_schema_produto() {
    return [
        '$schema' => 'http://json-schema.org/draft-04/schema#',
        'title' => 'Produto',
        'type' => 'object',
        'properties' => [
            'id' => [
                'type' => 'integer',
                'description' => 'ID único do produto',
                'readonly' => true
            ],
            'nome' => [
                'type' => 'string',
                'description' => 'Nome do produto',
                'minLength' => 3,
                'maxLength' => 255
            ],
            'descricao' => [
                'type' => 'string',
                'description' => 'Descrição detalhada',
                'minLength' => 0,
                'maxLength' => 5000
            ],
            'preco' => [
                'type' => 'number',
                'description' => 'Preço do produto',
                'minimum' => 0,
                'multipleOf' => 0.01
            ],
            'categoria' => [
                'type' => 'string',
                'description' => 'Categoria do produto',
                'enum' => ['eletrônicos', 'roupas', 'alimentos', 'livros']
            ],
            'estoque' => [
                'type' => 'integer',
                'description' => 'Quantidade em estoque',
                'minimum' => 0,
                'default' => 0
            ],
            'ativo' => [
                'type' => 'boolean',
                'description' => 'Se o produto está ativo',
                'default' => true
            ],
            'data_criacao' => [
                'type' => 'string',
                'format' => 'date-time',
                'description' => 'Data de criação',
                'readonly' => true
            ]
        ],
        'required' => ['nome', 'preco'],
        'additionalProperties' => false
    ];
}

// ========== USAR SCHEMA EM ROTA ==========

add_action('rest_api_init', function() {
    register_rest_route('meu-plugin/v1', '/produtos', [
        [
            'methods' => 'GET',
            'callback' => 'listar_produtos',
            'permission_callback' => '__return_true'
        ],
        [
            'methods' => 'POST',
            'callback' => 'criar_produto',
            'permission_callback' => 'verificar_permissao',
            'args' => [
                'nome' => [
                    'required' => true,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                    'description' => 'Nome do produto'
                ],
                'preco' => [
                    'required' => true,
                    'type' => 'number',
                    'sanitize_callback' => 'floatval',
                    'description' => 'Preço do produto'
                ],
                'categoria' => [
                    'required' => false,
                    'type' => 'string',
                    'enum' => ['eletrônicos', 'roupas', 'alimentos', 'livros'],
                    'description' => 'Categoria do produto'
                ]
            ]
        ]
    ]);
    
    // Registrar schema
    register_rest_field('produto', null, [
        'schema' => obter_schema_produto()
    ]);
});
?>
```

---

## 🎨 REST Filters Avançados

### 2.13 Modificar Respostas com Filters

```php
<?php
// ========== FILTER: Modificar resposta de posts ==========

add_filter('rest_prepare_post', function($response, $post, $request) {
    $data = $response->get_data();
    
    // Adicionar campo customizado
    $data['tempo_leitura'] = calcular_tempo_leitura($post->post_content);
    
    // Remover campo sensível
    unset($data['author_email']);
    
    // Reformatar campo
    $data['data_formatada'] = date_i18n('d/m/Y', strtotime($post->post_date));
    
    $response->set_data($data);
    return $response;
}, 10, 3);

// ========== FILTER: Adicionar campos customizados ==========

add_filter('rest_insert_post', function($post, $request) {
    $custom_field = $request->get_param('meu_campo');
    
    if ($custom_field) {
        update_post_meta($post->ID, 'meu_campo', sanitize_text_field($custom_field));
    }
    
    return $post;
}, 10, 2);

// ========== FILTER: Modificar query de listagem ==========

add_filter('rest_post_query', function($args, $request) {
    // Adicionar filtro customizado
    if ($request->get_param('meu_filtro')) {
        $args['meta_query'][] = [
            'key' => 'campo_especial',
            'value' => $request->get_param('meu_filtro'),
            'compare' => '='
        ];
    }
    
    // Adicionar orderby customizado
    if ($request->get_param('ordenar_por_custom')) {
        $args['meta_key'] = 'preco';
        $args['orderby'] = 'meta_value_num';
    }
    
    return $args;
}, 10, 2);

// ========== FILTER: Adicionar parâmetros à coleção ==========

add_filter('rest_post_collection_params', function($params, $post_type) {
    // Adicionar novo parâmetro
    $params['meu_parametro'] = [
        'description' => 'Meu parâmetro customizado',
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'validate_callback' => 'is_string'
    ];
    
    return $params;
}, 10, 2);

// ========== ACTION: Ao criar post via API ==========

add_action('rest_insert_post', function($post, $request) {
    // Log da criação
    error_log('Post criado via API: ' . $post->ID);
    
    // Enviar notificação
    if ($post->post_status === 'publish') {
        wp_mail(
            'admin@site.com',
            'Novo post criado via API',
            'Post: ' . $post->post_title
        );
    }
}, 10, 2);

// ========== ACTION: Antes de retornar resposta ==========

add_action('rest_post_dispatch', function($result, $server, $request) {
    // Adicionar header customizado
    if ($result instanceof WP_REST_Response) {
        $result->header('X-API-Version', '1.0');
    }
    
    return $result;
}, 10, 3);
?>
```

---

## 🎓 Resumo da Fase 2

Ao dominar a **Fase 2**, você entenderá:

✅ **Conceitos REST** - Stateless, resources, HTTP methods  
✅ **Registrar Rotas** - `register_rest_route()` completo  
✅ **REST Controllers** - OOP profissional com `WP_REST_Controller`  
✅ **Validação & Sanitização** - Proteger dados de entrada  
✅ **Autenticação** - Básica, nonces, JWT, Application Passwords  
✅ **Permissões** - Verificação granular de capabilities  
✅ **Responses & Erros** - Status codes apropriados  
✅ **Documentação** - Schema JSON e OpenAPI  
✅ **Filters Avançados** - Modificar respostas dinamicamente  

**Próximo passo:** Fase 3 - Estrutura Profissional de Plugins

---

**Versão:** 1.0  
**Última atualização:** Janeiro 2026  
**Autor:** André | Especialista em PHP e WordPress
