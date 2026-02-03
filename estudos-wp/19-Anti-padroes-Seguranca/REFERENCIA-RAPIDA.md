# Referência rápida – Anti-padrões e Segurança

Uma página. Use Ctrl+F. Fonte: **019-WordPress-Fase-19-Anti-padroes-Seguranca.md**.

---

## Fase 1: Core

- **XSS:** Nunca exibir input sem escape. Usar: `esc_html()`, `esc_attr()`, `esc_url()`, `esc_js()`, `wp_json_encode()`, `esc_textarea()`.
- **SQL Injection:** Sempre `$wpdb->prepare()` com placeholders `%d`, `%s`, `%f`; para LIKE usar `$wpdb->esc_like()`.
- **Capabilities:** Não confiar em role; usar `current_user_can('delete_posts')`, `current_user_can('delete_post', $post_id)`; em formulários verificar nonce com `wp_verify_nonce()`.
- **Dados sensíveis:** Não salvar em post meta sem criptografia; usar tokens/hash quando possível; `wp_salt('auth')` + openssl para criptografar; opções sensíveis com `autoload => false`.

---

## Fase 2: REST API

- **Validação:** `args` com `validate_callback` e `sanitize_callback`; nunca aceitar JSON sem validar tipo, formato e limites.
- **Permissões:** Nunca `permission_callback => '__return_true'` para ações destrutivas; verificar `is_user_logged_in()` e `current_user_can()`; para recurso específico `current_user_can('delete_post', $post_id)`.
- **IDs:** Evitar expor IDs sequenciais; usar UUID ou hash (ex.: hash_hmac com salt); filtrar campos sensíveis (email só para quem pode list_users).
- **Logs:** Nunca logar senha, token, API key; logar apenas username/código de erro; helper que redacta campos sensíveis.

---

## Fase 4: Settings API

- **Validação:** `register_setting` com `sanitize_callback` e `validate_callback`; em falha usar `add_settings_error()` e retornar valor anterior.
- **HTML:** Não salvar raw input; `wp_kses()` com allowed tags ou `wp_kses_post()`; ao exibir usar `esc_textarea()`.
- **Credenciais:** Nunca hardcoded; usar `getenv()`, constantes em wp-config (não versionado), ou get_option com fallback; .env com dotenv e .env no .gitignore.

---

## Fase 5: CPT

- **save_post:** Verificar nonce, DOING_AUTOSAVE e `current_user_can('edit_post', $post_id)` antes de salvar meta.
- **Meta box:** Verificar capability antes de exibir; `wp_nonce_field()`; escape: `esc_attr()` em atributos, `wp_kses_post()` em HTML.
- **Drafts:** Em listagens públicas usar `post_status => 'publish'`; expor status só para quem tem `edit_post`; filtrar por author para drafts próprios.

---

## Fase 13: Arquitetura

- **DI:** Type hint com interface; não aceitar qualquer objeto; container que resolve por interface.
- **Eventos:** Logar evento (sem dados sensíveis: apenas chaves ou contexto redactado); logar falhas de listeners.
- **Repository:** Validar e sanitizar antes de persistir; usar APIs WP (wp_insert_post, etc.) em vez de $wpdb->insert direto com input; exceções para dados inválidos.

---

## Fase 14: DevOps

- **Secrets:** Variáveis de ambiente; .env não versionado; .env.example como template; Docker/K8s secrets; CI/CD secrets (GitHub Actions).
- **SSL/TLS:** Redirecionar HTTP → HTTPS; TLS 1.2+; HSTS; FORCE_SSL_ADMIN/FORCE_SSL_LOGIN no WP.
- **Database:** Não expor porta 3306 publicamente; rede interna no Docker; socket ou host interno; firewall; usuário com privilégios mínimos.

---

## Checklist (resumo)

- Input: validar no servidor, tipos e limites.
- Output: escape por contexto (HTML, URL, JS, attr).
- SQL: sempre prepare.
- Auth: current_user_can, nonces.
- Sensitive: criptografar, não logar, não expor em API.
- Uploads: whitelist tipo, tamanho, fora do web root quando possível.
- REST: permission_callback, args, filtrar resposta.
- Erros: não expor stack/detalhes em produção.
- Secrets: env, não hardcoded, .gitignore .env.
- Infra: HTTPS, DB não público, firewall, logs.
