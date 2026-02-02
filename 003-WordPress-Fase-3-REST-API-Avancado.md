# 🎯 FASE 3: REST API - Controllers e OOP

**Versão:** 1.0  
**Data:** Janeiro 2026  
**Nível:** Especialista em PHP  
**Status:** Conteúdo extraído e organizado  

---

**Navegação:** [Índice](000-WordPress-Indice-Topicos.md) | [← Fase 2](002-WordPress-Fase-2-REST-API-Fundamentos.md) | [Fase 4 →](004-WordPress-Fase-4-Configuracoes-Admin.md)

---

## 📑 Índice

1. [Conceitos Fundamentais](#conceitos-fundamentais)
2. [REST API Controllers](#rest-api-controllers)
3. [Resposta Estruturada](#resposta-estruturada)
4. [Validação e Sanitização](#validação-e-sanitização)
5. [Autenticação e Permissões](#autenticação-e-permissões)
6. [Tratamento de Erros](#tratamento-de-erros)
7. [Testes de API](#testes-de-api)
8. [Boas Práticas](#boas-práticas)

---

## 🎯 Objetivos de Aprendizado

Ao final desta fase, você será capaz de:

1. ✅ Construir controllers REST API complexos usando padrões de herança OOP
2. ✅ Estruturar respostas de API consistentemente usando wrappers de resposta
3. ✅ Implementar validação avançada com regras de validação customizadas
4. ✅ Criar sistemas reutilizáveis de autenticação e permissões
5. ✅ Tratar cenários de erro complexos com respostas de erro adequadas
6. ✅ Escrever testes abrangentes para endpoints da REST API
7. ✅ Aplicar boas práticas e padrões de design da REST API
8. ✅ Otimizar performance da API com cache e otimização de queries

## 📝 Autoavaliação

Teste seu entendimento:

- [ ] Como você estende `WP_REST_Controller` para criar controllers customizados?
- [ ] Qual é a diferença entre `WP_REST_Response` e `WP_Error`?
- [ ] Como você cria regras de validação customizadas além dos padrões do WordPress?
- [ ] Quais são as implicações de segurança de expor IDs internos do WordPress em APIs?
- [ ] Como você implementa rate limiting em endpoints da REST API?
- [ ] Qual é a forma adequada de tratar operações em lote na REST API?
- [ ] Como você testa endpoints da REST API programaticamente?
- [ ] Quais estratégias de cache são apropriadas para respostas da REST API?

## 🛠️ Projeto Prático

**Construir:** API Avançada de Gerenciamento de Blog

Crie uma REST API abrangente que:
- Estenda `WP_REST_Controller` para posts, comentários e usuários
- Implemente validação e sanitização customizadas
- Suporte operações em lote (criar/atualizar múltiplos recursos)
- Inclua rate limiting e cache
- Tenha cobertura de testes abrangente
- Siga boas práticas da REST API

**Tempo estimado:** 12-15 horas  
**Dificuldade:** Avançado

---

## ❌ Equívocos Comuns

### Equívoco 1: "Estender WP_REST_Controller é sempre necessário"
**Realidade:** Para endpoints simples, `register_rest_route()` com uma função callback é suficiente. Use controllers para endpoints complexos e reutilizáveis.

**Por que é importante:** Super-engenharia em endpoints simples adiciona complexidade desnecessária. Use a ferramenta certa para o trabalho.

**Como lembrar:** Endpoint simples = função callback. Complexo/reutilizável = classe Controller.

### Equívoco 2: "Operações em lote são apenas múltiplas requisições individuais"
**Realidade:** Operações em lote devem ser atômicas - ou todas têm sucesso ou todas falham. Elas também precisam de tratamento de erro adequado e mecanismos de rollback.

**Por que é importante:** Sem atomicidade, falhas parciais podem deixar dados em estados inconsistentes.

**Como lembrar:** Lote = "Tudo ou nada". Use transações para operações de banco de dados.

### Equívoco 3: "Rate limiting não é necessário para APIs autenticadas"
**Realidade:** Mesmo usuários autenticados podem abusar de APIs (intencionalmente ou acidentalmente). Rate limiting protege contra abuso e ataques DoS.

**Por que é importante:** Sem rate limiting, um único usuário ou conta comprometida pode sobrecarregar seu servidor.

**Como lembrar:** Rate limiting = proteção para todos, não apenas usuários não autenticados.

### Equívoco 4: "Cachear respostas da REST API é sempre seguro"
**Realidade:** Cachear dados específicos do usuário ou sensíveis ao tempo pode expor informações privadas ou servir dados desatualizados. Cache apenas dados públicos e não específicos do usuário.

**Por que é importante:** Cachear dados do usuário pode levar a violações de privacidade. Cachear dados sensíveis ao tempo pode causar comportamento incorreto.

**Como lembrar:** Cache = público + não específico do usuário + não sensível ao tempo.

---

## Conceitos Fundamentais

### O que é uma REST API?

Uma **REST API** (Representational State Transfer) permite que aplicações cliente se comuniquem com o servidor WordPress através de endpoints HTTP estruturados. No WordPress, a REST API:

- Expõe dados através de URLs previsíveis
- Usa métodos HTTP padrão: GET, POST, PUT, DELETE
- Retorna dados em JSON
- Oferece autenticação e autorização
- Permite manipular posts, usuários, taxonomias e mais

### Vantagens de usar REST API em vez de funções do WordPress

```php
// ❌ Abordagem procedural (função PHP básica)
function meu_plugin_obter_posts() {
    return get_posts(['numberposts' => 5]);
}
// Problema: Limitado ao contexto WordPress, sem documentação automática

// ✅ Abordagem REST API (Profissional)
class Meu_Plugin_Posts_Controller extends WP_REST_Controller {
    public function register_routes() {
        register_rest_route('meu-plugin/v1', '/posts', [
            'methods'  => 'GET',
            'callback' => [$this, 'get_items'],
            'permission_callback' => [$this, 'get_items_permissions_check'],
        ]);
    }
}
// Vantagens: Documentação automática, segurança integrada, escalável
```

---

## REST API Controllers

### Estrutura Base de um Controller

```php
<?php
/**
 * REST API Controller para Posts
 * 
 * Estende WP_REST_Controller para criar endpoints profissionais
 * com validação, sanitização e permissões integradas
 */

class Meu_Plugin_Posts_Controller extends WP_REST_Controller {
    
    /**
     * Namespace da API
     * 
     * @var string
     */
    protected $namespace = 'meu-plugin/v1';
    
    /**
     * Base do recurso
     * 
     * @var string
     */
    protected $rest_base = 'posts';
    
    /**
     * Construtor
     */
    public function __construct() {
        $this->register_hooks();
    }
    
    /**
     * Registrar hooks e rotas
     */
    public function register_hooks() {
        add_action('rest_api_init', [$this, 'register_routes']);
    }
    
    /**
     * Registrar as rotas REST
     */
    public function register_routes() {
        // GET /meu-plugin/v1/posts
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            [
                'methods'  => 'GET',
                'callback' => [$this, 'get_items'],
                'permission_callback' => [$this, 'get_items_permissions_check'],
                'args' => $this->get_collection_params(),
            ]
        );
        
        // GET /meu-plugin/v1/posts/123
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/(?P<id>\d+)',
            [
                'methods'  => 'GET',
                'callback' => [$this, 'get_item'],
                'permission_callback' => [$this, 'get_item_permissions_check'],
                'args' => [
                    'id' => [
                        'description' => 'ID único do post',
                        'type' => 'integer',
                        'required' => true,
                    ],
                ],
            ]
        );
        
        // POST /meu-plugin/v1/posts
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            [
                'methods'  => 'POST',
                'callback' => [$this, 'create_item'],
                'permission_callback' => [$this, 'create_item_permissions_check'],
                'args' => $this->get_endpoint_args_for_item_schema('POST'),
            ]
        );
        
        // PUT /meu-plugin/v1/posts/123
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/(?P<id>\d+)',
            [
                'methods'  => 'PUT',
                'callback' => [$this, 'update_item'],
                'permission_callback' => [$this, 'update_item_permissions_check'],
                'args' => $this->get_endpoint_args_for_item_schema('POST'),
            ]
        );
        
        // DELETE /meu-plugin/v1/posts/123
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/(?P<id>\d+)',
            [
                'methods'  => 'DELETE',
                'callback' => [$this, 'delete_item'],
                'permission_callback' => [$this, 'delete_item_permissions_check'],
            ]
        );
    }
    
    /**
     * GET - Obter múltiplos posts
     * 
     * @param WP_REST_Request $request Request com parâmetros
     * @return WP_REST_Response
     */
    public function get_items($request) {
        $params = $request->get_json_params();
        
        $args = [
            'numberposts' => absint($request->get_param('per_page')) ?? 10,
            'paged'       => absint($request->get_param('page')) ?? 1,
            'orderby'     => sanitize_text_field($request->get_param('orderby')) ?? 'date',
            'order'       => strtoupper(sanitize_text_field($request->get_param('order'))) ?? 'DESC',
        ];
        
        $posts = get_posts($args);
        $items = array_map([$this, 'prepare_item_for_response'], $posts);
        
        return new WP_REST_Response([
            'success' => true,
            'data'    => $items,
            'count'   => count($items),
        ], 200);
    }
    
    /**
     * GET - Obter um post específico
     * 
     * @param WP_REST_Request $request Request com ID do post
     * @return WP_REST_Response|WP_Error
     */
    public function get_item($request) {
        $post_id = absint($request->get_param('id'));
        
        $post = get_post($post_id);
        
        if (!$post) {
            return new WP_Error(
                'rest_post_not_found',
                'Post não encontrado',
                ['status' => 404]
            );
        }
        
        $item = $this->prepare_item_for_response($post);
        
        return new WP_REST_Response([
            'success' => true,
            'data'    => $item,
        ], 200);
    }
    
    /**
     * POST - Criar novo post
     * 
     * @param WP_REST_Request $request Request com dados do post
     * @return WP_REST_Response|WP_Error
     */
    public function create_item($request) {
        $params = $request->get_json_params();
        
        // Validar dados
        $validation = $this->validate_post_data($params);
        if (is_wp_error($validation)) {
            return $validation;
        }
        
        // Sanitizar dados
        $post_data = [
            'post_title'   => sanitize_text_field($params['title']),
            'post_content' => wp_kses_post($params['content']),
            'post_status'  => 'draft',
            'post_author'  => get_current_user_id(),
        ];
        
        $post_id = wp_insert_post($post_data);
        
        if (is_wp_error($post_id)) {
            return new WP_Error(
                'rest_create_post_failed',
                'Falha ao criar post: ' . $post_id->get_error_message(),
                ['status' => 400]
            );
        }
        
        $post = get_post($post_id);
        $item = $this->prepare_item_for_response($post);
        
        return new WP_REST_Response([
            'success' => true,
            'data'    => $item,
            'message' => 'Post criado com sucesso',
        ], 201);
    }
    
    /**
     * PUT - Atualizar post existente
     * 
     * @param WP_REST_Request $request Request com dados atualizados
     * @return WP_REST_Response|WP_Error
     */
    public function update_item($request) {
        $post_id = absint($request->get_param('id'));
        $params = $request->get_json_params();
        
        // Verificar se post existe
        $post = get_post($post_id);
        if (!$post) {
            return new WP_Error(
                'rest_post_not_found',
                'Post não encontrado',
                ['status' => 404]
            );
        }
        
        // Validar dados
        $validation = $this->validate_post_data($params, true);
        if (is_wp_error($validation)) {
            return $validation;
        }
        
        // Sanitizar dados
        $post_data = [
            'ID' => $post_id,
        ];
        
        if (isset($params['title'])) {
            $post_data['post_title'] = sanitize_text_field($params['title']);
        }
        
        if (isset($params['content'])) {
            $post_data['post_content'] = wp_kses_post($params['content']);
        }
        
        $updated = wp_update_post($post_data);
        
        if (is_wp_error($updated)) {
            return new WP_Error(
                'rest_update_post_failed',
                'Falha ao atualizar post',
                ['status' => 400]
            );
        }
        
        $post = get_post($post_id);
        $item = $this->prepare_item_for_response($post);
        
        return new WP_REST_Response([
            'success' => true,
            'data'    => $item,
            'message' => 'Post atualizado com sucesso',
        ], 200);
    }
    
    /**
     * DELETE - Deletar post
     * 
     * @param WP_REST_Request $request Request com ID do post
     * @return WP_REST_Response|WP_Error
     */
    public function delete_item($request) {
        $post_id = absint($request->get_param('id'));
        
        $post = get_post($post_id);
        if (!$post) {
            return new WP_Error(
                'rest_post_not_found',
                'Post não encontrado',
                ['status' => 404]
            );
        }
        
        $deleted = wp_delete_post($post_id, true);
        
        if (!$deleted) {
            return new WP_Error(
                'rest_delete_post_failed',
                'Falha ao deletar post',
                ['status' => 400]
            );
        }
        
        return new WP_REST_Response([
            'success' => true,
            'message' => 'Post deletado com sucesso',
        ], 200);
    }
    
    /**
     * Preparar post para resposta JSON
     * 
     * @param WP_Post $post Post object
     * @return array
     */
    public function prepare_item_for_response($post) {
        return [
            'id'      => $post->ID,
            'title'   => $post->post_title,
            'content' => $post->post_content,
            'excerpt' => $post->post_excerpt,
            'status'  => $post->post_status,
            'author'  => $post->post_author,
            'date'    => $post->post_date,
            'modified' => $post->post_modified,
        ];
    }
    
    /**
     * Permissão: GET (lista de posts)
     * 
     * @param WP_REST_Request $request
     * @return bool|WP_Error
     */
    public function get_items_permissions_check($request) {
        // Posts públicos podem ser acessados por qualquer um
        return true;
    }
    
    /**
     * Permissão: GET (post específico)
     * 
     * @param WP_REST_Request $request
     * @return bool|WP_Error
     */
    public function get_item_permissions_check($request) {
        return true;
    }
    
    /**
     * Permissão: POST (criar post)
     * 
     * @param WP_REST_Request $request
     * @return bool|WP_Error
     */
    public function create_item_permissions_check($request) {
        if (!is_user_logged_in()) {
            return new WP_Error(
                'rest_forbidden',
                'Você precisa estar autenticado para criar posts',
                ['status' => 403]
            );
        }
        
        if (!current_user_can('edit_posts')) {
            return new WP_Error(
                'rest_forbidden',
                'Você não tem permissão para criar posts',
                ['status' => 403]
            );
        }
        
        return true;
    }
    
    /**
     * Permissão: PUT (atualizar post)
     * 
     * @param WP_REST_Request $request
     * @return bool|WP_Error
     */
    public function update_item_permissions_check($request) {
        $post_id = absint($request->get_param('id'));
        
        if (!is_user_logged_in()) {
            return new WP_Error(
                'rest_forbidden',
                'Você precisa estar autenticado',
                ['status' => 403]
            );
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return new WP_Error(
                'rest_forbidden',
                'Você não tem permissão para editar este post',
                ['status' => 403]
            );
        }
        
        return true;
    }
    
    /**
     * Permissão: DELETE (deletar post)
     * 
     * @param WP_REST_Request $request
     * @return bool|WP_Error
     */
    public function delete_item_permissions_check($request) {
        $post_id = absint($request->get_param('id'));
        
        if (!is_user_logged_in()) {
            return new WP_Error(
                'rest_forbidden',
                'Você precisa estar autenticado',
                ['status' => 403]
            );
        }
        
        if (!current_user_can('delete_post', $post_id)) {
            return new WP_Error(
                'rest_forbidden',
                'Você não tem permissão para deletar este post',
                ['status' => 403]
            );
        }
        
        return true;
    }
    
    /**
     * Parâmetros de query para coleção
     * 
     * @return array
     */
    public function get_collection_params() {
        return [
            'page' => [
                'description' => 'Página atual',
                'type' => 'integer',
                'default' => 1,
            ],
            'per_page' => [
                'description' => 'Itens por página',
                'type' => 'integer',
                'default' => 10,
            ],
            'orderby' => [
                'description' => 'Campo para ordenação',
                'type' => 'string',
                'enum' => ['date', 'title', 'author'],
                'default' => 'date',
            ],
            'order' => [
                'description' => 'Ordem de classificação',
                'type' => 'string',
                'enum' => ['ASC', 'DESC'],
                'default' => 'DESC',
            ],
        ];
    }
    
    /**
     * Validar dados do post
     * 
     * @param array $data Dados para validar
     * @param bool $is_update Se é uma atualização
     * @return true|WP_Error
     */
    public function validate_post_data($data, $is_update = false) {
        if (!$is_update && empty($data['title'])) {
            return new WP_Error(
                'rest_missing_title',
                'O título é obrigatório',
                ['status' => 400]
            );
        }
        
        if (!$is_update && empty($data['content'])) {
            return new WP_Error(
                'rest_missing_content',
                'O conteúdo é obrigatório',
                ['status' => 400]
            );
        }
        
        if (isset($data['title']) && strlen($data['title']) > 255) {
            return new WP_Error(
                'rest_title_too_long',
                'O título não pode ter mais de 255 caracteres',
                ['status' => 400]
            );
        }
        
        return true;
    }
}

// Instanciar o controller
new Meu_Plugin_Posts_Controller();
```

---

## Resposta Estruturada

### Padrão de Resposta Consistente

```php
// ✅ Resposta bem-estruturada
return new WP_REST_Response([
    'success'  => true,
    'data'     => $post_data,
    'message'  => 'Post criado com sucesso',
    'code'     => 'rest_post_created',
    'meta'     => [
        'timestamp' => current_time('mysql'),
        'version'   => '1.0',
    ],
], 201);

// ❌ Resposta mal-estruturada (evitar)
return $post_array;  // Sem contexto, sem mensagem de sucesso
```

### Estrutura Completa de Resposta

```php
/**
 * Classe para padronizar respostas
 */
class Meu_Plugin_REST_Response {
    
    /**
     * Resposta de sucesso
     */
    public static function success($data, $message = '', $status = 200) {
        return new WP_REST_Response([
            'success'  => true,
            'data'     => $data,
            'message'  => $message,
            'code'     => 'rest_success',
            'meta'     => [
                'timestamp' => current_time('mysql'),
                'status'    => $status,
            ],
        ], $status);
    }
    
    /**
     * Resposta de erro
     */
    public static function error($code, $message, $status = 400) {
        return new WP_Error(
            $code,
            $message,
            [
                'status'    => $status,
                'timestamp' => current_time('mysql'),
            ]
        );
    }
    
    /**
     * Resposta de validação com erros
     */
    public static function validation_error($errors) {
        return new WP_Error(
            'rest_validation_failed',
            'Validação falhou',
            [
                'status' => 400,
                'errors' => $errors,
                'timestamp' => current_time('mysql'),
            ]
        );
    }
    
    /**
     * Resposta de lista paginada
     */
    public static function paginated_list($items, $total, $page, $per_page) {
        return new WP_REST_Response([
            'success'  => true,
            'data'     => $items,
            'pagination' => [
                'total'        => $total,
                'current_page' => $page,
                'per_page'     => $per_page,
                'total_pages'  => ceil($total / $per_page),
            ],
            'meta' => [
                'timestamp' => current_time('mysql'),
            ],
        ], 200);
    }
}
```

---

## Validação e Sanitização

### Validação de Entrada (Input Validation)

```php
/**
 * Validar diferentes tipos de dados
 */
class Meu_Plugin_Validator {
    
    /**
     * Validar email
     */
    public static function validate_email($email) {
        if (!is_email($email)) {
            return new WP_Error(
                'invalid_email',
                'Email inválido',
                ['status' => 400]
            );
        }
        return true;
    }
    
    /**
     * Validar URL
     */
    public static function validate_url($url) {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return new WP_Error(
                'invalid_url',
                'URL inválida',
                ['status' => 400]
            );
        }
        return true;
    }
    
    /**
     * Validar número inteiro
     */
    public static function validate_integer($value, $min = null, $max = null) {
        if (!is_numeric($value) || intval($value) != $value) {
            return new WP_Error(
                'invalid_integer',
                'Deve ser um número inteiro',
                ['status' => 400]
            );
        }
        
        $int_value = intval($value);
        
        if ($min !== null && $int_value < $min) {
            return new WP_Error(
                'value_too_small',
                "O valor deve ser no mínimo $min",
                ['status' => 400]
            );
        }
        
        if ($max !== null && $int_value > $max) {
            return new WP_Error(
                'value_too_large',
                "O valor deve ser no máximo $max",
                ['status' => 400]
            );
        }
        
        return true;
    }
    
    /**
     * Validar string com comprimento
     */
    public static function validate_string($value, $min_length = 0, $max_length = null) {
        if (!is_string($value)) {
            return new WP_Error(
                'invalid_string',
                'Deve ser uma string',
                ['status' => 400]
            );
        }
        
        $length = strlen($value);
        
        if ($length < $min_length) {
            return new WP_Error(
                'string_too_short',
                "Deve ter no mínimo $min_length caracteres",
                ['status' => 400]
            );
        }
        
        if ($max_length && $length > $max_length) {
            return new WP_Error(
                'string_too_long',
                "Deve ter no máximo $max_length caracteres",
                ['status' => 400]
            );
        }
        
        return true;
    }
    
    /**
     * Validar opções permitidas (enum)
     */
    public static function validate_enum($value, $allowed_values) {
        if (!in_array($value, $allowed_values, true)) {
            return new WP_Error(
                'invalid_enum',
                'Valor inválido. Valores permitidos: ' . implode(', ', $allowed_values),
                ['status' => 400]
            );
        }
        return true;
    }
}
```

### Sanitização de Saída (Output Escaping)

```php
/**
 * Classe para padronizar sanitização
 */
class Meu_Plugin_Sanitizer {
    
    /**
     * Sanitizar texto para HTML (remover scripts)
     */
    public static function sanitize_text($text) {
        return wp_kses_post($text);
    }
    
    /**
     * Sanitizar para uso em HTML
     */
    public static function escape_html($text) {
        return esc_html($text);
    }
    
    /**
     * Sanitizar URL
     */
    public static function sanitize_url($url) {
        return esc_url($url);
    }
    
    /**
     * Sanitizar para JavaScript
     */
    public static function sanitize_js($data) {
        return wp_json_encode($data);
    }
    
    /**
     * Sanitizar dados para API
     */
    public static function prepare_api_response($data) {
        if (is_array($data)) {
            return array_map([self::class, 'prepare_api_response'], $data);
        }
        
        if (is_object($data)) {
            return (array) $data;
        }
        
        if (is_string($data)) {
            return esc_html($data);
        }
        
        return $data;
    }
}
```

---

## Autenticação e Permissões

### Autenticação JWT (JSON Web Token)

```php
/**
 * Sistema de autenticação JWT
 */
class Meu_Plugin_JWT_Auth {
    
    private $secret_key;
    private $token_expiration = 3600; // 1 hora
    
    public function __construct() {
        $this->secret_key = defined('JWT_SECRET') ? JWT_SECRET : wp_salt('auth');
        $this->register_hooks();
    }
    
    public function register_hooks() {
        add_action('rest_api_init', [$this, 'register_auth_endpoint']);
        add_filter('rest_pre_dispatch', [$this, 'validate_token'], 10, 3);
    }
    
    /**
     * Endpoint para gerar token
     */
    public function register_auth_endpoint() {
        register_rest_route('meu-plugin/v1', '/auth/login', [
            'methods'  => 'POST',
            'callback' => [$this, 'login'],
            'permission_callback' => '__return_true',
        ]);
        
        register_rest_route('meu-plugin/v1', '/auth/refresh', [
            'methods'  => 'POST',
            'callback' => [$this, 'refresh_token'],
            'permission_callback' => [$this, 'auth_required'],
        ]);
    }
    
    /**
     * Login e geração de token
     */
    public function login($request) {
        $body = $request->get_json_params();
        
        $user = wp_authenticate($body['username'], $body['password']);
        
        if (is_wp_error($user)) {
            return new WP_Error(
                'rest_auth_failed',
                'Autenticação falhou',
                ['status' => 401]
            );
        }
        
        $token = $this->generate_token($user->ID);
        
        return new WP_REST_Response([
            'success' => true,
            'token'   => $token,
            'user'    => [
                'id'   => $user->ID,
                'name' => $user->display_name,
                'email' => $user->user_email,
            ],
        ], 200);
    }
    
    /**
     * Gerar JWT token
     */
    public function generate_token($user_id) {
        $issued_at = time();
        $expire = $issued_at + $this->token_expiration;
        
        $payload = [
            'iss'  => get_bloginfo('url'),
            'aud'  => get_bloginfo('url'),
            'iat'  => $issued_at,
            'exp'  => $expire,
            'data' => [
                'user_id' => $user_id,
            ],
        ];
        
        return $this->encode_token($payload);
    }
    
    /**
     * Codificar payload em JWT
     */
    private function encode_token($payload) {
        $header = $this->base64url_encode(wp_json_encode([
            'typ' => 'JWT',
            'alg' => 'HS256',
        ]));
        
        $payload_encoded = $this->base64url_encode(wp_json_encode($payload));
        
        $signature = hash_hmac(
            'sha256',
            "$header.$payload_encoded",
            $this->secret_key,
            true
        );
        
        $signature_encoded = $this->base64url_encode($signature);
        
        return "$header.$payload_encoded.$signature_encoded";
    }
    
    /**
     * Decodificar e validar token
     */
    public function validate_token($dispatch, $server, $request) {
        $token = $this->get_token_from_request($request);
        
        if (!$token) {
            return $dispatch;
        }
        
        $payload = $this->decode_token($token);
        
        if (is_wp_error($payload)) {
            return $payload;
        }
        
        // Token válido, continuar
        return $dispatch;
    }
    
    /**
     * Decodificar JWT
     */
    private function decode_token($token) {
        $parts = explode('.', $token);
        
        if (count($parts) !== 3) {
            return new WP_Error(
                'invalid_token',
                'Token inválido',
                ['status' => 401]
            );
        }
        
        [$header, $payload, $signature] = $parts;
        
        // Verificar assinatura
        $valid_signature = hash_hmac(
            'sha256',
            "$header.$payload",
            $this->secret_key,
            true
        );
        
        if (!hash_equals($this->base64url_encode($valid_signature), $signature)) {
            return new WP_Error(
                'invalid_signature',
                'Assinatura do token inválida',
                ['status' => 401]
            );
        }
        
        // Decodificar payload
        $decoded = json_decode($this->base64url_decode($payload), true);
        
        // Verificar expiração
        if ($decoded['exp'] < time()) {
            return new WP_Error(
                'token_expired',
                'Token expirou',
                ['status' => 401]
            );
        }
        
        return $decoded;
    }
    
    /**
     * Extrair token do header Authorization
     */
    private function get_token_from_request($request) {
        $auth_header = $request->get_header('Authorization');
        
        if (!$auth_header) {
            return null;
        }
        
        if (strpos($auth_header, 'Bearer ') !== 0) {
            return null;
        }
        
        return substr($auth_header, 7);
    }
    
    /**
     * Verificar autenticação
     */
    public function auth_required() {
        if (!is_user_logged_in()) {
            return new WP_Error(
                'rest_unauthorized',
                'Autenticação necessária',
                ['status' => 401]
            );
        }
        return true;
    }
    
    /**
     * Base64 URL encode
     */
    private function base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    /**
     * Base64 URL decode
     */
    private function base64url_decode($data) {
        return base64_decode(
            strtr($data, '-_', '+/') . str_repeat('=', 4 - strlen($data) % 4),
            true
        );
    }
}

new Meu_Plugin_JWT_Auth();
```

---

## Tratamento de Erros

### Error Handling Avançado em Controllers

**Padrão 1: Exception Handling em Controllers**

```php
<?php
/**
 * Controller com tratamento robusto de erros
 */
class Product_Controller extends WP_REST_Controller {
    
    public function create_item($request) {
        try {
            // Validação
            $validation = $this->validate_product_data($request);
            if (is_wp_error($validation)) {
                return $validation;
            }
            
            // Processamento
            $product_id = $this->create_product($request);
            
            if (is_wp_error($product_id)) {
                return $product_id;
            }
            
            // Sucesso
            return new WP_REST_Response([
                'id' => $product_id,
                'message' => 'Produto criado com sucesso',
            ], 201);
            
        } catch (InvalidArgumentException $e) {
            // Erro de validação
            return new WP_Error(
                'invalid_argument',
                $e->getMessage(),
                ['status' => 400]
            );
            
        } catch (DatabaseException $e) {
            // Erro de banco de dados
            error_log('Database error: ' . $e->getMessage());
            
            return new WP_Error(
                'database_error',
                'Erro ao salvar dados',
                ['status' => 500]
            );
            
        } catch (Exception $e) {
            // Erro genérico
            error_log('Unexpected error: ' . $e->getMessage());
            
            return new WP_Error(
                'server_error',
                'Erro ao processar requisição',
                ['status' => 500]
            );
        }
    }
    
    private function validate_product_data($request) {
        $name = $request->get_param('name');
        if (empty($name)) {
            throw new InvalidArgumentException('Nome é obrigatório');
        }
        
        $price = $request->get_param('price');
        if (!is_numeric($price) || $price < 0) {
            throw new InvalidArgumentException('Preço inválido');
        }
        
        return true;
    }
}
```

**Padrão 2: Error Handler Centralizado com Logging**

```php
<?php
/**
 * Error Handler centralizado para REST API
 */
class REST_API_Error_Handler {
    
    private static $error_logger = null;
    
    /**
     * Registrar error handler
     */
    public static function register() {
        add_filter('rest_pre_dispatch', [self::class, 'handle_errors'], 10, 3);
        add_action('rest_api_init', [self::class, 'register_error_routes']);
    }
    
    /**
     * Tratar erros antes do dispatch
     */
    public static function handle_errors($result, $server, $request) {
        if (is_wp_error($result)) {
            self::log_error($result, $request);
            return self::format_error_response($result);
        }
        
        return $result;
    }
    
    /**
     * Logar erro com contexto
     */
    private static function log_error(WP_Error $error, $request) {
        $context = [
            'error_code' => $error->get_error_code(),
            'error_message' => $error->get_error_message(),
            'error_data' => $error->get_error_data(),
            'request_method' => $request->get_method(),
            'request_route' => $request->get_route(),
            'request_params' => $request->get_params(),
            'user_id' => get_current_user_id(),
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'timestamp' => current_time('mysql'),
        ];
        
        // Log estruturado
        error_log(sprintf(
            '[REST API Error] %s: %s | Route: %s | Method: %s | User: %d',
            $error->get_error_code(),
            $error->get_error_message(),
            $request->get_route(),
            $request->get_method(),
            get_current_user_id()
        ));
        
        // Enviar para serviço de monitoramento (Sentry, etc.)
        if (function_exists('Sentry\\captureException')) {
            Sentry\captureMessage(
                $error->get_error_message(),
                \Sentry\Severity::error(),
                ['extra' => $context]
            );
        }
    }
    
    /**
     * Formatar resposta de erro
     */
    private static function format_error_response(WP_Error $error) {
        $status = $error->get_error_data()['status'] ?? 500;
        $error_data = $error->get_error_data();
        
        $response = [
            'code' => $error->get_error_code(),
            'message' => $error->get_error_message(),
            'data' => [
                'status' => $status,
            ],
        ];
        
        // Adicionar detalhes se disponíveis
        if (isset($error_data['errors'])) {
            $response['data']['errors'] = $error_data['errors'];
        }
        
        // Adicionar debug info apenas em desenvolvimento
        if (WP_DEBUG && isset($error_data['debug'])) {
            $response['data']['debug'] = $error_data['debug'];
        }
        
        return new WP_REST_Response($response, $status);
    }
    
    /**
     * Criar erro de validação
     */
    public static function validation_error(array $errors) {
        return new WP_Error(
            'validation_failed',
            'Dados de entrada inválidos',
            [
                'status' => 422,
                'errors' => $errors,
            ]
        );
    }
    
    /**
     * Criar erro de não encontrado
     */
    public static function not_found($resource_type, $id = null) {
        $message = $id 
            ? sprintf('%s com ID %s não encontrado', $resource_type, $id)
            : sprintf('%s não encontrado', $resource_type);
            
        return new WP_Error(
            'not_found',
            $message,
            [
                'status' => 404,
                'resource_type' => $resource_type,
                'id' => $id,
            ]
        );
    }
    
    /**
     * Criar erro de permissão
     */
    public static function forbidden($action = null) {
        $message = $action 
            ? sprintf('Você não tem permissão para %s', $action)
            : 'Você não tem permissão para realizar esta ação';
            
        return new WP_Error(
            'forbidden',
            $message,
            ['status' => 403]
        );
    }
}

// Registrar
REST_API_Error_Handler::register();
```

**Padrão 3: Retry Logic para Operações Transientes**

```php
<?php
/**
 * Retry logic para operações que podem falhar temporariamente
 */
class Retryable_Operation {
    
    private $max_attempts;
    private $delay_ms;
    
    public function __construct(int $max_attempts = 3, int $delay_ms = 1000) {
        $this->max_attempts = $max_attempts;
        $this->delay_ms = $delay_ms;
    }
    
    /**
     * Executar operação com retry
     */
    public function execute(callable $operation, callable $should_retry = null) {
        $attempt = 0;
        $last_error = null;
        
        while ($attempt < $this->max_attempts) {
            try {
                $result = $operation();
                
                // Se não é WP_Error, sucesso
                if (!is_wp_error($result)) {
                    return $result;
                }
                
                $last_error = $result;
                
                // Verificar se deve tentar novamente
                if ($should_retry && !$should_retry($result)) {
                    return $result;
                }
                
                // Erros permanentes não devem ser retentados
                $error_data = $result->get_error_data();
                $status = $error_data['status'] ?? 500;
                
                if (in_array($status, [400, 401, 403, 404, 422])) {
                    return $result; // Erro permanente, não retentar
                }
                
            } catch (Exception $e) {
                $last_error = new WP_Error(
                    'exception',
                    $e->getMessage(),
                    ['status' => 500]
                );
            }
            
            $attempt++;
            
            // Aguardar antes de tentar novamente (exponential backoff)
            if ($attempt < $this->max_attempts) {
                $delay = $this->delay_ms * pow(2, $attempt - 1);
                usleep($delay * 1000); // Converter para microsegundos
            }
        }
        
        return $last_error;
    }
}

// Uso
$retryable = new Retryable_Operation(3, 1000);

$result = $retryable->execute(function() {
    return wp_remote_post('https://api.example.com/webhook', [
        'body' => json_encode(['data' => 'test']),
        'timeout' => 5,
    ]);
}, function($error) {
    // Retentar apenas se for erro de rede/timeout
    $code = $error->get_error_code();
    return in_array($code, ['http_request_failed', 'timeout']);
});
```

**Padrão 4: Error Recovery e Fallbacks**

```php
<?php
/**
 * Error recovery com fallbacks
 */
class Resilient_API_Operation {
    
    /**
     * Executar operação com fallback
     */
    public static function execute_with_fallback(
        callable $primary_operation,
        callable $fallback_operation,
        callable $is_recoverable = null
    ) {
        $result = $primary_operation();
        
        // Se sucesso, retornar
        if (!is_wp_error($result)) {
            return $result;
        }
        
        // Verificar se erro é recuperável
        if ($is_recoverable && !$is_recoverable($result)) {
            return $result; // Erro não recuperável
        }
        
        // Tentar fallback
        error_log('Primary operation failed, using fallback: ' . $result->get_error_message());
        
        $fallback_result = $fallback_operation();
        
        if (is_wp_error($fallback_result)) {
            // Ambos falharam
            return new WP_Error(
                'operation_failed',
                'Operação principal e fallback falharam',
                [
                    'status' => 500,
                    'primary_error' => $result,
                    'fallback_error' => $fallback_result,
                ]
            );
        }
        
        // Fallback funcionou
        return $fallback_result;
    }
}

// Exemplo: Cache com fallback para database
$products = Resilient_API_Operation::execute_with_fallback(
    // Operação principal: buscar do cache
    function() {
        $cached = wp_cache_get('products_list', 'products');
        if ($cached !== false) {
            return $cached;
        }
        return new WP_Error('cache_miss', 'Cache miss');
    },
    // Fallback: buscar do database
    function() {
        $products = get_posts(['post_type' => 'product', 'posts_per_page' => 100]);
        wp_cache_set('products_list', $products, 'products', 3600);
        return $products;
    },
    // Verificar se erro é recuperável (cache miss sempre é)
    function($error) {
        return $error->get_error_code() === 'cache_miss';
    }
);
```

---

## Testes de API

### Testar Endpoints com cURL

```bash
# GET - Listar posts
curl -X GET "http://localhost/wp-json/meu-plugin/v1/posts" \
  -H "Content-Type: application/json"

# GET - Post específico
curl -X GET "http://localhost/wp-json/meu-plugin/v1/posts/123" \
  -H "Content-Type: application/json"

# POST - Criar post
curl -X POST "http://localhost/wp-json/meu-plugin/v1/posts" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Novo Post",
    "content": "Conteúdo do post"
  }'

# PUT - Atualizar post
curl -X PUT "http://localhost/wp-json/meu-plugin/v1/posts/123" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Título Atualizado"
  }'

# DELETE - Deletar post
curl -X DELETE "http://localhost/wp-json/meu-plugin/v1/posts/123" \
  -H "Content-Type: application/json"

# LOGIN - Gerar token
curl -X POST "http://localhost/wp-json/meu-plugin/v1/auth/login" \
  -H "Content-Type: application/json" \
  -d '{
    "username": "admin",
    "password": "senha123"
  }'

# Usar token
curl -X GET "http://localhost/wp-json/meu-plugin/v1/posts" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer eyJhbGc..."
```

### Testes com PHPUnit

```php
<?php
/**
 * Testes para REST API Controller
 */

class Test_REST_API_Controller extends WP_UnitTestCase {
    
    protected $server;
    
    public function setUp(): void {
        parent::setUp();
        $this->server = rest_get_server();
    }
    
    /**
     * Testar GET /posts
     */
    public function test_get_posts() {
        $request = new WP_REST_Request('GET', '/meu-plugin/v1/posts');
        
        $response = $this->server->dispatch($request);
        
        $this->assertEquals(200, $response->get_status());
        $this->assertTrue($response->get_data()['success']);
    }
    
    /**
     * Testar GET /posts/123
     */
    public function test_get_single_post() {
        $post_id = $this->factory->post->create();
        
        $request = new WP_REST_Request('GET', "/meu-plugin/v1/posts/$post_id");
        
        $response = $this->server->dispatch($request);
        
        $this->assertEquals(200, $response->get_status());
        $this->assertEquals($post_id, $response->get_data()['data']['id']);
    }
    
    /**
     * Testar POST /posts
     */
    public function test_create_post() {
        $user_id = $this->factory->user->create(['role' => 'editor']);
        wp_set_current_user($user_id);
        
        $request = new WP_REST_Request('POST', '/meu-plugin/v1/posts');
        $request->set_json_params([
            'title'   => 'Test Post',
            'content' => 'Test content',
        ]);
        
        $response = $this->server->dispatch($request);
        
        $this->assertEquals(201, $response->get_status());
        $this->assertTrue($response->get_data()['success']);
    }
    
    /**
     * Testar autenticação
     */
    public function test_create_post_without_auth() {
        wp_set_current_user(0);
        
        $request = new WP_REST_Request('POST', '/meu-plugin/v1/posts');
        $request->set_json_params([
            'title'   => 'Test',
            'content' => 'Test',
        ]);
        
        $response = $this->server->dispatch($request);
        
        $this->assertEquals(403, $response->get_status());
    }
}
```

---

## Boas Práticas

### Checklist de Qualidade

- ✅ Usar `WP_REST_Controller` como base
- ✅ Implementar `permission_callback` em todas as rotas
- ✅ Validar e sanitizar TODOS os dados de entrada
- ✅ Retornar respostas estruturadas com status HTTP correto
- ✅ Usar nonces para proteção CSRF
- ✅ Implementar rate limiting
- ✅ Documentar endpoints com parâmetros
- ✅ Usar versionamento de API (`/v1/`, `/v2/`)
- ✅ Testar todas as permissões
- ✅ Logs para debugging e auditoria
- ✅ Tratamento de erros completo
- ✅ Suportar CORS quando necessário

### Estrutura de Projeto Recomendada

```
meu-plugin/
├── src/
│   ├── REST/
│   │   ├── Controllers/
│   │   │   └── Posts_Controller.php
│   │   └── Validators/
│   │       └── Post_Validator.php
│   ├── Services/
│   │   └── Post_Service.php
│   └── Traits/
│       └── Response_Trait.php
├── tests/
│   └── REST/
│       └── test-posts-controller.php
├── composer.json
└── meu-plugin.php
```

---

**Versão:** 1.0  
**Atualizado:** Janeiro 2026  
**Próxima fase:** Fase 4 - REST API Avançada (Customizações, Extensões, Performance)
