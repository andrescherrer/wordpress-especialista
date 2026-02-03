<?php
/**
 * REFERÊNCIA RÁPIDA – Padrões: Try-catch com contexto, Result object
 *
 * Try-catch: contexto (order_id, user_id, timestamp); log por tipo; Result ou re-lançar.
 * Result: success($data), failure($error, $data); isSuccess(), getData(), getError(), getOrThrow().
 *
 * Fonte: 020-WordPress-Fase-20-Boas-Praticas-Tratamento-Erros.md
 */

// ========== 1. Result object ==========

class Result {
    private bool $success;
    private $data;
    private ?Exception $error = null;

    private function __construct(bool $success, $data = null, ?Exception $error = null) {
        $this->success = $success;
        $this->data = $data;
        $this->error = $error;
    }

    public static function success($data = null): self {
        return new self(true, $data);
    }

    public static function failure(Exception $error, $data = null): self {
        return new self(false, $data, $error);
    }

    public function isSuccess(): bool {
        return $this->success;
    }

    public function isFailure(): bool {
        return !$this->success;
    }

    public function getData() {
        return $this->data;
    }

    public function getError(): ?Exception {
        return $this->error;
    }

    public function getOrThrow() {
        if ($this->isFailure()) {
            throw $this->error;
        }
        return $this->data;
    }
}

// ========== 2. Try-catch com contexto ==========

class OrderProcessor {
    /** @var object Logger com método log(level, message, context) */
    private $logger;

    public function processOrder(int $order_id): Result {
        $context = [
            'order_id' => $order_id,
            'user_id'  => get_current_user_id(),
            'timestamp'=> time(),
        ];

        try {
            $order = $this->doProcessOrder($order_id);
            return Result::success($order);
        } catch (ValidationException $e) {
            $this->logger->log('warning', 'Order validation failed', array_merge($context, ['exception' => $e->getMessage()]));
            return Result::failure($e, null);
        } catch (PaymentException $e) {
            $this->logger->log('error', 'Payment processing failed', array_merge($context, ['exception' => $e->getMessage()]));
            return Result::failure($e, null);
        } catch (Exception $e) {
            $this->logger->log('critical', 'Unexpected error', array_merge($context, ['trace' => $e->getTraceAsString()]));
            return Result::failure($e, null);
        }
    }

    private function doProcessOrder(int $order_id) {
        // implementação
        return (object) ['id' => $order_id];
    }
}

// Uso do Result
// $result = $processor->processOrder(123);
// if ($result->isFailure()) {
//     $error = $result->getError();
//     // tratar ou exibir mensagem amigável
// } else {
//     $order = $result->getData();
// }

class PaymentException extends Exception {}
