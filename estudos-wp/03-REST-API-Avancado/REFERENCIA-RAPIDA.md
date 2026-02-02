# Referência rápida – REST API Avançado

Uma página. Use Ctrl+F. Fonte: **003-WordPress-Fase-3-REST-API-Avancado.md**.

---

## Controller (WP_REST_Controller)

- Estender `WP_REST_Controller`; `$namespace`, `$rest_base`.
- `register_routes()`: register_rest_route para coleção (GET/POST) e item (GET/PUT/DELETE).
- Métodos: `get_items`, `get_item`, `create_item`, `update_item`, `delete_item`.
- `prepare_item_for_response( $object )` → array para JSON.
- `get_collection_params()` → page, per_page, orderby, order (e enum quando aplicável).
- Validação no controller: método `validate_*_data( $params )` retornando `true` ou `WP_Error`.
- Permissions: `*_permissions_check( $request )` com `is_user_logged_in()` e `current_user_can()`.

---

## Resposta estruturada

- Sucesso: `[ 'success' => true, 'data' => ..., 'message' => '', 'meta' => [ 'timestamp' => ... ] ]`.
- Erro: `new WP_Error( 'code', 'message', [ 'status' => 4xx/5xx ] )`.
- Lista paginada: `data` + `pagination` (total, current_page, per_page, total_pages).
- Evitar retornar só o array “nu”; sempre contexto (success, message, code).

---

## Validação avançada

- Validator: funções estáticas que retornam `true` ou `new WP_Error( 'code', 'msg', [ 'status' => 400 ] )`.
- Tipos: email (`is_email`), URL (`filter_var( FILTER_VALIDATE_URL )`), integer (min/max), string (min_length/max_length), enum (`in_array`).
- Sanitização de saída: esc_html, esc_url, wp_kses_post ao montar a resposta da API.

---

## Tratamento de erros

- Controller: try/catch; retornar WP_Error com status adequado (400, 404, 422, 500).
- Handler centralizado: filter `rest_pre_dispatch` para logar e formatar WP_Error.
- Helpers: `validation_error( $errors )` (422), `not_found( $resource, $id )` (404), `forbidden( $action )` (403).
- Não expor stack trace em produção; usar WP_DEBUG para debug.

---

## Testes

- **curl:** GET/POST/PUT/DELETE em `/wp-json/namespace/v1/...`; header `Content-Type: application/json`; `-d` para body.
- **PHPUnit:** `$server = rest_get_server();` → `$request = new WP_REST_Request( 'GET', '/namespace/v1/...' );` → `$response = $server->dispatch( $request );` → `$response->get_status()`, `$response->get_data()`.
- Testar com e sem autenticação (`wp_set_current_user( 0 )` ou `$user_id`).

---

## Boas práticas

- Usar WP_REST_Controller para endpoints complexos; callback simples para rotas simples.
- permission_callback em todas as rotas; validar e sanitizar toda entrada.
- Respostas estruturadas e status HTTP correto; versionamento (/v1/, /v2/).
- Rate limiting e cache quando fizer sentido; documentar parâmetros; logs para auditoria.

---

## Checklist

- [ ] Controller com prepare_item_for_response e get_collection_params
- [ ] Resposta com success/data/message (ou wrapper)
- [ ] Validação além dos args (validate_*_data) quando necessário
- [ ] Erros com WP_Error e status correto; handler centralizado opcional
- [ ] Testes com dispatch e verificação de status
- [ ] permission_callback e sanitização de saída em toda resposta
