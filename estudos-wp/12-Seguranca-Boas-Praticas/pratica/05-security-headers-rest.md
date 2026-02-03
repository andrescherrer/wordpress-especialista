# Security headers e REST API segura

Referência. Fonte: **012-WordPress-Fase-12-Seguranca-Boas-Praticas.md**.

---

## Security headers (PHP)

Enviar no hook **send_headers** (evitar em admin se não for necessário):

```php
add_action( 'send_headers', function() {
    if ( is_admin() ) {
        return;
    }
    header( 'X-Frame-Options: SAMEORIGIN' );
    header( 'X-Content-Type-Options: nosniff' );
    header( 'X-XSS-Protection: 1; mode=block' );
    header( 'Referrer-Policy: strict-origin-when-cross-origin' );
    if ( is_ssl() ) {
        header( 'Strict-Transport-Security: max-age=31536000; includeSubDomains; preload' );
    }
} );
```

**Content-Security-Policy** é mais complexo (diretivas por tipo de recurso); configurar conforme necessidade e testar para não quebrar scripts legítimos.

---

## REST API – segurança

- **permission_callback** obrigatório: retornar `current_user_can( 'capability' )` ou `__return_true` apenas para rotas públicas.
- **args** em cada parâmetro: **sanitize_callback** (ex.: `sanitize_text_field`, `absint`) e **validate_callback** (retornar true ou WP_Error).
- **Nonce:** em requisições autenticadas pelo cookie, o nonce do REST é enviado pelo core; para aplicações externas usar Application Passwords ou OAuth.
- **Rate limiting:** usar transient por user_id ou IP (ex.: contador por hora); retornar 429 se ultrapassar.

Exemplo de args:

```php
'args' => [
    'id' => [
        'required'          => true,
        'type'              => 'integer',
        'sanitize_callback' => 'absint',
        'validate_callback' => function( $v ) {
            return $v > 0;
        },
    ],
    'name' => [
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'validate_callback' => function( $v ) {
            return strlen( $v ) >= 2;
        },
    ],
],
```

---

## Resumo

| Tópico            | Ação principal                                      |
|-------------------|-----------------------------------------------------|
| Headers           | X-Frame-Options, X-Content-Type-Options, HSTS (SSL) |
| REST permission   | permission_callback em toda rota                   |
| REST input        | sanitize_callback + validate_callback em args      |
| Rate limit        | Transient por user/IP; retornar 429                 |
