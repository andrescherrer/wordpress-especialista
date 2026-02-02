# Referência rápida – REST API Fundamentos

Uma página. Use Ctrl+F. Fonte: **002-WordPress-Fase-2-REST-API-Fundamentos.md**.

---

## URL e métodos

- **Base:** `https://seusite.com/wp-json/`
- **Namespace:** ex. `meu-plugin/v1` → `/wp-json/meu-plugin/v1/...`
- **Métodos:** `WP_REST_Server::READABLE` (GET), `CREATABLE` (POST), `EDITABLE` (PUT/PATCH), `DELETABLE` (DELETE)

---

## Registrar rota

```php
add_action( 'rest_api_init', function() {
    register_rest_route( 'meu-plugin/v1', '/itens', [
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => 'listar_itens',
        'permission_callback'  => '__return_true',  // ou função
        'args'                => [ /* ver abaixo */ ],
    ] );
} );
```

Rota com ID: `'/' . $rest_base . '/(?P<id>\d+)'`

---

## Args (validação e sanitização)

| Chave | Uso |
|-------|-----|
| `type` | string, integer, number, boolean, array, object |
| `required` | true/false |
| `default` | valor padrão |
| `enum` | array de valores permitidos |
| `minimum` / `maximum` | para number/integer |
| `sanitize_callback` | sanitize_text_field, absint, floatval, sanitize_email, wp_kses_post |
| `validate_callback` | function( $value ) { return true ou new WP_Error( 'code', 'msg' ); } |

Fluxo: **Validar** (formato) → **Sanitizar** (limpar) → processar. Na saída: **escapar** (esc_html, esc_attr, esc_url).

---

## Resposta e erro

- Sucesso: `rest_ensure_response( $dados )` ou `new WP_REST_Response( $dados, 201 )`
- Erro: `return new WP_Error( 'codigo', 'Mensagem', [ 'status' => 404 ] );`
- Status: 200 OK, 201 Created, 204 No Content, 400 Bad Request, 401 Unauthorized, 403 Forbidden, 404 Not Found, 422 Unprocessable Entity, 500

Paginação: `$response->header( 'X-WP-Total', $total );` e `X-WP-TotalPages`.

---

## Permission callback

- Público: `'permission_callback' => '__return_true'`
- Autenticado: `function( $request ) { return is_user_logged_in(); }`
- Capability: `function( $request ) { return current_user_can( 'edit_posts' ); }`
- Retornar WP_Error com status 401/403 se quiser mensagem customizada.

---

## WP_REST_Controller (OOP)

- Estender `WP_REST_Controller`; definir `$namespace` e `$rest_base`.
- Registrar: `add_action( 'rest_api_init', [ $this, 'register_routes' ] );`
- Métodos: `get_items`, `get_item`, `create_item`, `update_item`, `delete_item`
- Permissions: `get_items_permissions_check`, `create_item_permissions_check`, etc.
- Params de coleção: `get_collection_params()` (per_page, page, orderby, order).

---

## Segurança

- **Nonce:** `wp_create_nonce( 'wp_rest' )` no header `X-WP-Nonce` (Cookie auth no admin).
- **Application Passwords:** WP 5.6+ em Usuário → Perfil; Basic Auth com user:application_password.
- **Capability:** sempre `permission_callback` com `current_user_can( 'capability' )` quando for operação sensível.
- **Validate + Sanitize** em todos os args; nunca confiar em input sem sanitize.

---

## Checklist

- [ ] Rotas registradas em `rest_api_init`
- [ ] Todo parâmetro com `sanitize_callback` (e `validate_callback` quando necessário)
- [ ] permission_callback definido (nunca omitir: usar `__return_true` se for público)
- [ ] Erros com WP_Error e status HTTP correto (401, 403, 404, 422, 500)
- [ ] Resposta com rest_ensure_response ou WP_REST_Response
- [ ] Dados de saída escapados quando aplicável (esc_html, esc_url)
