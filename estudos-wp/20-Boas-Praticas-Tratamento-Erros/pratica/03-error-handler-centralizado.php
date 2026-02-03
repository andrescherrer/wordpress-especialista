<?php
/**
 * REFERÊNCIA RÁPIDA – Error handler centralizado
 *
 * set_error_handler: erros PHP (E_WARNING, E_NOTICE, etc.); retornar true para suprimir, false para padrão.
 * set_exception_handler: exceções não capturadas; logar + opcional Sentry; em prod não exibir trace.
 * register_shutdown_function: fatal (E_ERROR, E_PARSE, etc.) via error_get_last().
 *
 * Registrar no bootstrap: WordPressErrorHandler::register();
 * Fonte: 020-WordPress-Fase-20-Boas-Praticas-Tratamento-Erros.md
 */

class WordPressErrorHandler {
    private static bool $registered = false;

    public static function register(): void {
        if (self::$registered) {
            return;
        }
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleShutdown']);
        self::$registered = true;
    }

    public static function handleError(int $errno, string $errstr, string $errfile, int $errline): bool {
        if (!(error_reporting() & $errno)) {
            return false;
        }
        $context = [
            'type' => self::getErrorType($errno),
            'message' => $errstr,
            'file' => $errfile,
            'line' => $errline,
        ];
        error_log(sprintf('[PHP Error] %s: %s in %s:%d', $context['type'], $errstr, $errfile, $errline));
        if (function_exists('Sentry\captureMessage')) {
            \Sentry\captureMessage($errstr, \Sentry\Severity::error(), ['extra' => $context]);
        }
        if (defined('WP_DEBUG') && WP_DEBUG) {
            return false;
        }
        return true;
    }

    public static function handleException(Throwable $exception): void {
        error_log(sprintf(
            '[Uncaught Exception] %s: %s in %s:%d',
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        ));
        if (function_exists('Sentry\captureException')) {
            \Sentry\captureException($exception);
        }
        if (defined('WP_DEBUG') && WP_DEBUG) {
            echo '<pre>' . get_class($exception) . ': ' . $exception->getMessage() . "\n" . $exception->getTraceAsString() . '</pre>';
        } else {
            status_header(500);
            if (function_exists('wp_die')) {
                wp_die('An error occurred. Please try again later.');
            } else {
                echo 'An error occurred. Please try again later.';
            }
        }
    }

    public static function handleShutdown(): void {
        $error = error_get_last();
        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
            self::handleError($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }

    private static function getErrorType(int $errno): string {
        $types = [
            E_ERROR => 'E_ERROR',
            E_WARNING => 'E_WARNING',
            E_PARSE => 'E_PARSE',
            E_NOTICE => 'E_NOTICE',
            E_USER_ERROR => 'E_USER_ERROR',
            E_USER_WARNING => 'E_USER_WARNING',
            E_USER_NOTICE => 'E_USER_NOTICE',
            E_STRICT => 'E_STRICT',
            E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
            E_DEPRECATED => 'E_DEPRECATED',
            E_USER_DEPRECATED => 'E_USER_DEPRECATED',
        ];
        return $types[$errno] ?? 'UNKNOWN';
    }
}

// No plugin: WordPressErrorHandler::register();
