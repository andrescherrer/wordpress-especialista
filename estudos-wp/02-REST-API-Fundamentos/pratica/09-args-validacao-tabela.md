# Tabela – args em register_rest_route

Resumo dos parâmetros de validação/sanitização. Fonte: **002-WordPress-Fase-2-REST-API-Fundamentos.md**.

---

## Parâmetros comuns em `args`

| Chave | Tipo | Descrição |
|-------|------|-----------|
| **type** | string | `string`, `integer`, `number`, `boolean`, `array`, `object` |
| **required** | bool | Se o parâmetro é obrigatório |
| **default** | mixed | Valor padrão quando não enviado |
| **minimum** / **maximum** | number | Para integer/number (inclusive) |
| **enum** | array | Lista de valores permitidos |
| **sanitize_callback** | callable | Função que sanitiza (ex.: `sanitize_text_field`, `absint`) |
| **validate_callback** | callable | Função que retorna true/false ou WP_Error; se false, 400 |
| **description** | string | Descrição do parâmetro (schema) |

---

## Exemplos por tipo

```php
'id' => [
    'type'              => 'integer',
    'required'          => true,
    'minimum'           => 1,
    'sanitize_callback' => 'absint',
],
'status' => [
    'type'    => 'string',
    'default' => 'draft',
    'enum'    => ['draft', 'publish', 'pending'],
],
'email' => [
    'type'              => 'string',
    'required'          => true,
    'format'            => 'email',
    'validate_callback' => function ($v) { return is_email($v); },
    'sanitize_callback' => 'sanitize_email',
],
```

---

## Onde os args são usados

- **URL/query:** parâmetros da rota `(?P<id>\d+)` e query string são validados/sanitizados pelos `args` registrados.
- **Body (POST/PUT):** para JSON, use `args` no mesmo registro; o REST API passa o body pelos sanitize/validate antes de chamar o callback.
