<?php
/**
 * REFERÊNCIA RÁPIDA – Logging estruturado
 *
 * Níveis: debug, info, warning, error, critical.
 * Contexto: timestamp, level, message, context (IDs, user_id, request_uri, IP).
 * error_log(json_encode($entry)); opcional Sentry (captureMessage/captureException).
 *
 * Fonte: 020-WordPress-Fase-20-Boas-Praticas-Tratamento-Erros.md
 */

class StructuredLogger {
    public function log(string $level, string $message, array $context = []): void {
        $log_entry = [
            'timestamp'  => function_exists('current_time') ? current_time('mysql') : date('Y-m-d H:i:s'),
            'level'      => $level,
            'message'    => $message,
            'context'    => $context,
            'user_id'    => function_exists('get_current_user_id') ? get_current_user_id() : null,
            'request_uri'=> $_SERVER['REQUEST_URI'] ?? null,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
        ];
        error_log(json_encode($log_entry));

        if (function_exists('Sentry\captureMessage')) {
            $severity = $this->mapLevelToSentry($level);
            \Sentry\captureMessage($message, $severity, ['extra' => $context]);
        }
    }

    public function error(string $message, array $context = []): void {
        $this->log('error', $message, $context);
    }

    public function warning(string $message, array $context = []): void {
        $this->log('warning', $message, $context);
    }

    public function info(string $message, array $context = []): void {
        $this->log('info', $message, $context);
    }

    public function debug(string $message, array $context = []): void {
        $this->log('debug', $message, $context);
    }

    private function mapLevelToSentry(string $level): \Sentry\Severity {
        $map = [
            'debug'    => \Sentry\Severity::debug(),
            'info'     => \Sentry\Severity::info(),
            'warning'  => \Sentry\Severity::warning(),
            'error'    => \Sentry\Severity::error(),
            'critical' => \Sentry\Severity::fatal(),
        ];
        return $map[$level] ?? \Sentry\Severity::error();
    }
}

// Uso com contexto suficiente para reprodução
// $logger = new StructuredLogger();
// $logger->error('Order processing failed', [
//     'order_id' => $order_id,
//     'user_id'  => get_current_user_id(),
//     'exception'=> $e->getMessage(),
// ]);
