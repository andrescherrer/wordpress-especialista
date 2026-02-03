# Checklist – Revisão de código (segurança)

Referência. Fonte: **012-WordPress-Fase-12-Seguranca-Boas-Praticas.md**.

---

## Input / Output

- [ ] **Input:** sanitize_text_field, sanitize_email, absint, esc_url_raw conforme o tipo; whitelist para opções fixas
- [ ] **Output:** esc_html para texto em HTML; esc_attr para atributos; esc_url para URLs; wp_kses para HTML permitido; wp_json_encode para JS
- [ ] Nenhum echo/print de dado do usuário sem escape

---

## Nonces

- [ ] Formulários: wp_nonce_field( action, name ) no form
- [ ] Processamento: wp_verify_nonce( $_POST['name'], action ) antes de qualquer alteração
- [ ] Links de ação: wp_nonce_url( url, action, param )
- [ ] AJAX: nonce criado (wp_create_nonce) e verificado no handler

---

## Capabilities

- [ ] current_user_can( 'capability' ) antes de ações sensíveis (salvar, deletar, ver dados)
- [ ] REST: permission_callback em todos os endpoints
- [ ] Verificação de “dono” do recurso quando aplicável (ex.: edit_post com post_id)

---

## Banco de dados

- [ ] Todas as queries com dados do usuário usam $wpdb->prepare( query, ... )
- [ ] Placeholders %d, %s, %f; nunca concatenação de input em SQL
- [ ] Nomes de tabela/coluna dinâmicos: whitelist (array de permitidos)

---

## Arquivos

- [ ] Upload: MIME real verificado (finfo_file ou getimagesize), não só extensão
- [ ] Whitelist de tipos e tamanho máximo
- [ ] Nome do arquivo sanitizado (sanitize_file_name ou equivalente); sem path traversal (..)
- [ ] Acesso direto bloqueado: defined('ABSPATH') no topo de todos os PHP

---

## Erros e dados sensíveis

- [ ] WP_DEBUG e display desligados em produção; erros não expostos ao usuário
- [ ] Senhas e secrets não em código; preferir constantes (wp-config) ou variáveis de ambiente
- [ ] Logs não devem conter senhas ou tokens

---

## Geral

- [ ] Nenhum eval() ou create_function()
- [ ] Código segue WordPress Coding Standards quando aplicável
- [ ] Funções/arquivos deprecated não usados
