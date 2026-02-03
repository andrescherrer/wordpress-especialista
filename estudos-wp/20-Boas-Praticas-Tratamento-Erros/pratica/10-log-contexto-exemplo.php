<?php
/**
 * REFERÊNCIA RÁPIDA – Log com contexto (order_id, user_id) em formato estruturado (JSON)
 *
 * error_log(json_encode(['timestamp' => ..., 'level' => 'error', 'message' => ..., 'context' => ['order_id' => ..., 'user_id' => ...]]));
 * Ou usar StructuredLogger (ver 05-logging-estruturado.php).
 *
 * Fonte: 020-WordPress-Fase-20-Boas-Praticas-Tratamento-Erros.md
 *
 * @package EstudosWP
 * @subpackage 20-Boas-Praticas-Tratamento-Erros
 */

if (!defined('ABSPATH')) {
    exit;
}

function estudos_wp_log_error(string $message, array $context = []): void {
    $entry = [
        'timestamp' => current_time('mysql'),
        'level'     => 'error',
        'message'   => $message,
        'context'   => array_merge($context, [
            'user_id'     => get_current_user_id(),
            'request_uri' => $_SERVER['REQUEST_URI'] ?? '',
        ]),
    ];
    error_log(json_encode($entry));
}

// Uso: estudos_wp_log_error('Order processing failed', ['order_id' => 123, 'exception' => $e->getMessage()]);
