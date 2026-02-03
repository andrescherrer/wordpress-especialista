<?php
/**
 * REFERÊNCIA RÁPIDA – Gateway de pagamento (esqueleto)
 *
 * Classe estende WC_Payment_Gateway; init_form_fields, process_payment;
 * retorno success/failure com redirect; documentação: WooCommerce Payment Gateway API.
 *
 * @package EstudosWP
 * @subpackage 15-Topicos-Complementares-Avancados
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WC_Payment_Gateway')) {
    return;
}

class Estudos_WC_Gateway_Esqueleto extends WC_Payment_Gateway {

    public function __construct() {
        $this->id                 = 'estudos_esqueleto';
        $this->method_title       = __('Gateway Esqueleto', 'estudos-wp');
        $this->method_description = __('Exemplo mínimo de gateway de pagamento.', 'estudos-wp');
        $this->has_fields         = false;
        $this->supports           = ['products'];

        $this->init_form_fields();
        $this->init_settings();
        $this->title       = $this->get_option('title', __('Pagamento Esqueleto', 'estudos-wp'));
        $this->description = $this->get_option('description', '');

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, [$this, 'process_admin_options']);
    }

    public function init_form_fields() {
        $this->form_fields = [
            'enabled' => [
                'title'   => __('Ativar/Desativar', 'estudos-wp'),
                'type'    => 'checkbox',
                'label'   => __('Ativar este gateway', 'estudos-wp'),
                'default' => 'no',
            ],
            'title' => [
                'title'       => __('Título', 'estudos-wp'),
                'type'        => 'text',
                'description' => __('Título exibido no checkout.', 'estudos-wp'),
                'default'     => __('Pagamento Esqueleto', 'estudos-wp'),
            ],
            'description' => [
                'title'   => __('Descrição', 'estudos-wp'),
                'type'    => 'textarea',
                'default' => '',
            ],
        ];
    }

    public function process_payment($order_id) {
        $order = wc_get_order($order_id);
        if (!$order) {
            return ['result' => 'failure', 'redirect' => ''];
        }

        // Aqui: chamar API do gateway real, criar transação, etc.
        // Exemplo: marcar como "on-hold" até confirmação
        $order->update_status('on-hold', __('Aguardando confirmação do gateway.', 'estudos-wp'));

        WC()->cart->empty_cart();

        return [
            'result'   => 'success',
            'redirect' => $this->get_return_url($order),
        ];

        // Em caso de falha:
        // wc_add_notice(__('Pagamento recusado.', 'estudos-wp'), 'error');
        // return ['result' => 'failure', 'redirect' => ''];
    }
}

add_filter('woocommerce_payment_gateways', function ($gateways) {
    $gateways[] = 'Estudos_WC_Gateway_Esqueleto';
    return $gateways;
});
