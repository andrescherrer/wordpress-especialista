<?php
/**
 * REFERÊNCIA RÁPIDA – DI Container e Event-Driven
 *
 * Container: register(id, factory, singleton), get(id), alias(alias, id).
 *            Factory recebe container para resolver outras dependências.
 * Event-Driven: Domain events (publish/subscribe); ou do_action/add_action.
 *
 * Fonte: 013-WordPress-Fase-13-Arquitetura-Avancada.md (13.5, 13.6)
 */

// ========== Container de Injeção de Dependência (simplificado) ==========

class ServiceContainer {
    private array $services = [];
    private array $singletons = [];
    private array $aliases = [];

    public function register(string $id, callable $factory, bool $singleton = false): void {
        $this->services[$id] = [
            'factory'   => $factory,
            'singleton' => $singleton,
        ];
    }

    public function singleton(string $id, $instance): void {
        $this->singletons[$id] = $instance;
    }

    public function alias(string $alias, string $id): void {
        $this->aliases[$alias] = $id;
    }

    public function get(string $id) {
        if (isset($this->singletons[$id])) {
            return $this->singletons[$id];
        }
        if (isset($this->aliases[$id])) {
            return $this->get($this->aliases[$id]);
        }
        if (! isset($this->services[$id])) {
            throw new RuntimeException("Serviço '$id' não registrado");
        }
        $def = $this->services[$id];
        $instance = $def['factory']($this);
        if ($def['singleton']) {
            $this->singletons[$id] = $instance;
        }
        return $instance;
    }

    public function has(string $id): bool {
        return isset($this->services[$id]) || isset($this->aliases[$id]) || isset($this->singletons[$id]);
    }
}

// Exemplo de configuração
/*
$container = new ServiceContainer();

$container->register('productRepository', function () {
    return new ProductRepositoryWordPress();
}, true);

$container->register('pricingDomainService', function () {
    return new PricingDomainService();
}, true);

$container->register('eventPublisher', function ($c) {
    return new WordPressEventPublisher();
}, true);

$container->register('createOrderService', function ($c) {
    return new CreateOrderService(
        $c->get('productRepository'),
        $c->get('pricingDomainService'),
        $c->get('eventPublisher')
    );
});

$service = $container->get('createOrderService');
*/

// ========== Event-Driven: Domain Events ==========

abstract class DomainEvent {
    protected \DateTimeImmutable $occurredAt;

    public function __construct() {
        $this->occurredAt = new \DateTimeImmutable();
    }

    public function getOccurredAt(): \DateTimeImmutable {
        return $this->occurredAt;
    }
}

class OrderCreatedEvent extends DomainEvent {
    private array $orderData;

    public function __construct(array $orderData) {
        parent::__construct();
        $this->orderData = $orderData;
    }

    public function getOrderData(): array {
        return $this->orderData;
    }
}

interface EventPublisherInterface {
    public function publish(DomainEvent $event): void;
    public function subscribe(string $eventClass, callable $handler): void;
}

class WordPressEventPublisher implements EventPublisherInterface {
    private array $subscribers = [];

    public function publish(DomainEvent $event): void {
        $eventClass = get_class($event);
        if (! isset($this->subscribers[$eventClass])) {
            return;
        }
        foreach ($this->subscribers[$eventClass] as $handler) {
            do_action('domain_event_' . $eventClass, $event);
            $handler($event);
        }
    }

    public function subscribe(string $eventClass, callable $handler): void {
        $this->subscribers[$eventClass][] = $handler;
    }
}

// Uso: após criar pedido no Service
// $this->eventPublisher->publish(new OrderCreatedEvent([ 'id' => $orderId, 'customer_id' => $dto->customerId ]));

// Registrar handlers (bootstrap)
/*
$publisher = $container->get('eventPublisher');
$publisher->subscribe(OrderCreatedEvent::class, function (OrderCreatedEvent $event) {
    $data = $event->getOrderData();
    wp_mail(get_option('admin_email'), 'Novo pedido', 'Pedido #' . ($data['id'] ?? '') . ' criado.');
});
$publisher->subscribe(OrderCreatedEvent::class, function (OrderCreatedEvent $event) {
    // Atualizar estoque, enviar para fila, etc.
});
*/

// ========== Event-Driven: hooks nativos WordPress ==========

// Publicar
// do_action('meu_plugin_order_created', $order_id, $customer_id);

// Inscrever
// add_action('meu_plugin_order_created', function ($order_id, $customer_id) {
//     wp_mail(get_option('admin_email'), 'Novo pedido', "Pedido #$order_id");
// }, 10, 2);
