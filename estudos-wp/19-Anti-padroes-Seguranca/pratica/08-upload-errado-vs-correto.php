<?php
/**
 * REFERÊNCIA RÁPIDA – Upload: inseguro vs seguro
 *
 * Inseguro: aceitar qualquer $_FILES sem validar tipo/tamanho; mover com move_uploaded_file sem checar MIME real.
 * Seguro: whitelist de extensões e MIME; wp_handle_upload com 'mimes'; checar $file['error'] e tamanho (wp_max_upload_size).
 * Fonte: 019-WordPress-Fase-19-Anti-padroes-Seguranca.md
 *
 * @package EstudosWP
 * @subpackage 19-Anti-padroes-Seguranca
 */

if (!defined('ABSPATH')) {
    exit;
}

// ERRADO: aceitar qualquer arquivo
// move_uploaded_file($_FILES['file']['tmp_name'], $dest);
// update_post_meta($post_id, '_anexo', $_FILES['file']['name']);

// CORRETO: validar tipo, tamanho e usar wp_handle_upload
add_action('admin_init', function () {
    if (!isset($_POST['upload_nonce']) || !wp_verify_nonce($_POST['upload_nonce'], 'estudos_wp_upload')) {
        return;
    }
    if (!current_user_can('upload_files')) {
        wp_die(__('Sem permissão.'));
    }
    $file = isset($_FILES['estudos_wp_file']) ? $_FILES['estudos_wp_file'] : null;
    if (!$file || empty($file['name'])) {
        return;
    }
    $max = wp_max_upload_size();
    if ($file['size'] > $max) {
        wp_die(__('Arquivo muito grande.'));
    }
    $allowed = ['jpg|jpeg|jpe' => 'image/jpeg', 'png' => 'image/png'];
    $move = wp_handle_upload($file, ['test_form' => false, 'mimes' => $allowed]);
    if (isset($move['error'])) {
        wp_die(esc_html($move['error']));
    }
});
