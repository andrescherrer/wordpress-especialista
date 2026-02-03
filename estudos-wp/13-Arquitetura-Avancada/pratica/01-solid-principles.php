<?php
/**
 * REFERÊNCIA RÁPIDA – SOLID em WordPress
 *
 * S (SRP): Uma classe, uma responsabilidade. Orquestrar com Service.
 * O (OCP): Interface + implementações; novo comportamento = nova classe.
 * L (LSP): Subtipos respeitam o contrato (substituíveis).
 * I (ISP): Interfaces pequenas e específicas.
 * D (DIP): Depender de abstrações; injetar dependências (construtor).
 *
 * Fonte: 013-WordPress-Fase-13-Arquitetura-Avancada.md (seção 13.1)
 */

// ========== SRP: separar responsabilidades ==========

class UserValidator {
    public function validate(array $data): void {
        if (empty($data['email']) || ! is_email($data['email'])) {
            throw new InvalidArgumentException('Email inválido');
        }
        if (isset($data['password']) && strlen($data['password']) < 8) {
            throw new InvalidArgumentException('Senha deve ter 8+ caracteres');
        }
    }
}

class UserRepository {
    public function create(string $email, string $password, string $name = ''): int {
        $user_id = wp_create_user($email, $password, $email);
        if (is_wp_error($user_id)) {
            throw new RuntimeException('Falha ao criar usuário');
        }
        if ($name) {
            update_user_meta($user_id, 'first_name', sanitize_text_field($name));
        }
        return $user_id;
    }
}

class UserRegistrationService {
    private UserValidator $validator;
    private UserRepository $repository;

    public function __construct(UserValidator $validator, UserRepository $repository) {
        $this->validator = $validator;
        $this->repository = $repository;
    }

    public function register(array $data): int {
        $this->validator->validate($data);
        return $this->repository->create(
            sanitize_email($data['email']),
            $data['password'],
            $data['name'] ?? ''
        );
    }
}

// ========== OCP: interface + implementações ==========

interface PaymentGateway {
    public function process(float $amount, string $description): bool;
}

class CreditCardGateway implements PaymentGateway {
    public function process(float $amount, string $description): bool {
        // Lógica cartão
        return true;
    }
}

class PixGateway implements PaymentGateway {
    public function process(float $amount, string $description): bool {
        // Lógica PIX
        return true;
    }
}

class PaymentProcessor {
    private PaymentGateway $gateway;

    public function __construct(PaymentGateway $gateway) {
        $this->gateway = $gateway;
    }

    public function pay(float $amount, string $description): bool {
        return $this->gateway->process($amount, $description);
    }
}

// Novo método = nova classe, sem alterar PaymentProcessor
// $processor = new PaymentProcessor(new PixGateway());

// ========== LSP: substituibilidade ==========

interface StorageBackend {
    public function save(string $key, string $content): bool;
    public function get(string $key): string;
}

class LocalStorage implements StorageBackend {
    private string $basePath;

    public function __construct(string $basePath) {
        $this->basePath = $basePath;
    }

    public function save(string $key, string $content): bool {
        $path = $this->basePath . '/' . sanitize_file_name($key);
        return file_put_contents($path, $content) !== false;
    }

    public function get(string $key): string {
        $path = $this->basePath . '/' . sanitize_file_name($key);
        if (! file_exists($path)) {
            throw new RuntimeException("Arquivo não encontrado: $key");
        }
        return (string) file_get_contents($path);
    }
}

// Qualquer StorageBackend pode ser usado no FileManager
class FileManager {
    private StorageBackend $storage;

    public function __construct(StorageBackend $storage) {
        $this->storage = $storage;
    }

    public function put(string $key, string $content): void {
        $this->storage->save($key, $content);
    }

    public function fetch(string $key): string {
        return $this->storage->get($key);
    }
}

// ========== ISP: interfaces segregadas ==========

interface Chargeable {
    public function charge(float $amount): bool;
}

interface Refundable {
    public function refund(string $transactionId, float $amount): bool;
}

class CreditCardGatewayFull implements Chargeable, Refundable {
    public function charge(float $amount): bool {
        return true;
    }
    public function refund(string $transactionId, float $amount): bool {
        return true;
    }
}

class BoletoGatewaySimple implements Chargeable {
    public function charge(float $amount): bool {
        return true;
    }
}

// Cliente que só cobra depende só de Chargeable
// Cliente que reembolsa depende de Refundable

// ========== DIP: depender de abstrações ==========

interface OrderRepositoryInterface {
    public function save(array $order): int;
    public function findById(int $id): ?array;
}

class OrderService {
    private OrderRepositoryInterface $repository;

    public function __construct(OrderRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    public function createOrder(array $data): int {
        $order = [ 'customer_id' => $data['customer_id'], 'total' => $data['total'] ];
        return $this->repository->save($order);
    }
}

// Em bootstrap/container: registrar implementação concreta (ex.: OrderRepositoryWordPress)
// e injetar em OrderService. Testes usam FakeOrderRepository.
