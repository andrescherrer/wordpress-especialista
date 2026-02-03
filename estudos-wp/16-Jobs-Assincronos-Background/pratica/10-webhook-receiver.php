<?php
/**
 * REFERÊNCIA RÁPIDA – Webhook receiver (verificar assinatura, idempotência)
 *
 * Receber POST; validar header de assinatura (HMAC); X-Idempotency-Key: salvar e ignorar se já processado.
 * Responder 202 Accepted e processar em background (as_enqueue_async_action) ou 200 após processar.
 * Fonte: 016-WordPress-Fase-16-Jobs-Assincronos-Background.md
 *
 * @package EstudosWP
 * @subpackage 16-Jobs-Assincronos-Background
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('rest_api_init', function () {
    register_rest_route('estudos-wp/v1', '/webhook', [
        'methods'             => 'POST',
        'callback'            => function ($request) {
            $body = $request->get_body();
            $sig  = $request->get_header('X-Signature');
            $key  = $request->get_header('X-Idempotency-Key');
            if (!$sig || !hash_equals($sig, hash_hmac('sha256', $body, wp_salt('auth')))) {
                return new WP_Error('invalid_signature', 'Invalid signature', ['status' => 401]);
            }
            if ($key) {
                $seen = get_transient('webhook_idem_' . md5($key));
                if ($seen) {
                    return new WP_REST_Response(['status' => 'already_processed'], 200);
                }
                set_transient('webhook_idem_' . md5($key), true, DAY_IN_SECONDS);
            }
            if (function_exists('as_enqueue_async_action')) {
                as_enqueue_async_action('estudos_wp_process_webhook', [json_decode($body, true)], 'webhooks');
            }
            return new WP_REST_Response(['status' => 'accepted'], 202);
        },
        'permission_callback' => '__return_true',
    ]);
});
