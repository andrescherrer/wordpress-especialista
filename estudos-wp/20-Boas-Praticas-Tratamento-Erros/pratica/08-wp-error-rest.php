<?php
/**
 * REFERÊNCIA RÁPIDA – WP_Error em REST (retorno, status, dados) e try/catch para WP_Error
 *
 * return new WP_Error('code', 'message', ['status' => 400|404|403|500, 'data' => [...]]);
 * try { ... } catch (Exception $e) { return new WP_Error('error', $e->getMessage(), ['status' => 500]); }
 * Em produção não expor $e->getTraceAsString() no WP_Error.
 *
 * Fonte: 020-WordPress-Fase-20-Boas-Praticas-Tratamento-Erros.md
 *
 * @package EstudosWP
 * @subpackage 20-Boas-Praticas-Tratamento-Erros
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('rest_api_init', function () {
    register_rest_route('estudos-wp/v1', '/order/(?P<id>\d+)', [
        'methods'             => 'GET',
        'callback'            => function ($request) {
            try {
                $id = (int) $request->get_param('id');
                $order = get_post($id);
                if (!$order) {
                    return new WP_Error('not_found', 'Pedido não encontrado', ['status' => 404]);
                }
                if (!current_user_can('read_post', $id)) {
                    return new WP_Error('forbidden', 'Sem permissão', ['status' => 403]);
                }
                return rest_ensure_response(['id' => $order->ID, 'title' => $order->post_title]);
            } catch (Exception $e) {
                error_log('Order API error: ' . $e->getMessage());
                return new WP_Error('error', 'Erro ao processar pedido', ['status' => 500]);
            }
        },
        'permission_callback' => '__return_true',
    ]);
});
