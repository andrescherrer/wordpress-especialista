# 🔌 FASE 2: WordPress REST API Fundamentals

**Versão:** 1.0  
**Data:** Janeiro 2026  
**Nível:** Especialista em PHP  
**Objetivo:** Dominar a criação e gerenciamento de APIs REST no WordPress

---

**Navegação:** [Índice](000-WordPress-Topicos-Index.md) | [← Fase 1](001-WordPress-Fase-1-Fundamentals%20of%20WordPress%20Core.md) | [Fase 3 →](003-WordPress-Fase-3-REST-API-Advanced.md)

---

## 📑 Índice

1. [Conceitos Básicos da REST API](#conceitos-básicos-da-rest-api)
2. [Registrar Rotas Customizadas](#registrar-rotas-customizadas)
3. [REST Controllers (OOP)](#rest-controllers-oop)
4. [Validação e Sanitização](#validação-e-sanitização)
5. [Security Essentials](#security-essentials)
6. [REST Authentication](#rest-authentication)
7. [REST Permissions](#rest-permissions)
8. [REST Response e Error Handling](#rest-response-e-error-handling)
9. [Documentação e Schema](#documentação-e-schema)
10. [REST Filters Avançados](#rest-filters-avançados)

---

## 🎯 Objetivos de Aprendizado

Ao final desta fase, você será capaz de:

1. ✅ Registrar rotas e endpoints customizados da REST API usando `register_rest_route()`
2. ✅ Implementar controllers REST usando padrões OOP com `WP_REST_Controller`
3. ✅ Aplicar princípios adequados de validação, sanitização e escaping (VSE) em endpoints de API
4. ✅ Implementar métodos seguros de autenticação (Cookie, Application Passwords, JWT)
5. ✅ Criar callbacks de permissão customizados para controle de acesso granular
6. ✅ Tratar erros adequadamente e retornar respostas de erro estruturadas
7. ✅ Documentar APIs usando schemas OpenAPI/Swagger
8. ✅ Usar filters REST para customizar parâmetros de coleção e comportamento de queries

## 📝 Autoavaliação

Teste seu entendimento:

- [ ] Qual é a diferença entre `validate_callback` e `sanitize_callback` na REST API?
- [ ] Como você previne SQL injection em endpoints da REST API?
- [ ] Quais códigos de status HTTP você deve usar para diferentes cenários (200, 201, 400, 401, 404)?
- [ ] Como a autenticação JWT funciona e como você verifica tokens?
- [ ] Qual é o propósito de `permission_callback` em rotas REST?
- [ ] Como você adiciona parâmetros de query customizados em endpoints de coleção da REST API?
- [ ] Qual é a diferença entre `WP_REST_Response` e `WP_Error`?
- [ ] Como você implementa idempotência em endpoints da REST API?

## 🛠️ Projeto Prático

**Construir:** API de Produtos E-commerce

Crie uma REST API para gerenciar produtos que:
- Suporte operações CRUD (Create, Read, Update, Delete)
- Implemente autenticação JWT
- Inclua validação e sanitização adequadas
- Retorne documentação OpenAPI/Swagger
- Trate erros graciosamente com códigos de status apropriados
- Suporte filtragem, ordenação e paginação

**Tempo estimado:** 10-12 horas  
**Dificuldade:** Intermediário

---

## ❌ Equívocos Comuns

### Equívoco 1: "Endpoints da REST API são automaticamente seguros"
**Realidade:** Endpoints da REST API requerem verificações explícitas de permissão, validação de entrada e sanitização de saída. Por padrão, eles não são seguros.

**Por que é importante:** Sem segurança adequada, sua API pode ser explorada para acesso não autorizado, vazamento de dados ou ataques.

**Como lembrar:** Sempre implemente `permission_callback`, valide entradas e sanitize saídas para cada endpoint.

### Equívoco 2: "Sanitização e validação são a mesma coisa"
**Realidade:** Validação verifica se os dados estão corretos (ex: é um email?), sanitização limpa/transforma dados (ex: remove tags HTML). Você precisa de ambos.

**Por que é importante:** Validação previne dados ruins, sanitização previne problemas de segurança. Usar apenas um deixa vulnerabilidades.

**Como lembrar:** Validar = "Está correto?", Sanitizar = "Tornar seguro".

### Equívoco 3: "Tokens JWT não expiram"
**Realidade:** Tokens JWT devem ter tempos de expiração (claim `exp`) e ser validados em cada requisição. Tokens sem expiração são um risco de segurança.

**Por que é importante:** Tokens roubados sem expiração podem ser usados indefinidamente. Expiração limita a janela de dano.

**Como lembrar:** Sempre defina o claim `exp` e valide-o. Renove tokens antes da expiração.

### Equívoco 4: "REST API não precisa de nonces"
**Realidade:** Embora a REST API use autenticação diferente (Application Passwords, JWT), você ainda precisa proteger contra CSRF em certos cenários, especialmente para operações que alteram estado.

**Por que é importante:** Endpoints da REST API ainda podem ser vulneráveis a ataques CSRF se não protegidos adequadamente.

**Como lembrar:** REST API = método de autenticação diferente, mas ainda precisa de proteção para operações que alteram estado.

### Equívoco 5: "Todos os endpoints REST devem retornar JSON"
**Realidade:** Embora JSON seja comum, a REST API pode retornar diferentes formatos. O importante é consistência e headers de content-type adequados.

**Por que é importante:** Diferentes clientes podem precisar de diferentes formatos. Sempre defina headers `Content-Type` apropriados.

**Como lembrar:** REST = Representação, não necessariamente JSON. Use tipos de conteúdo apropriados.

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

## 🔒 Security Essentials

### 2.9 Por Que Segurança Desde o Início?

**Problema:** Desenvolvedores aprendem padrões inseguros primeiro e depois precisam "desaprender".

**Solução:** Estabelecer padrões de segurança corretos desde o início, integrando segurança em cada etapa do desenvolvimento.

**Benefícios:**
- ✅ Código seguro desde o primeiro dia
- ✅ Padrões corretos estabelecidos cedo
- ✅ Menos vulnerabilidades em produção
- ✅ Menos retrabalho

### 2.9.1 Input Validation vs Sanitization vs Escaping

É fundamental entender a diferença entre esses três conceitos:

#### **Input Validation (Validação de Entrada)**

**Quando:** Antes de processar dados  
**O que faz:** Verifica se os dados estão no formato correto  
**Resultado:** Aceita ou rejeita os dados

```php
<?php
/**
 * VALIDAÇÃO: Verifica se dados estão corretos
 * 
 * Se inválido, REJEITA e retorna erro
 * Se válido, ACEITA para processamento
 */

// Validar email
function validar_email(string $email): bool {
    return is_email($email);
}

// Validar idade
function validar_idade(int $idade): bool {
    return $idade >= 18 && $idade <= 120;
}

// Validar CPF
function validar_cpf(string $cpf): bool {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    return strlen($cpf) === 11;
}

// Uso em REST API
$args = [
    'email' => [
        'required' => true,
        'type' => 'string',
        'validate_callback' => function($value) {
            if (!is_email($value)) {
                return new WP_Error(
                    'invalid_email',
                    'Email inválido',
                    ['status' => 400]
                );
            }
            return true;
        }
    ]
];
```

#### **Input Sanitization (Sanitização de Entrada)**

**Quando:** Ao receber dados do usuário  
**O que faz:** Remove ou limpa dados perigosos  
**Resultado:** Dados limpos prontos para armazenamento

```php
<?php
/**
 * SANITIZAÇÃO: Remove dados perigosos
 * 
 * Remove HTML, scripts, caracteres especiais
 * Mantém dados seguros para armazenamento
 */

// Sanitizar texto
$nome = sanitize_text_field($_POST['nome']);
// Remove HTML, tags, caracteres especiais

// Sanitizar email
$email = sanitize_email($_POST['email']);
// Remove caracteres inválidos de email

// Sanitizar URL
$url = esc_url_raw($_POST['url']);
// Remove caracteres perigosos de URL

// Sanitizar conteúdo HTML
$conteudo = wp_kses_post($_POST['conteudo']);
// Permite apenas HTML permitido (tags seguras)

// Sanitizar número inteiro
$idade = absint($_POST['idade']);
// Garante número inteiro positivo

// Sanitizar array
$tags = array_map('sanitize_text_field', $_POST['tags']);
// Sanitiza cada item do array

// Uso em REST API
$args = [
    'nome' => [
        'required' => true,
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field'
    ],
    'email' => [
        'required' => true,
        'type' => 'string',
        'sanitize_callback' => 'sanitize_email'
    ]
];
```

#### **Output Escaping (Escapamento de Saída)**

**Quando:** Ao exibir dados na tela  
**O que faz:** Escapa caracteres especiais para HTML seguro  
**Resultado:** Dados seguros para exibição

```php
<?php
/**
 * ESCAPING: Prepara dados para output seguro
 * 
 * Escapa caracteres especiais para HTML
 * Previne XSS (Cross-Site Scripting)
 */

// Escapar HTML
echo esc_html($user_input);
// Converte < para &lt;, > para &gt;, etc.

// Escapar atributos HTML
echo '<input value="' . esc_attr($user_input) . '">';
// Escapa para uso em atributos HTML

// Escapar URLs
echo '<a href="' . esc_url($url) . '">Link</a>';
// Escapa URL para uso seguro

// Escapar JavaScript
echo '<script>var data = ' . esc_js($json_data) . ';</script>';
// Escapa para uso em JavaScript

// Escapar texto para textarea
echo '<textarea>' . esc_textarea($user_input) . '</textarea>';
// Escapa para uso em textarea

// Escapar URL em atributo
echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($alt) . '">';

// ❌ ERRADO: Não escapar
echo $user_input; // PERIGOSO! Pode conter XSS

// ✅ CORRETO: Sempre escapar
echo esc_html($user_input); // SEGURO!
```

### 2.9.2 Regra de Ouro: Validate, Sanitize, Escape

```
ENTRADA (Input)
    ↓
1. VALIDAR (Validar formato)
    ↓ (se válido)
2. SANITIZAR (Limpar dados)
    ↓
3. PROCESSAR/ARMAZENAR
    ↓
4. ESCAPAR (Ao exibir)
    ↓
SAÍDA (Output)
```

**Exemplo Completo:**

```php
<?php
/**
 * Fluxo completo: Validate → Sanitize → Store → Escape
 */

// 1. VALIDAR (antes de processar)
function processar_formulario(): void {
    $email = $_POST['email'] ?? '';
    
    // Validar
    if (!is_email($email)) {
        wp_die('Email inválido');
    }
    
    // 2. SANITIZAR (antes de armazenar)
    $email = sanitize_email($email);
    
    // 3. ARMAZENAR (dados já sanitizados)
    update_option('user_email', $email);
    
    // 4. ESCAPAR (ao exibir)
    $saved_email = get_option('user_email');
    echo esc_html($saved_email); // Sempre escapar na saída!
}
```

### 2.9.3 Funções de Sanitização Comuns

```php
<?php
/**
 * Funções de Sanitização do WordPress
 */

// Texto simples
sanitize_text_field($text);        // Remove HTML, tags, caracteres especiais
sanitize_textarea_field($text);    // Para textareas (preserva quebras de linha)

// Email
sanitize_email($email);             // Remove caracteres inválidos de email

// URL
esc_url_raw($url);                 // Sanitiza URL para armazenamento
esc_url($url);                     // Escapa URL para exibição

// Números
absint($number);                    // Número inteiro positivo
intval($number);                    // Converte para inteiro
floatval($number);                  // Converte para float

// HTML
wp_kses_post($html);                // Permite apenas HTML permitido
wp_kses($html, $allowed_html);      // HTML customizado permitido
wp_strip_all_tags($html);           // Remove todas as tags HTML

// Chaves e slugs
sanitize_key($key);                 // Para chaves de opções, meta keys
sanitize_file_name($filename);      // Para nomes de arquivos
sanitize_title($title);             // Para slugs, títulos

// Array
array_map('sanitize_text_field', $array);  // Sanitiza cada item
```

### 2.9.4 Funções de Escaping Comuns

```php
<?php
/**
 * Funções de Escaping do WordPress
 */

// HTML
esc_html($text);                    // Escapa HTML
esc_html__('Text', 'text-domain');  // Escapa e traduz
esc_html_e('Text', 'text-domain');  // Escapa, traduz e imprime

// Atributos HTML
esc_attr($value);                   // Para atributos HTML
esc_attr__('Text', 'text-domain');  // Escapa, traduz

// URLs
esc_url($url);                      // Escapa URL
esc_url_raw($url);                  // Sanitiza URL (para armazenamento)

// JavaScript
esc_js($text);                      // Escapa para JavaScript

// Textarea
esc_textarea($text);                // Escapa para textarea

// JSON
wp_json_encode($data);              // Codifica JSON seguro
```

### 2.9.5 Nonces Básico

**Nonce** = "Number Used Once" - Token único para prevenir CSRF (Cross-Site Request Forgery)

#### **Por Que Usar Nonces?**

- ✅ Previne ataques CSRF
- ✅ Verifica que requisição vem do site correto
- ✅ Validação de intenção do usuário

#### **Como Usar Nonces**

```php
<?php
/**
 * NONCES: Prevenção de CSRF
 */

// ========== 1. GERAR NONCE (no formulário) ==========

// Em formulários HTML
<form method="post">
    <?php wp_nonce_field('my_action', 'my_nonce'); ?>
    <input type="text" name="data">
    <button type="submit">Enviar</button>
</form>

// Em URLs
$url = wp_nonce_url(
    admin_url('admin.php?page=my-page&action=delete&id=123'),
    'delete_item_123',
    'nonce'
);
// Resultado: /wp-admin/admin.php?page=my-page&action=delete&id=123&nonce=abc123

// Em JavaScript/AJAX
wp_localize_script('my-script', 'myData', [
    'nonce' => wp_create_nonce('my_ajax_action'),
    'ajax_url' => admin_url('admin-ajax.php')
]);

// ========== 2. VERIFICAR NONCE (ao processar) ==========

// Em formulários POST
function processar_formulario(): void {
    // Verificar nonce
    if (!isset($_POST['my_nonce']) || 
        !wp_verify_nonce($_POST['my_nonce'], 'my_action')) {
        wp_die('Security check failed');
    }
    
    // Processar dados (nonce válido)
    $data = sanitize_text_field($_POST['data']);
    // ...
}

// Em AJAX
add_action('wp_ajax_my_action', 'handle_ajax_request');
function handle_ajax_request(): void {
    // Verificar nonce
    check_ajax_referer('my_ajax_action', 'nonce');
    
    // Processar requisição
    wp_send_json_success(['message' => 'Success']);
}

// Em REST API
function verificar_nonce_rest(WP_REST_Request $request): bool {
    $nonce = $request->get_header('X-WP-Nonce');
    
    if (!wp_verify_nonce($nonce, 'wp_rest')) {
        return false;
    }
    
    return true;
}

// ========== 3. VERIFICAÇÕES ADICIONAIS ==========

// check_admin_referer() - Verifica nonce + referer
if (!check_admin_referer('my_action', 'my_nonce')) {
    wp_die('Security check failed');
}

// check_ajax_referer() - Para AJAX
check_ajax_referer('my_ajax_action', 'nonce');
```

#### **Exemplo Completo: Formulário com Nonce**

```php
<?php
/**
 * Exemplo completo: Formulário seguro com nonce
 */

// ========== FORMULÁRIO ==========
function render_form(): void {
    ?>
    <form method="post" action="">
        <?php wp_nonce_field('save_user_data', 'user_nonce'); ?>
        
        <label>
            Nome:
            <input type="text" name="user_name" 
                   value="<?php echo esc_attr(get_option('user_name')); ?>">
        </label>
        
        <label>
            Email:
            <input type="email" name="user_email"
                   value="<?php echo esc_attr(get_option('user_email')); ?>">
        </label>
        
        <button type="submit">Salvar</button>
    </form>
    <?php
}

// ========== PROCESSAR FORMULÁRIO ==========
add_action('admin_init', 'processar_user_form');
function processar_user_form(): void {
    // Verificar se é POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }
    
    // Verificar nonce
    if (!isset($_POST['user_nonce']) || 
        !wp_verify_nonce($_POST['user_nonce'], 'save_user_data')) {
        wp_die('Security check failed');
    }
    
    // Verificar capability
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    
    // Validar e sanitizar
    $name = isset($_POST['user_name']) 
        ? sanitize_text_field($_POST['user_name']) 
        : '';
    $email = isset($_POST['user_email']) 
        ? sanitize_email($_POST['user_email']) 
        : '';
    
    // Validar email
    if (!is_email($email)) {
        add_settings_error('user_email', 'invalid_email', 'Email inválido');
        return;
    }
    
    // Salvar
    update_option('user_name', $name);
    update_option('user_email', $email);
    
    add_settings_error('user_data', 'success', 'Dados salvos com sucesso', 'updated');
}
```

### 2.9.6 Capability Checks

**Capabilities** = Permissões específicas que usuários podem ter  
**Roles** = Grupos de capabilities (administrator, editor, author, etc.)

#### **Por Que Verificar Capabilities?**

- ✅ Controle de acesso granular
- ✅ Segurança baseada em permissões
- ✅ Previne acesso não autorizado

#### **Como Verificar Capabilities**

```php
<?php
/**
 * CAPABILITY CHECKS: Verificar permissões
 */

// ========== 1. VERIFICAÇÃO BÁSICA ==========

// Verificar se usuário tem capability
if (!current_user_can('edit_posts')) {
    wp_die('Você não tem permissão para editar posts');
}

// Verificar capability específica
if (!current_user_can('edit_post', $post_id)) {
    wp_die('Você não tem permissão para editar este post');
}

// ========== 2. CAPABILITIES COMUNS ==========

// Posts
current_user_can('edit_posts');           // Pode editar posts
current_user_can('publish_posts');        // Pode publicar posts
current_user_can('delete_posts');          // Pode deletar posts
current_user_can('edit_post', $post_id);  // Pode editar post específico

// Pages
current_user_can('edit_pages');           // Pode editar páginas
current_user_can('publish_pages');        // Pode publicar páginas

// Users
current_user_can('edit_users');           // Pode editar usuários
current_user_can('create_users');          // Pode criar usuários
current_user_can('delete_users');         // Pode deletar usuários

// Options
current_user_can('manage_options');       // Pode gerenciar opções (admin)

// Plugins/Themes
current_user_can('activate_plugins');     // Pode ativar plugins
current_user_can('install_plugins');      // Pode instalar plugins
current_user_can('switch_themes');        // Pode trocar temas

// ========== 3. VERIFICAÇÃO EM REST API ==========

function verificar_permissao_editar(WP_REST_Request $request): bool {
    // Verificar capability básica
    if (!current_user_can('edit_posts')) {
        return false;
    }
    
    // Verificar capability específica do post
    $post_id = $request->get_param('id');
    if ($post_id && !current_user_can('edit_post', $post_id)) {
        return false;
    }
    
    return true;
}

// Usar em register_rest_route
register_rest_route('myapp/v1', '/posts/(?P<id>\d+)', [
    'methods' => 'PUT',
    'callback' => 'update_post',
    'permission_callback' => 'verificar_permissao_editar'
]);

// ========== 4. VERIFICAÇÃO EM FORMULÁRIOS ==========

function processar_formulario_admin(): void {
    // Verificar nonce primeiro
    if (!check_admin_referer('my_action', 'my_nonce')) {
        wp_die('Security check failed');
    }
    
    // Verificar capability
    if (!current_user_can('manage_options')) {
        wp_die('Você não tem permissão para fazer isso');
    }
    
    // Processar (usuário tem permissão)
    // ...
}

// ========== 5. VERIFICAÇÃO CONDICIONAL ==========

// Verificar múltiplas capabilities
if (current_user_can('edit_posts') || current_user_can('edit_pages')) {
    // Usuário pode editar posts OU páginas
}

// Verificar role específica
if (current_user_can('administrator')) {
    // Apenas administradores
}

// Verificar usuário específico
$current_user = wp_get_current_user();
if ($current_user->ID === 1) {
    // Apenas usuário ID 1
}
```

#### **Exemplo Completo: Endpoint REST com Capability Check**

```php
<?php
/**
 * Exemplo completo: REST endpoint com segurança completa
 */

add_action('rest_api_init', 'registrar_endpoint_seguro');

function registrar_endpoint_seguro(): void {
    register_rest_route('myapp/v1', '/posts/(?P<id>\d+)', [
        'methods' => 'PUT',
        'callback' => 'atualizar_post_seguro',
        'permission_callback' => function(WP_REST_Request $request) {
            // 1. Verificar se está autenticado
            if (!is_user_logged_in()) {
                return new WP_Error(
                    'rest_unauthorized',
                    'Você precisa estar autenticado',
                    ['status' => 401]
                );
            }
            
            // 2. Verificar capability
            $post_id = $request->get_param('id');
            if (!current_user_can('edit_post', $post_id)) {
                return new WP_Error(
                    'rest_forbidden',
                    'Você não tem permissão para editar este post',
                    ['status' => 403]
                );
            }
            
            return true;
        },
        'args' => [
            'title' => [
                'required' => false,
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'validate_callback' => function($value) {
                    return strlen($value) <= 200;
                }
            ],
            'content' => [
                'required' => false,
                'type' => 'string',
                'sanitize_callback' => 'wp_kses_post'
            ]
        ]
    ]);
}

function atualizar_post_seguro(WP_REST_Request $request): WP_REST_Response {
    $post_id = $request->get_param('id');
    
    // Dados já validados e sanitizados pelos args
    $title = $request->get_param('title');
    $content = $request->get_param('content');
    
    // Atualizar post
    $result = wp_update_post([
        'ID' => $post_id,
        'post_title' => $title,
        'post_content' => $content
    ]);
    
    if (is_wp_error($result)) {
        return new WP_Error(
            'update_failed',
            'Erro ao atualizar post',
            ['status' => 500]
        );
    }
    
    // Retornar post atualizado (escapado)
    $post = get_post($post_id);
    
    return new WP_REST_Response([
        'id' => $post->ID,
        'title' => esc_html($post->post_title),  // Escapar na saída!
        'content' => wp_kses_post($post->post_content)
    ], 200);
}
```

### 2.9.7 Checklist de Segurança

Use este checklist ao desenvolver qualquer funcionalidade:

```php
<?php
/**
 * CHECKLIST DE SEGURANÇA
 * 
 * Para cada funcionalidade, verificar:
 */

function minha_funcao(): void {
    // ✅ 1. Verificar nonce (se formulário/AJAX)
    if (!check_admin_referer('my_action', 'my_nonce')) {
        wp_die('Security check failed');
    }
    
    // ✅ 2. Verificar capability (se necessário)
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    
    // ✅ 3. Validar entrada
    $email = $_POST['email'] ?? '';
    if (!is_email($email)) {
        wp_die('Email inválido');
    }
    
    // ✅ 4. Sanitizar entrada
    $email = sanitize_email($email);
    $name = sanitize_text_field($_POST['name'] ?? '');
    
    // ✅ 5. Processar (dados já validados e sanitizados)
    update_option('user_email', $email);
    
    // ✅ 6. Escapar saída (ao exibir)
    $saved_email = get_option('user_email');
    echo esc_html($saved_email);
}
```

### 2.9.8 Erros Comuns de Segurança

```php
<?php
/**
 * ❌ ERRADOS - NÃO FAÇA ISSO
 */

// ❌ ERRADO 1: Não validar entrada
$email = $_POST['email'];  // Pode conter qualquer coisa!

// ❌ ERRADO 2: Não sanitizar antes de armazenar
update_option('user_data', $_POST['data']);  // Dados não sanitizados!

// ❌ ERRADO 3: Não escapar na saída
echo $user_input;  // XSS vulnerável!

// ❌ ERRADO 4: Não verificar nonce
if (isset($_POST['submit'])) {  // Sem verificação de nonce!
    // Processar...
}

// ❌ ERRADO 5: Não verificar capability
function delete_post() {  // Qualquer um pode chamar!
    wp_delete_post($_GET['id']);
}

// ❌ ERRADO 6: SQL Injection
global $wpdb;
$wpdb->query("SELECT * FROM posts WHERE id = {$_GET['id']}");  // PERIGOSO!

// ✅ CORRETOS - FAÇA ASSIM

// ✅ CORRETO 1: Validar entrada
$email = $_POST['email'] ?? '';
if (!is_email($email)) {
    wp_die('Email inválido');
}

// ✅ CORRETO 2: Sanitizar antes de armazenar
$data = sanitize_text_field($_POST['data']);
update_option('user_data', $data);

// ✅ CORRETO 3: Escapar na saída
echo esc_html($user_input);

// ✅ CORRETO 4: Verificar nonce
if (!check_admin_referer('my_action', 'my_nonce')) {
    wp_die('Security check failed');
}

// ✅ CORRETO 5: Verificar capability
function delete_post() {
    if (!current_user_can('delete_posts')) {
        wp_die('Unauthorized');
    }
    // Processar...
}

// ✅ CORRETO 6: Prepared statements
global $wpdb;
$wpdb->prepare("SELECT * FROM posts WHERE id = %d", $_GET['id']);
```

### 2.9.9 Diagrama de Decisão: Quando Usar Qual Função

```
┌─────────────────────────────────────────────────────────────┐
│                    DADOS DO USUÁRIO                         │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
        ┌───────────────────────────────────┐
        │  É um EMAIL?                      │
        └───────────────────────────────────┘
                │                    │
         SIM   │                    │   NÃO
                ▼                    ▼
    ┌──────────────────┐    ┌──────────────────┐
    │ sanitize_email() │    │ É uma URL?       │
    │ is_email()       │    └──────────────────┘
    └──────────────────┘            │
                            SIM     │     NÃO
                            │       │
                            ▼       ▼
                    ┌──────────────┐  ┌──────────────────┐
                    │ esc_url_raw()│  │ É HTML permitido?│
                    │ esc_url()    │  └──────────────────┘
                    └──────────────┘          │
                                      SIM     │     NÃO
                                      │       │
                                      ▼       ▼
                              ┌──────────────┐  ┌──────────────┐
                              │ wp_kses_post()│  │ sanitize_   │
                              │ wp_kses()     │  │ text_field()│
                              └──────────────┘  └──────────────┘
```

**Guia Rápido de Funções:**

| Tipo de Dado | Sanitização | Validação | Escape (Output) |
|--------------|-------------|-----------|-----------------|
| **Email** | `sanitize_email()` | `is_email()` | `esc_html()` |
| **URL** | `esc_url_raw()` | `filter_var($url, FILTER_VALIDATE_URL)` | `esc_url()` |
| **Texto Simples** | `sanitize_text_field()` | `!empty()` | `esc_html()` |
| **HTML Permitido** | `wp_kses_post()` | `wp_kses()` | Não precisa (já sanitizado) |
| **Número Inteiro** | `absint()` | `is_numeric()` | `esc_html()` |
| **Texto Longo** | `sanitize_textarea_field()` | `strlen() > 0` | `esc_textarea()` |
| **Atributo HTML** | `sanitize_text_field()` | - | `esc_attr()` |
| **JavaScript** | `sanitize_text_field()` | - | `esc_js()` |
| **JSON** | `wp_json_encode()` | `json_decode()` | `esc_html()` |

### 2.9.10 Exemplos Práticos Completos por Cenário

Veja exemplos completos de formulário de contato, upload de arquivo e busca na seção [Security Anti-patterns](019-WordPress-Security-Anti-patterns.md#fase-2-rest-api-security-mistakes).

### 2.9.11 Code Review Checklist Expandido

**Input Validation:**
- [ ] Todos os parâmetros obrigatórios são validados?
- [ ] Tipos de dados são verificados (string, int, array)?
- [ ] Ranges e limites são validados (min/max length, min/max value)?
- [ ] Formatos específicos são validados (email, URL, date)?
- [ ] Validação acontece no servidor (não apenas no cliente)?

**Sanitization:**
- [ ] Todos os inputs são sanitizados antes de processar?
- [ ] Função de sanitização correta é usada para cada tipo?
- [ ] Arrays são sanitizados elemento por elemento?
- [ ] Dados de arquivo são sanitizados (nome, tipo, tamanho)?

**Output Escaping:**
- [ ] Todo output é escapado antes de exibir?
- [ ] Contexto correto de escape é usado (HTML, URL, JS, atributo)?
- [ ] Dados de banco são escapados antes de retornar na API?
- [ ] JSON é escapado corretamente com `wp_json_encode()`?

**Security Headers:**
- [ ] Headers de segurança são adicionados (CORS, X-Frame-Options)?
- [ ] Rate limiting é implementado?
- [ ] Nonces são verificados quando necessário?

**Error Handling:**
- [ ] Mensagens de erro não expõem informações sensíveis?
- [ ] Stack traces são desabilitados em produção?
- [ ] Erros são logados de forma segura (sem dados sensíveis)?

---

## 🔐 REST Authentication

### 2.10 Métodos de Autenticação

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
```

### 2.10.1 JWT Verification Completo - Implementação Production-Ready

**Instalação:**

```bash
composer require firebase/php-jwt
```

**Implementação Completa:**

```php
<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

/**
 * Classe completa para gerenciamento de JWT
 */
class JWT_Manager {
    
    private string $secret;
    private string $issuer;
    private int $expiration;
    
    public function __construct() {
        // Secret deve vir de constante ou variável de ambiente
        $this->secret = defined('JWT_SECRET') ? JWT_SECRET : wp_salt('auth');
        $this->issuer = get_bloginfo('url');
        $this->expiration = 7 * DAY_IN_SECONDS; // 7 dias padrão
    }
    
    /**
     * Gerar token JWT
     */
    public function generate_token(int $user_id, array $additional_claims = []): string {
        $user = get_userdata($user_id);
        
        if (!$user) {
            throw new InvalidArgumentException('User not found');
        }
        
        $now = time();
        
        $payload = array_merge([
            'iss' => $this->issuer,           // Issuer
            'sub' => $user_id,                // Subject (user ID)
            'iat' => $now,                    // Issued at
            'exp' => $now + $this->expiration, // Expiration
            'nbf' => $now,                    // Not before
            'jti' => wp_generate_uuid(),      // JWT ID (unique)
            'user_login' => $user->user_login,
            'user_email' => $user->user_email,
        ], $additional_claims);
        
        return JWT::encode($payload, $this->secret, 'HS256');
    }
    
    /**
     * Verificar e decodificar token JWT
     */
    public function verify_token(string $token): object {
        try {
            // Decodificar e verificar
            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
            
            // Verificar issuer
            if ($decoded->iss !== $this->issuer) {
                throw new SignatureInvalidException('Invalid issuer');
            }
            
            // Verificar que usuário ainda existe
            $user = get_userdata($decoded->sub);
            if (!$user) {
                throw new SignatureInvalidException('User no longer exists');
            }
            
            // Verificar que usuário ainda está ativo
            if (!user_can($decoded->sub, 'read')) {
                throw new SignatureInvalidException('User account disabled');
            }
            
            return $decoded;
            
        } catch (ExpiredException $e) {
            throw new WP_Error(
                'jwt_expired',
                'Token expirado',
                ['status' => 401]
            );
        } catch (SignatureInvalidException $e) {
            throw new WP_Error(
                'jwt_invalid',
                'Token inválido: ' . $e->getMessage(),
                ['status' => 401]
            );
        } catch (Exception $e) {
            throw new WP_Error(
                'jwt_error',
                'Erro ao verificar token: ' . $e->getMessage(),
                ['status' => 401]
            );
        }
    }
    
    /**
     * Refresh token (gerar novo token com mesmo usuário)
     */
    public function refresh_token(string $old_token): string {
        $decoded = $this->verify_token($old_token);
        
        // Gerar novo token
        return $this->generate_token($decoded->sub);
    }
}

/**
 * Endpoint de login com JWT
 */
add_action('rest_api_init', function() {
    register_rest_route('myapp/v1', '/auth/login', [
        'methods' => 'POST',
        'callback' => function($request) {
            $username = $request->get_param('username');
            $password = $request->get_param('password');
            
            // Validar input
            if (empty($username) || empty($password)) {
                return new WP_Error(
                    'missing_credentials',
                    'Username e password são obrigatórios',
                    ['status' => 400]
                );
            }
            
            // Autenticar
            $user = wp_authenticate(sanitize_user($username), $password);
            
            if (is_wp_error($user)) {
                // Não expor detalhes do erro (segurança)
                return new WP_Error(
                    'invalid_credentials',
                    'Credenciais inválidas',
                    ['status' => 401]
                );
            }
            
            // Gerar token
            $jwt_manager = new JWT_Manager();
            $token = $jwt_manager->generate_token($user->ID);
            
            // Logar login bem-sucedido (sem dados sensíveis)
            error_log(sprintf('User %d logged in via JWT', $user->ID));
            
            return new WP_REST_Response([
                'success' => true,
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => 7 * DAY_IN_SECONDS,
                'user' => [
                    'id' => $user->ID,
                    'username' => $user->user_login,
                    'email' => $user->user_email,
                    'display_name' => $user->display_name,
                ],
            ], 200);
        },
        'args' => [
            'username' => [
                'required' => true,
                'type' => 'string',
                'sanitize_callback' => 'sanitize_user',
            ],
            'password' => [
                'required' => true,
                'type' => 'string',
            ],
        ],
        'permission_callback' => '__return_true',
    ]);
    
    /**
     * Endpoint para refresh token
     */
    register_rest_route('myapp/v1', '/auth/refresh', [
        'methods' => 'POST',
        'callback' => function($request) {
            $token = $request->get_param('token');
            
            if (empty($token)) {
                return new WP_Error(
                    'missing_token',
                    'Token é obrigatório',
                    ['status' => 400]
                );
            }
            
            $jwt_manager = new JWT_Manager();
            
            try {
                $new_token = $jwt_manager->refresh_token($token);
                
                return new WP_REST_Response([
                    'success' => true,
                    'token' => $new_token,
                    'token_type' => 'Bearer',
                    'expires_in' => 7 * DAY_IN_SECONDS,
                ], 200);
            } catch (WP_Error $e) {
                return $e;
            }
        },
        'args' => [
            'token' => [
                'required' => true,
                'type' => 'string',
            ],
        ],
        'permission_callback' => '__return_true',
    ]);
});

/**
 * Permission callback para verificar JWT
 */
function verify_jwt_permission($request) {
    $auth_header = $request->get_header('authorization');
    
    if (empty($auth_header)) {
        return new WP_Error(
            'rest_unauthorized',
            'Token de autenticação não fornecido',
            ['status' => 401]
        );
    }
    
    // Extrair token do header "Bearer <token>"
    if (preg_match('/Bearer\s+(.*)$/i', $auth_header, $matches)) {
        $token = $matches[1];
    } else {
        return new WP_Error(
            'rest_unauthorized',
            'Formato de token inválido. Use: Bearer <token>',
            ['status' => 401]
        );
    }
    
    $jwt_manager = new JWT_Manager();
    
    try {
        $decoded = $jwt_manager->verify_token($token);
        
        // Definir usuário atual
        wp_set_current_user($decoded->sub);
        
        return true;
    } catch (WP_Error $e) {
        return $e;
    }
}

// Usar em endpoints protegidos
register_rest_route('myapp/v1', '/protected', [
    'methods' => 'GET',
    'callback' => function($request) {
        return new WP_REST_Response([
            'message' => 'Acesso autorizado',
            'user_id' => get_current_user_id(),
        ], 200);
    },
    'permission_callback' => 'verify_jwt_permission',
]);
```

**Uso no Cliente (JavaScript):**

```javascript
// Login
async function login(username, password) {
    const response = await fetch('/wp-json/myapp/v1/auth/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ username, password }),
    });
    
    const data = await response.json();
    
    if (data.token) {
        // Salvar token (use localStorage ou sessionStorage)
        localStorage.setItem('jwt_token', data.token);
        return data;
    }
    
    throw new Error(data.message || 'Login failed');
}

// Fazer requisição autenticada
async function fetchProtectedData() {
    const token = localStorage.getItem('jwt_token');
    
    const response = await fetch('/wp-json/myapp/v1/protected', {
        headers: {
            'Authorization': `Bearer ${token}`,
        },
    });
    
    if (response.status === 401) {
        // Token expirado, fazer refresh ou login novamente
        await refreshToken();
        return fetchProtectedData();
    }
    
    return response.json();
}

// Refresh token
async function refreshToken() {
    const token = localStorage.getItem('jwt_token');
    
    const response = await fetch('/wp-json/myapp/v1/auth/refresh', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ token }),
    });
    
    const data = await response.json();
    
    if (data.token) {
        localStorage.setItem('jwt_token', data.token);
        return data.token;
    }
    
    // Se refresh falhar, fazer login novamente
    throw new Error('Token refresh failed');
}
```

// ========== 5. OAUTH2 (para integração com apps terceiros) ==========

// Requer implementação mais complexa
// Considerar usar plugins como "WP OAuth"
?>
```

---

## 👮 REST Permissions

### 2.11 Verificação de Permissões

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

### 2.12 WP_REST_Response e WP_Error

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

### 2.12.1 Error Handling Patterns Completos

**Padrão 1: Try-Catch em Controllers**

```php
<?php
function create_product_controller(WP_REST_Request $request) {
    try {
        // Validação
        $name = $request->get_param('name');
        if (empty($name)) {
            return new WP_Error(
                'validation_error',
                'Nome é obrigatório',
                ['status' => 400, 'field' => 'name']
            );
        }
        
        // Processamento
        $product_id = create_product([
            'name' => sanitize_text_field($name),
            'price' => floatval($request->get_param('price')),
        ]);
        
        if (is_wp_error($product_id)) {
            return $product_id;
        }
        
        return new WP_REST_Response([
            'id' => $product_id,
            'message' => 'Produto criado com sucesso',
        ], 201);
        
    } catch (Exception $e) {
        // Log erro (sem expor detalhes ao cliente)
        error_log('Error creating product: ' . $e->getMessage());
        
        return new WP_Error(
            'server_error',
            'Erro ao processar requisição',
            ['status' => 500]
        );
    }
}
```

**Padrão 2: Error Handler Centralizado**

```php
<?php
class REST_Error_Handler {
    
    public static function handle_error($error, $context = []) {
        // Log erro com contexto
        error_log(sprintf(
            'REST API Error: %s - Context: %s',
            $error->get_error_message(),
            wp_json_encode($context)
        ));
        
        // Não expor detalhes em produção
        if (!WP_DEBUG) {
            return new WP_Error(
                'server_error',
                'Erro ao processar requisição',
                ['status' => 500]
            );
        }
        
        // Em desenvolvimento, retornar erro completo
        return $error;
    }
    
    public static function validation_error($field, $message) {
        return new WP_Error(
            'validation_error',
            $message,
            [
                'status' => 422,
                'field' => $field,
            ]
        );
    }
    
    public static function not_found($resource_type, $id) {
        return new WP_Error(
            'not_found',
            sprintf('%s com ID %d não encontrado', $resource_type, $id),
            [
                'status' => 404,
                'resource_type' => $resource_type,
                'id' => $id,
            ]
        );
    }
}

// Uso
function get_product($request) {
    $id = $request->get_param('id');
    $product = get_post($id);
    
    if (!$product) {
        return REST_Error_Handler::not_found('Produto', $id);
    }
    
    return new WP_REST_Response($product, 200);
}
```

**Padrão 3: Validação com Múltiplos Erros**

```php
<?php
function validate_product_data($data) {
    $errors = [];
    
    // Validar nome
    if (empty($data['name'])) {
        $errors['name'] = 'Nome é obrigatório';
    } elseif (strlen($data['name']) < 3) {
        $errors['name'] = 'Nome deve ter pelo menos 3 caracteres';
    }
    
    // Validar preço
    if (!isset($data['price'])) {
        $errors['price'] = 'Preço é obrigatório';
    } elseif (!is_numeric($data['price'])) {
        $errors['price'] = 'Preço deve ser um número';
    } elseif ($data['price'] < 0) {
        $errors['price'] = 'Preço não pode ser negativo';
    }
    
    // Validar categoria
    if (isset($data['category_id'])) {
        if (!term_exists($data['category_id'], 'product_category')) {
            $errors['category_id'] = 'Categoria inválida';
        }
    }
    
    if (!empty($errors)) {
        return new WP_Error(
            'validation_failed',
            'Dados inválidos',
            [
                'status' => 422,
                'errors' => $errors,
            ]
        );
    }
    
    return true;
}
```

**Padrão 4: Error Response Padronizado**

```php
<?php
function standard_error_response($code, $message, $data = []) {
    $response = [
        'success' => false,
        'error' => [
            'code' => $code,
            'message' => $message,
        ],
    ];
    
    if (!empty($data)) {
        $response['error']['data'] = $data;
    }
    
    return $response;
}

// Uso
if (is_wp_error($result)) {
    return new WP_REST_Response(
        standard_error_response(
            $result->get_error_code(),
            $result->get_error_message(),
            $result->get_error_data()
        ),
        $result->get_error_data()['status'] ?? 400
    );
}
```

---

## 📖 Documentação e Schema

### 2.13 Schema JSON e Documentação

### 2.13.1 Schema Validation Completo (OpenAPI/Swagger)

**Instalação:**

```bash
composer require zircote/swagger-php
```

**Gerar Documentação OpenAPI:**

```php
<?php
/**
 * @OA\Info(
 *     title="MyApp API",
 *     version="1.0.0",
 *     description="API completa do MyApp",
 *     @OA\Contact(
 *         email="api@myapp.com"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="https://seusite.com/wp-json/myapp/v1",
 *     description="Servidor de produção"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */

/**
 * @OA\Post(
 *     path="/products",
 *     summary="Criar produto",
 *     tags={"Products"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name", "price"},
 *             @OA\Property(property="name", type="string", example="Produto Exemplo"),
 *             @OA\Property(property="description", type="string", example="Descrição do produto"),
 *             @OA\Property(property="price", type="number", format="float", example=99.99, minimum=0),
 *             @OA\Property(property="category_id", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Produto criado com sucesso",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=123),
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="price", type="number")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Dados inválidos"),
 *     @OA\Response(response=401, description="Não autenticado")
 * )
 */
register_rest_route('myapp/v1', '/products', [
    'methods' => 'POST',
    'callback' => 'create_product',
    'permission_callback' => 'verify_jwt_permission',
    'args' => [
        'name' => [
            'required' => true,
            'type' => 'string',
            'minLength' => 3,
            'maxLength' => 255,
            'sanitize_callback' => 'sanitize_text_field',
            'validate_callback' => function($value) {
                return !empty($value) && strlen($value) >= 3;
            },
        ],
        'price' => [
            'required' => true,
            'type' => 'number',
            'minimum' => 0,
            'maximum' => 10000,
            'sanitize_callback' => 'floatval',
        ],
    ],
]);
```

**Endpoint para Gerar OpenAPI JSON:**

```php
<?php
register_rest_route('myapp/v1', '/openapi.json', [
    'methods' => 'GET',
    'callback' => function() {
        $openapi = \OpenApi\scan(__DIR__);
        return new WP_REST_Response(json_decode($openapi->toJson()), 200);
    },
    'permission_callback' => '__return_true',
]);
```

**Integração com Swagger UI:**

```html
<!DOCTYPE html>
<html>
<head>
    <title>API Documentation</title>
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/swagger-ui-dist@3/swagger-ui.css" />
</head>
<body>
    <div id="swagger-ui"></div>
    <script src="https://unpkg.com/swagger-ui-dist@3/swagger-ui-bundle.js"></script>
    <script>
        SwaggerUIBundle({
            url: '/wp-json/myapp/v1/openapi.json',
            dom_id: '#swagger-ui',
            presets: [
                SwaggerUIBundle.presets.apis,
                SwaggerUIBundle.presets.standalone
            ]
        });
    </script>
</body>
</html>
```

### 2.13 Schema JSON e Documentação

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

### 2.14 Modificar Respostas com Filters

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
```

### 2.14.1 rest_post_collection_params - Guia Completo

O filter `rest_post_collection_params` permite adicionar parâmetros customizados ao schema de endpoints de coleção (GET /wp-json/wp/v2/posts).

**Exemplo Completo:**

```php
<?php
/**
 * Adicionar parâmetros customizados ao endpoint de posts
 */
add_filter('rest_post_collection_params', function($params, $post_type) {
    // Adicionar parâmetro de busca por meta field
    $params['meta_key'] = [
        'description' => 'Filtrar por meta key específico',
        'type' => 'string',
        'sanitize_callback' => 'sanitize_key',
        'validate_callback' => function($value) {
            return !empty($value) && strlen($value) <= 255;
        },
    ];
    
    $params['meta_value'] = [
        'description' => 'Valor do meta field para filtrar',
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
    ];
    
    // Adicionar parâmetro de ordenação customizada
    $params['orderby_custom'] = [
        'description' => 'Ordenar por campo customizado',
        'type' => 'string',
        'enum' => ['price', 'rating', 'popularity'],
        'default' => 'price',
    ];
    
    // Adicionar parâmetro de filtro por categoria customizada
    $params['product_category'] = [
        'description' => 'Filtrar por categoria de produto',
        'type' => 'integer',
        'sanitize_callback' => 'absint',
        'validate_callback' => function($value) {
            return term_exists($value, 'product_category') !== null;
        },
    ];
    
    // Adicionar parâmetro de range de preço
    $params['price_min'] = [
        'description' => 'Preço mínimo',
        'type' => 'number',
        'minimum' => 0,
        'sanitize_callback' => 'floatval',
    ];
    
    $params['price_max'] = [
        'description' => 'Preço máximo',
        'type' => 'number',
        'minimum' => 0,
        'sanitize_callback' => 'floatval',
    ];
    
    return $params;
}, 10, 2);

/**
 * Usar os parâmetros customizados na query
 */
add_filter('rest_post_query', function($args, $request) {
    // Meta key/value
    if ($request->get_param('meta_key') && $request->get_param('meta_value')) {
        $args['meta_query'][] = [
            'key' => $request->get_param('meta_key'),
            'value' => $request->get_param('meta_value'),
            'compare' => '=',
        ];
    }
    
    // Orderby customizado
    if ($request->get_param('orderby_custom')) {
        $orderby = $request->get_param('orderby_custom');
        
        switch ($orderby) {
            case 'price':
                $args['meta_key'] = '_price';
                $args['orderby'] = 'meta_value_num';
                break;
            case 'rating':
                $args['meta_key'] = '_rating';
                $args['orderby'] = 'meta_value_num';
                break;
            case 'popularity':
                $args['orderby'] = 'comment_count';
                break;
        }
    }
    
    // Categoria customizada
    if ($request->get_param('product_category')) {
        $args['tax_query'][] = [
            'taxonomy' => 'product_category',
            'field' => 'term_id',
            'terms' => $request->get_param('product_category'),
        ];
    }
    
    // Range de preço
    if ($request->get_param('price_min') || $request->get_param('price_max')) {
        $meta_query = [
            'key' => '_price',
            'type' => 'NUMERIC',
        ];
        
        if ($request->get_param('price_min')) {
            $meta_query['value'] = $request->get_param('price_min');
            $meta_query['compare'] = '>=';
        }
        
        if ($request->get_param('price_max')) {
            if (isset($meta_query['value'])) {
                // Range completo
                $args['meta_query'][] = [
                    'key' => '_price',
                    'value' => [
                        $request->get_param('price_min'),
                        $request->get_param('price_max'),
                    ],
                    'type' => 'NUMERIC',
                    'compare' => 'BETWEEN',
                ];
            } else {
                $meta_query['value'] = $request->get_param('price_max');
                $meta_query['compare'] = '<=';
                $args['meta_query'][] = $meta_query;
            }
        } else {
            $args['meta_query'][] = $meta_query;
        }
    }
    
    return $args;
}, 10, 2);
```

**Uso:**

```bash
# Buscar posts com meta_key específico
GET /wp-json/wp/v2/posts?meta_key=_featured&meta_value=1

# Ordenar por preço
GET /wp-json/wp/v2/posts?orderby_custom=price&order=asc

# Filtrar por categoria
GET /wp-json/wp/v2/posts?product_category=5

# Filtrar por range de preço
GET /wp-json/wp/v2/posts?price_min=10&price_max=100
```

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
