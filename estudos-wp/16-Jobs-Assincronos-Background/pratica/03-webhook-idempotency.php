<?php
/**
 * REFERÊNCIA RÁPIDA – Webhook receiver com assinatura e idempotency
 *
 * Verificar HMAC-SHA256 (hash_equals); header X-Idempotency-Key para evitar duplicatas;
 * enqueue com as_enqueue_async_action; responder 202 Accepted.
 *
 * Fonte: 016-WordPress-Fase-16-Jobs-Assincronos-Background.md (Webhook Receivers)
 */

if (! function_exists('as_enqueue_async_action')) {
    return;
}

class WebhookReceiver
{
    private const PROCESSED_TTL = 7 * DAY_IN_SECONDS;

    public static function verifySignature(string $payload, string $signature, string $secret): bool
    {
        $expected = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expected, $signature);
    }

    public static function handleIncoming(\WP_REST_Request $request): \WP_REST_Response
    {
        if ($request->get_method() !== 'POST') {
            return new \WP_REST_Response(['error' => 'Method not allowed'], 405);
        }

        $payload   = $request->get_body();
        $signature = $request->get_header('X-Webhook-Signature');
        $secret    = defined('WEBHOOK_SECRET') ? WEBHOOK_SECRET : '';

        if (empty($secret)) {
            return new \WP_REST_Response(['error' => 'Webhook secret not configured'], 500);
        }

        if (! self::verifySignature($payload, $signature, $secret)) {
            return new \WP_REST_Response(['error' => 'Unauthorized'], 401);
        }

        $data = json_decode($payload, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new \WP_REST_Response(['error' => 'Invalid JSON'], 400);
        }

        $idempotency_key = $request->get_header('X-Idempotency-Key');
        if ($idempotency_key && self::hasBeenProcessed($idempotency_key)) {
            return new \WP_REST_Response([
                'message'   => 'Already processed',
                'webhook_id' => get_option("webhook_id_{$idempotency_key}"),
            ], 200);
        }

        $webhook_id = wp_generate_uuid4();
        as_enqueue_async_action('process_webhook', [
            'webhook_id'      => $webhook_id,
            'payload'         => $data,
            'idempotency_key' => $idempotency_key,
            'source'          => $request->get_header('X-Webhook-Source'),
        ], 'webhook_processing');

        if ($idempotency_key) {
            update_option("webhook_id_{$idempotency_key}", $webhook_id);
        }

        return new \WP_REST_Response([
            'webhook_id' => $webhook_id,
            'status'     => 'accepted',
        ], 202);
    }

    public static function hasBeenProcessed(string $idempotency_key): bool
    {
        return (bool) get_transient("webhook_processed_{$idempotency_key}");
    }

    public static function markProcessed(string $idempotency_key): void
    {
        set_transient("webhook_processed_{$idempotency_key}", true, self::PROCESSED_TTL);
    }

    public static function process(string $webhook_id, array $payload, ?string $idempotency_key = null, ?string $source = null): void
    {
        try {
            do_action('webhook_received', $payload, $source);
            $event_type = $payload['event'] ?? 'unknown';
            do_action("webhook_event_{$event_type}", $payload, $source);
            if ($idempotency_key) {
                self::markProcessed($idempotency_key);
            }
        } catch (Throwable $e) {
            error_log("Webhook {$webhook_id} failed: " . $e->getMessage());
            throw $e;
        }
    }
}

add_action('rest_api_init', function () {
    register_rest_route('myapp/v1', '/webhook', [
        'methods'             => 'POST',
        'callback'            => [WebhookReceiver::class, 'handleIncoming'],
        'permission_callback' => '__return_true',
    ]);
});

add_action('process_webhook', function ($webhook_id, $payload, $idempotency_key, $source) {
    WebhookReceiver::process($webhook_id, $payload, $idempotency_key, $source);
}, 10, 4);

// Exemplo: processar evento order.created
// add_action('webhook_event_order.created', function ($payload, $source) {
//     $order_id = wp_insert_post([...]);
//     update_post_meta($order_id, '_order_data', $payload['order'] ?? []);
// });
