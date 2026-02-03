<?php
/**
 * REFERÊNCIA RÁPIDA – Core: XSS, SQL Injection, capabilities, dados sensíveis
 *
 * XSS: esc_html, esc_attr, esc_url, esc_js, wp_json_encode, esc_textarea.
 * SQL: $wpdb->prepare('%d','%s','%f'); $wpdb->esc_like() para LIKE.
 * Auth: current_user_can('capability'), current_user_can('capability', $post_id); wp_verify_nonce.
 * Sensitive: criptografar (wp_salt + openssl) ou hash; autoload false.
 *
 * Fonte: 019-WordPress-Fase-19-Anti-padroes-Seguranca.md
 */

// ========== 1. XSS – Escape de output ==========

// ❌ ERRADO
// $user_name = $_GET['name'];
// echo "<h1>Welcome, $user_name!</h1>";

// ✅ CORRETO
$user_name = isset($_GET['name']) ? sanitize_text_field($_GET['name']) : '';
echo '<h1>Welcome, ' . esc_html($user_name) . '!</h1>';
echo '<a href="' . esc_url($url) . '">Link</a>';
echo '<input value="' . esc_attr($value) . '">';
echo '<script>var data = ' . wp_json_encode($data) . ';</script>';

// ========== 2. SQL Injection – Prepared statements ==========

global $wpdb;

// ❌ ERRADO
// $user_id = $_GET['id'];
// $results = $wpdb->get_results("SELECT * FROM {$wpdb->users} WHERE ID = $user_id");

// ✅ CORRETO
$user_id = isset($_GET['id']) ? absint($_GET['id']) : 0;
$query = $wpdb->prepare(
    "SELECT * FROM {$wpdb->users} WHERE ID = %d",
    $user_id
);
$results = $wpdb->get_results($query);

// LIKE
$search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
$query = $wpdb->prepare(
    "SELECT * FROM {$wpdb->posts} WHERE post_title LIKE %s",
    '%' . $wpdb->esc_like($search) . '%'
);

// ========== 3. Capabilities e nonces ==========

// ❌ ERRADO: confiar em role ou só is_user_logged_in()
// if (current_user_can('administrator')) { ... }
// if (is_user_logged_in()) { wp_delete_post($post_id); }

// ✅ CORRETO
if (!current_user_can('delete_post', $post_id)) {
    wp_die(__('Você não tem permissão para deletar este post.'));
}
wp_delete_post($post_id);

// Formulário: nonce + capability
if (isset($_POST['action']) && $_POST['action'] === 'delete_post') {
    if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'delete_post_' . $post_id)) {
        wp_die(__('Nonce inválido.'));
    }
    if (!current_user_can('delete_post', $post_id)) {
        wp_die(__('Sem permissão.'));
    }
    wp_delete_post($post_id);
}

// ========== 4. Dados sensíveis ==========

// ❌ ERRADO
// update_post_meta($post_id, '_credit_card', $_POST['credit_card']);

// ✅ CORRETO: criptografar antes de salvar (exemplo simplificado)
if (function_exists('openssl_encrypt')) {
    $key = wp_salt('auth');
    $iv = openssl_random_pseudo_bytes(16);
    $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
    $to_store = base64_encode($iv . $encrypted);
    update_post_meta($post_id, '_credit_card', $to_store);
}

// Alternativa: não armazenar; usar token/hash
// update_option('_api_key_hash', wp_hash_password($api_key), false);
