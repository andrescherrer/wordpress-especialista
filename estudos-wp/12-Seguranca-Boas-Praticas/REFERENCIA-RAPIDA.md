# Referência rápida – Segurança e Boas Práticas

Uma página. Use Ctrl+F. Fonte: **012-WordPress-Fase-12-Seguranca-Boas-Praticas.md**.

---

## Validação e sanitização

- **sanitize_email()** + **is_email()** para e-mail.
- **sanitize_text_field()** para texto de uma linha; **sanitize_textarea_field()** para texto longo.
- **esc_url_raw()** para URL (salvar); **filter_var( $url, FILTER_VALIDATE_URL )** para validar.
- **absint()** para inteiros positivos; **sanitize_key()** para chaves (a-z, 0-9, _-).
- **Whitelist:** permitir apenas valores conhecidos: `in_array( $valor, $permitidos, true )`.
- **Arrays:** `array_map( 'sanitize_text_field', $array )`.

---

## Escape de saída

- **esc_html( $texto )** – conteúdo HTML (texto).
- **esc_attr( $texto )** – atributos HTML (value, title, etc.).
- **esc_url( $url )** – URLs em href, src.
- **wp_kses( $html, $allowed_html )** – HTML com tags permitidas (array de tag => atributos).
- **wp_json_encode( $data )** – dados para JavaScript (evita quebra e XSS).
- **esc_html__()**, **esc_attr__()** – versões que traduzem e escapam.

---

## Nonces (CSRF)

- **wp_nonce_field( 'action', 'name' );** no formulário.
- **wp_verify_nonce( $_POST['name'], 'action' );** no processamento; retorna false se inválido.
- **wp_nonce_url( $url, 'action', 'param' );** para links de ação.
- **wp_create_nonce( 'action' );** para AJAX; enviar no body/header e verificar no handler.

---

## Capabilities

- **current_user_can( 'capability' );** antes de qualquer ação sensível.
- **current_user_can( 'edit_post', $post_id );** – verifica se pode editar aquele post.
- REST: **'permission_callback' => function() { return current_user_can( 'manage_options' ); }**.
- Registrar capabilities: **get_role( 'administrator' )->add_cap( 'minha_cap' );** (na ativação).

---

## Prepared statements

- **$wpdb->prepare( "SELECT ... WHERE id = %d AND name = %s", $id, $name );** – %d inteiro, %s string, %f float.
- Nunca concatenar input do usuário em SQL; sempre prepare.
- **$wpdb->insert( $table, $data, $format );** e **$wpdb->update( $table, $data, $where, $format, $where_format );** já escapam valores.

---

## Upload de arquivos

- Verificar **MIME real** (finfo_file ou getimagesize), não confiar na extensão.
- **Whitelist** de tipos (ex.: image/jpeg, image/png); validar tamanho máximo.
- **sanitize_file_name()** ou nome gerado (wp_unique_filename); evitar path traversal.
- **chmod( $file, 0644 )** após mover; armazenar fora do document root quando possível para arquivos sensíveis.

---

## Security headers

- **X-Frame-Options: SAMEORIGIN** – evita clickjacking.
- **X-Content-Type-Options: nosniff** – evita MIME sniffing.
- **Content-Security-Policy** – restringe origens de script/style.
- Enviar em **send_headers** (ou no servidor web).
