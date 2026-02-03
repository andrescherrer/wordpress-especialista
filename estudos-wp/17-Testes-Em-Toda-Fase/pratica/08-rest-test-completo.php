<?php
/**
 * REFERÊNCIA RÁPIDA – Teste REST completo (status 200/400/403, body)
 *
 * rest_get_server(); do_action('rest_api_init'); WP_REST_Request GET/POST; set_body_params(); dispatch(); get_status(); get_data().
 * Assert 200/201 sucesso; 400 validação; 403 sem permissão; 404 não encontrado.
 * Fonte: 017-WordPress-Fase-17-Testes-Em-Toda-Fase.md
 *
 * @package EstudosWP
 * @subpackage 17-Testes-Em-Toda-Fase
 */

if (!defined('ABSPATH')) {
    exit;
}

// Exemplo (WP_UnitTestCase): setUp: $this->server = rest_get_server(); do_action('rest_api_init');
// test_get_returns_200: $request = new WP_REST_Request('GET', '/estudos-wp/v1/hello'); $request->set_param('nome', 'Teste');
// $response = $this->server->dispatch($request); $this->assertSame(200, $response->get_status());
// test_post_without_auth: WP_REST_Request('POST', '/estudos-wp/v1/itens'); set_body_params(['title' => 'Título']);
// dispatch(); assertContains($response->get_status(), [401, 403]);
