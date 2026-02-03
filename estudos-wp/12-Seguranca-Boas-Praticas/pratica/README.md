# Prática – Como usar (Segurança)

1. **Entrada:** sempre validar e sanitizar (sanitize_text_field, sanitize_email, absint, whitelist). Validar = formato/regras; sanitizar = limpar para armazenar.
2. **Saída:** escapar conforme contexto: esc_html (HTML), esc_attr (atributos), esc_url (URLs), wp_kses (HTML permitido), wp_json_encode (JS).
3. **Formulários e ações:** wp_nonce_field no form; wp_verify_nonce no processamento. Para AJAX, wp_create_nonce e verificar no handler.
4. **Ações sensíveis:** current_user_can() antes de salvar/deletar; em REST use permission_callback.
5. **Banco:** sempre $wpdb->prepare() para valores vindos do usuário; nunca concatenar em SQL.
6. **Upload:** validar MIME real, tamanho e extensão (whitelist); sanitizar nome do arquivo.

**Arquivos 08–11:** capability por recurso (08), security headers/CORS (09), upload seguro (10), checklist segurança (11).

**Teoria rápida:** no topo de cada `.php` há um bloco **REFERÊNCIA RÁPIDA**. Tudo em uma página: [../REFERENCIA-RAPIDA.md](../REFERENCIA-RAPIDA.md).
