<?php
/**
 * REFERÊNCIA RÁPIDA – Customizar checkout (campos, validação)
 *
 * woocommerce_checkout_fields: adicionar/remover campos.
 * woocommerce_checkout_process: validação; wp_add_notice em caso de erro.
 * woocommerce_checkout_create_order_line_item ou order meta: salvar no pedido.
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

// Adicionar campo custom no checkout (billing)
add_filter('woocommerce_checkout_fields', function ($fields) {
    $fields['billing']['billing_cpf'] = [
        'type'        => 'text',
        'label'       => 'CPF',
        'placeholder' => '000.000.000-00',
        'required'    => true,
        'class'       => ['form-row-wide'],
        'priority'    => 25,
    ];
    return $fields;
});

// Salvar o valor no pedido (order meta)
add_action('woocommerce_checkout_create_order_line_item', function ($item, $cart_item_key, $values, $order) {
    // Para dados por item use $item->add_meta_data()
}, 10, 4);

add_action('woocommerce_checkout_create_order', function ($order, $data) {
    if (!empty($_POST['billing_cpf'])) {
        $order->update_meta_data('_billing_cpf', sanitize_text_field(wp_unslash($_POST['billing_cpf'])));
    }
}, 10, 2);

// Validação no checkout
add_action('woocommerce_checkout_process', function () {
    $cpf = isset($_POST['billing_cpf']) ? sanitize_text_field(wp_unslash($_POST['billing_cpf'])) : '';
    if (empty($cpf)) {
        wc_add_notice(__('CPF é obrigatório.', 'estudos-wp'), 'error');
        return;
    }
    // Validação mínima de formato (11 dígitos)
    $cpf_limpo = preg_replace('/\D/', '', $cpf);
    if (strlen($cpf_limpo) !== 11) {
        wc_add_notice(__('CPF inválido.', 'estudos-wp'), 'error');
    }
});

// Exibir o CPF no admin (pedido) e no email
add_action('woocommerce_admin_order_data_after_billing_address', function ($order) {
    $cpf = $order->get_meta('_billing_cpf');
    if ($cpf) {
        echo '<p><strong>CPF:</strong> ' . esc_html($cpf) . '</p>';
    }
});
