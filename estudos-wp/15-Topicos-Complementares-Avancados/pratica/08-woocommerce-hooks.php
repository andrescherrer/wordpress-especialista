<?php
/**
 * REFERÊNCIA RÁPIDA – WooCommerce: hooks em pedido e produto
 *
 * Pedido: woocommerce_new_order, woocommerce_checkout_order_processed; order_id no callback.
 * Produto: woocommerce_add_to_cart; ver documentação WooCommerce para lista completa.
 * Fonte: 015-WordPress-Fase-15-Topicos-Complementares-Avancados.md
 *
 * @package EstudosWP
 * @subpackage 15-Topicos-Complementares-Avancados
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('wc_get_order')) {
    return;
}

add_action('woocommerce_new_order', function ($order_id) {
    $order = wc_get_order($order_id);
    if (!$order) {
        return;
    }
    error_log('Novo pedido: ' . $order_id);
}, 10, 1);

add_action('woocommerce_checkout_order_processed', function ($order_id) {
    // Notificar gateway, invalidar cache
}, 10, 1);
