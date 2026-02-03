# Anti-padrões por fase – Resumo em tabela

Resumo: o que evitar em cada fase. Fonte: **019-WordPress-Fase-19-Anti-padroes-Seguranca.md**.

---

## Core (Fase 1)

| Anti-padrão | Solução |
|-------------|---------|
| Output sem escape (XSS) | esc_html, esc_attr, esc_url, wp_json_encode |
| SQL concatenado | $wpdb->prepare com %d, %s, %f; $wpdb->esc_like para LIKE |
| Confiar em role / só is_user_logged_in | current_user_can('capability'), current_user_can('capability', $id) |
| Dados sensíveis em post meta sem criptografia | Criptografar ou usar token/hash; autoload false |

---

## REST API (Fase 2)

| Anti-padrão | Solução |
|-------------|---------|
| Sem validação de input | args com validate_callback e sanitize_callback |
| permission_callback => __return_true em ações destrutivas | Verificar is_user_logged_in e current_user_can; capability por recurso |
| Expor IDs sequenciais | UUID ou hash; filtrar dados sensíveis por capability |
| Logar senha/token | Logar só username/código de erro; redactar campos sensíveis |

---

## Settings (Fase 4)

| Anti-padrão | Solução |
|-------------|---------|
| Sem sanitize/validate | sanitize_callback e validate_callback em register_setting |
| Salvar raw input (HTML) | wp_kses ou wp_kses_post; esc_textarea ao exibir |
| Credenciais hardcoded | getenv(), wp-config (não versionado), .env no .gitignore |

---

## CPT (Fase 5)

| Anti-padrão | Solução |
|-------------|---------|
| save_post sem nonce/capability | wp_verify_nonce, current_user_can('edit_post', $post_id) |
| Meta box sem escape | esc_attr, wp_kses_post; sanitize ao salvar |
| Expor drafts publicamente | post_status => 'publish'; status só para quem pode edit_post |

---

## Arquitetura (Fase 13)

| Anti-padrão | Solução |
|-------------|---------|
| DI sem tipo (aceitar qualquer objeto) | Type hint com interface; container por interface |
| Eventos sem logging | Logar evento (sem dados sensíveis); logar falhas de listeners |
| Repository sem sanitização | Validar e sanitizar antes de persistir; usar APIs WP |

---

## DevOps (Fase 14)

| Anti-padrão | Solução |
|-------------|---------|
| Secrets em código ou versionados | Variáveis de ambiente; .env não versionado |
| Sem SSL / DB exposto | HTTPS e HSTS; DB em rede interna ou socket; firewall |
