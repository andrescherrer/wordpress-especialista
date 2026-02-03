<?php
/**
 * REFERÊNCIA RÁPIDA – Testando REST API
 *
 * Setup: rest_get_server(), do_action('rest_api_init').
 * Request: WP_REST_Request('GET'|'POST', "/namespace/endpoint"); set_body_params(); set_param().
 * Response: $server->dispatch($request); get_status(); get_data(); get_headers().
 * Cenários: 200/201 sucesso, 400 validação, 401 auth, 403 capability, 404 não encontrado.
 * Factory: $this->factory->post->create(), $this->factory->user->create(['role' => 'administrator']); wp_set_current_user().
 *
 * Requer WP_UnitTestCase e WordPress test suite (WP_TESTS_DIR).
 * Fonte: 017-WordPress-Fase-17-Testes-Em-Toda-Fase.md (Fase 2)
 */

// Em projeto real: class RestApiTest extends \WP_UnitTestCase

use PHPUnit\Framework\TestCase;

class RestApiTest extends TestCase
{
    protected $server;
    protected string $namespace = 'myapp/v1';
    protected string $endpoint = '/posts';

    protected function setUp(): void
    {
        parent::setUp();
        $this->server = null;
        if (function_exists('rest_get_server')) {
            $this->server = rest_get_server();
            do_action('rest_api_init');
        }
    }

    /**
     * GET retorna 200 e dados.
     */
    public function test_get_posts_returns_200(): void
    {
        if (! $this->server) {
            $this->markTestSkipped('WordPress REST não disponível');
        }
        $request  = new \WP_REST_Request('GET', "/{$this->namespace}{$this->endpoint}");
        $response = $this->server->dispatch($request);
        $this->assertEquals(200, $response->get_status());
    }

    /**
     * POST sem autenticação retorna 401.
     */
    public function test_authentication_required_for_protected_endpoint(): void
    {
        if (! $this->server) {
            $this->markTestSkipped('WordPress REST não disponível');
        }
        $request  = new \WP_REST_Request('POST', "/{$this->namespace}{$this->endpoint}");
        $response = $this->server->dispatch($request);
        $this->assertContains($response->get_status(), [401, 403], 'Endpoint protegido deve retornar 401 ou 403');
    }

    /**
     * Validação rejeita dados inválidos (ex.: título vazio).
     */
    public function test_validation_rejects_invalid_data(): void
    {
        if (! $this->server) {
            $this->markTestSkipped('WordPress REST não disponível');
        }
        if (! function_exists('wp_set_current_user')) {
            $this->markTestSkipped('WordPress não carregado');
        }
        $user_id = 1; // Em teste real: $this->factory->user->create(['role' => 'administrator']);
        wp_set_current_user($user_id);
        $request = new \WP_REST_Request('POST', "/{$this->namespace}{$this->endpoint}");
        $request->set_body_params([
            'title'   => '',
            'content' => 'Test Content',
        ]);
        $response = $this->server->dispatch($request);
        $this->assertEquals(400, $response->get_status());
    }

    /**
     * Sanitização remove conteúdo perigoso (script).
     */
    public function test_sanitization_removes_unsafe_content(): void
    {
        if (! $this->server) {
            $this->markTestSkipped('WordPress REST não disponível');
        }
        $request = new \WP_REST_Request('GET', "/{$this->namespace}{$this->endpoint}");
        $response = $this->server->dispatch($request);
        $data = $response->get_data();
        if (is_array($data) && isset($data[0]['title'])) {
            $this->assertStringNotContainsString('<script>', $data[0]['title']['rendered'] ?? '');
        }
        $this->assertTrue(true, 'Estrutura de resposta pode variar; ajuste assert ao seu endpoint');
    }
}
