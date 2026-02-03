<?php
/**
 * REFERÊNCIA RÁPIDA – Meta box para CPT (salvar meta no save_post)
 *
 * add_meta_box(id, título, callback, post_type, context, priority).
 * save_post_{post_type}: verificar nonce, DOING_AUTOSAVE, current_user_can('edit_post', $post_id).
 * update_post_meta($post_id, $meta_key, $value) após sanitize.
 *
 * Fonte: 005-WordPress-Fase-5-CPT-Taxonomias.md
 *
 * @package EstudosWP
 * @subpackage 05-CPT-Taxonomias
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('add_meta_boxes', function () {
    add_meta_box(
        'produto_preco',
        'Preço',
        'estudos_wp_meta_box_preco_callback',
        'produto',
        'normal',
        'high'
    );
});

function estudos_wp_meta_box_preco_callback($post) {
    wp_nonce_field('estudos_wp_save_preco', 'estudos_wp_preco_nonce');
    $preco = get_post_meta($post->ID, '_preco', true);
    echo '<input type="number" name="estudos_wp_preco" value="' . esc_attr($preco) . '" step="0.01" min="0" class="regular-text">';
}

add_action('save_post_produto', function ($post_id) {
    if (!isset($_POST['estudos_wp_preco_nonce']) || !wp_verify_nonce($_POST['estudos_wp_preco_nonce'], 'estudos_wp_save_preco')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if (isset($_POST['estudos_wp_preco'])) {
        $preco = is_numeric($_POST['estudos_wp_preco']) ? floatval($_POST['estudos_wp_preco']) : 0;
        update_post_meta($post_id, '_preco', $preco);
    }
});
