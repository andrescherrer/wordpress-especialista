# Checklist – Segurança (input, output, SQL, nonces, capabilities)

Use em revisão de código. Fonte: **012-WordPress-Fase-12-Seguranca-Boas-Praticas.md**.

---

## Input

- [ ] Todo input do usuário validado (tipo, formato, range)
- [ ] Sanitização antes de usar (sanitize_text_field, sanitize_email, absint, wp_kses)
- [ ] Whitelist para opções (select, radio); não confiar em valor livre

## Output

- [ ] Todo output escapado (esc_html, esc_attr, esc_url, wp_kses_post conforme contexto)
- [ ] Dados de banco escapados antes de exibir
- [ ] wp_json_encode para dados em JavaScript

## SQL

- [ ] Todas as queries com $wpdb->prepare() e placeholders (%d, %s, %f)
- [ ] $wpdb->esc_like() para LIKE
- [ ] Nenhuma concatenação direta de input em SQL

## Nonces e capabilities

- [ ] wp_nonce_field / wp_verify_nonce em formulários e ações
- [ ] check_ajax_referer em AJAX
- [ ] current_user_can() antes de ações sensíveis (edit_post, delete_post, manage_options)
- [ ] permission_callback em toda rota REST; capability por recurso quando aplicável

## Upload

- [ ] MIME e extensão permitidos (whitelist); validar tipo real após upload
- [ ] Tamanho limitado (wp_max_upload_size ou menor)
- [ ] Nome de arquivo sanitizado (sanitize_file_name)
- [ ] Armazenar fora do web root quando sensível, ou com extensão não executável
