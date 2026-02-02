# 📝 Testing Throughout - Integrando Testes em Cada Fase

**Versão:** 1.0  
**Data:** Fevereiro 2026  
**Nível:** Especialista em PHP  
**Objetivo:** Aprender testes junto com cada tópico, não esperar até a Fase 10

---

**Navegação:** [Índice](./000-WordPress-Indice-Topicos.md) | [← Fase 16](./016-WordPress-Fase-16-Jobs-Assincronos-Background.md) | [Fase 10 →](./010-WordPress-Fase-10-Testes-Debug-Implantacao.md)

---

## 📑 Índice

1. [Por Que Testing Throughout?](#por-que-testing-throughout)
2. [Setup Básico de Testes](#setup-basico-de-testes)
3. [Fase 1: Testando Hook System](#fase-1-testando-hook-system)
4. [Fase 2: Testando REST API](#fase-2-testando-rest-api)
5. [Fase 3: Testando REST API Avançado](#fase-3-testando-rest-api-avancado)
6. [Fase 4: Testando Settings API](#fase-4-testando-settings-api)
7. [Fase 5: Testando Custom Post Types](#fase-5-testando-custom-post-types)
8. [Fase 6: Testando Shortcodes e Blocks](#fase-6-testando-shortcodes-e-blocks)
9. [Fase 7: Testando WP-CLI Commands](#fase-7-testando-wp-cli-commands)
10. [Fase 8: Testando Performance e Cache](#fase-8-testando-performance-e-cache)
11. [Fase 12: Testando Segurança](#fase-12-testando-seguranca)
12. [Fase 13: Testando Arquitetura](#fase-13-testando-arquitetura)
13. [Fase 16: Testando Async Jobs](#fase-16-testando-async-jobs)
14. [Boas Práticas](#boas-praticas)
15. [Resumo](#resumo)

---

<a id="por-que-testing-throughout"></a>
## Por Que Testing Throughout?

### Problema: Testing como Tema Isolado

**Cenário Atual:**
- Desenvolvedor aprende Fases 1-9 sem testes
- Padrões incorretos já estabelecidos
- Código sem testes escrito
- Dificuldade de retrofitting
- Testing visto como "luxo", não necessidade

**Resultado:**
- Código legacy sem testes
- Padrões incorretos difíceis de mudar
- Testing nunca implementado na prática

### Solução: Testing Throughout

**Novo Cenário:**
- Desenvolvedor aprende testes junto com cada tópico
- Padrões corretos estabelecidos desde o início
- Código testável desde o primeiro dia
- Testing integrado ao workflow

**Resultado:**
- Código production-ready desde o início
- Padrões corretos estabelecidos
- Testing como parte natural do desenvolvimento

### Benefícios

✅ **Aprendizado Contextual** - Aprende testes no contexto real  
✅ **Padrões Corretos** - Estabelece padrões desde o início  
✅ **Código Testável** - Código escrito pensando em testes  
✅ **Confiança** - Refactoring seguro desde o início  
✅ **Documentação** - Testes documentam comportamento  

---

<a id="setup-basico-de-testes"></a>
## Setup Básico de Testes

### Instalação PHPUnit

```bash
# Via Composer
composer require --dev phpunit/phpunit ^11.0

# Verificar instalação
./vendor/bin/phpunit --version
```

### Estrutura de Diretórios

```
plugin-name/
├── src/
│   └── ...
├── tests/
│   ├── bootstrap.php
│   ├── Unit/
│   │   └── HookSystemTest.php
│   ├── Integration/
│   │   └── RestApiTest.php
│   └── E2E/
│       └── ...
└── phpunit.xml
```

### phpunit.xml Básico

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/11.0/phpunit.xsd"
         bootstrap="tests/bootstrap.php"
         colors="true">
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Integration">
            <directory>tests/Integration</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

### tests/bootstrap.php

```php
<?php
/**
 * Bootstrap para testes WordPress
 */

// Carregar WordPress
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Definir constantes de teste
define('WP_TESTS_PHPUNIT_POLYFILLS_PATH', __DIR__ . '/../vendor/yoast/phpunit-polyfills');

// Carregar WordPress test suite
require_once getenv('WP_TESTS_DIR') . '/includes/functions.php';

function _manually_load_plugin() {
    require dirname(__DIR__) . '/plugin-name.php';
}
tests_add_filter('muplugins_loaded', '_manually_load_plugin');

require getenv('WP_TESTS_DIR') . '/includes/bootstrap.php';
```

### Executar Testes

```bash
# Todos os testes
./vendor/bin/phpunit

# Apenas unit tests
./vendor/bin/phpunit tests/Unit

# Teste específico
./vendor/bin/phpunit tests/Unit/HookSystemTest.php

# Com coverage
./vendor/bin/phpunit --coverage-html coverage/
```

---

<a id="fase-1-testando-hook-system"></a>
## Fase 1: Testando Hook System

### Mocking WordPress Functions

Antes de testar hooks, é importante entender como mockar funções do WordPress que não estão disponíveis no ambiente de teste.

```php
<?php
// tests/Unit/MockingWordPressTest.php

use PHPUnit\Framework\TestCase;

class MockingWordPressTest extends TestCase {
    
    /**
     * @test
     * Mockar função WordPress usando namespaces
     */
    public function test_mock_wordpress_function(): void {
        // Criar função mock no namespace do teste
        namespace MyPlugin\Tests {
            function get_option($option, $default = false) {
                return 'mocked_value';
            }
        }
        
        // No código do plugin, usar namespace
        $value = \MyPlugin\get_option('test_option');
        $this->assertEquals('mocked_value', $value);
    }
    
    /**
     * @test
     * Usar WP_UnitTestCase para funções WordPress reais
     */
    public function test_with_real_wordpress_functions(): void {
        // WP_UnitTestCase já tem WordPress carregado
        update_option('test_option', 'real_value');
        $value = get_option('test_option');
        $this->assertEquals('real_value', $value);
    }
}
```

### Testando Actions

```php
<?php
// tests/Unit/HookSystemTest.php

use PHPUnit\Framework\TestCase;

class HookSystemTest extends TestCase {
    
    private bool $hook_fired = false;
    
    protected function setUp(): void {
        parent::setUp();
        $this->hook_fired = false;
    }
    
    protected function tearDown(): void {
        // Limpar hooks após cada teste
        remove_all_actions('test_action');
        parent::tearDown();
    }
    
    /**
     * @test
     * Verificar se action é disparada
     */
    public function test_action_fires_at_correct_time(): void {
        // Arrange (Setup)
        add_action('test_action', function() {
            $this->hook_fired = true;
        });
        
        // Act
        do_action('test_action');
        
        // Assert
        $this->assertTrue($this->hook_fired);
    }
    
    /**
     * @test
     * Verificar ordem de execução por prioridade
     */
    public function test_hooks_execute_in_priority_order(): void {
        $order = [];
        
        add_action('sequence_test', function() use (&$order) {
            $order[] = 'first';
        }, 10);
        
        add_action('sequence_test', function() use (&$order) {
            $order[] = 'second';
        }, 20);
        
        add_action('sequence_test', function() use (&$order) {
            $order[] = 'third';
        }, 5);
        
        do_action('sequence_test');
        
        $this->assertEquals(['third', 'first', 'second'], $order);
    }
    
    /**
     * @test
     * Verificar que hook pode ser removido
     */
    public function test_hook_can_be_removed(): void {
        $callback = function() {
            $this->hook_fired = true;
        };
        
        add_action('test_action', $callback, 10);
        remove_action('test_action', $callback, 10);
        
        do_action('test_action');
        
        $this->assertFalse($this->hook_fired);
    }
    
    /**
     * @test
     * Verificar múltiplos callbacks no mesmo hook
     */
    public function test_multiple_callbacks_on_same_hook(): void {
        $callbacks_fired = 0;
        
        add_action('test_action', function() use (&$callbacks_fired) {
            $callbacks_fired++;
        });
        
        add_action('test_action', function() use (&$callbacks_fired) {
            $callbacks_fired++;
        });
        
        do_action('test_action');
        
        $this->assertEquals(2, $callbacks_fired);
    }
}
```

### Testando Filters

```php
<?php
// tests/Unit/FilterSystemTest.php

use PHPUnit\Framework\TestCase;

class FilterSystemTest extends TestCase {
    
    /**
     * @test
     * Verificar que filter modifica valor
     */
    public function test_filter_modifies_value(): void {
        add_filter('test_filter', function($value) {
            return $value . '_modified';
        });
        
        $result = apply_filters('test_filter', 'original');
        
        $this->assertEquals('original_modified', $result);
    }
    
    /**
     * @test
     * Verificar múltiplos filters em cascata
     */
    public function test_multiple_filters_in_cascade(): void {
        add_filter('test_filter', function($value) {
            return $value . '_first';
        }, 10);
        
        add_filter('test_filter', function($value) {
            return $value . '_second';
        }, 20);
        
        $result = apply_filters('test_filter', 'original');
        
        $this->assertEquals('original_first_second', $result);
    }
    
    /**
     * @test
     * Verificar que filter pode retornar early
     */
    public function test_filter_can_return_early(): void {
        add_filter('test_filter', function($value) {
            return 'early_return';
        }, 5);
        
        add_filter('test_filter', function($value) {
            return $value . '_should_not_run';
        }, 10);
        
        $result = apply_filters('test_filter', 'original');
        
        // Segundo filter não deve executar se primeiro retornar early
        $this->assertEquals('early_return', $result);
    }
}
```

### Testando Hooks Condicionais

```php
<?php
// tests/Unit/ConditionalHooksTest.php

use PHPUnit\Framework\TestCase;

class ConditionalHooksTest extends TestCase {
    
    /**
     * @test
     * Verificar hook condicional baseado em contexto
     */
    public function test_conditional_hook_based_on_context(): void {
        $hook_fired = false;
        
        add_action('wp', function() use (&$hook_fired) {
            if (is_single()) {
                $hook_fired = true;
            }
        });
        
        // Simular contexto single
        global $wp_query;
        $wp_query->is_single = true;
        
        do_action('wp');
        
        $this->assertTrue($hook_fired);
    }
}
```

### Exemplo Completo: Plugin Testável

Aqui está um exemplo completo de um plugin simples e totalmente testável:

```php
<?php
/**
 * Plugin Name: Product Manager
 * Description: Gerencia produtos com hooks testáveis
 */

class ProductManager {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('init', [$this, 'register_product_post_type']);
        add_action('save_post_product', [$this, 'save_product_meta'], 10, 2);
        add_filter('the_content', [$this, 'display_product_price'], 20);
    }
    
    public function register_product_post_type() {
        register_post_type('product', [
            'public' => true,
            'label' => 'Products',
            'supports' => ['title', 'editor', 'thumbnail'],
        ]);
    }
    
    public function save_product_meta($post_id, $post) {
        if ($post->post_type !== 'product') {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        if (isset($_POST['product_price'])) {
            update_post_meta($post_id, '_product_price', sanitize_text_field($_POST['product_price']));
        }
    }
    
    public function display_product_price($content) {
        if (get_post_type() !== 'product') {
            return $content;
        }
        
        $price = get_post_meta(get_the_ID(), '_product_price', true);
        
        if ($price) {
            $price_html = '<div class="product-price">Price: $' . esc_html($price) . '</div>';
            $content = $price_html . $content;
        }
        
        return $content;
    }
}

ProductManager::get_instance();
```

**Testes para o Plugin:**

```php
<?php
// tests/Integration/ProductManagerTest.php

class ProductManagerTest extends WP_UnitTestCase {
    
    protected $product_manager;
    
    public function setUp(): void {
        parent::setUp();
        $this->product_manager = ProductManager::get_instance();
    }
    
    /**
     * @test
     * Verificar que CPT é registrado
     */
    public function test_product_post_type_is_registered(): void {
        $post_types = get_post_types();
        $this->assertArrayHasKey('product', $post_types);
    }
    
    /**
     * @test
     * Verificar salvamento de meta
     */
    public function test_product_meta_is_saved(): void {
        $user_id = $this->factory->user->create(['role' => 'administrator']);
        wp_set_current_user($user_id);
        
        $post_id = $this->factory->post->create([
            'post_type' => 'product',
            'post_title' => 'Test Product',
        ]);
        
        // Simular POST
        $_POST['product_price'] = '99.99';
        
        // Disparar save_post
        do_action('save_post_product', $post_id, get_post($post_id));
        
        $saved_price = get_post_meta($post_id, '_product_price', true);
        $this->assertEquals('99.99', $saved_price);
    }
    
    /**
     * @test
     * Verificar que price é exibido no conteúdo
     */
    public function test_product_price_is_displayed(): void {
        $post_id = $this->factory->post->create([
            'post_type' => 'product',
            'post_content' => 'Product description',
        ]);
        
        update_post_meta($post_id, '_product_price', '99.99');
        
        global $post;
        $post = get_post($post_id);
        
        $content = apply_filters('the_content', 'Product description');
        
        $this->assertStringContainsString('Price: $99.99', $content);
    }
    
    /**
     * @test
     * Verificar que meta não é salva sem permissão
     */
    public function test_product_meta_not_saved_without_permission(): void {
        $user_id = $this->factory->user->create(['role' => 'subscriber']);
        wp_set_current_user($user_id);
        
        $post_id = $this->factory->post->create([
            'post_type' => 'product',
        ]);
        
        $_POST['product_price'] = '99.99';
        
        do_action('save_post_product', $post_id, get_post($post_id));
        
        $saved_price = get_post_meta($post_id, '_product_price', true);
        $this->assertEmpty($saved_price);
    }
}

---

<a id="fase-2-testando-rest-api"></a>
## Fase 2: Testando REST API

### Setup para Testes REST

```php
<?php
// tests/Integration/RestApiTest.php

class RestApiTest extends WP_UnitTestCase {
    
    protected $server;
    protected string $namespace = 'myapp/v1';
    protected string $endpoint = '/posts';
    
    public function setUp(): void {
        parent::setUp();
        $this->server = rest_get_server();
        
        // Registrar rotas
        do_action('rest_api_init');
    }
    
    /**
     * @test
     * Verificar que endpoint retorna 200
     */
    public function test_get_posts_returns_200(): void {
        // Criar alguns posts de teste
        $this->factory->post->create_many(3);
        
        $request = new WP_REST_Request('GET', "/{$this->namespace}{$this->endpoint}");
        $response = $this->server->dispatch($request);
        
        $this->assertEquals(200, $response->get_status());
        $this->assertNotEmpty($response->get_data());
    }
    
    /**
     * @test
     * Verificar autenticação requerida para endpoint protegido
     */
    public function test_authentication_required_for_protected_endpoint(): void {
        $request = new WP_REST_Request('POST', "/{$this->namespace}{$this->endpoint}");
        $response = $this->server->dispatch($request);
        
        $this->assertEquals(401, $response->get_status());
    }
    
    /**
     * @test
     * Verificar criação de post via REST API
     */
    public function test_create_post_via_rest_api(): void {
        $user_id = $this->factory->user->create(['role' => 'administrator']);
        wp_set_current_user($user_id);
        
        $request = new WP_REST_Request('POST', "/{$this->namespace}{$this->endpoint}");
        $request->set_body_params([
            'title' => 'Test Post',
            'content' => 'Test Content',
            'status' => 'publish',
        ]);
        
        $response = $this->server->dispatch($request);
        
        $this->assertEquals(201, $response->get_status());
        $data = $response->get_data();
        $this->assertEquals('Test Post', $data['title']['rendered']);
    }
    
    /**
     * @test
     * Verificar validação de dados
     */
    public function test_validation_rejects_invalid_data(): void {
        $user_id = $this->factory->user->create(['role' => 'administrator']);
        wp_set_current_user($user_id);
        
        $request = new WP_REST_Request('POST', "/{$this->namespace}{$this->endpoint}");
        $request->set_body_params([
            'title' => '', // Título vazio deve ser rejeitado
            'content' => 'Test Content',
        ]);
        
        $response = $this->server->dispatch($request);
        
        $this->assertEquals(400, $response->get_status());
    }
    
    /**
     * @test
     * Verificar sanitização de dados
     */
    public function test_sanitization_removes_unsafe_content(): void {
        $user_id = $this->factory->user->create(['role' => 'administrator']);
        wp_set_current_user($user_id);
        
        $request = new WP_REST_Request('POST', "/{$this->namespace}{$this->endpoint}");
        $request->set_body_params([
            'title' => '<script>alert("xss")</script>Test Post',
            'content' => 'Test Content',
            'status' => 'publish',
        ]);
        
        $response = $this->server->dispatch($request);
        
        $this->assertEquals(201, $response->get_status());
        $data = $response->get_data();
        // Script tag deve ser removida
        $this->assertStringNotContainsString('<script>', $data['title']['rendered']);
    }
    
    /**
     * @test
     * Verificar capabilities check
     */
    public function test_capability_check_prevents_unauthorized_access(): void {
        // Criar usuário sem permissão
        $user_id = $this->factory->user->create(['role' => 'subscriber']);
        wp_set_current_user($user_id);
        
        $request = new WP_REST_Request('DELETE', "/{$this->namespace}{$this->endpoint}/1");
        $response = $this->server->dispatch($request);
        
        $this->assertEquals(403, $response->get_status());
    }
}
```

### Testando REST Controllers Customizados

```php
<?php
// tests/Integration/CustomRestControllerTest.php

class CustomRestControllerTest extends WP_UnitTestCase {
    
    protected $server;
    
    public function setUp(): void {
        parent::setUp();
        $this->server = rest_get_server();
        do_action('rest_api_init');
    }
    
    /**
     * @test
     * Verificar endpoint customizado retorna dados corretos
     */
    public function test_custom_endpoint_returns_correct_data(): void {
        $request = new WP_REST_Request('GET', '/myapp/v1/custom-endpoint');
        $response = $this->server->dispatch($request);
        
        $this->assertEquals(200, $response->get_status());
        $data = $response->get_data();
        
        $this->assertArrayHasKey('message', $data);
        $this->assertEquals('Hello World', $data['message']);
    }
    
    /**
     * @test
     * Verificar schema de resposta
     */
    public function test_response_matches_schema(): void {
        $request = new WP_REST_Request('GET', '/myapp/v1/custom-endpoint');
        $response = $this->server->dispatch($request);
        
        $schema = $response->get_matched_route()->get_schema();
        $data = $response->get_data();
        
        // Verificar que resposta corresponde ao schema
        $this->assertArrayHasKey('message', $data);
        $this->assertIsString($data['message']);
    }
    
    /**
     * @test
     * Verificar paginação funciona corretamente
     */
    public function test_pagination_works_correctly(): void {
        // Criar 25 posts
        $this->factory->post->create_many(25);
        
        // Primeira página
        $request = new WP_REST_Request('GET', '/myapp/v1/posts');
        $request->set_param('per_page', 10);
        $request->set_param('page', 1);
        $response = $this->server->dispatch($request);
        
        $data = $response->get_data();
        $this->assertCount(10, $data);
        
        // Verificar headers de paginação
        $headers = $response->get_headers();
        $this->assertArrayHasKey('X-WP-Total', $headers);
        $this->assertEquals(25, $headers['X-WP-Total']);
        $this->assertEquals(3, $headers['X-WP-TotalPages']);
    }
    
    /**
     * @test
     * Verificar filtros de query funcionam
     */
    public function test_query_filters_work(): void {
        $category = $this->factory->category->create(['name' => 'Test Category']);
        $post1 = $this->factory->post->create();
        $post2 = $this->factory->post->create();
        
        wp_set_object_terms($post1, [$category], 'category');
        
        $request = new WP_REST_Request('GET', '/myapp/v1/posts');
        $request->set_param('categories', $category);
        $response = $this->server->dispatch($request);
        
        $data = $response->get_data();
        $this->assertCount(1, $data);
        $this->assertEquals($post1, $data[0]['id']);
    }
}
```

### Testando Error Handling em REST API

```php
<?php
// tests/Integration/RestErrorHandlingTest.php

class RestErrorHandlingTest extends WP_UnitTestCase {
    
    protected $server;
    
    public function setUp(): void {
        parent::setUp();
        $this->server = rest_get_server();
        do_action('rest_api_init');
    }
    
    /**
     * @test
     * Verificar tratamento de erro 404
     */
    public function test_404_for_nonexistent_resource(): void {
        $request = new WP_REST_Request('GET', '/myapp/v1/posts/99999');
        $response = $this->server->dispatch($request);
        
        $this->assertEquals(404, $response->get_status());
        $data = $response->get_data();
        $this->assertArrayHasKey('code', $data);
        $this->assertEquals('rest_post_invalid_id', $data['code']);
    }
    
    /**
     * @test
     * Verificar tratamento de erro de validação
     */
    public function test_validation_error_returns_400(): void {
        $user_id = $this->factory->user->create(['role' => 'administrator']);
        wp_set_current_user($user_id);
        
        $request = new WP_REST_Request('POST', '/myapp/v1/posts');
        $request->set_body_params([
            'email' => 'invalid-email', // Email inválido
        ]);
        
        $response = $this->server->dispatch($request);
        
        $this->assertEquals(400, $response->get_status());
        $data = $response->get_data();
        $this->assertArrayHasKey('code', $data);
        $this->assertEquals('rest_invalid_param', $data['code']);
    }
    
    /**
     * @test
     * Verificar que erros são logados mas não expostos ao cliente
     */
    public function test_errors_are_logged_but_not_exposed(): void {
        // Mock de logger
        $logged_errors = [];
        add_action('rest_api_error', function($error) use (&$logged_errors) {
            $logged_errors[] = $error;
        });
        
        // Forçar erro interno
        add_filter('rest_pre_dispatch', function($result, $server, $request) {
            throw new Exception('Internal server error');
        }, 10, 3);
        
        $request = new WP_REST_Request('GET', '/myapp/v1/posts');
        $response = $this->server->dispatch($request);
        
        // Cliente não deve ver detalhes do erro
        $data = $response->get_data();
        $this->assertArrayNotHasKey('message', $data); // Detalhes não expostos
        
        // Mas erro deve ser logado internamente
        $this->assertNotEmpty($logged_errors);
    }
}
```

---

<a id="fase-3-testando-rest-api-avancado"></a>
## Fase 3: Testando REST API Avançado

### Testando Schema Validation Completo

```php
<?php
// tests/Integration/RestSchemaValidationTest.php

class RestSchemaValidationTest extends WP_UnitTestCase {
    
    protected $server;
    
    public function setUp(): void {
        parent::setUp();
        $this->server = rest_get_server();
        
        // Registrar endpoint com schema completo
        register_rest_route('myapp/v1', '/products', [
            'methods' => 'POST',
            'callback' => function($request) {
                return new WP_REST_Response($request->get_params(), 201);
            },
            'permission_callback' => function() {
                return current_user_can('publish_posts');
            },
            'args' => [
                'name' => [
                    'required' => true,
                    'type' => 'string',
                    'minLength' => 3,
                    'maxLength' => 100,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'price' => [
                    'required' => true,
                    'type' => 'number',
                    'minimum' => 0,
                    'maximum' => 10000,
                ],
                'category' => [
                    'required' => false,
                    'type' => 'string',
                    'enum' => ['electronics', 'clothing', 'books'],
                ],
            ],
        ]);
        
        do_action('rest_api_init');
    }
    
    /**
     * @test
     * Verificar que schema valida tipos corretamente
     */
    public function test_schema_validates_types(): void {
        $user_id = $this->factory->user->create(['role' => 'administrator']);
        wp_set_current_user($user_id);
        
        $request = new WP_REST_Request('POST', '/myapp/v1/products');
        $request->set_body_params([
            'name' => 123, // ❌ Deve ser string
            'price' => 'invalid', // ❌ Deve ser number
        ]);
        
        $response = $this->server->dispatch($request);
        
        $this->assertEquals(400, $response->get_status());
    }
    
    /**
     * @test
     * Verificar que schema valida required fields
     */
    public function test_schema_validates_required_fields(): void {
        $user_id = $this->factory->user->create(['role' => 'administrator']);
        wp_set_current_user($user_id);
        
        $request = new WP_REST_Request('POST', '/myapp/v1/products');
        $request->set_body_params([
            // name e price são required, mas não fornecidos
            'category' => 'electronics',
        ]);
        
        $response = $this->server->dispatch($request);
        
        $this->assertEquals(400, $response->get_status());
        $data = $response->get_data();
        $this->assertArrayHasKey('code', $data);
        $this->assertEquals('rest_missing_callback_param', $data['code']);
    }
    
    /**
     * @test
     * Verificar que schema valida enum values
     */
    public function test_schema_validates_enum(): void {
        $user_id = $this->factory->user->create(['role' => 'administrator']);
        wp_set_current_user($user_id);
        
        $request = new WP_REST_Request('POST', '/myapp/v1/products');
        $request->set_body_params([
            'name' => 'Test Product',
            'price' => 99.99,
            'category' => 'invalid_category', // ❌ Não está no enum
        ]);
        
        $response = $this->server->dispatch($request);
        
        $this->assertEquals(400, $response->get_status());
    }
    
    /**
     * @test
     * Verificar que schema valida min/max values
     */
    public function test_schema_validates_min_max(): void {
        $user_id = $this->factory->user->create(['role' => 'administrator']);
        wp_set_current_user($user_id);
        
        // Testar minLength
        $request = new WP_REST_Request('POST', '/myapp/v1/products');
        $request->set_body_params([
            'name' => 'AB', // ❌ Menor que minLength (3)
            'price' => 99.99,
        ]);
        
        $response = $this->server->dispatch($request);
        $this->assertEquals(400, $response->get_status());
        
        // Testar maximum
        $request = new WP_REST_Request('POST', '/myapp/v1/products');
        $request->set_body_params([
            'name' => 'Test Product',
            'price' => 15000, // ❌ Maior que maximum (10000)
        ]);
        
        $response = $this->server->dispatch($request);
        $this->assertEquals(400, $response->get_status());
    }
}
```

### Testando Permissions Avançadas

```php
<?php
// tests/Integration/RestPermissionsTest.php

class RestPermissionsTest extends WP_UnitTestCase {
    
    protected $server;
    
    public function setUp(): void {
        parent::setUp();
        $this->server = rest_get_server();
        do_action('rest_api_init');
    }
    
    /**
     * @test
     * Verificar permissão baseada em meta do post
     */
    public function test_permission_based_on_post_meta(): void {
        $post_id = $this->factory->post->create();
        $user_id = $this->factory->user->create(['role' => 'author']);
        
        // Adicionar meta que dá permissão
        update_post_meta($post_id, '_allowed_user', $user_id);
        
        wp_set_current_user($user_id);
        
        $request = new WP_REST_Request('GET', "/wp/v2/posts/{$post_id}");
        $response = $this->server->dispatch($request);
        
        $this->assertEquals(200, $response->get_status());
    }
    
    /**
     * @test
     * Verificar rate limiting
     */
    public function test_rate_limiting_prevents_abuse(): void {
        $user_id = $this->factory->user->create();
        wp_set_current_user($user_id);
        
        // Fazer muitas requisições rapidamente
        for ($i = 0; $i < 100; $i++) {
            $request = new WP_REST_Request('POST', '/myapp/v1/endpoint');
            $response = $this->server->dispatch($request);
        }
        
        // Última requisição deve ser bloqueada
        $request = new WP_REST_Request('POST', '/myapp/v1/endpoint');
        $response = $this->server->dispatch($request);
        
        $this->assertEquals(429, $response->get_status()); // Too Many Requests
    }
}
```

### Testando Error Handling

```php
<?php
// tests/Integration/RestErrorHandlingTest.php

class RestErrorHandlingTest extends WP_UnitTestCase {
    
    /**
     * @test
     * Verificar tratamento de erro 404
     */
    public function test_404_for_nonexistent_resource(): void {
        $request = new WP_REST_Request('GET', '/myapp/v1/posts/99999');
        $response = rest_get_server()->dispatch($request);
        
        $this->assertEquals(404, $response->get_status());
        $data = $response->get_data();
        $this->assertArrayHasKey('code', $data);
        $this->assertEquals('rest_post_invalid_id', $data['code']);
    }
    
    /**
     * @test
     * Verificar tratamento de erro de validação
     */
    public function test_validation_error_returns_400(): void {
        $user_id = $this->factory->user->create(['role' => 'administrator']);
        wp_set_current_user($user_id);
        
        $request = new WP_REST_Request('POST', '/myapp/v1/posts');
        $request->set_body_params([
            'email' => 'invalid-email', // Email inválido
        ]);
        
        $response = rest_get_server()->dispatch($request);
        
        $this->assertEquals(400, $response->get_status());
        $data = $response->get_data();
        $this->assertArrayHasKey('code', $data);
        $this->assertEquals('rest_invalid_param', $data['code']);
    }
}
```

---

<a id="fase-4-testando-settings-api"></a>
## Fase 4: Testando Settings API

### Testando Settings Registration

```php
<?php
// tests/Integration/SettingsApiTest.php

class SettingsApiTest extends WP_UnitTestCase {
    
    /**
     * @test
     * Verificar que setting é registrado corretamente
     */
    public function test_setting_is_registered(): void {
        register_setting('myapp_options', 'myapp_email', [
            'sanitize_callback' => 'sanitize_email',
        ]);
        
        $this->assertTrue(isset(get_registered_settings()['myapp_email']));
    }
    
    /**
     * @test
     * Verificar sanitização de input
     */
    public function test_setting_sanitizes_input(): void {
        register_setting('myapp_options', 'myapp_email', [
            'sanitize_callback' => 'sanitize_email',
        ]);
        
        // Simular POST
        $_POST['myapp_email'] = '<script>alert("xss")</script>user@example.com';
        
        // Processar settings
        do_settings_sections('myapp_options');
        
        // Salvar
        update_option('myapp_email', $_POST['myapp_email']);
        
        // Verificar que foi sanitizado
        $saved = get_option('myapp_email');
        $this->assertStringNotContainsString('<script>', $saved);
        $this->assertEquals('user@example.com', $saved);
    }
    
    /**
     * @test
     * Verificar validação de setting
     */
    public function test_setting_validates_input(): void {
        register_setting('myapp_options', 'myapp_number', [
            'sanitize_callback' => 'absint',
            'validate_callback' => function($value) {
                return $value > 0 && $value < 100;
            },
        ]);
        
        // Valor inválido
        $result = update_option('myapp_number', 150);
        
        // Deve retornar false ou WP_Error
        $this->assertFalse($result);
    }
    
    /**
     * @test
     * Verificar nonce em form de settings
     */
    public function test_settings_form_includes_nonce(): void {
        ob_start();
        settings_fields('myapp_options');
        $output = ob_get_clean();
        
        $this->assertStringContainsString('_wpnonce', $output);
    }
    
    /**
     * @test
     * Verificar que settings são persistidos corretamente
     */
    public function test_settings_are_persisted(): void {
        register_setting('myapp_options', 'myapp_test_option', [
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        ]);
        
        update_option('myapp_test_option', 'test_value');
        
        $value = get_option('myapp_test_option');
        $this->assertEquals('test_value', $value);
    }
    
    /**
     * @test
     * Verificar que default values são aplicados
     */
    public function test_default_values_are_applied(): void {
        register_setting('myapp_options', 'myapp_option_with_default', [
            'type' => 'string',
            'default' => 'default_value',
            'sanitize_callback' => 'sanitize_text_field',
        ]);
        
        // Deletar option para testar default
        delete_option('myapp_option_with_default');
        
        $value = get_option('myapp_option_with_default', 'default_value');
        $this->assertEquals('default_value', $value);
    }
    
    /**
     * @test
     * Verificar que settings sections são registradas
     */
    public function test_settings_sections_are_registered(): void {
        add_settings_section(
            'myapp_section',
            'My Section',
            function() {},
            'myapp_settings'
        );
        
        global $wp_settings_sections;
        $this->assertArrayHasKey('myapp_section', $wp_settings_sections['myapp_settings']);
    }
    
    /**
     * @test
     * Verificar que settings fields são registradas
     */
    public function test_settings_fields_are_registered(): void {
        add_settings_field(
            'myapp_field',
            'My Field',
            function() {
                echo '<input type="text" name="myapp_field">';
            },
            'myapp_settings',
            'myapp_section'
        );
        
        global $wp_settings_fields;
        $this->assertArrayHasKey('myapp_field', $wp_settings_fields['myapp_settings']['myapp_section']);
    }
}
```

### Testando Meta Boxes

```php
<?php
// tests/Integration/MetaBoxTest.php

class MetaBoxTest extends WP_UnitTestCase {
    
    /**
     * @test
     * Verificar que meta box é registrada
     */
    public function test_meta_box_is_registered(): void {
        add_meta_box(
            'test_meta_box',
            'Test Meta Box',
            function() {},
            'post'
        );
        
        global $wp_meta_boxes;
        $this->assertArrayHasKey('test_meta_box', $wp_meta_boxes['post']['normal']['default']);
    }
    
    /**
     * @test
     * Verificar salvamento de meta box data
     */
    public function test_meta_box_saves_data(): void {
        $post_id = $this->factory->post->create();
        
        // Simular POST
        $_POST['test_meta_field'] = 'test_value';
        $_POST['test_meta_box_nonce'] = wp_create_nonce('test_meta_box');
        
        // Simular save_post hook
        do_action('save_post', $post_id);
        
        $saved_value = get_post_meta($post_id, 'test_meta_field', true);
        $this->assertEquals('test_value', $saved_value);
    }
    
    /**
     * @test
     * Verificar que nonce é verificado
     */
    public function test_meta_box_verifies_nonce(): void {
        $post_id = $this->factory->post->create();
        
        $_POST['test_meta_field'] = 'test_value';
        $_POST['test_meta_box_nonce'] = 'invalid_nonce';
        
        do_action('save_post', $post_id);
        
        $saved_value = get_post_meta($post_id, 'test_meta_field', true);
        $this->assertEmpty($saved_value); // Não deve salvar sem nonce válido
    }
}
```

---

<a id="fase-5-testando-custom-post-types"></a>
## Fase 5: Testando Custom Post Types

### Testando CPT Registration

```php
<?php
// tests/Integration/CustomPostTypeTest.php

class CustomPostTypeTest extends WP_UnitTestCase {
    
    /**
     * @test
     * Verificar que CPT é registrado
     */
    public function test_cpt_is_registered(): void {
        register_post_type('product', [
            'public' => true,
            'label' => 'Products',
        ]);
        
        $post_types = get_post_types();
        $this->assertArrayHasKey('product', $post_types);
    }
    
    /**
     * @test
     * Verificar criação de post do CPT
     */
    public function test_create_cpt_post(): void {
        register_post_type('product', ['public' => true]);
        
        $post_id = $this->factory->post->create([
            'post_type' => 'product',
            'post_title' => 'Test Product',
        ]);
        
        $this->assertGreaterThan(0, $post_id);
        $post = get_post($post_id);
        $this->assertEquals('product', $post->post_type);
    }
    
    /**
     * @test
     * Verificar query de CPT
     */
    public function test_query_cpt_posts(): void {
        register_post_type('product', ['public' => true]);
        
        $this->factory->post->create_many(3, ['post_type' => 'product']);
        $this->factory->post->create_many(2, ['post_type' => 'post']); // Outros posts
        
        $query = new WP_Query([
            'post_type' => 'product',
            'posts_per_page' => -1,
        ]);
        
        $this->assertEquals(3, $query->post_count);
    }
}
```

### Testando Taxonomies

```php
<?php
// tests/Integration/TaxonomyTest.php

class TaxonomyTest extends WP_UnitTestCase {
    
    /**
     * @test
     * Verificar que taxonomy é registrada
     */
    public function test_taxonomy_is_registered(): void {
        register_taxonomy('product_category', 'product', [
            'public' => true,
        ]);
        
        $taxonomies = get_taxonomies();
        $this->assertArrayHasKey('product_category', $taxonomies);
    }
    
    /**
     * @test
     * Verificar criação de term
     */
    public function test_create_taxonomy_term(): void {
        register_taxonomy('product_category', 'product');
        
        $term = wp_insert_term('Electronics', 'product_category');
        
        $this->assertNotWPError($term);
        $this->assertArrayHasKey('term_id', $term);
    }
    
    /**
     * @test
     * Verificar associação de term a post
     */
    public function test_associate_term_with_post(): void {
        register_post_type('product', ['public' => true]);
        register_taxonomy('product_category', 'product');
        
        $post_id = $this->factory->post->create(['post_type' => 'product']);
        $term = wp_insert_term('Electronics', 'product_category');
        
        wp_set_object_terms($post_id, [$term['term_id']], 'product_category');
        
        $terms = wp_get_object_terms($post_id, 'product_category');
        $this->assertCount(1, $terms);
        $this->assertEquals('Electronics', $terms[0]->name);
    }
    
    /**
     * @test
     * Verificar que termos podem ser removidos
     */
    public function test_terms_can_be_removed(): void {
        register_post_type('product', ['public' => true]);
        register_taxonomy('product_category', 'product');
        
        $post_id = $this->factory->post->create(['post_type' => 'product']);
        $term = wp_insert_term('Electronics', 'product_category');
        
        wp_set_object_terms($post_id, [$term['term_id']], 'product_category');
        wp_set_object_terms($post_id, [], 'product_category'); // Remover
        
        $terms = wp_get_object_terms($post_id, 'product_category');
        $this->assertEmpty($terms);
    }
    
    /**
     * @test
     * Verificar query por taxonomy term
     */
    public function test_query_by_taxonomy_term(): void {
        register_post_type('product', ['public' => true]);
        register_taxonomy('product_category', 'product');
        
        $term = wp_insert_term('Electronics', 'product_category');
        $post1 = $this->factory->post->create(['post_type' => 'product']);
        $post2 = $this->factory->post->create(['post_type' => 'product']);
        
        wp_set_object_terms($post1, [$term['term_id']], 'product_category');
        
        $query = new WP_Query([
            'post_type' => 'product',
            'tax_query' => [
                [
                    'taxonomy' => 'product_category',
                    'field' => 'term_id',
                    'terms' => $term['term_id'],
                ],
            ],
        ]);
        
        $this->assertEquals(1, $query->post_count);
        $this->assertEquals($post1, $query->posts[0]->ID);
    }
    
    /**
     * @test
     * Verificar que CPT supports são aplicados corretamente
     */
    public function test_cpt_supports_are_applied(): void {
        register_post_type('product', [
            'public' => true,
            'supports' => ['title', 'editor', 'thumbnail', 'custom-fields'],
        ]);
        
        $post_id = $this->factory->post->create([
            'post_type' => 'product',
            'post_title' => 'Test Product',
            'post_content' => 'Product description',
        ]);
        
        $post = get_post($post_id);
        $this->assertEquals('product', $post->post_type);
        $this->assertEquals('Test Product', $post->post_title);
        $this->assertEquals('Product description', $post->post_content);
    }
    
    /**
     * @test
     * Verificar que CPT capabilities são verificadas
     */
    public function test_cpt_capabilities_are_checked(): void {
        register_post_type('product', [
            'public' => true,
            'capability_type' => 'product',
            'map_meta_cap' => true,
        ]);
        
        $user_id = $this->factory->user->create(['role' => 'subscriber']);
        wp_set_current_user($user_id);
        
        // Subscriber não deve poder criar products
        $this->assertFalse(current_user_can('publish_products'));
        
        $admin_id = $this->factory->user->create(['role' => 'administrator']);
        wp_set_current_user($admin_id);
        
        // Admin deve poder criar products
        $this->assertTrue(current_user_can('publish_products'));
    }
}
```

---

<a id="fase-6-testando-shortcodes-e-blocks"></a>
## Fase 6: Testando Shortcodes e Blocks

### Testando Shortcodes

```php
<?php
// tests/Unit/ShortcodeTest.php

class ShortcodeTest extends WP_UnitTestCase {
    
    /**
     * @test
     * Verificar que shortcode é registrado
     */
    public function test_shortcode_is_registered(): void {
        add_shortcode('test_shortcode', function($atts) {
            return 'Test Output';
        });
        
        $this->assertTrue(shortcode_exists('test_shortcode'));
    }
    
    /**
     * @test
     * Verificar output do shortcode
     */
    public function test_shortcode_output(): void {
        add_shortcode('test_shortcode', function($atts) {
            return 'Test Output';
        });
        
        $output = do_shortcode('[test_shortcode]');
        
        $this->assertEquals('Test Output', $output);
    }
    
    /**
     * @test
     * Verificar shortcode com atributos
     */
    public function test_shortcode_with_attributes(): void {
        add_shortcode('test_shortcode', function($atts) {
            $atts = shortcode_atts([
                'name' => 'World',
            ], $atts);
            
            return 'Hello ' . esc_html($atts['name']);
        });
        
        $output = do_shortcode('[test_shortcode name="John"]');
        
        $this->assertEquals('Hello John', $output);
    }
    
    /**
     * @test
     * Verificar shortcode com conteúdo aninhado
     */
    public function test_shortcode_with_nested_content(): void {
        add_shortcode('test_wrapper', function($atts, $content = '') {
            return '<div class="wrapper">' . do_shortcode($content) . '</div>';
        });
        
        $output = do_shortcode('[test_wrapper]Inner Content[/test_wrapper]');
        
        $this->assertStringContainsString('Inner Content', $output);
        $this->assertStringContainsString('<div class="wrapper">', $output);
    }
}
```

### Testando Gutenberg Blocks

```php
<?php
// tests/Integration/BlockTest.php

class BlockTest extends WP_UnitTestCase {
    
    /**
     * @test
     * Verificar que block é registrado
     */
    public function test_block_is_registered(): void {
        register_block_type('myapp/test-block', [
            'render_callback' => function($attributes) {
                return '<div>Test Block</div>';
            },
        ]);
        
        $blocks = WP_Block_Type_Registry::get_instance()->get_all_registered();
        $this->assertArrayHasKey('myapp/test-block', $blocks);
    }
    
    /**
     * @test
     * Verificar renderização do block
     */
    public function test_block_renders_correctly(): void {
        register_block_type('myapp/test-block', [
            'render_callback' => function($attributes) {
                $name = $attributes['name'] ?? 'World';
                return '<div>Hello ' . esc_html($name) . '</div>';
            },
        ]);
        
        $block = new WP_Block([
            'blockName' => 'myapp/test-block',
            'attrs' => ['name' => 'John'],
        ]);
        
        $output = $block->render();
        
        $this->assertStringContainsString('Hello John', $output);
    }
}
```

---

<a id="fase-7-testando-wp-cli-commands"></a>
## Fase 7: Testando WP-CLI Commands

### Testando Custom WP-CLI Commands

```php
<?php
// tests/Integration/WpCliCommandTest.php

class WpCliCommandTest extends WP_UnitTestCase {
    
    /**
     * @test
     * Verificar que comando WP-CLI é registrado
     */
    public function test_wp_cli_command_is_registered(): void {
        WP_CLI::add_command('myapp test', function($args, $assoc_args) {
            WP_CLI::success('Command executed');
        });
        
        // Verificar que comando existe
        $commands = WP_CLI::get_runner()->get_commands();
        $this->assertArrayHasKey('myapp test', $commands);
    }
    
    /**
     * @test
     * Verificar execução de comando WP-CLI
     */
    public function test_wp_cli_command_executes(): void {
        $output = [];
        
        WP_CLI::add_command('myapp test', function($args, $assoc_args) use (&$output) {
            $output[] = 'Command executed';
        });
        
        // Simular execução
        WP_CLI::get_runner()->run_command(['myapp', 'test']);
        
        $this->assertContains('Command executed', $output);
    }
}
```

---

<a id="fase-8-testando-performance-e-cache"></a>
## Fase 8: Testando Performance e Cache

### Testando Transients

```php
<?php
// tests/Unit/CacheTest.php

class CacheTest extends WP_UnitTestCase {
    
    /**
     * @test
     * Verificar que transient é salvo
     */
    public function test_transient_is_saved(): void {
        set_transient('test_key', 'test_value', 3600);
        
        $value = get_transient('test_key');
        
        $this->assertEquals('test_value', $value);
    }
    
    /**
     * @test
     * Verificar que transient expira
     */
    public function test_transient_expires(): void {
        set_transient('test_key', 'test_value', 1);
        
        // Esperar expiração
        sleep(2);
        
        $value = get_transient('test_key');
        
        $this->assertFalse($value);
    }
    
    /**
     * @test
     * Verificar cache de objeto
     */
    public function test_object_cache(): void {
        wp_cache_set('test_key', 'test_value', 'group', 3600);
        
        $value = wp_cache_get('test_key', 'group');
        
        $this->assertEquals('test_value', $value);
    }
    
    /**
     * @test
     * Verificar invalidação de cache
     */
    public function test_cache_invalidation(): void {
        wp_cache_set('test_key', 'test_value', 'group');
        wp_cache_delete('test_key', 'group');
        
        $value = wp_cache_get('test_key', 'group');
        
        $this->assertFalse($value);
    }
}
```

### Testando Query Performance

```php
<?php
// tests/Integration/QueryPerformanceTest.php

class QueryPerformanceTest extends WP_UnitTestCase {
    
    /**
     * @test
     * Verificar que query não causa N+1
     */
    public function test_query_does_not_cause_n_plus_one(): void {
        $this->factory->post->create_many(10);
        
        // Contar queries
        $queries_before = get_num_queries();
        
        $posts = get_posts(['posts_per_page' => 10]);
        foreach ($posts as $post) {
            get_post_meta($post->ID, 'test_meta'); // Potencial N+1
        }
        
        $queries_after = get_num_queries();
        $query_count = $queries_after - $queries_before;
        
        // Deve fazer menos queries que número de posts (se usar cache)
        $this->assertLessThan(15, $query_count);
    }
}
```

---

<a id="fase-12-testando-seguranca"></a>
## Fase 12: Testando Segurança

### Testando Sanitização

```php
<?php
// tests/Unit/SecurityTest.php

class SecurityTest extends WP_UnitTestCase {
    
    /**
     * @test
     * Verificar sanitização de email
     */
    public function test_email_sanitization(): void {
        $input = '<script>alert("xss")</script>user@example.com';
        $sanitized = sanitize_email($input);
        
        $this->assertStringNotContainsString('<script>', $sanitized);
        $this->assertEquals('user@example.com', $sanitized);
    }
    
    /**
     * @test
     * Verificar escaping de output
     */
    public function test_output_escaping(): void {
        $input = '<script>alert("xss")</script>';
        $escaped = esc_html($input);
        
        $this->assertStringNotContainsString('<script>', $escaped);
        $this->assertStringContainsString('&lt;script&gt;', $escaped);
    }
    
    /**
     * @test
     * Verificar prepared statements
     */
    public function test_prepared_statements(): void {
        global $wpdb;
        
        $user_input = "'; DROP TABLE posts; --";
        
        // Deve usar prepared statement
        $query = $wpdb->prepare(
            "SELECT * FROM {$wpdb->posts} WHERE post_title = %s",
            $user_input
        );
        
        // Query não deve conter SQL injection
        $this->assertStringNotContainsString('DROP TABLE', $query);
    }
    
    /**
     * @test
     * Verificar nonce validation
     */
    public function test_nonce_validation(): void {
        $nonce = wp_create_nonce('test_action');
        
        $valid = wp_verify_nonce($nonce, 'test_action');
        $this->assertTrue($valid);
        
        $invalid = wp_verify_nonce('invalid_nonce', 'test_action');
        $this->assertFalse($invalid);
    }
    
    /**
     * @test
     * Verificar capability check
     */
    public function test_capability_check(): void {
        $admin_user = $this->factory->user->create(['role' => 'administrator']);
        $subscriber_user = $this->factory->user->create(['role' => 'subscriber']);
        
        wp_set_current_user($admin_user);
        $this->assertTrue(current_user_can('edit_posts'));
        
        wp_set_current_user($subscriber_user);
        $this->assertFalse(current_user_can('edit_posts'));
    }
}
```

---

<a id="fase-13-testando-arquitetura"></a>
## Fase 13: Testando Arquitetura

### Testando SOLID Principles

```php
<?php
// tests/Unit/SolidPrinciplesTest.php

class SolidPrinciplesTest extends WP_UnitTestCase {
    
    /**
     * @test
     * Verificar Single Responsibility Principle
     */
    public function test_single_responsibility_principle(): void {
        // Classe deve ter apenas uma responsabilidade
        $validator = new UserValidator();
        $repository = new UserRepository();
        
        // Validator só valida
        $this->assertTrue(method_exists($validator, 'validate'));
        $this->assertFalse(method_exists($validator, 'save'));
        
        // Repository só salva
        $this->assertTrue(method_exists($repository, 'save'));
        $this->assertFalse(method_exists($repository, 'validate'));
    }
    
    /**
     * @test
     * Verificar Dependency Injection
     */
    public function test_dependency_injection(): void {
        $validator = new UserValidator();
        $repository = new UserRepository();
        $service = new UserService($validator, $repository);
        
        // Service deve usar dependências injetadas
        $this->assertInstanceOf(UserValidator::class, $service->getValidator());
        $this->assertInstanceOf(UserRepository::class, $service->getRepository());
    }
}
```

### Testando Repository Pattern

```php
<?php
// tests/Unit/RepositoryTest.php

class RepositoryTest extends WP_UnitTestCase {
    
    /**
     * @test
     * Verificar que repository abstrai acesso a dados
     */
    public function test_repository_abstracts_data_access(): void {
        $repository = new PostRepository();
        
        $post_id = $repository->create([
            'title' => 'Test Post',
            'content' => 'Test Content',
        ]);
        
        $post = $repository->find($post_id);
        
        $this->assertNotNull($post);
        $this->assertEquals('Test Post', $post->getTitle());
    }
    
    /**
     * @test
     * Verificar que repository pode ser mockado
     */
    public function test_repository_can_be_mocked(): void {
        $mock_repository = $this->createMock(PostRepository::class);
        $mock_repository->method('find')
            ->willReturn(new Post(['title' => 'Mocked Post']));
        
        $service = new PostService($mock_repository);
        $post = $service->getPost(1);
        
        $this->assertEquals('Mocked Post', $post->getTitle());
    }
    
    /**
     * @test
     * Verificar que repository valida dados antes de salvar
     */
    public function test_repository_validates_data(): void {
        $repository = new PostRepository();
        
        $this->expectException(InvalidArgumentException::class);
        
        $repository->create([
            'title' => '', // Título vazio deve lançar exceção
            'content' => 'Test Content',
        ]);
    }
    
    /**
     * @test
     * Verificar que repository sanitiza dados
     */
    public function test_repository_sanitizes_data(): void {
        $repository = new PostRepository();
        
        $post_id = $repository->create([
            'title' => '<script>alert("xss")</script>Test Post',
            'content' => 'Test Content',
        ]);
        
        $post = $repository->find($post_id);
        
        // Script tag deve ser removida
        $this->assertStringNotContainsString('<script>', $post->getTitle());
    }
    
    /**
     * @test
     * Verificar que repository retorna null para ID inexistente
     */
    public function test_repository_returns_null_for_nonexistent_id(): void {
        $repository = new PostRepository();
        
        $post = $repository->find(99999);
        
        $this->assertNull($post);
    }
}
```

### Testando Service Layer

```php
<?php
// tests/Unit/ServiceLayerTest.php

class ServiceLayerTest extends WP_UnitTestCase {
    
    /**
     * @test
     * Verificar que service layer orquestra operações
     */
    public function test_service_layer_orchestrates_operations(): void {
        $repository = $this->createMock(PostRepository::class);
        $validator = $this->createMock(PostValidator::class);
        $notifier = $this->createMock(NotificationService::class);
        
        $service = new PostService($repository, $validator, $notifier);
        
        $validator->expects($this->once())
            ->method('validate')
            ->willReturn(true);
        
        $repository->expects($this->once())
            ->method('create')
            ->willReturn(123);
        
        $notifier->expects($this->once())
            ->method('notify')
            ->with('post_created', 123);
        
        $post_id = $service->createPost([
            'title' => 'Test Post',
            'content' => 'Test Content',
        ]);
        
        $this->assertEquals(123, $post_id);
    }
    
    /**
     * @test
     * Verificar que service layer valida antes de salvar
     */
    public function test_service_validates_before_saving(): void {
        $repository = $this->createMock(PostRepository::class);
        $validator = $this->createMock(PostValidator::class);
        
        $service = new PostService($repository, $validator, null);
        
        $validator->expects($this->once())
            ->method('validate')
            ->willReturn(false);
        
        $repository->expects($this->never())
            ->method('create');
        
        $this->expectException(ValidationException::class);
        
        $service->createPost([
            'title' => '', // Inválido
        ]);
    }
}
```

### Testando DI Container

```php
<?php
// tests/Unit/DIContainerTest.php

class DIContainerTest extends WP_UnitTestCase {
    
    /**
     * @test
     * Verificar que container resolve dependências
     */
    public function test_container_resolves_dependencies(): void {
        $container = new Container();
        
        $container->bind(PostRepository::class, function() {
            return new PostRepository();
        });
        
        $container->bind(PostService::class, function($container) {
            return new PostService(
                $container->make(PostRepository::class)
            );
        });
        
        $service = $container->make(PostService::class);
        
        $this->assertInstanceOf(PostService::class, $service);
    }
    
    /**
     * @test
     * Verificar que container cria singleton
     */
    public function test_container_creates_singleton(): void {
        $container = new Container();
        
        $container->singleton(PostRepository::class, function() {
            return new PostRepository();
        });
        
        $instance1 = $container->make(PostRepository::class);
        $instance2 = $container->make(PostRepository::class);
        
        $this->assertSame($instance1, $instance2);
    }
    
    /**
     * @test
     * Verificar que container lança exceção para binding não encontrado
     */
    public function test_container_throws_exception_for_missing_binding(): void {
        $container = new Container();
        
        $this->expectException(ContainerException::class);
        
        $container->make(NonExistentClass::class);
    }
}
```

---

<a id="fase-15-testando-async-jobs"></a>
## Fase 16: Testando Async Jobs

### Testando Action Scheduler

```php
<?php
// tests/Integration/ActionSchedulerTest.php

class ActionSchedulerTest extends WP_UnitTestCase {
    
    /**
     * @test
     * Verificar que ação é agendada
     */
    public function test_action_is_scheduled(): void {
        as_enqueue_async_action('test_action', ['arg1' => 'value1']);
        
        $has_action = as_has_scheduled_action('test_action', ['arg1' => 'value1']);
        
        $this->assertTrue($has_action);
    }
    
    /**
     * @test
     * Verificar que ação é executada
     */
    public function test_action_is_executed(): void {
        $executed = false;
        
        add_action('test_action', function() use (&$executed) {
            $executed = true;
        });
        
        as_enqueue_async_action('test_action');
        
        // Executar ações pendentes
        do_action('action_scheduler_run_queue');
        
        $this->assertTrue($executed);
    }
    
    /**
     * @test
     * Verificar retry em caso de falha
     */
    public function test_action_retries_on_failure(): void {
        $attempts = 0;
        
        add_action('test_action', function() use (&$attempts) {
            $attempts++;
            if ($attempts < 3) {
                throw new Exception('Temporary failure');
            }
        });
        
        as_enqueue_async_action('test_action');
        
        // Executar até sucesso
        for ($i = 0; $i < 5; $i++) {
            try {
                do_action('action_scheduler_run_queue');
            } catch (Exception $e) {
                // Continuar tentando
            }
        }
        
        $this->assertEquals(3, $attempts);
    }
    
    /**
     * @test
     * Verificar que ações agendadas são executadas no tempo correto
     */
    public function test_scheduled_actions_run_at_correct_time(): void {
        $executed = false;
        
        add_action('scheduled_action', function() use (&$executed) {
            $executed = true;
        });
        
        // Agendar para executar em 1 hora
        $timestamp = time() + HOUR_IN_SECONDS;
        as_schedule_single_action($timestamp, 'scheduled_action');
        
        // Verificar que ainda não executou
        $this->assertFalse($executed);
        
        // Simular tempo passando (em teste real, usar mocks)
        // Para este teste, vamos apenas verificar que está agendado
        $has_action = as_has_scheduled_action('scheduled_action');
        $this->assertTrue($has_action);
    }
    
    /**
     * @test
     * Verificar que ações recorrentes são agendadas corretamente
     */
    public function test_recurring_actions_are_scheduled(): void {
        // Agendar ação recorrente (a cada hora)
        as_schedule_recurring_action(
            time(),
            HOUR_IN_SECONDS,
            'recurring_action'
        );
        
        $has_action = as_has_scheduled_action('recurring_action');
        $this->assertTrue($has_action);
        
        // Verificar próximo agendamento
        $next_scheduled = as_next_scheduled_action('recurring_action');
        $this->assertGreaterThan(time(), $next_scheduled);
    }
    
    /**
     * @test
     * Verificar que ações podem ser canceladas
     */
    public function test_actions_can_be_cancelled(): void {
        as_enqueue_async_action('test_action');
        
        $has_action = as_has_scheduled_action('test_action');
        $this->assertTrue($has_action);
        
        as_unschedule_action('test_action');
        
        $has_action = as_has_scheduled_action('test_action');
        $this->assertFalse($has_action);
    }
    
    /**
     * @test
     * Verificar que ações passam argumentos corretamente
     */
    public function test_actions_receive_arguments(): void {
        $received_args = null;
        
        add_action('test_action', function($arg1, $arg2) use (&$received_args) {
            $received_args = [$arg1, $arg2];
        });
        
        as_enqueue_async_action('test_action', ['value1', 'value2']);
        
        do_action('action_scheduler_run_queue');
        
        $this->assertEquals(['value1', 'value2'], $received_args);
    }
    
    /**
     * @test
     * Verificar tratamento de erro em ações assíncronas
     */
    public function test_error_handling_in_async_actions(): void {
        $errors_logged = [];
        
        add_action('action_scheduler_failed_action', function($action_id, $error) use (&$errors_logged) {
            $errors_logged[] = $error;
        });
        
        add_action('failing_action', function() {
            throw new Exception('Action failed');
        });
        
        as_enqueue_async_action('failing_action');
        
        // Executar ação (deve falhar)
        try {
            do_action('action_scheduler_run_queue');
        } catch (Exception $e) {
            // Erro esperado
        }
        
        // Verificar que erro foi logado
        $this->assertNotEmpty($errors_logged);
    }
    
    /**
     * @test
     * Verificar idempotency de ações
     */
    public function test_action_idempotency(): void {
        $execution_count = 0;
        
        add_action('idempotent_action', function() use (&$execution_count) {
            $execution_count++;
        });
        
        // Enfileirar mesma ação múltiplas vezes com mesmo idempotency key
        $idempotency_key = 'unique-key-123';
        
        as_enqueue_async_action('idempotent_action', [], 'my-group', true, $idempotency_key);
        as_enqueue_async_action('idempotent_action', [], 'my-group', true, $idempotency_key);
        as_enqueue_async_action('idempotent_action', [], 'my-group', true, $idempotency_key);
        
        // Executar ações
        do_action('action_scheduler_run_queue');
        
        // Com idempotency, deve executar apenas uma vez
        $this->assertEquals(1, $execution_count);
    }
}
```

### Testando Background Processing

```php
<?php
// tests/Integration/BackgroundProcessingTest.php

class BackgroundProcessingTest extends WP_UnitTestCase {
    
    /**
     * @test
     * Verificar que processamento em background funciona
     */
    public function test_background_processing_works(): void {
        $processed_items = [];
        
        $processor = new BackgroundProcessor();
        $processor->set_callback(function($item) use (&$processed_items) {
            $processed_items[] = $item;
        });
        
        // Adicionar itens para processar
        $processor->push_to_queue('item1');
        $processor->push_to_queue('item2');
        $processor->push_to_queue('item3');
        
        // Processar
        $processor->save()->dispatch();
        
        // Simular processamento (em teste real, usar mocks ou aguardar)
        // Para este teste, vamos verificar que itens foram adicionados
        $this->assertGreaterThan(0, $processor->get_queue_size());
    }
    
    /**
     * @test
     * Verificar que processamento em lote funciona
     */
    public function test_batch_processing_works(): void {
        $batch_size = 5;
        $total_items = 20;
        
        $processor = new BackgroundProcessor();
        $processor->set_batch_size($batch_size);
        
        // Adicionar muitos itens
        for ($i = 0; $i < $total_items; $i++) {
            $processor->push_to_queue("item{$i}");
        }
        
        $processor->save()->dispatch();
        
        // Verificar que processamento será feito em lotes
        $expected_batches = ceil($total_items / $batch_size);
        $this->assertEquals($expected_batches, $processor->get_batch_count());
    }
}
```

---

<a id="boas-praticas"></a>
## Boas Práticas

### 1. Nomenclatura de Testes

```php
// ✅ BOM: Descritivo e claro
public function test_user_cannot_access_admin_without_permission(): void

// ❌ RUIM: Vago
public function test_user(): void
```

### 2. Arrange-Act-Assert Pattern

```php
public function test_example(): void {
    // Arrange (Setup)
    $user = $this->factory->user->create();
    
    // Act (Execute)
    $result = do_something($user);
    
    // Assert (Verify)
    $this->assertEquals('expected', $result);
}
```

### 3. Um Assertion por Teste (quando possível)

```php
// ✅ BOM: Um conceito por teste
public function test_user_is_created(): void {
    $user = create_user(['name' => 'John']);
    $this->assertNotNull($user);
}

public function test_user_has_correct_name(): void {
    $user = create_user(['name' => 'John']);
    $this->assertEquals('John', $user->getName());
}

// ⚠️ ACEITÁVEL: Múltiplos assertions relacionados
public function test_user_creation(): void {
    $user = create_user(['name' => 'John', 'email' => 'john@example.com']);
    $this->assertNotNull($user);
    $this->assertEquals('John', $user->getName());
    $this->assertEquals('john@example.com', $user->getEmail());
}
```

### 4. Testes Independentes

```php
// ✅ BOM: Cada teste é independente
public function setUp(): void {
    parent::setUp();
    // Setup limpo para cada teste
}

public function tearDown(): void {
    // Limpar após cada teste
    parent::tearDown();
}
```

### 5. Mocking Apropriado

```php
// ✅ BOM: Mockar dependências externas
$mock_api = $this->createMock(ExternalApi::class);
$mock_api->method('fetch')->willReturn(['data' => 'test']);

// ❌ RUIM: Mockar tudo
// Mockar apenas o que é necessário
```

### 6. Testes Rápidos

```php
// ✅ BOM: Testes unitários rápidos
public function test_calculation(): void {
    $result = calculate(2, 2);
    $this->assertEquals(4, $result);
}

// ⚠️ CUIDADO: Testes de integração podem ser mais lentos
// Mas ainda devem ser razoavelmente rápidos
```

### 7. Cobertura de Código

```bash
# Gerar relatório de cobertura
./vendor/bin/phpunit --coverage-html coverage/

# Target: 80%+ de cobertura
# Focar em código crítico primeiro
```

### 8. Testes de Regressão

```php
/**
 * @test
 * Teste de regressão para bug #123
 * 
 * Bug: Usuário podia acessar admin sem permissão
 * Fix: Adicionar capability check
 */
public function test_user_cannot_access_admin_without_permission(): void {
    // Teste que garante que bug não volta
}
```

---

<a id="resumo"></a>
## Resumo

### O Que Você Aprendeu

✅ **Testing Throughout** - Aprender testes junto com cada tópico  
✅ **Setup Básico** - PHPUnit e estrutura de testes  
✅ **Testes por Fase** - Exemplos práticos para cada fase  
✅ **Boas Práticas** - Padrões e convenções  

### Próximos Passos

1. **Implementar testes** enquanto aprende cada fase
2. **Aumentar cobertura** gradualmente
3. **Refatorar código** para ser mais testável
4. **Aprender Fase 10** para testes avançados

### Recursos Adicionais

- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [WordPress Test Suite](https://make.wordpress.org/core/handbook/testing/automated-testing/phpunit/)
- [Fase 10: Testing Avançado](./010-WordPress-Fase-10-Testes-Debug-Implantacao.md)

---

**Navegação:** [Índice](./000-WordPress-Indice-Topicos.md) | [← Fase 16](./016-WordPress-Fase-16-Jobs-Assincronos-Background.md) | [Fase 10 →](./010-WordPress-Fase-10-Testes-Debug-Implantacao.md)
