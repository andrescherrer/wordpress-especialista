# 🔐 WordPress Security Anti-patterns

**Versão:** 1.0  
**Data:** Fevereiro 2026  
**Nível:** Especialista em PHP  
**Objetivo:** Identificar e evitar padrões inseguros comuns no desenvolvimento WordPress

---

**Navegação:** [Índice](000-WordPress-Topicos-Index.md) | [← Learning Paths](018-WordPress-Learning-Paths.md) | [Fase 12: Segurança](012-WordPress-Fase-12-Seguranca-Boas-Praticas.md) | [Error Handling →](020-WordPress-Error-Handling-Best-Practices.md)

---

## 📑 Índice

1. [Fase 1: Core Security Mistakes](#fase-1-core-security-mistakes)
2. [Fase 2: REST API Security Mistakes](#fase-2-rest-api-security-mistakes)
3. [Fase 4: Settings API Security Mistakes](#fase-4-settings-api-security-mistakes)
4. [Fase 5: CPT Security Mistakes](#fase-5-cpt-security-mistakes)
5. [Fase 13: Architecture Security Mistakes](#fase-13-architecture-security-mistakes)
6. [Fase 14: DevOps Security Mistakes](#fase-14-devops-security-mistakes)
7. [Code Review Checklist](#code-review-checklist)

---

## Fase 1: Core Security Mistakes

### ❌ Anti-pattern 1.1: Output User Input Directly (XSS)

**Problema:** Exibir dados do usuário sem escapar permite Cross-Site Scripting (XSS).

```php
<?php
// ❌ ERRADO: Vulnerável a XSS
$user_name = $_GET['name'];
echo "<h1>Welcome, $user_name!</h1>";

// ❌ ERRADO: Mesmo problema com variáveis de template
$title = get_post_meta($post_id, 'custom_title', true);
echo "<h2>$title</h2>";
```

**Impacto:**
- Atacante pode injetar JavaScript malicioso
- Roubo de cookies de sessão
- Redirecionamento para sites maliciosos
- Modificação de conteúdo da página

**✅ Solução Correta:**

```php
<?php
// ✅ CORRETO: Escapar output HTML
$user_name = sanitize_text_field($_GET['name']);
echo "<h1>Welcome, " . esc_html($user_name) . "!</h1>";

// ✅ CORRETO: Escapar em diferentes contextos
$title = get_post_meta($post_id, 'custom_title', true);
echo "<h2>" . esc_html($title) . "</h2>";                    // HTML
echo "<a href='" . esc_url($url) . "'>Link</a>";             // URL
echo "<script>var data = " . wp_json_encode($data) . ";</script>"; // JavaScript
echo esc_attr($value);                                        // Atributo HTML
```

**Contextos de Escape:**
- `esc_html()` - Para conteúdo HTML
- `esc_attr()` - Para atributos HTML
- `esc_url()` - Para URLs
- `esc_js()` - Para JavaScript
- `wp_json_encode()` - Para dados JSON
- `esc_textarea()` - Para textareas

---

### ❌ Anti-pattern 1.2: Direct SQL Queries (SQL Injection)

**Problema:** Construir queries SQL concatenando strings permite SQL Injection.

```php
<?php
// ❌ ERRADO: Vulnerável a SQL Injection
$user_id = $_GET['id'];
$query = "SELECT * FROM wp_users WHERE ID = $user_id";
$results = $wpdb->get_results($query);

// ❌ ERRADO: Mesmo problema com strings
$search = $_POST['search'];
$query = "SELECT * FROM wp_posts WHERE post_title LIKE '%$search%'";
$results = $wpdb->get_results($query);
```

**Impacto:**
- Leitura de dados sensíveis do banco
- Modificação ou exclusão de dados
- Acesso não autorizado a outras tabelas
- Comprometimento completo do banco de dados

**✅ Solução Correta:**

```php
<?php
// ✅ CORRETO: Usar prepared statements
$user_id = absint($_GET['id']);
$query = $wpdb->prepare(
    "SELECT * FROM {$wpdb->users} WHERE ID = %d",
    $user_id
);
$results = $wpdb->get_results($query);

// ✅ CORRETO: Para strings
$search = sanitize_text_field($_POST['search']);
$query = $wpdb->prepare(
    "SELECT * FROM {$wpdb->posts} WHERE post_title LIKE %s",
    '%' . $wpdb->esc_like($search) . '%'
);
$results = $wpdb->get_results($query);

// ✅ CORRETO: Múltiplos parâmetros
$query = $wpdb->prepare(
    "SELECT * FROM {$wpdb->posts} WHERE post_status = %s AND post_type = %s AND post_author = %d",
    'publish',
    'post',
    $author_id
);
```

**Placeholders:**
- `%d` - Inteiro
- `%s` - String
- `%f` - Float

---

### ❌ Anti-pattern 1.3: Trust User Roles Without Check

**Problema:** Assumir que usuário tem permissão sem verificar capabilities.

```php
<?php
// ❌ ERRADO: Confiar em role sem verificar
add_action('admin_init', function() {
    if (current_user_can('administrator')) {
        // Mas e se role foi modificado?
        delete_all_posts();
    }
});

// ❌ ERRADO: Verificar apenas se está logado
if (is_user_logged_in()) {
    // Qualquer usuário logado pode executar
    wp_delete_post($post_id);
}
```

**Impacto:**
- Escalação de privilégios
- Acesso não autorizado a funcionalidades administrativas
- Modificação ou exclusão de dados por usuários sem permissão

**✅ Solução Correta:**

```php
<?php
// ✅ CORRETO: Verificar capability específica
add_action('admin_init', function() {
    if (!current_user_can('delete_posts')) {
        wp_die(__('Você não tem permissão para executar esta ação.'));
    }
    delete_all_posts();
});

// ✅ CORRETO: Verificar capability para ação específica
if (current_user_can('delete_post', $post_id)) {
    wp_delete_post($post_id);
} else {
    wp_die(__('Você não tem permissão para deletar este post.'));
}

// ✅ CORRETO: Verificar nonce também
if (isset($_POST['action']) && $_POST['action'] === 'delete_post') {
    if (!wp_verify_nonce($_POST['_wpnonce'], 'delete_post_' . $post_id)) {
        wp_die(__('Nonce inválido.'));
    }
    
    if (!current_user_can('delete_post', $post_id)) {
        wp_die(__('Sem permissão.'));
    }
    
    wp_delete_post($post_id);
}
```

**Capabilities Comuns:**
- `edit_posts` - Editar posts próprios
- `edit_others_posts` - Editar posts de outros
- `delete_posts` - Deletar posts próprios
- `delete_others_posts` - Deletar posts de outros
- `publish_posts` - Publicar posts
- `manage_options` - Gerenciar opções

---

### ❌ Anti-pattern 1.4: Store Sensitive Data in Post Meta

**Problema:** Armazenar dados sensíveis em post meta sem criptografia.

```php
<?php
// ❌ ERRADO: Armazenar dados sensíveis sem proteção
update_post_meta($post_id, '_credit_card', $_POST['credit_card']);
update_post_meta($post_id, '_password', $_POST['password']);
update_post_meta($post_id, '_api_key', 'secret-key-12345');
update_post_meta($post_id, '_ssn', $_POST['ssn']);
```

**Impacto:**
- Dados sensíveis acessíveis via API REST
- Exposição em backups
- Acesso por qualquer plugin com permissão de leitura
- Violação de LGPD/GDPR

**✅ Solução Correta:**

```php
<?php
// ✅ CORRETO: Criptografar dados sensíveis
function encrypt_sensitive_data($data) {
    if (!function_exists('openssl_encrypt')) {
        return false;
    }
    
    $key = wp_salt('auth'); // Usar salt do WordPress
    $iv = openssl_random_pseudo_bytes(16);
    $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
    
    return base64_encode($iv . $encrypted);
}

function decrypt_sensitive_data($encrypted_data) {
    if (!function_exists('openssl_decrypt')) {
        return false;
    }
    
    $data = base64_decode($encrypted_data);
    $iv = substr($data, 0, 16);
    $encrypted = substr($data, 16);
    $key = wp_salt('auth');
    
    return openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
}

// Armazenar criptografado
$encrypted_card = encrypt_sensitive_data($_POST['credit_card']);
update_post_meta($post_id, '_credit_card', $encrypted_card);

// Recuperar e descriptografar
$encrypted = get_post_meta($post_id, '_credit_card', true);
$decrypted = decrypt_sensitive_data($encrypted);

// ✅ CORRETO: Usar opções com autoload = false para dados sensíveis
// Ou melhor ainda: não armazenar dados sensíveis, usar tokens
update_option('_api_key_hash', wp_hash_password('secret-key-12345'), false);
```

**Alternativa: Não Armazenar**
- Para cartões de crédito: usar tokens de gateway de pagamento
- Para senhas: nunca armazenar, apenas hash
- Para API keys: armazenar hash, não valor original

---

## Fase 2: REST API Security Mistakes

### ❌ Anti-pattern 2.1: No Input Validation

**Problema:** Aceitar dados sem validação permite dados inválidos ou maliciosos.

```php
<?php
// ❌ ERRADO: Sem validação
register_rest_route('myapp/v1', '/users', [
    'methods' => 'POST',
    'callback' => function($request) {
        $data = $request->get_json_params();
        
        // Aceita qualquer coisa!
        $user_id = wp_insert_user([
            'user_login' => $data['username'],
            'user_email' => $data['email'],
            'user_pass' => $data['password'],
        ]);
        
        return new WP_REST_Response(['id' => $user_id], 201);
    },
    'permission_callback' => '__return_true',
]);
```

**Impacto:**
- Dados inválidos no banco
- Violação de integridade de dados
- Possível SQL Injection se usado em queries
- Comportamento inesperado da aplicação

**✅ Solução Correta:**

```php
<?php
// ✅ CORRETO: Validação completa
register_rest_route('myapp/v1', '/users', [
    'methods' => 'POST',
    'callback' => function($request) {
        $data = $request->get_json_params();
        $errors = [];
        
        // Validar username
        if (empty($data['username']) || !is_string($data['username'])) {
            $errors['username'] = 'Username é obrigatório e deve ser string';
        } elseif (strlen($data['username']) < 3) {
            $errors['username'] = 'Username deve ter pelo menos 3 caracteres';
        } elseif (!validate_username($data['username'])) {
            $errors['username'] = 'Username inválido';
        }
        
        // Validar email
        if (empty($data['email']) || !is_email($data['email'])) {
            $errors['email'] = 'Email inválido';
        }
        
        // Validar password
        if (empty($data['password']) || strlen($data['password']) < 8) {
            $errors['password'] = 'Senha deve ter pelo menos 8 caracteres';
        }
        
        if (!empty($errors)) {
            return new WP_Error('validation_error', 'Dados inválidos', [
                'status' => 400,
                'errors' => $errors,
            ]);
        }
        
        // Sanitizar antes de usar
        $user_id = wp_insert_user([
            'user_login' => sanitize_user($data['username']),
            'user_email' => sanitize_email($data['email']),
            'user_pass' => $data['password'], // wp_insert_user já sanitiza
        ]);
        
        if (is_wp_error($user_id)) {
            return new WP_Error('user_creation_failed', $user_id->get_error_message(), [
                'status' => 500,
            ]);
        }
        
        return new WP_REST_Response(['id' => $user_id], 201);
    },
    'permission_callback' => '__return_true',
    // ✅ CORRETO: Validação via args
    'args' => [
        'username' => [
            'required' => true,
            'type' => 'string',
            'validate_callback' => function($param) {
                return validate_username($param) && strlen($param) >= 3;
            },
            'sanitize_callback' => 'sanitize_user',
        ],
        'email' => [
            'required' => true,
            'type' => 'string',
            'validate_callback' => 'is_email',
            'sanitize_callback' => 'sanitize_email',
        ],
        'password' => [
            'required' => true,
            'type' => 'string',
            'validate_callback' => function($param) {
                return strlen($param) >= 8;
            },
        ],
    ],
]);
```

---

### ❌ Anti-pattern 2.2: No Permission Checks

**Problema:** Endpoints públicos sem verificação de permissão permitem acesso não autorizado.

```php
<?php
// ❌ ERRADO: Endpoint público que deleta posts
register_rest_route('myapp/v1', '/posts/(?P<id>\d+)', [
    'methods' => 'DELETE',
    'callback' => function($request) {
        $post_id = $request->get_param('id');
        wp_delete_post($post_id, true);
        return new WP_REST_Response(['success' => true], 200);
    },
    'permission_callback' => '__return_true', // ❌ Qualquer um pode deletar!
]);
```

**Impacto:**
- Qualquer pessoa pode deletar posts
- Modificação não autorizada de dados
- Acesso a informações sensíveis
- Comprometimento da integridade dos dados

**✅ Solução Correta:**

```php
<?php
// ✅ CORRETO: Verificar permissão específica
register_rest_route('myapp/v1', '/posts/(?P<id>\d+)', [
    'methods' => 'DELETE',
    'callback' => function($request) {
        $post_id = $request->get_param('id');
        
        // Verificar se post existe
        $post = get_post($post_id);
        if (!$post) {
            return new WP_Error('post_not_found', 'Post não encontrado', [
                'status' => 404,
            ]);
        }
        
        // Verificar permissão específica para este post
        if (!current_user_can('delete_post', $post_id)) {
            return new WP_Error('forbidden', 'Você não tem permissão para deletar este post', [
                'status' => 403,
            ]);
        }
        
        $result = wp_delete_post($post_id, true);
        
        if (!$result) {
            return new WP_Error('deletion_failed', 'Falha ao deletar post', [
                'status' => 500,
            ]);
        }
        
        return new WP_REST_Response(['success' => true], 200);
    },
    'permission_callback' => function($request) {
        // Verificar se usuário está autenticado
        if (!is_user_logged_in()) {
            return false;
        }
        
        // Verificar capability básica
        return current_user_can('delete_posts');
    },
]);

// ✅ CORRETO: Permissões diferentes por método
register_rest_route('myapp/v1', '/posts', [
    [
        'methods' => 'GET',
        'callback' => 'get_posts_callback',
        'permission_callback' => '__return_true', // Público pode ler
    ],
    [
        'methods' => 'POST',
        'callback' => 'create_post_callback',
        'permission_callback' => function() {
            return current_user_can('publish_posts');
        },
    ],
]);
```

---

### ❌ Anti-pattern 2.3: Expose Internal IDs

**Problema:** Expor IDs internos do banco de dados facilita enumeração e ataques.

```php
<?php
// ❌ ERRADO: Expor IDs sequenciais
register_rest_route('myapp/v1', '/users', [
    'methods' => 'GET',
    'callback' => function($request) {
        $users = get_users();
        $response = [];
        
        foreach ($users as $user) {
            $response[] = [
                'id' => $user->ID, // ❌ ID sequencial exposto
                'username' => $user->user_login,
                'email' => $user->user_email,
            ];
        }
        
        return new WP_REST_Response($response, 200);
    },
]);
```

**Impacto:**
- Enumeração de usuários
- Ataques de força bruta facilitados
- Descoberta de estrutura do banco
- Violação de privacidade

**✅ Solução Correta:**

```php
<?php
// ✅ CORRETO: Usar UUIDs ou hashes
function generate_user_public_id($user_id) {
    return hash_hmac('sha256', $user_id . wp_salt('auth'), wp_salt('logged_in'));
}

register_rest_route('myapp/v1', '/users', [
    'methods' => 'GET',
    'callback' => function($request) {
        $users = get_users();
        $response = [];
        
        foreach ($users as $user) {
            $response[] = [
                'id' => generate_user_public_id($user->ID), // ✅ Hash ao invés de ID
                'username' => $user->user_login,
                'email' => $user->user_email,
            ];
        }
        
        return new WP_REST_Response($response, 200);
    },
]);

// ✅ CORRETO: Filtrar dados sensíveis
register_rest_route('myapp/v1', '/users', [
    'methods' => 'GET',
    'callback' => function($request) {
        $users = get_users();
        $response = [];
        
        foreach ($users as $user) {
            $response[] = [
                'id' => generate_user_public_id($user->ID),
                'username' => $user->user_login,
                // Não expor email para usuários não autenticados
                'email' => current_user_can('list_users') ? $user->user_email : null,
                'display_name' => $user->display_name,
                // Não expor dados internos
                // 'user_registered' => $user->user_registered, // ❌ Remove
                // 'user_status' => $user->user_status, // ❌ Remove
            ];
        }
        
        return new WP_REST_Response($response, 200);
    },
    'permission_callback' => '__return_true',
]);
```

---

### ❌ Anti-pattern 2.4: Log Sensitive Data

**Problema:** Registrar dados sensíveis em logs pode expor informações.

```php
<?php
// ❌ ERRADO: Logar dados sensíveis
register_rest_route('myapp/v1', '/login', [
    'methods' => 'POST',
    'callback' => function($request) {
        $data = $request->get_json_params();
        
        error_log('Login attempt: ' . $data['username'] . ' / ' . $data['password']); // ❌
        
        $user = wp_authenticate($data['username'], $data['password']);
        
        if (is_wp_error($user)) {
            error_log('Login failed: ' . serialize($user)); // ❌ Pode conter dados sensíveis
        }
        
        return new WP_REST_Response(['token' => '...'], 200);
    },
]);
```

**Impacto:**
- Senhas em logs
- Tokens de acesso em logs
- Dados pessoais em logs
- Violação de LGPD/GDPR
- Comprometimento se logs forem acessados

**✅ Solução Correta:**

```php
<?php
// ✅ CORRETO: Logar apenas informações seguras
register_rest_route('myapp/v1', '/login', [
    'methods' => 'POST',
    'callback' => function($request) {
        $data = $request->get_json_params();
        $username = sanitize_user($data['username']);
        
        // ✅ Logar apenas username (não senha)
        error_log('Login attempt for user: ' . $username);
        
        $user = wp_authenticate($username, $data['password']);
        
        if (is_wp_error($user)) {
            // ✅ Logar apenas código de erro, não detalhes sensíveis
            error_log('Login failed for user: ' . $username . ' - Error: ' . $user->get_error_code());
            
            // ✅ Não expor detalhes do erro ao cliente
            return new WP_Error('authentication_failed', 'Credenciais inválidas', [
                'status' => 401,
            ]);
        }
        
        // ✅ Gerar token seguro
        $token = wp_generate_password(32, false);
        update_user_meta($user->ID, '_api_token', wp_hash_password($token));
        
        // ✅ Não logar token
        // error_log('Token generated: ' . $token); // ❌ NUNCA
        
        return new WP_REST_Response([
            'user_id' => $user->ID,
            'token' => $token, // Enviar apenas uma vez, nunca logar
        ], 200);
    },
]);

// ✅ CORRETO: Função helper para logging seguro
function secure_log($message, $context = []) {
    $safe_context = [];
    
    foreach ($context as $key => $value) {
        // Remover campos sensíveis
        if (in_array($key, ['password', 'token', 'api_key', 'secret', 'credit_card', 'ssn'])) {
            $safe_context[$key] = '[REDACTED]';
        } else {
            $safe_context[$key] = $value;
        }
    }
    
    error_log($message . ' - Context: ' . wp_json_encode($safe_context));
}
```

---

## Fase 4: Settings API Security Mistakes

### ❌ Anti-pattern 4.1: No Validation

**Problema:** Aceitar dados de settings sem validação permite valores inválidos.

```php
<?php
// ❌ ERRADO: Sem validação
register_setting('myapp_options', 'myapp_email', []);

add_action('admin_init', function() {
    if (isset($_POST['myapp_email'])) {
        // Aceita qualquer coisa!
        update_option('myapp_email', $_POST['myapp_email']);
    }
});
```

**Impacto:**
- Dados inválidos salvos no banco
- Comportamento inesperado da aplicação
- Possível XSS se dados forem exibidos sem escape

**✅ Solução Correta:**

```php
<?php
// ✅ CORRETO: Validação e sanitização
register_setting('myapp_options', 'myapp_email', [
    'type' => 'string',
    'sanitize_callback' => function($value) {
        $email = sanitize_email($value);
        
        if (!is_email($email)) {
            add_settings_error(
                'myapp_email',
                'invalid_email',
                'Email inválido'
            );
            return get_option('myapp_email'); // Retornar valor anterior
        }
        
        return $email;
    },
    'validate_callback' => function($value) {
        return is_email($value);
    },
    'default' => '',
]);

// ✅ CORRETO: Validação customizada
register_setting('myapp_options', 'myapp_api_rate_limit', [
    'type' => 'integer',
    'sanitize_callback' => 'absint',
    'validate_callback' => function($value) {
        if ($value < 1 || $value > 1000) {
            add_settings_error(
                'myapp_api_rate_limit',
                'invalid_range',
                'Rate limit deve estar entre 1 e 1000'
            );
            return false;
        }
        return true;
    },
    'default' => 100,
]);
```

---

### ❌ Anti-pattern 4.2: Save Raw User Input

**Problema:** Salvar input do usuário sem sanitização permite código malicioso.

```php
<?php
// ❌ ERRADO: Salvar input sem sanitizar
add_action('admin_init', function() {
    register_setting('myapp_options', 'myapp_custom_html');
});

add_settings_field('myapp_custom_html', 'HTML Customizado', function() {
    $value = get_option('myapp_custom_html', '');
    echo "<textarea name='myapp_custom_html'>$value</textarea>"; // ❌ Também sem escape
}, 'myapp_settings', 'myapp_section');

// Salvar diretamente
update_option('myapp_custom_html', $_POST['myapp_custom_html']); // ❌
```

**Impacto:**
- XSS se HTML for exibido sem escape
- Execução de JavaScript malicioso
- Comprometimento da segurança do admin

**✅ Solução Correta:**

```php
<?php
// ✅ CORRETO: Sanitizar antes de salvar
register_setting('myapp_options', 'myapp_custom_html', [
    'type' => 'string',
    'sanitize_callback' => function($value) {
        // Permitir apenas tags específicas
        $allowed_tags = [
            'p' => [],
            'strong' => [],
            'em' => [],
            'a' => ['href' => [], 'title' => []],
        ];
        
        return wp_kses($value, $allowed_tags);
    },
]);

add_settings_field('myapp_custom_html', 'HTML Customizado', function() {
    $value = get_option('myapp_custom_html', '');
    // ✅ Escapar ao exibir também
    echo "<textarea name='myapp_custom_html'>" . esc_textarea($value) . "</textarea>";
}, 'myapp_settings', 'myapp_section');

// ✅ CORRETO: Para campos que precisam de HTML completo (com cuidado!)
register_setting('myapp_options', 'myapp_advanced_html', [
    'type' => 'string',
    'sanitize_callback' => function($value) {
        // Usar wp_kses_post para tags permitidas pelo WordPress
        return wp_kses_post($value);
    },
]);
```

---

### ❌ Anti-pattern 4.3: Hardcoded Credentials

**Problema:** Credenciais hardcoded no código são expostas em repositórios.

```php
<?php
// ❌ ERRADO: Credenciais hardcoded
define('API_KEY', 'sk_live_1234567890abcdef');
define('DB_PASSWORD', 'senha123');
$api_secret = 'secret-key-abc123';

class PaymentGateway {
    private $api_key = 'sk_live_1234567890abcdef'; // ❌
    
    public function process_payment() {
        // Usa API key hardcoded
    }
}
```

**Impacto:**
- Credenciais expostas em Git
- Acesso não autorizado a serviços externos
- Comprometimento de contas de API
- Custos financeiros se API for paga

**✅ Solução Correta:**

```php
<?php
// ✅ CORRETO: Usar constantes de ambiente
define('API_KEY', getenv('MYAPP_API_KEY') ?: '');
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: '');

// ✅ CORRETO: Usar wp-config.php (não versionado)
// Em wp-config.php:
define('MYAPP_API_KEY', 'sk_live_...'); // Não versionar este arquivo!

// ✅ CORRETO: Usar get_option com fallback
class PaymentGateway {
    private function get_api_key() {
        $key = get_option('myapp_api_key');
        
        if (empty($key)) {
            // Fallback para constante (desenvolvimento)
            $key = defined('MYAPP_API_KEY') ? MYAPP_API_KEY : '';
        }
        
        return $key;
    }
    
    public function process_payment() {
        $api_key = $this->get_api_key();
        // Usar API key
    }
}

// ✅ CORRETO: Usar .env (com dotenv library)
// composer require vlucas/phpdotenv
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$api_key = $_ENV['API_KEY'];
```

**Arquivo .env.example (versionado):**
```
API_KEY=your_api_key_here
DB_PASSWORD=your_password_here
```

**Arquivo .env (NÃO versionado):**
```
API_KEY=sk_live_1234567890abcdef
DB_PASSWORD=senha123
```

---

## Fase 5: CPT Security Mistakes

### ❌ Anti-pattern 5.1: No Capability Checks

**Problema:** Permitir ações em CPTs sem verificar capabilities.

```php
<?php
// ❌ ERRADO: Sem verificação de capability
add_action('save_post_product', function($post_id) {
    // Qualquer um pode executar se conseguir salvar post
    update_post_meta($post_id, '_price', $_POST['price']);
    update_post_meta($post_id, '_stock', $_POST['stock']);
});

// ❌ ERRADO: Meta box sem verificação
add_action('add_meta_boxes', function() {
    add_meta_box(
        'product_data',
        'Dados do Produto',
        function($post) {
            // Sem verificar se usuário pode editar
            echo '<input name="price" value="' . get_post_meta($post->ID, '_price', true) . '">';
        },
        'product'
    );
});
```

**Impacto:**
- Usuários sem permissão podem modificar dados
- Escalação de privilégios
- Modificação não autorizada de conteúdo

**✅ Solução Correta:**

```php
<?php
// ✅ CORRETO: Verificar capability específica
add_action('save_post_product', function($post_id) {
    // Verificar nonce primeiro
    if (!isset($_POST['product_meta_nonce']) || 
        !wp_verify_nonce($_POST['product_meta_nonce'], 'save_product_meta')) {
        return;
    }
    
    // Verificar autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Verificar capability
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Agora seguro para salvar
    if (isset($_POST['price'])) {
        update_post_meta($post_id, '_price', sanitize_text_field($_POST['price']));
    }
    
    if (isset($_POST['stock'])) {
        update_post_meta($post_id, '_stock', absint($_POST['stock']));
    }
});

// ✅ CORRETO: Meta box com verificação
add_action('add_meta_boxes', function() {
    add_meta_box(
        'product_data',
        'Dados do Produto',
        function($post) {
            // Verificar capability
            if (!current_user_can('edit_post', $post->ID)) {
                echo '<p>Você não tem permissão para editar este produto.</p>';
                return;
            }
            
            wp_nonce_field('save_product_meta', 'product_meta_nonce');
            
            $price = get_post_meta($post->ID, '_price', true);
            echo '<input name="price" value="' . esc_attr($price) . '">';
        },
        'product',
        'normal',
        'high'
    );
});
```

---

### ❌ Anti-pattern 5.2: Expose Draft Posts

**Problema:** Expor posts em draft via REST API ou queries públicas.

```php
<?php
// ❌ ERRADO: Expor drafts publicamente
register_rest_route('myapp/v1', '/products', [
    'methods' => 'GET',
    'callback' => function($request) {
        $query = new WP_Query([
            'post_type' => 'product',
            'posts_per_page' => -1,
            // ❌ Sem post_status, pode incluir drafts
        ]);
        
        $products = [];
        foreach ($query->posts as $post) {
            $products[] = [
                'id' => $post->ID,
                'title' => $post->post_title,
                'status' => $post->post_status, // ❌ Expõe status
            ];
        }
        
        return new WP_REST_Response($products, 200);
    },
    'permission_callback' => '__return_true', // ❌ Público
]);
```

**Impacto:**
- Conteúdo não publicado exposto
- Informações sensíveis em drafts visíveis
- Violação de privacidade
- Conteúdo roubado antes de publicação

**✅ Solução Correta:**

```php
<?php
// ✅ CORRETO: Filtrar por status e verificar permissões
register_rest_route('myapp/v1', '/products', [
    'methods' => 'GET',
    'callback' => function($request) {
        $args = [
            'post_type' => 'product',
            'posts_per_page' => 10,
            'post_status' => 'publish', // ✅ Apenas publicados
        ];
        
        // Se usuário autenticado e tem permissão, pode ver drafts próprios
        if (is_user_logged_in() && current_user_can('edit_products')) {
            $author_id = get_current_user_id();
            $args['post_status'] = ['publish', 'draft', 'pending'];
            $args['author'] = $author_id; // Apenas próprios drafts
        }
        
        $query = new WP_Query($args);
        
        $products = [];
        foreach ($query->posts as $post) {
            // ✅ Não expor status para usuários não autorizados
            $product = [
                'id' => $post->ID,
                'title' => $post->post_title,
            ];
            
            // Apenas incluir status se usuário tem permissão
            if (current_user_can('edit_post', $post->ID)) {
                $product['status'] = $post->post_status;
            }
            
            $products[] = $product;
        }
        
        return new WP_REST_Response($products, 200);
    },
    'permission_callback' => '__return_true',
]);
```

---

### ❌ Anti-pattern 5.3: Meta Box XSS

**Problema:** Exibir dados de meta boxes sem escape permite XSS.

```php
<?php
// ❌ ERRADO: Sem escape em meta box
add_meta_box('product_description', 'Descrição', function($post) {
    $description = get_post_meta($post->ID, '_description', true);
    echo "<div>$description</div>"; // ❌ XSS se contiver JavaScript
}, 'product');

// ❌ ERRADO: Input sem sanitização
add_meta_box('product_data', 'Dados', function($post) {
    $value = get_post_meta($post->ID, '_custom_field', true);
    echo "<input name='custom_field' value='$value'>"; // ❌ XSS em atributo
}, 'product');
```

**Impacto:**
- XSS no admin do WordPress
- Execução de JavaScript malicioso
- Comprometimento da conta de admin

**✅ Solução Correta:**

```php
<?php
// ✅ CORRETO: Escapar output
add_meta_box('product_description', 'Descrição', function($post) {
    $description = get_post_meta($post->ID, '_description', true);
    echo "<div>" . wp_kses_post($description) . "</div>"; // ✅ Escapar HTML
}, 'product');

// ✅ CORRETO: Escapar atributos
add_meta_box('product_data', 'Dados', function($post) {
    wp_nonce_field('save_product_meta', 'product_meta_nonce');
    
    $value = get_post_meta($post->ID, '_custom_field', true);
    echo "<input name='custom_field' value='" . esc_attr($value) . "'>"; // ✅ Escapar atributo
}, 'product');

// ✅ CORRETO: Sanitizar ao salvar também
add_action('save_post_product', function($post_id) {
    if (!isset($_POST['custom_field'])) {
        return;
    }
    
    // Sanitizar antes de salvar
    $sanitized = sanitize_text_field($_POST['custom_field']);
    update_post_meta($post_id, '_custom_field', $sanitized);
});
```

---

## Fase 13: Architecture Security Mistakes

### ❌ Anti-pattern 13.1: DI Without Validation

**Problema:** Injetar dependências sem validar permite objetos inválidos ou maliciosos.

```php
<?php
// ❌ ERRADO: DI sem validação
class PaymentService {
    private $gateway;
    
    public function __construct($gateway) {
        $this->gateway = $gateway; // ❌ Aceita qualquer coisa
    }
    
    public function process($amount) {
        return $this->gateway->charge($amount); // ❌ Pode falhar ou ser malicioso
    }
}

// Uso inseguro
$malicious_gateway = new MaliciousGateway();
$service = new PaymentService($malicious_gateway); // ❌ Aceita
```

**Impacto:**
- Objetos inválidos injetados
- Comportamento inesperado
- Possível execução de código malicioso
- Falhas silenciosas

**✅ Solução Correta:**

```php
<?php
// ✅ CORRETO: Validar tipo de dependência
interface PaymentGatewayInterface {
    public function charge($amount);
}

class PaymentService {
    private $gateway;
    
    public function __construct(PaymentGatewayInterface $gateway) {
        // ✅ Type hint garante interface correta
        if (!$gateway instanceof PaymentGatewayInterface) {
            throw new InvalidArgumentException('Gateway must implement PaymentGatewayInterface');
        }
        
        $this->gateway = $gateway;
    }
    
    public function process($amount) {
        // Validar amount também
        if (!is_numeric($amount) || $amount <= 0) {
            throw new InvalidArgumentException('Amount must be positive number');
        }
        
        return $this->gateway->charge($amount);
    }
}

// ✅ CORRETO: Usar DI Container com validação
class Container {
    private $bindings = [];
    
    public function bind($abstract, $concrete) {
        $this->bindings[$abstract] = $concrete;
    }
    
    public function make($abstract) {
        if (!isset($this->bindings[$abstract])) {
            throw new RuntimeException("Binding not found: {$abstract}");
        }
        
        $concrete = $this->bindings[$abstract];
        
        if (is_callable($concrete)) {
            return $concrete($this);
        }
        
        return new $concrete();
    }
}

// Uso seguro
$container = new Container();
$container->bind(PaymentGatewayInterface::class, StripeGateway::class);

$gateway = $container->make(PaymentGatewayInterface::class); // ✅ Tipo garantido
$service = new PaymentService($gateway);
```

---

### ❌ Anti-pattern 13.2: Event-Driven Without Logging

**Problema:** Sistema de eventos sem logging dificulta auditoria e debug de segurança.

```php
<?php
// ❌ ERRADO: Eventos sem logging
class EventDispatcher {
    private $listeners = [];
    
    public function dispatch($event_name, $data) {
        if (isset($this->listeners[$event_name])) {
            foreach ($this->listeners[$event_name] as $listener) {
                $listener($data); // ❌ Sem log, sem rastreamento
            }
        }
    }
}

// Uso
$dispatcher->dispatch('user_registered', ['user_id' => 123]); // ❌ Sem rastreamento
```

**Impacto:**
- Impossível auditar ações
- Dificuldade em detectar atividades suspeitas
- Sem rastreamento de eventos críticos
- Dificuldade em debug de problemas de segurança

**✅ Solução Correta:**

```php
<?php
// ✅ CORRETO: Eventos com logging seguro
class SecureEventDispatcher {
    private $listeners = [];
    private $logger;
    
    public function __construct($logger) {
        $this->logger = $logger;
    }
    
    public function dispatch($event_name, $data) {
        // ✅ Logar evento (sem dados sensíveis)
        $this->logger->info("Event dispatched: {$event_name}", [
            'event' => $event_name,
            'timestamp' => current_time('mysql'),
            'user_id' => get_current_user_id(),
            // Não logar dados sensíveis
            'data_keys' => array_keys($data), // Apenas chaves, não valores
        ]);
        
        if (isset($this->listeners[$event_name])) {
            foreach ($this->listeners[$event_name] as $listener) {
                try {
                    $listener($data);
                } catch (Exception $e) {
                    // ✅ Logar erros
                    $this->logger->error("Event listener failed: {$event_name}", [
                        'error' => $e->getMessage(),
                        'listener' => get_class($listener),
                    ]);
                    throw $e;
                }
            }
        }
    }
}

// ✅ CORRETO: Logger que remove dados sensíveis
class SecureLogger {
    private $sensitive_keys = ['password', 'token', 'api_key', 'secret', 'credit_card'];
    
    public function info($message, $context = []) {
        $safe_context = $this->sanitize_context($context);
        error_log($message . ' - ' . wp_json_encode($safe_context));
    }
    
    private function sanitize_context($context) {
        $safe = [];
        
        foreach ($context as $key => $value) {
            if (in_array(strtolower($key), $this->sensitive_keys)) {
                $safe[$key] = '[REDACTED]';
            } elseif (is_array($value)) {
                $safe[$key] = $this->sanitize_context($value);
            } else {
                $safe[$key] = $value;
            }
        }
        
        return $safe;
    }
}
```

---

### ❌ Anti-pattern 13.3: Repository Without Sanitization

**Problema:** Repository Pattern sem sanitização permite dados não sanitizados no banco.

```php
<?php
// ❌ ERRADO: Repository sem sanitização
class PostRepository {
    public function create($data) {
        global $wpdb;
        
        $wpdb->insert(
            $wpdb->posts,
            [
                'post_title' => $data['title'], // ❌ Sem sanitização
                'post_content' => $data['content'], // ❌ Sem sanitização
                'post_status' => $data['status'], // ❌ Sem validação
            ]
        );
        
        return $wpdb->insert_id;
    }
}
```

**Impacto:**
- Dados não sanitizados no banco
- Possível SQL Injection
- XSS se dados forem exibidos
- Violação de integridade de dados

**✅ Solução Correta:**

```php
<?php
// ✅ CORRETO: Repository com sanitização e validação
class PostRepository {
    public function create($data) {
        // Validar dados obrigatórios
        if (empty($data['title'])) {
            throw new InvalidArgumentException('Title is required');
        }
        
        // Sanitizar dados
        $title = sanitize_text_field($data['title']);
        $content = wp_kses_post($data['content'] ?? '');
        
        // Validar status
        $allowed_statuses = ['draft', 'publish', 'pending', 'private'];
        $status = isset($data['status']) && in_array($data['status'], $allowed_statuses)
            ? $data['status']
            : 'draft';
        
        // Usar WordPress API que já sanitiza
        $post_id = wp_insert_post([
            'post_title' => $title,
            'post_content' => $content,
            'post_status' => $status,
        ]);
        
        if (is_wp_error($post_id)) {
            throw new RuntimeException($post_id->get_error_message());
        }
        
        return $post_id;
    }
    
    public function update($post_id, $data) {
        // Verificar se post existe
        $post = get_post($post_id);
        if (!$post) {
            throw new InvalidArgumentException('Post not found');
        }
        
        // Preparar dados para atualização
        $update_data = [];
        
        if (isset($data['title'])) {
            $update_data['post_title'] = sanitize_text_field($data['title']);
        }
        
        if (isset($data['content'])) {
            $update_data['post_content'] = wp_kses_post($data['content']);
        }
        
        // Usar WordPress API
        $result = wp_update_post(array_merge(['ID' => $post_id], $update_data));
        
        if (is_wp_error($result)) {
            throw new RuntimeException($result->get_error_message());
        }
        
        return $result;
    }
}
```

---

## Fase 14: DevOps Security Mistakes

### ❌ Anti-pattern 14.1: Hardcoded Secrets

**Problema:** Secrets hardcoded em arquivos versionados são expostos.

```php
<?php
// ❌ ERRADO: Secrets em código versionado
// docker-compose.yml
services:
  db:
    environment:
      MYSQL_ROOT_PASSWORD: senha123 # ❌ Exposto no Git
      MYSQL_PASSWORD: senha456 # ❌

// wp-config.php (versionado)
define('DB_PASSWORD', 'senha123'); // ❌ Exposto
define('AUTH_KEY', 'hardcoded-key'); // ❌

// .env (versionado por engano)
API_KEY=sk_live_1234567890 # ❌ Exposto
```

**Impacto:**
- Credenciais expostas em repositórios públicos
- Acesso não autorizado a bancos de dados
- Comprometimento de serviços externos
- Necessidade de rotacionar todas as credenciais

**✅ Solução Correta:**

```php
<?php
// ✅ CORRETO: Usar variáveis de ambiente
// docker-compose.yml
services:
  db:
    environment:
      MYSQL_ROOT_PASSWORD: ${MYSQL_ROOT_PASSWORD} # ✅ Do .env
      MYSQL_PASSWORD: ${MYSQL_PASSWORD} # ✅

// wp-config.php (não versionado)
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: ''); // ✅ Do ambiente

// .env.example (versionado - template)
API_KEY=your_api_key_here
DB_PASSWORD=your_password_here

// .env (NÃO versionado - no .gitignore)
API_KEY=sk_live_1234567890
DB_PASSWORD=senha123

// ✅ CORRETO: Docker Secrets (Docker Swarm)
echo "senha123" | docker secret create mysql_password -
docker service create \
  --secret mysql_password \
  mysql:latest

// ✅ CORRETO: Kubernetes Secrets
apiVersion: v1
kind: Secret
metadata:
  name: mysql-secret
type: Opaque
stringData:
  password: senha123

// ✅ CORRETO: CI/CD Secrets (GitHub Actions)
# .github/workflows/deploy.yml
- name: Deploy
  env:
    API_KEY: ${{ secrets.API_KEY }}
    DB_PASSWORD: ${{ secrets.DB_PASSWORD }}
```

---

### ❌ Anti-pattern 14.2: No SSL/TLS

**Problema:** Comunicação sem criptografia permite interceptação de dados.

```nginx
# ❌ ERRADO: Sem SSL
server {
    listen 80;
    server_name meusite.com;
    # Sem redirecionamento para HTTPS
}

# ❌ ERRADO: SSL mal configurado
server {
    listen 443;
    ssl on;
    ssl_certificate /path/to/cert.pem;
    ssl_certificate_key /path/to/key.pem;
    # Sem configurações de segurança
}
```

**Impacto:**
- Dados transmitidos em texto plano
- Interceptação de credenciais
- Man-in-the-middle attacks
- Violação de LGPD/GDPR

**✅ Solução Correta:**

```nginx
# ✅ CORRETO: Redirecionar HTTP para HTTPS
server {
    listen 80;
    server_name meusite.com;
    return 301 https://$server_name$request_uri; # ✅ Redirecionar
}

# ✅ CORRETO: SSL/TLS configurado corretamente
server {
    listen 443 ssl http2;
    server_name meusite.com;
    
    # Certificados
    ssl_certificate /etc/ssl/certs/meusite.com.crt;
    ssl_certificate_key /etc/ssl/private/meusite.com.key;
    
    # Protocolos seguros
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers 'ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384';
    ssl_prefer_server_ciphers on;
    
    # HSTS
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    
    # OCSP Stapling
    ssl_stapling on;
    ssl_stapling_verify on;
    
    # WordPress
    root /var/www/wordpress;
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$args;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

```php
<?php
// ✅ CORRETO: Forçar HTTPS no WordPress
// wp-config.php
define('FORCE_SSL_ADMIN', true);
define('FORCE_SSL_LOGIN', true);

// Adicionar ao functions.php
add_action('init', function() {
    if (!is_ssl() && !WP_DEBUG) {
        wp_redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], 301);
        exit;
    }
});
```

---

### ❌ Anti-pattern 14.3: Public Database Access

**Problema:** Banco de dados acessível publicamente permite ataques diretos.

```yaml
# ❌ ERRADO: Database exposto publicamente
# docker-compose.yml
services:
  db:
    image: mysql:8.0
    ports:
      - "3306:3306" # ❌ Exposto na porta padrão
    environment:
      MYSQL_ROOT_PASSWORD: senha123
```

```php
<?php
// ❌ ERRADO: Conexão sem firewall
// wp-config.php
define('DB_HOST', 'meusite.com'); // ❌ Acessível publicamente
define('DB_USER', 'root');
define('DB_PASSWORD', 'senha123');
```

**Impacto:**
- Ataques diretos ao banco de dados
- Tentativas de força bruta
- SQL Injection direto
- Comprometimento completo dos dados

**✅ Solução Correta:**

```yaml
# ✅ CORRETO: Database em rede interna
# docker-compose.yml
services:
  db:
    image: mysql:8.0
    # Não expor porta publicamente
    # ports:
    #   - "3306:3306" # ❌ Remover
    environment:
      MYSQL_ROOT_PASSWORD: ${MYSQL_ROOT_PASSWORD}
    networks:
      - internal # ✅ Rede interna apenas
    # Ou usar socket
    volumes:
      - db_data:/var/lib/mysql

networks:
  internal:
    internal: true # ✅ Rede interna
```

```php
<?php
// ✅ CORRETO: Conexão via socket ou rede privada
// wp-config.php
// Usar socket ao invés de TCP
define('DB_HOST', 'localhost:/var/run/mysqld/mysqld.sock'); // ✅ Socket

// Ou rede privada
define('DB_HOST', 'db.internal'); // ✅ Hostname interno do Docker

// ✅ CORRETO: Firewall rules
// Permitir apenas de IPs específicos
# iptables -A INPUT -p tcp --dport 3306 -s 10.0.0.0/8 -j ACCEPT
# iptables -A INPUT -p tcp --dport 3306 -j DROP

// ✅ CORRETO: Usar usuário com privilégios mínimos
define('DB_USER', 'wp_user'); // ✅ Não root
define('DB_PASSWORD', 'strong_password_here');
define('DB_NAME', 'wordpress_db');

// No MySQL, criar usuário com privilégios mínimos:
// CREATE USER 'wp_user'@'localhost' IDENTIFIED BY 'strong_password_here';
// GRANT SELECT, INSERT, UPDATE, DELETE ON wordpress_db.* TO 'wp_user'@'localhost';
// FLUSH PRIVILEGES;
```

---

## Code Review Checklist

### Input Validation
- [ ] Todos os inputs do usuário são validados?
- [ ] Validação acontece no servidor (não apenas no cliente)?
- [ ] Tipos de dados são verificados?
- [ ] Ranges e limites são validados?

### Output Escaping
- [ ] Todo output é escapado?
- [ ] Contexto correto de escape é usado (HTML, URL, JS, etc)?
- [ ] Dados de banco são escapados antes de exibir?

### SQL Injection
- [ ] Todas as queries usam prepared statements?
- [ ] `$wpdb->prepare()` é usado sempre?
- [ ] Nenhuma concatenação direta de strings em SQL?

### Authentication & Authorization
- [ ] Permissões são verificadas antes de ações sensíveis?
- [ ] `current_user_can()` é usado corretamente?
- [ ] Nonces são verificados em formulários?
- [ ] Capabilities específicas são verificadas?

### Sensitive Data
- [ ] Dados sensíveis são criptografados?
- [ ] Senhas nunca são armazenadas em texto plano?
- [ ] Tokens e API keys não são logados?
- [ ] Dados sensíveis não são expostos em APIs públicas?

### File Uploads
- [ ] Tipos de arquivo são validados (whitelist)?
- [ ] Tamanho de arquivo é limitado?
- [ ] Arquivos são escaneados por malware?
- [ ] Arquivos são salvos fora do web root quando possível?

### REST API
- [ ] Endpoints têm `permission_callback`?
- [ ] Inputs são validados via `args`?
- [ ] Dados sensíveis são filtrados das respostas?
- [ ] Rate limiting é implementado?

### Error Handling
- [ ] Mensagens de erro não expõem informações sensíveis?
- [ ] Stack traces são desabilitados em produção?
- [ ] Erros são logados de forma segura?

### Secrets Management
- [ ] Nenhum secret está hardcoded?
- [ ] Secrets vêm de variáveis de ambiente?
- [ ] `.env` está no `.gitignore`?
- [ ] Secrets são rotacionados regularmente?

### Infrastructure
- [ ] HTTPS é forçado?
- [ ] Banco de dados não é acessível publicamente?
- [ ] Firewall está configurado?
- [ ] Logs são monitorados?

---

## Resumo

### O Que Você Aprendeu

✅ **Anti-patterns Comuns** - Padrões inseguros em cada fase  
✅ **Soluções Corretas** - Como implementar segurança corretamente  
✅ **Impacto de Vulnerabilidades** - Consequências de cada erro  
✅ **Code Review Checklist** - Lista de verificação de segurança

### Próximos Passos

1. **Revisar código existente** usando este documento
2. **Implementar correções** nos anti-patterns encontrados
3. **Usar checklist** em code reviews
4. **Aprender Fase 12** para segurança avançada

### Recursos Adicionais

- [Fase 12: Segurança Avançada](012-WordPress-Fase-12-Seguranca-Boas-Praticas.md)
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [WordPress Security Handbook](https://developer.wordpress.org/advanced-administration/security/)

---

**Navegação:** [Índice](000-WordPress-Topicos-Index.md) | [← Learning Paths](018-WordPress-Learning-Paths.md) | [Fase 12: Segurança](012-WordPress-Fase-12-Seguranca-Boas-Praticas.md) | [Error Handling →](020-WordPress-Error-Handling-Best-Practices.md)
