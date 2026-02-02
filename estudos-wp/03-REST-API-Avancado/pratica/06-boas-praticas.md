# Boas práticas – REST API Avançado

**Referência rápida:** WP_REST_Controller para endpoints complexos; permission_callback em todas as rotas; validar e sanitizar entrada; resposta estruturada (success/data/message); versionamento /v1/; rate limit e cache quando fizer sentido.

---

## Checklist de qualidade

- [ ] Usar `WP_REST_Controller` para CRUD e rotas reutilizáveis; callback simples para rotas pontuais
- [ ] Implementar `permission_callback` em **todas** as rotas (use `__return_true` se for público)
- [ ] Validar e sanitizar **toda** entrada (args + validate_* no controller quando necessário)
- [ ] Retornar respostas estruturadas (success, data, message) e status HTTP correto
- [ ] Escapar saída (esc_html, esc_url) em dados que vêm do usuário
- [ ] Usar versionamento de API (`/meu-plugin/v1/`, `/v2/`)
- [ ] Documentar parâmetros (description nos args)
- [ ] Testar permissões (sem auth, com auth, com capability)
- [ ] Logs para erros (sem expor dados sensíveis em produção)
- [ ] Tratamento de erros completo (WP_Error com status 4xx/5xx)
- [ ] Rate limiting em endpoints sensíveis (opcional)
- [ ] Cache (transients, wp_cache) para listagens pesadas quando apropriado
- [ ] CORS configurado se a API for consumida por outro domínio

---

## Estrutura de projeto recomendada

```
meu-plugin/
├── meu-plugin.php                 # Bootstrap; carrega REST
├── includes/
│   └── REST/
│       ├── class-posts-controller.php
│       ├── class-response-helper.php   # Wrapper success/error
│       ├── class-validator.php
│       └── class-error-handler.php
├── tests/
│   └── REST/
│       └── test-posts-controller.php
└── ...
```

Registro no plugin:

```php
add_action( 'rest_api_init', function() {
    $controller = new Meu_Plugin_Posts_Controller();
    $controller->register_routes();
} );
```

---

## Quando usar Controller vs callback

| Cenário | Abordagem |
|--------|-----------|
| Um endpoint GET simples, poucos parâmetros | `register_rest_route` + função callback |
| CRUD completo, prepare_item, get_collection_params | Classe estendendo `WP_REST_Controller` |
| Vários recursos semelhantes (posts, produtos) | Um controller por recurso; reutilizar base se quiser |
| Apenas POST para webhook | Callback; validar nonce/assinatura |

---

## Equívocos comuns

1. **“Sempre usar WP_REST_Controller”** – Para rotas simples, callback é suficiente. Controller vale para complexidade e reuso.
2. **“Operações em lote = várias requisições”** – Lote deve ser atômico (tudo ou nada) e ter rollback em caso de erro.
3. **“Expor IDs internos é seguro”** – IDs são necessários, mas não exponha dados sensíveis (email, hash) sem necessidade e sem controle de permissão.

---

*Fonte: 003-WordPress-Fase-3-REST-API-Avancado.md*
