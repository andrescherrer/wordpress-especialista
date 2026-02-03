<?php
/**
 * REFERÊNCIA RÁPIDA – DDD, Repository e Service Layer
 *
 * DDD: Linguagem ubíqua; Entity (identidade); Value Object (imutável, valor);
 *      Repository (interface no domínio); Domain Service (lógica entre entidades).
 * Service Layer: Um caso de uso por serviço; orquestra validação, repo, domain service, eventos.
 *
 * Fonte: 013-WordPress-Fase-13-Arquitetura-Avancada.md (13.2, 13.3, 13.4)
 */

// ========== Value Objects (imutáveis, igualdade por valor) ==========

final class Price {
    private float $amount;
    private string $currency;

    public function __construct(float $amount, string $currency = 'BRL') {
        if ($amount < 0) {
            throw new InvalidArgumentException('Preço não pode ser negativo');
        }
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public function getAmount(): float {
        return $this->amount;
    }

    public function increase(float $percent): self {
        return new self($this->amount * (1 + $percent / 100), $this->currency);
    }
}

final class ProductId {
    private string $value;

    public function __construct(string $value) {
        if (empty($value)) {
            throw new InvalidArgumentException('ID não pode estar vazio');
        }
        $this->value = $value;
    }

    public function value(): string {
        return $this->value;
    }

    public function equals(self $other): bool {
        return $this->value === $other->value;
    }
}

// ========== Entity (identidade) ==========

class Product {
    private ProductId $id;
    private string $name;
    private Price $price;
    private int $stock;

    public function __construct(ProductId $id, string $name, Price $price, int $stock) {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->stock = max(0, $stock);
    }

    public function getId(): ProductId {
        return $this->id;
    }

    public function getPrice(): Price {
        return $this->price;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getStock(): int {
        return $this->stock;
    }

    public function decreaseStock(int $qty): void {
        if ($qty > $this->stock) {
            throw new RuntimeException('Estoque insuficiente');
        }
        $this->stock -= $qty;
    }
}

// ========== Repository (interface no domínio) ==========

interface ProductRepositoryInterface {
    public function save(Product $product): void;
    public function findById(ProductId $id): ?Product;
    public function delete(ProductId $id): void;
}

// Implementação na infra (WordPress)
class ProductRepositoryWordPress implements ProductRepositoryInterface {
    private const POST_TYPE = 'product';

    public function save(Product $product): void {
        $post_id = wp_insert_post([
            'post_type'   => self::POST_TYPE,
            'post_title'  => $product->getName(),
            'post_status' => 'publish',
        ]);
        if (is_wp_error($post_id)) {
            throw new RuntimeException('Falha ao salvar produto');
        }
        update_post_meta($post_id, '_product_id', $product->getId()->value());
        update_post_meta($post_id, '_price', $product->getPrice()->getAmount());
        update_post_meta($post_id, '_stock', $product->getStock());
    }

    public function findById(ProductId $id): ?Product {
        $posts = get_posts([
            'post_type'      => self::POST_TYPE,
            'posts_per_page' => 1,
            'meta_query'     => [
                [ 'key' => '_product_id', 'value' => $id->value() ],
            ],
        ]);
        if (empty($posts)) {
            return null;
        }
        $p = $posts[0];
        return new Product(
            new ProductId(get_post_meta($p->ID, '_product_id', true)),
            $p->post_title,
            new Price((float) get_post_meta($p->ID, '_price', true)),
            (int) get_post_meta($p->ID, '_stock', true)
        );
    }

    public function delete(ProductId $id): void {
        $product = $this->findById($id);
        if ($product) {
            $posts = get_posts([
                'post_type'      => self::POST_TYPE,
                'posts_per_page' => 1,
                'meta_query'     => [ [ 'key' => '_product_id', 'value' => $id->value() ] ],
            ]);
            if (! empty($posts)) {
                wp_delete_post($posts[0]->ID, true);
            }
        }
    }
}

// ========== Domain Service (lógica que envolve mais de uma entidade) ==========

class PricingDomainService {
    public function calculateDiscount(Product $product, int $quantity): Price {
        $price = $product->getPrice();
        if ($quantity >= 100) {
            return new Price($price->getAmount() * 0.90, 'BRL');
        }
        if ($quantity >= 50) {
            return new Price($price->getAmount() * 0.95, 'BRL');
        }
        return $price;
    }
}

// ========== Application Service (Service Layer) ==========

class CreateOrderDTO {
    public string $productId;
    public int $quantity;
    public int $customerId;
}

class CreateOrderService {
    private ProductRepositoryInterface $productRepository;
    private PricingDomainService $pricingService;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        PricingDomainService $pricingService
    ) {
        $this->productRepository = $productRepository;
        $this->pricingService = $pricingService;
    }

    public function execute(CreateOrderDTO $dto): array {
        $this->validate($dto);

        $productId = new ProductId($dto->productId);
        $product = $this->productRepository->findById($productId);
        if (! $product) {
            throw new RuntimeException('Produto não encontrado');
        }
        if ($product->getStock() < $dto->quantity) {
            throw new RuntimeException('Estoque insuficiente');
        }

        $finalPrice = $this->pricingService->calculateDiscount($product, $dto->quantity);
        $product->decreaseStock($dto->quantity);
        $this->productRepository->save($product);

        // Aqui: salvar pedido em OrderRepository e publicar OrderCreatedEvent
        return [
            'product_id' => $dto->productId,
            'quantity'   => $dto->quantity,
            'total'      => $finalPrice->getAmount() * $dto->quantity,
        ];
    }

    private function validate(CreateOrderDTO $dto): void {
        if (empty($dto->productId) || $dto->quantity < 1 || $dto->customerId <= 0) {
            throw new InvalidArgumentException('Dados do pedido inválidos');
        }
    }
}

// Uso (com container ou manual):
// $repo = new ProductRepositoryWordPress();
// $pricing = new PricingDomainService();
// $service = new CreateOrderService($repo, $pricing);
// $result = $service->execute($dto);
