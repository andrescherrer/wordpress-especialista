# Testes de API – REST API Avançado

**Referência rápida:** curl para manual; PHPUnit com `rest_get_server()`, `WP_REST_Request`, `$server->dispatch( $request )`, `$response->get_status()`, `$response->get_data()`.

---

## Testar com cURL

Base: `https://seusite.com/wp-json/estudos-wp/v1/`

```bash
# GET – lista
curl -X GET "https://seusite.com/wp-json/estudos-wp/v1/artigos?per_page=5&page=1" \
  -H "Content-Type: application/json"

# GET – um item
curl -X GET "https://seusite.com/wp-json/estudos-wp/v1/artigos/123" \
  -H "Content-Type: application/json"

# POST – criar (requer autenticação)
curl -X POST "https://seusite.com/wp-json/estudos-wp/v1/artigos" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer SEU_TOKEN" \
  -d '{"title":"Título","content":"Conteúdo"}'

# PUT – atualizar
curl -X PUT "https://seusite.com/wp-json/estudos-wp/v1/artigos/123" \
  -H "Content-Type: application/json" \
  -d '{"title":"Novo título"}'

# DELETE
curl -X DELETE "https://seusite.com/wp-json/estudos-wp/v1/artigos/123" \
  -H "Content-Type: application/json"
```

Com Application Password (Basic Auth):

```bash
curl -u "usuario:xxxx xxxx xxxx xxxx" \
  -X GET "https://seusite.com/wp-json/estudos-wp/v1/artigos"
```

---

## Testes com PHPUnit (WordPress)

Exemplo de teste para o controller de artigos:

```php
class Test_Estudos_WP_Artigos_Controller extends WP_UnitTestCase {

	protected $server;

	public function setUp(): void {
		parent::setUp();
		$this->server = rest_get_server();
	}

	public function test_get_artigos_returns_200() {
		$request  = new WP_REST_Request( 'GET', '/estudos-wp/v1/artigos' );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 200, $response->get_status() );
		$data = $response->get_data();
		$this->assertIsArray( $data );
	}

	public function test_get_artigo_by_id() {
		$post_id  = $this->factory->post->create( [ 'post_status' => 'publish' ] );
		$request  = new WP_REST_Request( 'GET', '/estudos-wp/v1/artigos/' . $post_id );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 200, $response->get_status() );
		$data = $response->get_data();
		$this->assertEquals( $post_id, $data['id'] );
		$this->assertArrayHasKey( 'title', $data );
	}

	public function test_get_artigo_404_when_not_found() {
		$request  = new WP_REST_Request( 'GET', '/estudos-wp/v1/artigos/99999' );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 404, $response->get_status() );
	}

	public function test_create_artigo_requires_auth() {
		wp_set_current_user( 0 );

		$request = new WP_REST_Request( 'POST', '/estudos-wp/v1/artigos' );
		$request->set_header( 'Content-Type', 'application/json' );
		$request->set_body( wp_json_encode( [
			'title'   => 'Test',
			'content' => 'Content',
		] ) );

		$response = $this->server->dispatch( $request );

		$this->assertEquals( 403, $response->get_status() );
	}

	public function test_create_artigo_with_auth() {
		$user_id = $this->factory->user->create( [ 'role' => 'editor' ] );
		wp_set_current_user( $user_id );

		$request = new WP_REST_Request( 'POST', '/estudos-wp/v1/artigos' );
		$request->set_header( 'Content-Type', 'application/json' );
		$request->set_body( wp_json_encode( [
			'title'   => 'Test Post',
			'content' => 'Test content',
		] ) );

		$response = $this->server->dispatch( $request );

		$this->assertEquals( 201, $response->get_status() );
		$data = $response->get_data();
		$this->assertTrue( $data['success'] );
		$this->assertArrayHasKey( 'data', $data );
		$this->assertArrayHasKey( 'id', $data['data'] );
	}
}
```

---

## Checklist de testes

- [ ] GET coleção retorna 200 e array
- [ ] GET item por ID retorna 200 e dados corretos
- [ ] GET item inexistente retorna 404
- [ ] POST sem autenticação retorna 401 ou 403
- [ ] POST com autenticação e dados válidos retorna 201
- [ ] POST com dados inválidos retorna 400 ou 422
- [ ] PUT/DELETE com permissão correta retornam 200

---

*Fonte: 003-WordPress-Fase-3-REST-API-Avancado.md*
