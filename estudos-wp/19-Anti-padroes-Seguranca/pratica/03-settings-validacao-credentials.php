<?php
/**
 * REFERÊNCIA RÁPIDA – Settings API: validação, sanitização, credenciais
 *
 * Validação: register_setting com sanitize_callback e validate_callback; add_settings_error em falha.
 * HTML: wp_kses com allowed tags ou wp_kses_post; esc_textarea ao exibir.
 * Credenciais: getenv(), constante em wp-config (não versionado), get_option com fallback; .env no .gitignore.
 *
 * Fonte: 019-WordPress-Fase-19-Anti-padroes-Seguranca.md
 */

// ========== 1. Validação e sanitização em settings ==========

// ❌ ERRADO
// register_setting('myapp_options', 'myapp_email', []);
// update_option('myapp_email', $_POST['myapp_email']);

// ✅ CORRETO
add_action('admin_init', function () {
    register_setting('myapp_options', 'myapp_email', [
        'type'              => 'string',
        'sanitize_callback'  => function ($value) {
            $email = sanitize_email($value);
            if (!is_email($email)) {
                add_settings_error('myapp_email', 'invalid_email', 'Email inválido');
                return get_option('myapp_email');
            }
            return $email;
        },
        'validate_callback'  => function ($value) {
            return is_email($value);
        },
        'default'            => '',
    ]);

    register_setting('myapp_options', 'myapp_api_rate_limit', [
        'type'              => 'integer',
        'sanitize_callback'  => 'absint',
        'validate_callback'  => function ($value) {
            if ($value < 1 || $value > 1000) {
                add_settings_error('myapp_api_rate_limit', 'invalid_range', 'Rate limit entre 1 e 1000');
                return false;
            }
            return true;
        },
        'default'            => 100,
    ]);
});

// ========== 2. HTML em settings – sanitizar e escapar ==========

// ❌ ERRADO: salvar e exibir raw
// update_option('myapp_custom_html', $_POST['myapp_custom_html']);
// echo "<textarea>$value</textarea>";

// ✅ CORRETO
register_setting('myapp_options', 'myapp_custom_html', [
    'type'              => 'string',
    'sanitize_callback'  => function ($value) {
        $allowed = ['p' => [], 'strong' => [], 'em' => [], 'a' => ['href' => [], 'title' => []]];
        return wp_kses($value, $allowed);
    },
]);

// No campo:
// echo '<textarea name="myapp_custom_html">' . esc_textarea(get_option('myapp_custom_html', '')) . '</textarea>';

// ========== 3. Credenciais – nunca hardcoded ==========

// ❌ ERRADO
// define('API_KEY', 'sk_live_1234567890');
// private $api_key = 'sk_live_...';

// ✅ CORRETO: ambiente
if (!defined('MYAPP_API_KEY')) {
    define('MYAPP_API_KEY', getenv('MYAPP_API_KEY') ?: '');
}

// ✅ CORRETO: wp-config (não versionado) define('MYAPP_API_KEY', '...');

// ✅ CORRETO: get_option com fallback para constante
class MyApp_PaymentGateway {
    private function get_api_key() {
        $key = get_option('myapp_api_key');
        if (empty($key) && defined('MYAPP_API_KEY')) {
            $key = MYAPP_API_KEY;
        }
        return $key;
    }
}

// .env.example (versionado): API_KEY=your_api_key_here
// .env (NÃO versionado, no .gitignore): API_KEY=sk_live_...
