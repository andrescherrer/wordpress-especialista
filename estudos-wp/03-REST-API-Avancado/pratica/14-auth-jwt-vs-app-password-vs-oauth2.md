# Tabela – JWT vs Application Password vs OAuth2

Quando usar cada método de autenticação na REST API; prós e contras; cenário headless vs admin.

---

| Critério | Application Password | JWT (custom) | OAuth2 |
|----------|-----------------------|--------------|--------|
| **Cenário típico** | Admin/editor no browser; scripts server-side; testes. | App headless; mobile; SPA com login próprio. | Terceiros (outro app/serviço) acessando em nome do usuário. |
| **Quem emite** | WordPress (perfil do usuário). | Seu endpoint (ex.: POST /auth/jwt). | Servidor OAuth2 (plugin no WP). |
| **Credenciais** | user + Application Password (ou cookie). | user + password uma vez → token. | Authorization code ou client credentials. |
| **Revogação** | Usuário remove o Application Password no perfil. | Blacklist ou token_version; ou não revogável até expirar. | Revogar token/refresh no servidor OAuth2. |
| **Prós** | Nativo; sem código extra; seguro se HTTPS. | Stateless; bom para APIs; controle total do payload e exp. | Padrão; delegação de acesso; escopos. |
| **Contras** | Um password por app; usuário precisa criar no WP. | Você implementa emissão/validação e segurança. | Mais complexo; requer plugin ou implementação. |
| **Headless** | Possível (login no WP e usar app password). | Muito usado: login no front, guardar JWT, enviar em toda request. | Quando outro serviço precisa acessar a API em nome do usuário. |
| **Admin / scripts** | Ideal: criar Application Password e usar Basic Auth. | Útil se o cliente não quiser usar WP admin. | Geralmente desnecessário. |

---

## Resumo

- **Application Password:** uso diário no admin, scripts, testes; zero implementação.
- **JWT:** quando você controla o login (headless, app) e quer token stateless com expiração.
- **OAuth2:** quando terceiros (outro app/site) precisam de acesso autorizado pelo usuário (escopos, revogação).

Recursos: Plugin “JWT Authentication for WP REST API”; [OAuth2 Server (League)](https://oauth2.thephpleague.com/); documentação de JWT em PHP (firebase/php-jwt).
