<?php
/**
 * REFERÊNCIA RÁPIDA – Produto variável e dados custom
 *
 * Hooks para variações; salvar meta em produto/varição; exibir no carrinho e no pedido.
 * woocommerce_product_get_meta, woocommerce_add_cart_item_data, woocommerce_get_item_data.
 *
 * @package EstudosWP
 * @subpackage 15-Topicos-Complementares-Avancados
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('WC')) {
    return;
}

// Salvar meta custom na variação (ou produto simples) – via admin ou código
// Ex.: gravura personalizada no produto variável (tamanho + texto)

add_filter('woocommerce_add_cart_item_data', function ($cart_item_data, $product_id, $variation_id) {
    if (!empty($_POST['gravura_texto'])) {
        $cart_item_data['gravura_texto'] = sanitize_text_field(wp_unslash($_POST['gravura_texto']));
    }
    return $cart_item_data;
}, 10, 3);

// Exibir no carrinho e no checkout
add_filter('woocommerce_get_item_data', function ($item_data, $cart_item) {
    if (!empty($cart_item['gravura_texto'])) {
        $item_data[] = [
            'key'   => __('Gravura', 'estudos-wp'),
            'value' => $cart_item['gravura_texto'],
        ];
    }
    return $item_data;
}, 10, 2);

// Salvar no item do pedido (order item meta)
add_action('woocommerce_checkout_create_order_line_item', function ($item, $cart_item_key, $values, $order) {
    if (!empty($values['gravura_texto'])) {
        $item->add_meta_data(__('Gravura', 'estudos-wp'), $values['gravura_texto']);
    }
}, 10, 4);

// Meta em produto/varição (admin): usar woocommerce_product_options_inventory_product_data ou
// woocommerce_product_after_variable_attributes para campos na variação.
// Ler no front: get_post_meta($variation_id, '_meu_campo', true);
