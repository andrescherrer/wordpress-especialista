# Segurança – refresh token, revogação, armazenamento

Onde armazenar tokens; tempo de vida; **refresh token**; **revogação** (blacklist ou delete user_meta).

---

## Onde armazenar tokens

| Lado | Onde | Cuidado |
|------|------|---------|
| **Servidor (WordPress)** | user_meta (ex.: `jwt_tokens` ou tabela custom); ou blacklist de tokens revogados. | Não guardar o token em texto plano se não for necessário; para revogação, guardar jti (ID do token) ou hash. |
| **Cliente (app/site)** | Memória (SPA), httpOnly cookie, ou storage seguro; nunca em localStorage se o token for longo e sensível. | Evitar XSS; preferir cookie httpOnly para web. |

---

## Tempo de vida (exp)

- **Access token (JWT):** curto (ex.: 15 min a 1 h); reduz janela de abuso se vazado.
- **Refresh token:** longo (ex.: 7–30 dias); armazenado com mais cuidado; usado só para obter novo access token.

---

## Refresh token

- Cliente guarda **refresh_token**; quando o **access_token** expira, chama um endpoint (ex.: `POST /auth/refresh`) com o refresh_token.
- Servidor valida o refresh_token, gera novo access_token (e opcionalmente novo refresh_token) e retorna.
- Refresh tokens devem ser **rotacionados** (um uso só) ou ter **revogação** explícita.

---

## Revogação

- **Blacklist:** ao fazer logout ou “revogar acesso”, colocar o **jti** (ID do JWT) ou o próprio token em uma lista (tabela ou cache); no permission_callback, além de validar assinatura e exp, verificar se não está na blacklist.
- **Delete user_meta:** se os tokens forem guardados em user_meta (ex.: lista de refresh_tokens), remover o registro ao revogar.
- **Invalidar por user_id:** em “logout everywhere”, invalidar todos os tokens daquele usuário (ex.: incrementar um “token_version” em user_meta e incluir no payload do JWT; rejeitar se a versão não bater).

---

## Resumo

- Access token com **exp** curto; refresh token com armazenamento seguro e rotação/revogação.
- Revogação: blacklist (jti) ou controle por user_meta/token_version.
- Ver **14-auth-jwt-vs-app-password-vs-oauth2.md** para quando usar JWT vs Application Password vs OAuth2.
