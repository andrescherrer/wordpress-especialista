<?php
/**
 * REFERÊNCIA RÁPIDA – CPT: capability, drafts, meta box
 *
 * save_post: nonce, DOING_AUTOSAVE, current_user_can('edit_post', $post_id); sanitize antes de update_post_meta.
 * Meta box: current_user_can('edit_post', $post->ID); wp_nonce_field; esc_attr / wp_kses_post no output.
 * Listagens: post_status => 'publish' para público; status só para quem pode edit_post; author para drafts próprios.
 *
 * Fonte: 019-WordPress-Fase-19-Anti-padroes-Seguranca.md
 */

// ========== 1. save_post: nonce + capability + sanitize ==========

// ❌ ERRADO
// add_action('save_post_product', function ($post_id) {
//     update_post_meta($post_id, '_price', $_POST['price']);
// });

// ✅ CORRETO
add_action('save_post_product', function ($post_id) {
    if (!isset($_POST['product_meta_nonce']) || !wp_verify_nonce($_POST['product_meta_nonce'], 'save_product_meta')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if (isset($_POST['price'])) {
        update_post_meta($post_id, '_price', sanitize_text_field($_POST['price']));
    }
    if (isset($_POST['stock'])) {
        update_post_meta($post_id, '_stock', absint($_POST['stock']));
    }
});

// ========== 2. Meta box: capability + nonce + escape ==========

add_action('add_meta_boxes', function () {
    add_meta_box(
        'product_data',
        'Dados do Produto',
        function ($post) {
            if (!current_user_can('edit_post', $post->ID)) {
                echo '<p>Sem permissão.</p>';
                return;
            }
            wp_nonce_field('save_product_meta', 'product_meta_nonce');
            $price = get_post_meta($post->ID, '_price', true);
            echo '<input name="price" value="' . esc_attr($price) . '">';

            $description = get_post_meta($post->ID, '_description', true);
            echo '<div>' . wp_kses_post($description) . '</div>';
        },
        'product',
        'normal',
        'high'
    );
});

// ========== 3. REST/Query: não expor drafts publicamente ==========

// ❌ ERRADO: WP_Query sem post_status ou post_status com draft para público
// 'post_status' => 'any' com permission_callback => '__return_true'

// ✅ CORRETO em endpoint público
$args = [
    'post_type'      => 'product',
    'posts_per_page' => 10,
    'post_status'    => 'publish',
];
if (is_user_logged_in() && current_user_can('edit_posts')) {
    $args['post_status'] = ['publish', 'draft', 'pending'];
    $args['author']      = get_current_user_id();
}
$query = new WP_Query($args);

// Na resposta: incluir 'status' só se current_user_can('edit_post', $post->ID)
