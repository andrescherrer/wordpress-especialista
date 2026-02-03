# Fluxo OAuth2 (autorização)

Passos do fluxo **Authorization Code**: redirect para autorização; usuário aprova; callback com **code**; troca de **code** por **token**; uso do token na REST API.

---

## Visão geral

1. **Cliente** redireciona o usuário para o **servidor de autorização** (WordPress + plugin OAuth2).
2. Usuário **faz login** (se necessário) e **aprova** o acesso (escopos).
3. Servidor **redireciona de volta** ao cliente com um **authorization code** (na URL, query `code=...`).
4. Cliente troca o **code** por **access_token** (e opcionalmente **refresh_token**) em uma requisição **server-to-server** (com client_id e client_secret).
5. Cliente usa o **access_token** nas requisições à REST API (header `Authorization: Bearer <token>`).

---

## No WordPress

- **Plugin OAuth2 Server** (ou similar): expõe endpoints de autorização e token; armazena clientes (client_id, client_secret) e tokens.
- **Redirect de autorização:** URL que mostra tela “O app X quer acessar seus dados; permitir?”; ao confirmar, redirect para `redirect_uri?code=...`.
- **Endpoint de token:** POST com `grant_type=authorization_code`, `code`, `redirect_uri`, `client_id`, `client_secret`; retorna `access_token`, `expires_in`, opcionalmente `refresh_token`.

---

## Referências

- [RFC 6749 – OAuth 2.0](https://tools.ietf.org/html/rfc6749)
- [The PHP League – OAuth2 Server](https://oauth2.thephpleague.com/) – implementação em PHP; pode ser integrada ao WordPress.
- Para **JWT** no lugar de OAuth2 em cenários mais simples: ver arquivos **10-auth-jwt-endpoint.php** e **11-auth-jwt-middleware.php** na pasta 02-REST-API-Fundamentos/pratica.
