<?php
/**
 * REFERÊNCIA RÁPIDA – Upload seguro (allowed types, size, wp_handle_upload)
 *
 * wp_handle_upload($file, ['test_form' => false, 'mimes' => $allowed]); checar $file['error'] e type real.
 * Allowed: ['jpg|jpeg|jpe' => 'image/jpeg', 'png' => 'image/png']; tamanho: wp_max_upload_size() ou valor custom.
 * Validar extensão e MIME real (finfo_file ou $file['type'] após upload); nome seguro com sanitize_file_name.
 *
 * Fonte: 012-WordPress-Fase-12-Seguranca-Boas-Praticas.md
 *
 * @package EstudosWP
 * @subpackage 12-Seguranca-Boas-Praticas
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_init', function () {
    if (!isset($_POST['estudos_wp_upload_nonce']) || !wp_verify_nonce($_POST['estudos_wp_upload_nonce'], 'estudos_wp_upload')) {
        return;
    }
    if (!current_user_can('upload_files')) {
        wp_die(__('Sem permissão.'));
    }
    if (empty($_FILES['estudos_wp_arquivo']['name'])) {
        return;
    }
    $allowed = [
        'jpg|jpeg|jpe' => 'image/jpeg',
        'png'          => 'image/png',
        'gif'          => 'image/gif',
    ];
    $max_size = wp_max_upload_size();
    $file = $_FILES['estudos_wp_arquivo'];
    if ($file['size'] > $max_size) {
        wp_die(__('Arquivo muito grande.'));
    }
    $overrides = ['test_form' => false, 'mimes' => $allowed];
    $move = wp_handle_upload($file, $overrides);
    if (isset($move['error'])) {
        wp_die(esc_html($move['error']));
    }
    // $move['url'], $move['file']; salvar em option/post meta se necessário
});
