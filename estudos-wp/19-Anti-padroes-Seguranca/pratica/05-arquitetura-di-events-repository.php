<?php
/**
 * REFERÊNCIA RÁPIDA – Arquitetura: DI, eventos, repository
 *
 * DI: type hint com interface; não aceitar qualquer objeto; container resolve por interface.
 * Eventos: logar nome do evento e contexto seguro (sem senha/token); logar falhas de listeners.
 * Repository: validar e sanitizar antes de persistir; preferir wp_insert_post/wp_update_post; exceções para inválidos.
 *
 * Fonte: 019-WordPress-Fase-19-Anti-padroes-Seguranca.md
 */

// ========== 1. DI com interface ==========

// ❌ ERRADO: aceitar qualquer gateway
// public function __construct($gateway) { $this->gateway = $gateway; }

interface PaymentGatewayInterface {
    public function charge($amount);
}

class PaymentService {
    private PaymentGatewayInterface $gateway;

    public function __construct(PaymentGatewayInterface $gateway) {
        $this->gateway = $gateway;
    }

    public function process($amount) {
        if (!is_numeric($amount) || $amount <= 0) {
            throw new InvalidArgumentException('Amount must be positive');
        }
        return $this->gateway->charge($amount);
    }
}

// ========== 2. Eventos com logging seguro ==========

class SecureEventDispatcher {
    private $listeners = [];
    private $logger;

    public function __construct($logger) {
        $this->logger = $logger;
    }

    public function dispatch($event_name, $data) {
        $this->logger->info("Event: {$event_name}", [
            'event'      => $event_name,
            'timestamp'  => current_time('mysql'),
            'user_id'    => get_current_user_id(),
            'data_keys'  => array_keys($data),
        ]);
        if (isset($this->listeners[$event_name])) {
            foreach ($this->listeners[$event_name] as $listener) {
                try {
                    $listener($data);
                } catch (Exception $e) {
                    $this->logger->error("Listener failed: {$event_name}", [
                        'error'   => $e->getMessage(),
                        'listener' => is_object($listener) ? get_class($listener) : 'callable',
                    ]);
                    throw $e;
                }
            }
        }
    }
}

// ========== 3. Repository com sanitização ==========

// ❌ ERRADO: $wpdb->insert com $data['title'] sem sanitizar

class PostRepository {
    public function create(array $data): int {
        if (empty($data['title'])) {
            throw new InvalidArgumentException('Title is required');
        }
        $title   = sanitize_text_field($data['title']);
        $content = wp_kses_post($data['content'] ?? '');
        $status  = $data['status'] ?? 'draft';
        $allowed = ['draft', 'publish', 'pending', 'private'];
        if (!in_array($status, $allowed, true)) {
            $status = 'draft';
        }
        $post_id = wp_insert_post([
            'post_title'   => $title,
            'post_content' => $content,
            'post_status'  => $status,
        ]);
        if (is_wp_error($post_id)) {
            throw new RuntimeException($post_id->get_error_message());
        }
        return $post_id;
    }
}
