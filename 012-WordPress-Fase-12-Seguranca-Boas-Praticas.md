# 🔐 FASE 12: Segurança Avançada e Boas Práticas Finais

**Versão:** 1.0  
**Data:** Janeiro 2026  
**Nível:** Especialista em PHP  
**Objetivo:** Dominar segurança avançada e implementar boas práticas de produção

---

**Navegação:** [Índice](./000-WordPress-Indice-Topicos.md) | [← Fase 11](./011-WordPress-Fase-11-Multisite-Internacionalizacao.md) | [Fase 13 →](./013-WordPress-Fase-13-Arquitetura-Avancada.md)

---

## 📑 Índice

1. [Fundamentos de Segurança WordPress](#fundamentos-de-segurança-wordpress)
2. [Input Validation e Sanitization](#input-validation-e-sanitization)
3. [Escapando Output](#escapando-output)
4. [Nonces para Proteção CSRF](#nonces-para-proteção-csrf)
5. [Capabilities e Permissões](#capabilities-e-permissões)
6. [Prepared Statements](#prepared-statements)
7. [Authentication e Password Security](#authentication-e-password-security)
8. [File Upload Security](#file-upload-security)
9. [REST API Security](#rest-api-security)
10. [Security Headers](#security-headers)
11. [Logging & Monitoring](#logging--monitoring)
12. [Environment Configuration](#environment-configuration)
13. [Incident Response](#incident-response)
14. [Server Security](#server-security)
15. [Code Review Checklist](#code-review-checklist)
16. [Best Practices Finais](#best-practices-finais)

---

## 🎯 Objetivos de Aprendizado

Ao final desta fase, você será capaz de:

1. ✅ Aplicar validação e sanitização de entrada a todas as entradas do usuário
2. ✅ Escapar saída adequadamente para diferentes contextos (HTML, atributos, URLs, JavaScript)
3. ✅ Implementar proteção CSRF usando nonces
4. ✅ Usar capabilities e permissões corretamente para controle de acesso
5. ✅ Escrever queries de banco de dados seguras usando prepared statements
6. ✅ Implementar autenticação segura e tratamento de senhas
7. ✅ Proteger uploads de arquivos com validação e armazenamento adequados
8. ✅ Aplicar security headers e seguir boas práticas de segurança do WordPress

## 📝 Autoavaliação

Teste seu entendimento:

- [ ] Qual é a diferença entre validação e sanitização?
- [ ] Quando você deve usar `esc_html()`, `esc_attr()`, `esc_url()`, e `esc_js()`?
- [ ] Como nonces previnem ataques CSRF?
- [ ] Qual é a diferença entre `current_user_can()` e verificações de capability?
- [ ] Por que você deve sempre usar prepared statements ao invés de queries SQL diretas?
- [ ] Como você trata uploads de arquivos com segurança no WordPress?
- [ ] Quais security headers você deve implementar para sites WordPress?
- [ ] O que deve ser incluído em um checklist de revisão de código de segurança?

## 🛠️ Projeto Prático

**Construir:** Plugin Seguro Primeiro

Crie um plugin que:
- Valide e sanitize todas as entradas adequadamente
- Escape todas as saídas corretamente
- Implemente nonces para todos os formulários
- Use verificações de capability para todas as ações
- Use prepared statements para todas as queries de banco de dados
- Trate uploads de arquivos com segurança
- Implemente security headers
- Siga padrões de codificação de segurança do WordPress

**Tempo estimado:** 12-15 horas  
**Dificuldade:** Avançado

---

## ❌ Equívocos Comuns

### Equívoco 1: "WordPress é inseguro por padrão"
**Realidade:** O core do WordPress é seguro quando configurado e atualizado adequadamente. A maioria dos problemas de segurança vem de plugins, temas ou má configuração.

**Por que é importante:** Culpar o core do WordPress ignora problemas reais de segurança. Foque em plugins, temas e configuração.

**Como lembrar:** Core WordPress = seguro. Plugins/temas/config = vulnerabilidades potenciais.

### Equívoco 2: "Escaping previne todos os ataques XSS"
**Realidade:** Escaping previne XSS em contextos específicos, mas você também precisa de validação de entrada, headers CSP e filtragem adequada de conteúdo.

**Por que é importante:** Escaping sozinho não é suficiente. Defesa em profundidade é necessária.

**Como lembrar:** Escaping = uma camada. Múltiplas camadas = defesa em profundidade.

### Equívoco 3: "Nonces expiram após um uso"
**Realidade:** Nonces expiram após 24 horas (padrão) ou ao fazer logout, não após um uso. Eles podem ser reutilizados dentro do período de validade.

**Por que é importante:** Entender o tempo de vida de nonces ajuda com tratamento de formulários e requisições AJAX.

**Como lembrar:** Nonces = baseados em tempo, não em uso. Válidos por ~24 horas.

### Equívoco 4: "Prepared statements previnem toda SQL injection"
**Realidade:** Prepared statements previnem SQL injection quando usados corretamente. Mas nomes dinâmicos de tabela/coluna e queries complexas ainda precisam de tratamento cuidadoso.

**Por que é importante:** Prepared statements são essenciais mas não são bala de prata. Entenda suas limitações.

**Como lembrar:** Prepared statements = previnem injeção em valores. Nomes de tabela/coluna = precisam de whitelist.

### Equívoco 5: "Plugins de segurança tornam tudo seguro"
**Realidade:** Plugins de segurança ajudam mas não substituem práticas de codificação segura, configuração adequada e atualizações regulares.

**Por que é importante:** Confiar apenas em plugins de segurança cria falsa sensação de segurança. Segurança de código é fundamental.

**Como lembrar:** Plugins de segurança = camada adicional. Código seguro = fundação.

---

## 🔓 Fundamentos de Segurança WordPress

### Principais Vulnerabilidades

1. **SQL Injection** - Inserção de código SQL malicioso
2. **Cross-Site Scripting (XSS)** - Execução de JavaScript não autorizado
3. **Cross-Site Request Forgery (CSRF)** - Requisições não autorizadas
4. **Remote Code Execution (RCE)** - Execução de código no servidor
5. **Local File Inclusion (LFI)** - Acesso a arquivos do servidor
6. **Arbitrary File Upload** - Upload de arquivos maliciosos
7. **Authentication Bypass** - Contornar autenticação
8. **Privilege Escalation** - Ganhar permissões extras

### Princípios de Segurança

```
🔐 "Validate Input, Sanitize Data, Escape Output"
🔐 "Never Trust, Always Verify"
🔐 "Defense in Depth"
🔐 "Principle of Least Privilege"
🔐 "Fail Securely"
🔐 "Security by Design, not by Accident"
```

---

## ✅ Input Validation e Sanitization

### Exemplo 1: Sistema Completo de Validação

```php
<?php
/**
 * Sistema completo de validação e sanitização
 */

class Meu_Plugin_Security_Validator {
    
    /**
     * Validar e sanitizar dados de formulário
     */
    public function validate_form_data($data) {
        $validated = [];
        $errors = [];
        
        // Email
        if (isset($data['email'])) {
            $email = sanitize_email($data['email']);
            
            if (!is_email($email)) {
                $errors['email'] = __('E-mail inválido', 'meu-plugin');
            } else {
                $validated['email'] = $email;
            }
        }
        
        // URL
        if (isset($data['url'])) {
            $url = esc_url_raw($data['url']);
            
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                $errors['url'] = __('URL inválida', 'meu-plugin');
            } else {
                $validated['url'] = $url;
            }
        }
        
        // Texto simples
        if (isset($data['name'])) {
            $name = sanitize_text_field($data['name']);
            
            if (empty($name)) {
                $errors['name'] = __('Nome é obrigatório', 'meu-plugin');
            } elseif (strlen($name) < 3) {
                $errors['name'] = __('Nome deve ter pelo menos 3 caracteres', 'meu-plugin');
            } else {
                $validated['name'] = $name;
            }
        }
        
        // Textarea
        if (isset($data['description'])) {
            $description = sanitize_textarea_field($data['description']);
            
            if (empty($description)) {
                $errors['description'] = __('Descrição é obrigatória', 'meu-plugin');
            } else {
                $validated['description'] = $description;
            }
        }
        
        // Número
        if (isset($data['quantity'])) {
            $quantity = absint($data['quantity']);
            
            if ($quantity <= 0) {
                $errors['quantity'] = __('Quantidade deve ser maior que zero', 'meu-plugin');
            } else {
                $validated['quantity'] = $quantity;
            }
        }
        
        // Seleção (whitelist)
        if (isset($data['status'])) {
            $allowed_statuses = ['draft', 'published', 'archived'];
            $status = sanitize_key($data['status']);
            
            if (!in_array($status, $allowed_statuses)) {
                $errors['status'] = __('Status inválido', 'meu-plugin');
            } else {
                $validated['status'] = $status;
            }
        }
        
        // Array
        if (isset($data['tags']) && is_array($data['tags'])) {
            $validated['tags'] = array_map('sanitize_text_field', $data['tags']);
        }
        
        return [
            'success' => empty($errors),
            'data' => $validated,
            'errors' => $errors,
        ];
    }
}
```

### Exemplo 2: Validação com Regex

```php
<?php
/**
 * Validações avançadas com regex
 */

class Meu_Plugin_Advanced_Validator {
    
    /**
     * Validar telefone
     */
    public function validate_phone($phone) {
        // Padrão: (11) 98765-4321 ou 11 98765-4321
        $pattern = '/^\(?(\d{2})\)?[\s-]?(\d{4,5})[\s-]?(\d{4})$/';
        
        return preg_match($pattern, $phone) ? true : false;
    }
    
    /**
     * Validar CPF
     */
    public function validate_cpf($cpf) {
        // Remover caracteres especiais
        $cpf = preg_replace('/[^0-9]/is', '', $cpf);
        
        // Verificar tamanho
        if (strlen($cpf) != 11) {
            return false;
        }
        
        // Verificar se não é tudo igual
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }
        
        // Algoritmo de validação
        for ($t = 9; $t < 11; $t++) {
            $d = 0;
            $m = $t + 1;
            
            for ($i = 0; $i < $t; $i++) {
                $d += $cpf[$i] * ($m - $i);
            }
            
            $d = ((10 * $d) % 11) % 10;
            
            if ($cpf[$t] != $d) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Validar CNPJ
     */
    public function validate_cnpj($cnpj) {
        // Remover caracteres especiais
        $cnpj = preg_replace('/[^0-9]/is', '', $cnpj);
        
        // Verificar tamanho
        if (strlen($cnpj) != 14) {
            return false;
        }
        
        // Verificar se não é tudo igual
        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }
        
        // Algoritmo de validação
        $size = strlen($cnpj) - 2;
        $numbers = substr($cnpj, 0, $size);
        $digits = substr($cnpj, $size);
        $sum = 0;
        $pos = $size - 7;
        
        for ($i = $size - 1; $i >= 0; $i--) {
            $sum += $numbers[$i] * $pos;
            $pos--;
            if ($pos < 2) {
                $pos = 9;
            }
        }
        
        $result = $sum % 11 < 2 ? 0 : 11 - $sum % 11;
        
        if ($result != $digits[0]) {
            return false;
        }
        
        $size = strlen($cnpj) - 1;
        $numbers = substr($cnpj, 0, $size);
        $digits = substr($cnpj, $size);
        $sum = 0;
        $pos = $size - 7;
        
        for ($i = $size - 1; $i >= 0; $i--) {
            $sum += $numbers[$i] * $pos;
            $pos--;
            if ($pos < 2) {
                $pos = 9;
            }
        }
        
        $result = $sum % 11 < 2 ? 0 : 11 - $sum % 11;
        
        return $result == $digits[0] ? true : false;
    }
}
```

---

## 🏷️ Escapando Output

### Exemplo: Contextos de Escape

```php
<?php
/**
 * Escapar output em diferentes contextos
 */

class Meu_Plugin_Output_Escape {
    
    /**
     * Contexto: HTML
     */
    public function escape_html() {
        $title = get_the_title();
        
        // ERRADO
        echo $title;
        
        // CORRETO
        echo esc_html($title);
    }
    
    /**
     * Contexto: Atributos HTML
     */
    public function escape_attr() {
        $data = get_option('meu_plugin_data');
        
        // CORRETO
        echo '<input value="' . esc_attr($data) . '">';
    }
    
    /**
     * Contexto: URLs
     */
    public function escape_url() {
        $url = get_option('meu_plugin_url');
        
        // CORRETO - não escape valores internos
        echo '<a href="' . esc_url($url) . '">Link</a>';
    }
    
    /**
     * Contexto: JavaScript
     */
    public function escape_js() {
        $data = get_option('meu_plugin_data');
        
        // CORRETO
        echo '<script>';
        echo 'var data = ' . wp_json_encode($data) . ';';
        echo '</script>';
    }
    
    /**
     * Contexto: CSS
     */
    public function escape_css() {
        $color = get_option('meu_plugin_color');
        
        // Validar primeiro
        if (preg_match('/^#[0-9A-F]{6}$/i', $color)) {
            echo '<style>';
            echo 'body { color: ' . esc_attr($color) . '; }';
            echo '</style>';
        }
    }
    
    /**
     * Contexto: HTML com tags permitidas
     */
    public function escape_allowed_html() {
        $content = get_option('meu_plugin_content');
        
        // Permitir apenas tags específicas
        $allowed_html = [
            'a' => ['href' => [], 'title' => []],
            'strong' => [],
            'em' => [],
            'p' => [],
            'br' => [],
        ];
        
        echo wp_kses($content, $allowed_html);
    }
}
```

---

## 🛡️ Nonces para Proteção CSRF

### Exemplo: Nonces em Formulários

```php
<?php
/**
 * Proteção contra CSRF com Nonces
 */

class Meu_Plugin_Nonce_Security {
    
    /**
     * Gerar nonce em formulário
     */
    public function render_form() {
        ?>
        <form method="post">
            <?php wp_nonce_field('meu_plugin_action', 'meu_plugin_nonce'); ?>
            
            <input type="text" name="nome" placeholder="Nome" required>
            <button type="submit">Enviar</button>
        </form>
        <?php
    }
    
    /**
     * Verificar nonce no processamento
     */
    public function process_form() {
        if (!isset($_POST['meu_plugin_nonce'])) {
            wp_die('Nonce inválido');
        }
        
        if (!wp_verify_nonce($_POST['meu_plugin_nonce'], 'meu_plugin_action')) {
            wp_die('Verificação de segurança falhou');
        }
        
        // Processar formulário
        $nome = sanitize_text_field($_POST['nome']);
        
        // ... salvar dados ...
    }
    
    /**
     * Nonce em AJAX
     */
    public function ajax_request() {
        // PHP - criar nonce
        wp_localize_script('meu-plugin-js', 'meuPluginData', [
            'nonce' => wp_create_nonce('meu_plugin_ajax'),
        ]);
        
        // JavaScript
        // fetch(ajaxurl, {
        //     method: 'POST',
        //     body: new FormData({
        //         'action': 'meu_plugin_action',
        //         'nonce': meuPluginData.nonce,
        //     }),
        // });
    }
    
    /**
     * Verificar nonce em AJAX
     */
    public function verify_ajax_nonce() {
        if (!isset($_POST['nonce']) || 
            !wp_verify_nonce($_POST['nonce'], 'meu_plugin_ajax')) {
            wp_send_json_error('Nonce inválido', 403);
        }
        
        // Processar request
    }
    
    /**
     * Nonce em URLs
     */
    public function get_action_url($action) {
        return wp_nonce_url(
            admin_url('admin-ajax.php?action=' . $action),
            $action,
            'nonce'
        );
    }
}
```

---

## 👤 Capabilities e Permissões

### Exemplo: Sistema de Capacidades

```php
<?php
/**
 * Gerenciar capabilities e permissões
 */

class Meu_Plugin_Capabilities {
    
    /**
     * Registrar capabilities customizadas
     */
    public function register_custom_capabilities() {
        // Administrator
        $admin = get_role('administrator');
        $admin->add_cap('manage_meu_plugin');
        $admin->add_cap('edit_meu_plugin_posts');
        $admin->add_cap('publish_meu_plugin_posts');
        $admin->add_cap('delete_meu_plugin_posts');
        
        // Editor
        $editor = get_role('editor');
        $editor->add_cap('edit_meu_plugin_posts');
        $editor->add_cap('publish_meu_plugin_posts');
        
        // Author
        $author = get_role('author');
        $author->add_cap('edit_meu_plugin_posts');
    }
    
    /**
     * Verificar capability antes de ação
     */
    public function save_data() {
        // Verificar permission
        if (!current_user_can('manage_meu_plugin')) {
            wp_die(__('Acesso negado', 'meu-plugin'));
        }
        
        // ... salvar dados ...
    }
    
    /**
     * Verificar capability em endpoints REST
     */
    public function register_rest_endpoint() {
        register_rest_route('meu-plugin/v1', '/dados', [
            'methods' => 'POST',
            'callback' => [$this, 'rest_callback'],
            'permission_callback' => function() {
                return current_user_can('manage_meu_plugin');
            },
        ]);
    }
    
    /**
     * Verificar que usuário é dono do recurso
     */
    public function can_edit_post($post_id, $user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        $post = get_post($post_id);
        
        // Admin pode editar tudo
        if (current_user_can('manage_options')) {
            return true;
        }
        
        // Verificar se é dono
        if ($post->post_author == $user_id) {
            return current_user_can('edit_meu_plugin_posts');
        }
        
        return false;
    }
}
```

---

## 🔒 Prepared Statements

### Exemplo: Queries Seguras

```php
<?php
/**
 * Usar prepared statements para segurança
 */

class Meu_Plugin_Database_Security {
    
    /**
     * ERRADO - SQL Injection
     */
    public function insecure_query($user_id) {
        global $wpdb;
        
        // NUNCA FAÇA ISSO!
        $result = $wpdb->get_results(
            "SELECT * FROM {$wpdb->posts} WHERE post_author = " . $user_id
        );
        
        return $result;
    }
    
    /**
     * CORRETO - Prepared Statement
     */
    public function secure_query($user_id) {
        global $wpdb;
        
        // Usar %d para inteiros, %s para strings
        $result = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->posts} WHERE post_author = %d",
                $user_id
            )
        );
        
        return $result;
    }
    
    /**
     * Múltiplos placeholders
     */
    public function secure_query_multiple($post_type, $status) {
        global $wpdb;
        
        $result = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->posts} 
                 WHERE post_type = %s AND post_status = %s",
                $post_type,
                $status
            )
        );
        
        return $result;
    }
    
    /**
     * Inserção segura
     */
    public function safe_insert($data) {
        global $wpdb;
        
        $result = $wpdb->insert(
            $wpdb->prefix . 'meu_plugin_table',
            [
                'name' => $data['name'],
                'email' => $data['email'],
                'created_at' => current_time('mysql'),
            ],
            ['%s', '%s', '%s'] // Especificar tipos
        );
        
        return $result;
    }
    
    /**
     * Update seguro
     */
    public function safe_update($id, $data) {
        global $wpdb;
        
        $result = $wpdb->update(
            $wpdb->prefix . 'meu_plugin_table',
            [
                'name' => $data['name'],
                'email' => $data['email'],
                'updated_at' => current_time('mysql'),
            ],
            ['id' => $id],
            ['%s', '%s', '%s'],
            ['%d']
        );
        
        return $result;
    }
}
```

---

## 🔑 Authentication e Password Security

### Exemplo: Autenticação Segura

```php
<?php
/**
 * Autenticação e segurança de senha
 */

class Meu_Plugin_Authentication {
    
    /**
     * Login com verificação de tentativas
     */
    public function secure_login($username, $password) {
        // Limitar tentativas de login
        if ($this->check_login_attempts($username)) {
            return new WP_Error(
                'too_many_attempts',
                __('Muitas tentativas de login. Tente mais tarde.', 'meu-plugin')
            );
        }
        
        // Autenticar
        $user = wp_authenticate($username, $password);
        
        if (is_wp_error($user)) {
            $this->log_failed_login($username);
            return $user;
        }
        
        // Limpar tentativas ao sucesso
        $this->clear_login_attempts($username);
        
        return $user;
    }
    
    /**
     * Verificar limite de tentativas
     */
    private function check_login_attempts($username) {
        $transient_key = 'meu_plugin_login_attempts_' . md5($username);
        $attempts = get_transient($transient_key) ?? 0;
        
        return $attempts >= 5; // máximo 5 tentativas
    }
    
    /**
     * Incrementar tentativas
     */
    private function log_failed_login($username) {
        $transient_key = 'meu_plugin_login_attempts_' . md5($username);
        $attempts = get_transient($transient_key) ?? 0;
        
        // Incrementar e expirar em 15 minutos
        set_transient($transient_key, $attempts + 1, 15 * MINUTE_IN_SECONDS);
    }
    
    /**
     * Validar força da senha
     */
    public function validate_password($password) {
        $errors = [];
        
        // Mínimo 12 caracteres
        if (strlen($password) < 12) {
            $errors[] = __('Senha deve ter pelo menos 12 caracteres', 'meu-plugin');
        }
        
        // Letra maiúscula
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = __('Senha deve conter letra maiúscula', 'meu-plugin');
        }
        
        // Número
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = __('Senha deve conter número', 'meu-plugin');
        }
        
        // Caractere especial
        if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};:\'",.<>?\/\\|`~]/', $password)) {
            $errors[] = __('Senha deve conter caractere especial', 'meu-plugin');
        }
        
        return empty($errors) ? true : $errors;
    }
}
```

---

## 📁 File Upload Security

### Exemplo: Upload Seguro

```php
<?php
/**
 * Upload de arquivos seguro
 */

class Meu_Plugin_File_Upload {
    
    /**
     * Validar e processar upload
     */
    public function handle_file_upload($file) {
        // 1. Verificar se arquivo foi enviado
        if (!isset($file['name'])) {
            return new WP_Error('no_file', __('Nenhum arquivo enviado', 'meu-plugin'));
        }
        
        // 2. Validar tipo MIME real (não apenas extensão)
        $mime = $this->get_real_mime_type($file['tmp_name']);
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
        
        if (!in_array($mime, $allowed_types)) {
            return new WP_Error('invalid_type', __('Tipo de arquivo não permitido', 'meu-plugin'));
        }
        
        // 3. Validar tamanho (máximo 5MB)
        $max_size = 5 * 1024 * 1024;
        if ($file['size'] > $max_size) {
            return new WP_Error('file_too_large', __('Arquivo muito grande', 'meu-plugin'));
        }
        
        // 4. Gerar nome seguro
        $name = $this->sanitize_filename($file['name']);
        $name = wp_unique_filename(wp_upload_dir()['path'], $name);
        
        // 5. Mover arquivo
        $upload_dir = wp_upload_dir();
        $new_file = $upload_dir['path'] . '/' . $name;
        
        if (!move_uploaded_file($file['tmp_name'], $new_file)) {
            return new WP_Error('move_failed', __('Erro ao mover arquivo', 'meu-plugin'));
        }
        
        // 6. Definir permissões
        chmod($new_file, 0644);
        
        return [
            'path' => $new_file,
            'url' => $upload_dir['url'] . '/' . $name,
        ];
    }
    
    /**
     * Obter MIME type real
     */
    private function get_real_mime_type($file_path) {
        // Usar finfo (melhor método)
        if (function_exists('finfo_file')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file_path);
            finfo_close($finfo);
            return $mime;
        }
        
        // Fallback para getimagesize
        if (function_exists('getimagesize')) {
            $info = @getimagesize($file_path);
            return $info['mime'] ?? 'application/octet-stream';
        }
        
        return 'application/octet-stream';
    }
    
    /**
     * Sanitizar nome do arquivo
     */
    private function sanitize_filename($filename) {
        // Remover caracteres perigosos
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
        
        // Whitelist de extensões
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (!in_array($ext, $allowed_extensions)) {
            $ext = 'jpg';
        }
        
        // Remover extensão original e adicionar segura
        $base = pathinfo($filename, PATHINFO_FILENAME);
        $base = preg_replace('/[^a-zA-Z0-9_-]/', '', $base);
        
        return $base . '.' . $ext;
    }
}
```

---

## 🔌 REST API Security

### Exemplo: Endpoints Seguros

```php
<?php
/**
 * REST API com segurança
 */

class Meu_Plugin_REST_Security {
    
    /**
     * Registrar endpoint seguro
     */
    public function register_endpoint() {
        register_rest_route('meu-plugin/v1', '/dados', [
            'methods' => ['GET', 'POST'],
            'callback' => [$this, 'handle_request'],
            'permission_callback' => function() {
                return current_user_can('manage_meu_plugin');
            },
            'args' => [
                'id' => [
                    'type' => 'integer',
                    'required' => true,
                    'sanitize_callback' => 'absint',
                    'validate_callback' => function($param) {
                        return is_numeric($param);
                    },
                ],
                'name' => [
                    'type' => 'string',
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                    'validate_callback' => function($param) {
                        return strlen($param) >= 3;
                    },
                ],
            ],
        ]);
    }
    
    /**
     * Callback com validações
     */
    public function handle_request($request) {
        // Parâmetros já sanitizados e validados
        $id = $request->get_param('id');
        $name = $request->get_param('name');
        
        // Verificar nonce se POST
        if ('POST' === $request->get_method()) {
            $nonce = $request->get_header('X-WP-Nonce');
            
            if (!wp_verify_nonce($nonce, 'wp_rest')) {
                return new WP_Error(
                    'invalid_nonce',
                    __('Nonce inválido', 'meu-plugin'),
                    ['status' => 403]
                );
            }
        }
        
        // Rate limiting
        if (!$this->check_rate_limit()) {
            return new WP_Error(
                'rate_limit',
                __('Muitas requisições. Tente mais tarde.', 'meu-plugin'),
                ['status' => 429]
            );
        }
        
        // Processar request
        return new WP_REST_Response([
            'success' => true,
            'data' => [
                'id' => $id,
                'name' => $name,
            ],
        ], 200);
    }
    
    /**
     * Rate limiting
     */
    private function check_rate_limit() {
        $user_id = get_current_user_id();
        $transient_key = 'meu_plugin_rate_limit_' . $user_id;
        $count = get_transient($transient_key) ?? 0;
        
        // Máximo 100 requisições por hora
        if ($count >= 100) {
            return false;
        }
        
        set_transient($transient_key, $count + 1, HOUR_IN_SECONDS);
        return true;
    }
}
```

---

## 🔐 Security Headers

### Exemplo: Headers de Segurança

```php
<?php
/**
 * Security Headers
 */

class Meu_Plugin_Security_Headers {
    
    public function __construct() {
        add_action('send_headers', [$this, 'add_security_headers']);
    }
    
    /**
     * Adicionar headers de segurança
     */
    public function add_security_headers() {
        if (is_admin()) {
            return;
        }
        
        // 1. X-Frame-Options (Prevenir Clickjacking)
        header('X-Frame-Options: SAMEORIGIN');
        
        // 2. X-Content-Type-Options (Prevenir MIME sniffing)
        header('X-Content-Type-Options: nosniff');
        
        // 3. X-XSS-Protection (XSS Filter legado)
        header('X-XSS-Protection: 1; mode=block');
        
        // 4. Referrer-Policy
        header('Referrer-Policy: strict-origin-when-cross-origin');
        
        // 5. Permissions-Policy (Feature Policy)
        $permissions = [
            'geolocation=()',
            'microphone=()',
            'camera=()',
            'payment=()',
            'usb=()',
        ];
        header('Permissions-Policy: ' . implode(', ', $permissions));
        
        // 6. Content-Security-Policy
        $this->add_csp_header();
        
        // 7. Strict-Transport-Security (HSTS)
        if (is_ssl()) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
        }
    }
    
    /**
     * Content Security Policy
     */
    private function add_csp_header() {
        $csp_directives = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://trusted-cdn.com",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            "font-src 'self' https://fonts.gstatic.com",
            "img-src 'self' data: https:",
            "connect-src 'self'",
            "frame-ancestors 'self'",
            "base-uri 'self'",
            "form-action 'self'",
        ];
        
        $csp = apply_filters('meu_plugin_csp_directives', $csp_directives);
        
        // Modo enforcement
        header('Content-Security-Policy: ' . implode('; ', $csp));
    }
}

new Meu_Plugin_Security_Headers();
```

---

## 📊 Logging & Monitoring

### Exemplo: Sistema de Logs

```php
<?php
/**
 * Sistema de Logging de Segurança
 */

class Meu_Plugin_Security_Logger {
    
    private $table_name;
    
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'meu_plugin_security_logs';
        
        // Criar tabela na ativação
        register_activation_hook(MEU_PLUGIN_FILE, [$this, 'create_log_table']);
        
        // Hooks de segurança
        add_action('wp_login_failed', [$this, 'log_failed_login']);
        add_action('wp_login', [$this, 'log_successful_login'], 10, 2);
    }
    
    /**
     * Criar tabela de logs
     */
    public function create_log_table() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            event_type varchar(50) NOT NULL,
            severity varchar(20) NOT NULL,
            user_id bigint(20) UNSIGNED,
            username varchar(60),
            ip_address varchar(100) NOT NULL,
            user_agent text,
            message text NOT NULL,
            metadata longtext,
            created_at datetime NOT NULL,
            PRIMARY KEY (id),
            KEY event_type (event_type),
            KEY severity (severity),
            KEY user_id (user_id),
            KEY created_at (created_at),
            KEY ip_address (ip_address)
        ) {$charset_collate};";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Log genérico
     */
    public function log($event_type, $severity, $message, $metadata = []) {
        global $wpdb;
        
        $wpdb->insert(
            $this->table_name,
            [
                'event_type'  => sanitize_key($event_type),
                'severity'    => sanitize_key($severity),
                'user_id'     => get_current_user_id(),
                'username'    => wp_get_current_user()->user_login,
                'ip_address'  => $this->get_client_ip(),
                'user_agent'  => isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field($_SERVER['HTTP_USER_AGENT']) : '',
                'message'     => sanitize_text_field($message),
                'metadata'    => json_encode($metadata),
                'created_at'  => current_time('mysql'),
            ]
        );
    }
    
    /**
     * Log login falho
     */
    public function log_failed_login($username) {
        $this->log(
            'login_failed',
            'warning',
            'Tentativa de login falha',
            ['username' => $username]
        );
    }
    
    /**
     * Log login bem-sucedido
     */
    public function log_successful_login($user_login, $user) {
        $this->log(
            'login_success',
            'info',
            'Login bem-sucedido',
            ['user_id' => $user->ID, 'username' => $user_login]
        );
    }
    
    /**
     * Obter IP do cliente
     */
    private function get_client_ip() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        return sanitize_text_field($ip);
    }
}
```

---

## 🔧 Environment Configuration

### Exemplo: Gerenciamento de Secrets

```php
<?php
/**
 * Gerenciamento de Configurações e Secrets
 */

class Meu_Plugin_Config {
    
    /**
     * Obter configuração do ambiente
     */
    public static function get_config($key, $default = null) {
        // 1. Tentar obter de variável de ambiente
        $env_value = getenv($key);
        if ($env_value !== false) {
            return $env_value;
        }
        
        // 2. Tentar obter de constante do WordPress
        $constant_name = strtoupper($key);
        if (defined($constant_name)) {
            return constant($constant_name);
        }
        
        // 3. Retornar default
        return $default;
    }
    
    /**
     * Obter API key com segurança
     */
    public static function get_api_key() {
        // Prioridade: ENV > Constant > Option
        
        // 1. Variável de ambiente (melhor)
        $key = getenv('MEU_PLUGIN_API_KEY');
        if ($key) {
            return $key;
        }
        
        // 2. Constante no wp-config.php
        if (defined('MEU_PLUGIN_API_KEY')) {
            return MEU_PLUGIN_API_KEY;
        }
        
        // 3. Option do WordPress (menos seguro)
        return get_option('meu_plugin_api_key');
    }
}
```

### Arquivo .env.example

```
# Database
DB_HOST=localhost
DB_NAME=wordpress
DB_USER=root
DB_PASSWORD=

# WordPress
WP_DEBUG=false
WP_DEBUG_LOG=false
WP_DEBUG_DISPLAY=false

# Plugin
MEU_PLUGIN_API_KEY=your-api-key-here
MEU_PLUGIN_API_SECRET=your-secret-here
MEU_PLUGIN_ENV=production
```

---

## 🚨 Incident Response

### Plano de Resposta a Incidentes

```php
<?php
/**
 * Sistema de Resposta a Incidentes
 */

class Meu_Plugin_Incident_Response {
    
    /**
     * Lockdown em caso de ataque
     */
    public function emergency_lockdown() {
        // 1. Bloquear todos os logins
        add_filter('authenticate', function($user) {
            return new WP_Error(
                'site_lockdown',
                __('Site em modo de emergência.', 'meu-plugin')
            );
        }, 9999);
        
        // 2. Desabilitar REST API
        add_filter('rest_authentication_errors', function() {
            return new WP_Error(
                'rest_disabled',
                __('REST API desabilitada.')
            );
        });
        
        // 3. Log do incidente
        error_log('SECURITY ALERT: Emergency lockdown ativado em ' . current_time('mysql'));
    }
    
    /**
     * Checklist de resposta a incidente
     */
    public function incident_checklist() {
        return [
            '1. Identificar a vulnerabilidade',
            '2. Ativar modo emergência',
            '3. Fazer backup do site',
            '4. Verificar logs de acesso',
            '5. Reverter alterações não autorizadas',
            '6. Atualizar plugins/temas',
            '7. Resetar senhas de admin',
            '8. Escanear malware',
            '9. Revisar permissões de arquivo',
            '10. Monitorar por re-infecção',
        ];
    }
}
```

---

## ✅ Code Review Checklist

```php
<?php
/**
 * Security Code Review Checklist
 */

/*
INPUT/OUTPUT:
□ sanitize_text_field() para inputs
□ sanitize_email() para emails
□ esc_html() para output HTML
□ esc_attr() para atributos
□ esc_url() para URLs
□ wp_kses() para HTML com tags permitidas
□ wp_json_encode() para JavaScript

NONCES:
□ wp_nonce_field() em formulários
□ wp_verify_nonce() no processamento
□ wp_nonce_url() em links de ação

CAPABILITIES:
□ current_user_can() para permissões
□ Capabilities apropriadas usadas?
□ Verificação de ownership para edições?

FILES:
□ MIME type real verificado (finfo)?
□ Extensão validada contra whitelist?
□ Tamanho verificado?
□ Nome sanitizado?
□ Path traversal prevenido?

DIRECT ACCESS:
□ defined('ABSPATH') no topo dos arquivos?
□ Nenhum arquivo PHP acessível diretamente?

ERRORS:
□ Errors não expostos ao usuário?
□ Logging apropriado em produção?
□ WP_DEBUG false em produção?

SENSITIVE DATA:
□ Senhas/secrets não em código?
□ Dados sensíveis criptografados?
□ Logs não contêm dados sensíveis?

APIs:
□ Autenticação em endpoints?
□ Rate limiting implementado?
□ Input validado?

GENERAL:
□ Código segue WordPress Coding Standards?
□ Funções deprecated não usadas?
□ Escopo de variáveis apropriado?
□ Nenhum eval() ou create_function()?
*/
```

---

## 🎯 Best Practices Finais

### 20 Mantras de Segurança

```
🔐 "Validate Input, Sanitize Data, Escape Output"
🔐 "Never Trust, Always Verify"
🔐 "Defense in Depth"
🔐 "Principle of Least Privilege"
🔐 "Fail Securely"
🔐 "Security by Design, not by Accident"
🔐 "Keep It Simple"
🔐 "Assume Breach"
🔐 "Defense in Depth"
🔐 "Secure by Default"
🔐 "Check Thoroughly"
🔐 "Log Everything"
🔐 "Monitor Constantly"
🔐 "Update Regularly"
🔐 "Test Security"
🔐 "Have a Plan"
🔐 "Train Your Team"
🔐 "Use Standards"
🔐 "Review Code"
🔐 "Automate Security"
```

### Ferramentas Recomendadas

**Security Scanning:**
- Wordfence
- Sucuri Security
- iThemes Security
- WP Security Audit Log

**Code Analysis:**
- PHPCS (WordPress Coding Standards)
- PHPStan
- Psalm
- SonarQube

**Dependency Scanning:**
- Composer audit
- npm audit
- Snyk
- GitHub Dependabot

**Monitoring:**
- Sentry (error tracking)
- New Relic (APM)
- Query Monitor
- Debug Bar

**Testing:**
- PHPUnit
- WP_UnitTestCase
- Codeception
- WP_Browser

---

## 📋 Servidor Web Security

### Nginx Configuration

```nginx
# Security headers
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-Content-Type-Options "nosniff" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
add_header Permissions-Policy "geolocation=(), microphone=(), camera=()" always;

# Bloquear acesso a arquivos sensíveis
location ~ /wp-config.php {
    deny all;
}

location ~ /wp-settings.php {
    deny all;
}

location ~ /\.htaccess {
    deny all;
}

location ~ /\.git {
    deny all;
}

location ~ /readme.html {
    deny all;
}

# Desabilitar PHP em uploads
location ~* /wp-content/uploads/.*\.php$ {
    deny all;
}

# Rate limiting
limit_req_zone $binary_remote_addr zone=login:10m rate=5r/m;

location = /wp-login.php {
    limit_req zone=login burst=5 nodelay;
}
```

---

## 🎉 Resumo da Fase 12

### ✅ Tópicos Abordados

1. **Fundamentos de Segurança** - Vulnerabilidades e princípios
2. **Input Validation** - Sanitização completa
3. **Output Escaping** - Contextos de escape
4. **Nonces** - Proteção CSRF
5. **Capabilities** - Gerenciamento de permissões
6. **Prepared Statements** - Queries seguras
7. **Authentication** - Login e senhas
8. **File Upload** - Upload seguro
9. **REST API Security** - Endpoints seguros
10. **Security Headers** - Headers HTTP
11. **Logging** - Monitoramento de eventos
12. **Environment Config** - Gestão de secrets
13. **Incident Response** - Plano de resposta
14. **Server Security** - Configuração do servidor
15. **Code Review** - Checklist de segurança

### 🚀 Conclusão Completa

Você agora domina:

✅ Desenvolvimento completo de plugins WordPress  
✅ APIs oficiais do WordPress  
✅ Segurança em todos os níveis  
✅ Testing automatizado  
✅ CI/CD e deployment  
✅ Multisite e internacionalização  
✅ Performance e otimização  
✅ Debugging e monitoramento  
✅ Boas práticas de produção  

---

**Documentos relacionados:** [Fase 19 - Anti-padrões de Segurança](./019-WordPress-Fase-19-Anti-padroes-Seguranca.md) — Padrões inseguros a evitar

---

**Data de Conclusão:** Janeiro 2026  
**Total de Fases:** 12  
**Horas de Conteúdo:** 240+  
**Nível Atingido:** Especialista em WordPress Development
