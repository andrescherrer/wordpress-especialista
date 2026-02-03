<?php
/**
 * REFERÊNCIA RÁPIDA – MVC, Adapter, Strategy e Factory
 *
 * MVC: Model (dados/repo), View (render + escape), Controller (fluxo).
 * Adapter: Interface única para API externa; implementações por provedor.
 * Strategy: Algoritmo intercambiável (ex.: desconto); injetado no serviço.
 * Factory: Criação centralizada de objetos (ex.: gateway por tipo).
 *
 * Fonte: 013-WordPress-Fase-13-Arquitetura-Avancada.md (13.7, 13.8, 13.9, 13.10)
 */

// ========== MVC ==========

class PostModel {
    public function getPost(int $id): ?\WP_Post {
        return get_post($id);
    }

    public function getPosts(array $args = []): array {
        return get_posts(array_merge([ 'numberposts' => 10 ], $args));
    }
}

class PostView {
    public function renderSingle(?\WP_Post $post): string {
        if (! $post) {
            return '';
        }
        ob_start();
        ?>
        <article class="post">
            <h1><?php echo esc_html($post->post_title); ?></h1>
            <div class="content"><?php echo wp_kses_post($post->post_content); ?></div>
        </article>
        <?php
        return ob_get_clean();
    }
}

class PostController {
    private PostModel $model;
    private PostView $view;

    public function __construct(PostModel $model, PostView $view) {
        $this->model = $model;
        $this->view = $view;
    }

    public function show(int $id): string {
        return $this->view->renderSingle($this->model->getPost($id));
    }
}

// Integração: add_shortcode('post_single', fn($atts) => (new PostController(new PostModel(), new PostView()))->show((int)$atts['id']));

// ========== Adapter (API externa) ==========

interface PaymentGatewayAdapter {
    public function charge(float $amount, string $customerId): array;
    public function refund(string $transactionId, float $amount): bool;
}

class StripeAdapter implements PaymentGatewayAdapter {
    public function charge(float $amount, string $customerId): array {
        // Stripe API: valor em centavos, etc.
        return [ 'id' => 'ch_xxx', 'status' => 'succeeded' ];
    }

    public function refund(string $transactionId, float $amount): bool {
        return true;
    }
}

class PagarMeAdapter implements PaymentGatewayAdapter {
    public function charge(float $amount, string $customerId): array {
        // wp_remote_post para API Pagar.me
        return [ 'id' => 'pay_xxx', 'status' => 'paid' ];
    }

    public function refund(string $transactionId, float $amount): bool {
        return true;
    }
}

class OrderPaymentService {
    private PaymentGatewayAdapter $adapter;

    public function __construct(PaymentGatewayAdapter $adapter) {
        $this->adapter = $adapter;
    }

    public function process(float $total, string $customerId): array {
        return $this->adapter->charge($total, $customerId);
    }
}

// Bootstrap: $adapter = get_option('payment_provider') === 'stripe' ? new StripeAdapter() : new PagarMeAdapter();

// ========== Strategy ==========

interface DiscountStrategy {
    public function calculate(float $total): float;
}

class NoDiscountStrategy implements DiscountStrategy {
    public function calculate(float $total): float {
        return 0;
    }
}

class PercentageDiscountStrategy implements DiscountStrategy {
    private float $percentage;

    public function __construct(float $percentage) {
        $this->percentage = $percentage;
    }

    public function calculate(float $total): float {
        return $total * ($this->percentage / 100);
    }
}

class BulkDiscountStrategy implements DiscountStrategy {
    public function calculate(float $total): float {
        if ($total >= 1000) {
            return $total * 0.10;
        }
        if ($total >= 500) {
            return $total * 0.05;
        }
        return 0;
    }
}

class OrderPricingService {
    private DiscountStrategy $strategy;

    public function __construct(DiscountStrategy $strategy) {
        $this->strategy = $strategy;
    }

    public function finalTotal(float $subtotal): float {
        return max(0, $subtotal - $this->strategy->calculate($subtotal));
    }
}

// Uso: new OrderPricingService(new PercentageDiscountStrategy(10)); ou new BulkDiscountStrategy()

// ========== Factory ==========

interface PaymentGatewayFactoryInterface {
    public function create(): PaymentGatewayAdapter;
}

class StripeGatewayFactory implements PaymentGatewayFactoryInterface {
    public function create(): PaymentGatewayAdapter {
        return new StripeAdapter();
    }
}

class PagarMeGatewayFactory implements PaymentGatewayFactoryInterface {
    public function create(): PaymentGatewayAdapter {
        return new PagarMeAdapter();
    }
}

class PaymentGatewayRegistry {
    private array $factories = [];

    public function register(string $type, PaymentGatewayFactoryInterface $factory): void {
        $this->factories[$type] = $factory;
    }

    public function create(string $type): PaymentGatewayAdapter {
        if (! isset($this->factories[$type])) {
            throw new InvalidArgumentException("Gateway '$type' não registrado");
        }
        return $this->factories[$type]->create();
    }
}

// Bootstrap:
// $registry = new PaymentGatewayRegistry();
// $registry->register('stripe', new StripeGatewayFactory());
// $registry->register('pagarme', new PagarMeGatewayFactory());
// $adapter = $registry->create(get_option('payment_provider', 'stripe'));
