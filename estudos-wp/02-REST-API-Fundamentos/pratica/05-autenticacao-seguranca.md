# Autenticação e Segurança – REST API

**Referência rápida:** Cookie (admin logado), Application Passwords (WP 5.6+), nonce `X-WP-Nonce`. Permission: `is_user_logged_in()`, `current_user_can( 'capability' )`. Validate → Sanitize → Process → Escape.

---

## Autenticação na REST API

| Método | Uso |
|--------|-----|
| **Cookie** | Usuário logado no admin; nonce no header `X-WP-Nonce` (wp_create_nonce( 'wp_rest' )) |
| **Application Passwords** | WP 5.6+: Usuário → Perfil → Senhas de aplicativo; Basic Auth com user:application_password |
| **JWT** | Plugin (ex: JWT Authentication); header `Authorization: Bearer <token>` |

---

## Testando com Cookie (navegador logado)

1. Faça login no admin do WordPress.
2. No front, chame a API (mesmo domínio). O cookie de sessão já autentica.
3. Para requisições JavaScript: envie o nonce no header:
   - Obter nonce: em `wp_localize_script` passar `wp_create_nonce( 'wp_rest' )`.
   - Header: `X-WP-Nonce: <nonce>`.

---

## Testando com Application Passwords

1. Admin → Usuários → Editar usuário → Senhas de aplicativo: criar nova.
2. Copiar a senha (só aparece uma vez).
3. Em Postman/curl: Basic Auth com **usuário** e **senha de aplicativo** (não a senha normal).
4. Exemplo curl:
   ```bash
   curl -u "usuario:xxxx xxxx xxxx xxxx" https://seusite.com/wp-json/estudos-wp/v1/meu-perfil
   ```

---

## permission_callback – regras

- **Nunca omitir:** sempre definir `permission_callback`. Se for público, use `'__return_true'`.
- **401 Unauthorized:** não autenticado → use `is_user_logged_in()`.
- **403 Forbidden:** autenticado mas sem permissão → use `current_user_can( 'capability' )`.
- Retornar `WP_Error` com `status` 401/403 quando quiser mensagem customizada.

---

## Validação e sanitização (resumo)

- **args:** todo parâmetro com `sanitize_callback`; use `validate_callback` para regras (tamanho, formato, enum).
- **validate_callback:** retornar `true` ou `new WP_Error( 'code', 'msg', [ 'status' => 400 ] )`.
- **Sanitização comum:** sanitize_text_field, sanitize_email, absint, floatval, wp_kses_post, sanitize_textarea_field.
- **Saída:** escapar dados na resposta quando for exibir em outro contexto (esc_html, esc_url).

---

## Checklist

- [ ] permission_callback definido em toda rota
- [ ] Endpoints sensíveis exigem autenticação e/ou capability
- [ ] Args com sanitize_callback (e validate_callback quando necessário)
- [ ] Erros com WP_Error e status HTTP correto (401, 403, 404, 422, 500)
- [ ] Não expor dados sensíveis em mensagens de erro

---

*Fonte: 002-WordPress-Fase-2-REST-API-Fundamentos.md*
