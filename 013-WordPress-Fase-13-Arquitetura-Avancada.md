# 🏗️ FASE 13: Arquitetura Avançada em WordPress

**Versão:** 1.0  
**Data:** Janeiro 2026  
**Nível:** Especialista Sênior em PHP  
**Objetivo:** Dominar padrões de arquitetura enterprise e design patterns avançados em WordPress

---

## 📑 Índice

1. [13.1 - SOLID Principles em WordPress](#131-solid-principles-em-wordpress)
2. [13.2 - Domain-Driven Design (DDD)](#132-domain-driven-design-ddd)
3. [13.3 - Service Layer Pattern](#133-service-layer-pattern)
4. [13.4 - Repository Pattern](#134-repository-pattern)
5. [13.5 - Dependency Injection Container](#135-dependency-injection-container)
6. [13.6 - Event-Driven Architecture](#136-event-driven-architecture)
7. [13.7 - MVC em WordPress](#137-mvc-em-wordpress)
8. [13.8 - Adapter Pattern para APIs Externas](#138-adapter-pattern-para-apis-externas)
9. [13.9 - Strategy Pattern](#139-strategy-pattern)
10. [13.10 - Factory Pattern](#1310-factory-pattern)

---

## 13.1 SOLID Principles em WordPress

Os 5 princípios SOLID são a base para código limpo, manutenível e extensível. Vamos detalhar cada um com exemplos práticos em WordPress.

### 13.1.1 Single Responsibility Principle (S)

**Definição:** Uma classe deve ter apenas uma razão para mudar, ou seja, uma única responsabilidade.

**Problema - Classe com múltiplas responsabilidades:**

```php
<?php
/**
 * ❌ ERRADO - Classe faz tudo (salvar, validar, enviar email, logar)
 */
class UserHandler {
    
    public function handleUserRegistration($data) {
        // Validação
        if (empty($data['email'])) {
            throw new Exception('Email é obrigatório');
        }
        
        // Sanitização
        $email = sanitize_email($data['email']);
        $name = sanitize_text_field($data['name']);
        
        // Criar usuário
        $user_id = wp_create_user($email, $data['password'], $email);
        
        // Enviar email
        wp_mail($email, 'Bem-vindo!', 'Obrigado por se registrar');
        
        // Log
        error_log('Usuário criado: ' . $email);
        
        return $user_id;
    }
}
```

**Solução - Separar responsabilidades:**

```php
<?php
/**
 * ✅ CORRETO - Cada classe tem uma responsabilidade
 */

// 1. Validação
class UserValidator {
    public function validate(array $data): void {
        if (empty($data['email'])) {
            throw new InvalidUserDataException('Email é obrigatório');
        }
        
        if (!is_email($data['email'])) {
            throw new InvalidUserDataException('Email inválido');
        }
        
        if (strlen($data['password']) < 8) {
            throw new InvalidUserDataException('Senha deve ter 8+ caracteres');
        }
    }
}

// 2. Criação de usuário
class UserRepository {
    public function create(string $email, string $password, string $name): int {
        $user_id = wp_create_user($email, $password, $email);
        
        if (is_wp_error($user_id)) {
            throw new UserCreationException('Falha ao criar usuário');
        }
        
        update_user_meta($user_id, 'first_name', $name);
        
        return $user_id;
    }
}

// 3. Envio de emails
class WelcomeEmailService {
    public function sendWelcomeEmail(int $user_id, string $email): void {
        $subject = 'Bem-vindo ao nosso site!';
        $message = 'Obrigado por se registrar.';
        
        $result = wp_mail($email, $subject, $message);
        
        if (!$result) {
            throw new EmailException('Falha ao enviar email');
        }
    }
}

// 4. Logging
class UserRegistrationLogger {
    public function logRegistration(int $user_id, string $email): void {
        error_log("Usuário registrado: ID={$user_id}, Email={$email}");
        
        // Ou usar um serviço de logging mais robusto
        wp_insert_comment([
            'comment_post_ID' => 0,
            'comment_author' => 'SYSTEM',
            'comment_content' => "Novo usuário: $email"
        ]);
    }
}

// 5. Orquestração - Use Case / Service
class UserRegistrationService {
    private UserValidator $validator;
    private UserRepository $repository;
    private WelcomeEmailService $emailService;
    private UserRegistrationLogger $logger;
    
    public function __construct(
        UserValidator $validator,
        UserRepository $repository,
        WelcomeEmailService $emailService,
        UserRegistrationLogger $logger
    ) {
        $this->validator = $validator;
        $this->repository = $repository;
        $this->emailService = $emailService;
        $this->logger = $logger;
    }
    
    public function register(array $userData): int {
        // 1. Validar
        $this->validator->validate($userData);
        
        // 2. Criar usuário
        $user_id = $this->repository->create(
            $userData['email'],
            $userData['password'],
            $userData['name']
        );
        
        // 3. Enviar email
        $this->emailService->sendWelcomeEmail($user_id, $userData['email']);
        
        // 4. Log
        $this->logger->logRegistration($user_id, $userData['email']);
        
        return $user_id;
    }
}

// Uso
$validator = new UserValidator();
$repository = new UserRepository();
$emailService = new WelcomeEmailService();
$logger = new UserRegistrationLogger();

$service = new UserRegistrationService(
    $validator,
    $repository,
    $emailService,
    $logger
);

$user_id = $service->register([
    'name' => 'João Silva',
    'email' => 'joao@example.com',
    'password' => 'senha123'
]);
```

**Benefícios:**
- ✅ Cada classe é fácil de testar isoladamente
- ✅ Mudanças em uma parte não afetam outras
- ✅ Reutilização de código é mais fácil
- ✅ Código é mais legível e manutenível

---

### 13.1.2 Open/Closed Principle (O)

**Definição:** Classes devem estar abertas para extensão, mas fechadas para modificação.

**Problema - Modificar classe para adicionar novo comportamento:**

```php
<?php
/**
 * ❌ ERRADO - Precisa modificar PaymentProcessor para cada novo método
 */
class PaymentProcessor {
    public function process($amount, $type) {
        if ($type === 'credit_card') {
            // Lógica específica de cartão
            return $this->processCreditCard($amount);
        } elseif ($type === 'pix') {
            // Lógica específica de PIX
            return $this->processPix($amount);
        } elseif ($type === 'boleto') {
            // Lógica específica de boleto
            return $this->processBoleto($amount);
        } else {
            throw new Exception('Método de pagamento não suportado');
        }
    }
    
    // ... métodos específicos para cada tipo
}
```

**Solução - Usar abstração (interfaces/classes abstratas):**

```php
<?php
/**
 * ✅ CORRETO - Aberto para extensão via novos métodos de pagamento
 */

// Interface que define o contrato
interface PaymentGateway {
    /**
     * @throws PaymentException
     * @return PaymentResult
     */
    public function process(float $amount, string $description): PaymentResult;
    
    public function refund(string $transactionId, float $amount): PaymentResult;
    
    public function getStatus(string $transactionId): PaymentStatus;
}

// Implementação específica para Cartão de Crédito
class CreditCardGateway implements PaymentGateway {
    private string $apiKey;
    private string $apiSecret;
    
    public function __construct(string $apiKey, string $apiSecret) {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
    }
    
    public function process(float $amount, string $description): PaymentResult {
        // Lógica específica de cartão
        $response = wp_remote_post('https://api.payment.com/charge', [
            'headers' => ['Authorization' => 'Bearer ' . $this->apiKey],
            'body' => [
                'amount' => $amount,
                'description' => $description
            ]
        ]);
        
        if (is_wp_error($response)) {
            throw new PaymentException($response->get_error_message());
        }
        
        $data = json_decode(wp_remote_retrieve_body($response), true);
        
        return new PaymentResult(
            $data['transaction_id'],
            PaymentStatus::SUCCESS,
            $data['amount']
        );
    }
    
    public function refund(string $transactionId, float $amount): PaymentResult {
        // Lógica de reembolso para cartão
        // ...
    }
    
    public function getStatus(string $transactionId): PaymentStatus {
        // Verificar status da transação
        // ...
    }
}

// Implementação específica para PIX
class PixGateway implements PaymentGateway {
    private string $accountKey;
    
    public function __construct(string $accountKey) {
        $this->accountKey = $accountKey;
    }
    
    public function process(float $amount, string $description): PaymentResult {
        // Lógica específica de PIX
        $qrCode = $this->generateQRCode($amount, $description);
        
        return new PaymentResult(
            uniqid('pix_'),
            PaymentStatus::PENDING,
            $amount,
            $qrCode
        );
    }
    
    public function refund(string $transactionId, float $amount): PaymentResult {
        // Lógica de reembolso para PIX
        // ...
    }
    
    public function getStatus(string $transactionId): PaymentStatus {
        // Verificar status do PIX
        // ...
    }
    
    private function generateQRCode(float $amount, string $description): string {
        // Gerar QR code para PIX
        // ...
    }
}

// Implementação para Boleto
class BoletoGateway implements PaymentGateway {
    private string $bankCode;
    
    public function __construct(string $bankCode) {
        $this->bankCode = $bankCode;
    }
    
    public function process(float $amount, string $description): PaymentResult {
        // Lógica específica de boleto
        $boletoData = $this->generateBoleto($amount, $description);
        
        return new PaymentResult(
            $boletoData['barcode'],
            PaymentStatus::PENDING,
            $amount,
            ['barcode' => $boletoData['barcode'], 'dueDate' => $boletoData['dueDate']]
        );
    }
    
    public function refund(string $transactionId, float $amount): PaymentResult {
        throw new PaymentException('Boleto não suporta reembolso direto');
    }
    
    public function getStatus(string $transactionId): PaymentStatus {
        // Verificar status do boleto
        // ...
    }
    
    private function generateBoleto(float $amount, string $description): array {
        // Gerar dados do boleto
        // ...
    }
}

// Processor agora é estável e fechado para modificação
class PaymentProcessor {
    private PaymentGateway $gateway;
    
    public function __construct(PaymentGateway $gateway) {
        $this->gateway = $gateway;
    }
    
    public function processPayment(Order $order): PaymentResult {
        return $this->gateway->process(
            $order->getTotal(),
            'Pedido #' . $order->getId()
        );
    }
    
    public function refundPayment(string $transactionId, float $amount): PaymentResult {
        return $this->gateway->refund($transactionId, $amount);
    }
}

// Para adicionar novo método de pagamento, criamos nova classe sem modificar nada
class ApplePayGateway implements PaymentGateway {
    // Nova implementação sem tocar no código existente
    // ...
}

// Uso
$order = new Order(1, 100.00);

// Usar cartão de crédito
$creditCardGateway = new CreditCardGateway('key123', 'secret456');
$processor = new PaymentProcessor($creditCardGateway);
$result = $processor->processPayment($order);

// Mudar para PIX - sem modificar PaymentProcessor!
$pixGateway = new PixGateway('account789');
$processor = new PaymentProcessor($pixGateway);
$result = $processor->processPayment($order);
```

**Benefícios:**
- ✅ Adicionar nova funcionalidade sem modificar código existente
- ✅ Reduz risco de quebrar funcionalidades existentes
- ✅ Código estável e previsível
- ✅ Fácil para novos membros entenderem o padrão

---

### 13.1.3 Liskov Substitution Principle (L)

**Definição:** Objetos de uma classe derivada podem ser substituídos por objetos da classe base sem quebrar o programa.

**Problema - Contrato violado:**

```php
<?php
/**
 * ❌ ERRADO - Bird pode ser substituído por Penguin?
 */

class Bird {
    public function fly(): void {
        echo "Pássaro voando...\n";
    }
}

class Sparrow extends Bird {
    public function fly(): void {
        echo "Pardal voando...\n";
    }
}

class Penguin extends Bird {
    public function fly(): void {
        // ❌ VIOLAÇÃO - Pinguim não voa!
        throw new Exception('Pinguim não pode voar!');
    }
}

// Código esperando substituibilidade
function makeBirdFly(Bird $bird) {
    $bird->fly(); // Funcionará para Sparrow, mas lançará exceção para Penguin
}

$birds = [new Sparrow(), new Penguin()];
foreach ($birds as $bird) {
    makeBirdFly($bird); // ❌ Vai quebrar quando chegar no Penguin
}
```

**Solução - Respeitar o contrato:**

```php
<?php
/**
 * ✅ CORRETO - Cada classe implementa corretamente seu contrato
 */

// Interface para ser substituível
interface Movable {
    public function move(): void;
}

// Abstrato base
abstract class Bird implements Movable {
    protected string $name;
    
    public function __construct(string $name) {
        $this->name = $name;
    }
    
    abstract public function move(): void;
}

// Implementação para pássaros que voam
class FlyingBird extends Bird {
    public function move(): void {
        echo "{$this->name} está voando...\n";
    }
}

// Implementação para pinguins que nadam
class SwimmingBird extends Bird {
    public function move(): void {
        echo "{$this->name} está nadando...\n";
    }
}

// Agora é seguro substituir
function makeBirdMove(Movable $movable) {
    $movable->move();
}

$birds = [
    new FlyingBird('Pardal'),
    new SwimmingBird('Pinguim'),
    new FlyingBird('Águia')
];

foreach ($birds as $bird) {
    makeBirdMove($bird); // ✅ Funcionará corretamente para todos
}

// Exemplo mais prático em WordPress

interface StorageBackend {
    /**
     * @throws StorageException se houver erro
     */
    public function save(string $file, string $content): bool;
    
    /**
     * @throws StorageException se arquivo não existir
     */
    public function get(string $file): string;
    
    public function delete(string $file): bool;
}

// Implementação local
class LocalStorage implements StorageBackend {
    private string $basePath;
    
    public function __construct(string $basePath) {
        if (!is_dir($basePath)) {
            mkdir($basePath, 0755, true);
        }
        $this->basePath = $basePath;
    }
    
    public function save(string $file, string $content): bool {
        $path = $this->basePath . '/' . sanitize_file_name($file);
        return file_put_contents($path, $content) !== false;
    }
    
    public function get(string $file): string {
        $path = $this->basePath . '/' . sanitize_file_name($file);
        if (!file_exists($path)) {
            throw new StorageException("Arquivo não encontrado: $file");
        }
        return file_get_contents($path);
    }
    
    public function delete(string $file): bool {
        $path = $this->basePath . '/' . sanitize_file_name($file);
        return unlink($path);
    }
}

// Implementação S3
class S3Storage implements StorageBackend {
    private $s3Client;
    private string $bucket;
    
    public function __construct($s3Client, string $bucket) {
        $this->s3Client = $s3Client;
        $this->bucket = $bucket;
    }
    
    public function save(string $file, string $content): bool {
        try {
            $this->s3Client->putObject([
                'Bucket' => $this->bucket,
                'Key' => $file,
                'Body' => $content
            ]);
            return true;
        } catch (\Exception $e) {
            throw new StorageException($e->getMessage());
        }
    }
    
    public function get(string $file): string {
        try {
            $result = $this->s3Client->getObject([
                'Bucket' => $this->bucket,
                'Key' => $file
            ]);
            return (string)$result['Body'];
        } catch (\Exception $e) {
            throw new StorageException("Arquivo não encontrado: $file");
        }
    }
    
    public function delete(string $file): bool {
        try {
            $this->s3Client->deleteObject([
                'Bucket' => $this->bucket,
                'Key' => $file
            ]);
            return true;
        } catch (\Exception $e) {
            throw new StorageException($e->getMessage());
        }
    }
}

// Uso - ambas são perfeitamente substituíveis
class FileManager {
    private StorageBackend $storage;
    
    public function __construct(StorageBackend $storage) {
        $this->storage = $storage;
    }
    
    public function uploadFile(string $filename, string $content): void {
        $this->storage->save($filename, $content);
    }
    
    public function downloadFile(string $filename): string {
        return $this->storage->get($filename);
    }
}

// Pode usar local
$fileManager = new FileManager(new LocalStorage('/var/uploads'));

// Ou S3 - sem mudar o código de FileManager
$fileManager = new FileManager(new S3Storage($s3Client, 'my-bucket'));
```

**Benefícios:**
- ✅ Polimorfismo real e confiável
- ✅ Evita surpresas em runtime
- ✅ Código previsível e seguro
- ✅ Fácil fazer testes com mocks

---

### 13.1.4 Interface Segregation Principle (I)

**Definição:** Clientes não devem ser forçados a depender de interfaces que não usam. Muitas interfaces específicas são melhor que uma interface genérica.

**Problema - Interface muito grande:**

```php
<?php
/**
 * ❌ ERRADO - Força implementar métodos não necessários
 */

interface Worker {
    public function work(): void;
    public function eat(): void;
    public function sleep(): void;
}

class Human implements Worker {
    public function work(): void { /* ... */ }
    public function eat(): void { /* ... */ }
    public function sleep(): void { /* ... */ }
}

class Robot implements Worker {
    public function work(): void { /* ... */ }
    
    // ❌ Robot não come nem dorme!
    public function eat(): void {
        throw new Exception('Robot não come!');
    }
    
    public function sleep(): void {
        throw new Exception('Robot não dorme!');
    }
}
```

**Solução - Segregar interfaces:**

```php
<?php
/**
 * ✅ CORRETO - Interfaces específicas e focadas
 */

// Cada interface tem uma responsabilidade clara
interface Workable {
    public function work(): void;
}

interface Eatable {
    public function eat(): void;
}

interface Sleepable {
    public function sleep(): void;
}

// Human implementa o que de fato precisa
class Human implements Workable, Eatable, Sleepable {
    public function work(): void {
        echo "Humano trabalhando...\n";
    }
    
    public function eat(): void {
        echo "Humano comendo...\n";
    }
    
    public function sleep(): void {
        echo "Humano dormindo...\n";
    }
}

// Robot só implementa o que faz
class Robot implements Workable {
    public function work(): void {
        echo "Robô trabalhando...\n";
    }
}

// Exemplo prático em WordPress

interface PaymentGateway {
    public function charge(float $amount): PaymentResult;
}

interface RefundablePaymentGateway {
    public function refund(string $transactionId, float $amount): PaymentResult;
}

interface SubscriptionPaymentGateway {
    public function createSubscription(string $planId, string $customerId): SubscriptionResult;
    public function cancelSubscription(string $subscriptionId): bool;
}

// Cartão de crédito - suporta tudo
class CreditCardGateway implements PaymentGateway, RefundablePaymentGateway, SubscriptionPaymentGateway {
    public function charge(float $amount): PaymentResult { /* ... */ }
    public function refund(string $transactionId, float $amount): PaymentResult { /* ... */ }
    public function createSubscription(string $planId, string $customerId): SubscriptionResult { /* ... */ }
    public function cancelSubscription(string $subscriptionId): bool { /* ... */ }
}

// Boleto - só cobra
class BoletoGateway implements PaymentGateway {
    public function charge(float $amount): PaymentResult { /* ... */ }
    // Não é obrigado a implementar refund ou subscription
}

// PIX - cobra mas não tem subscription
class PixGateway implements PaymentGateway, RefundablePaymentGateway {
    public function charge(float $amount): PaymentResult { /* ... */ }
    public function refund(string $transactionId, float $amount): PaymentResult { /* ... */ }
}

// Código cliente seguro usa apenas o que precisa
class OrderService {
    private PaymentGateway $gateway;
    
    public function __construct(PaymentGateway $gateway) {
        $this->gateway = $gateway;
    }
    
    public function createOrder(float $amount): void {
        $this->gateway->charge($amount);
    }
}

// Para refund, verificar se suporta
class RefundService {
    private PaymentGateway $gateway;
    
    public function __construct(PaymentGateway $gateway) {
        if (!$gateway instanceof RefundablePaymentGateway) {
            throw new Exception('Gateway não suporta reembolsos');
        }
        $this->gateway = $gateway;
    }
    
    public function refund(string $transactionId, float $amount): void {
        assert($this->gateway instanceof RefundablePaymentGateway);
        $this->gateway->refund($transactionId, $amount);
    }
}
```

**Benefícios:**
- ✅ Classes não são forçadas a implementar métodos inúteis
- ✅ Interfaces específicas são mais reutilizáveis
- ✅ Código mais compreensível
- ✅ Flexibilidade ao implementar novos tipos

---

### 13.1.5 Dependency Inversion Principle (D)

**Definição:** Dependa de abstrações, não de implementações concretas. Classes de alto nível não devem depender de classes de baixo nível; ambas devem depender de abstrações.

**Problema - Acoplamento forte:**

```php
<?php
/**
 * ❌ ERRADO - Acoplado a implementação concreta
 */

class OrderService {
    private MySQLOrderRepository $repository;
    private GmailEmailService $emailService;
    
    public function __construct() {
        // ❌ Cria dependências internas - muito acoplado
        $this->repository = new MySQLOrderRepository();
        $this->emailService = new GmailEmailService();
    }
    
    public function createOrder($data) {
        // Usar services
        $order = $this->repository->save($data);
        $this->emailService->sendConfirmation($order);
    }
}

// Problemas:
// 1. Impossível testar sem banco de dados real
// 2. Impossível trocar MySQL por PostgreSQL sem modificar a classe
// 3. Impossível trocar Gmail por outro serviço sem modificar
// 4. Ordem de inicialização é rígida
```

**Solução - Injeção de dependências:**

```php
<?php
/**
 * ✅ CORRETO - Depende de abstrações
 */

// Abstrações (Interfaces)
interface OrderRepository {
    public function save(Order $order): Order;
    public function findById(int $id): ?Order;
}

interface EmailService {
    public function sendConfirmation(Order $order): bool;
}

// Implementações específicas
class MySQLOrderRepository implements OrderRepository {
    private $wpdb;
    
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }
    
    public function save(Order $order): Order {
        // Salvar no MySQL
        return $order;
    }
    
    public function findById(int $id): ?Order {
        // Buscar do MySQL
        return null;
    }
}

class GmailEmailService implements EmailService {
    public function sendConfirmation(Order $order): bool {
        // Enviar com Gmail
        return true;
    }
}

class PostmarkEmailService implements EmailService {
    public function sendConfirmation(Order $order): bool {
        // Enviar com Postmark
        return true;
    }
}

// Classe de alto nível depende de abstrações
class OrderService {
    private OrderRepository $repository;
    private EmailService $emailService;
    
    // ✅ Injeção por construtor
    public function __construct(
        OrderRepository $repository,
        EmailService $emailService
    ) {
        $this->repository = $repository;
        $this->emailService = $emailService;
    }
    
    public function createOrder(array $data): Order {
        $order = Order::fromArray($data);
        
        $savedOrder = $this->repository->save($order);
        
        $this->emailService->sendConfirmation($savedOrder);
        
        return $savedOrder;
    }
}

// Container de Injeção de Dependência
class ServiceContainer {
    private array $services = [];
    
    public function register(string $name, callable $factory): void {
        $this->services[$name] = $factory;
    }
    
    public function get(string $name) {
        if (!isset($this->services[$name])) {
            throw new Exception("Serviço $name não registrado");
        }
        
        return $this->services[$name]($this);
    }
}

// Configurar o container
$container = new ServiceContainer();

$container->register('orderRepository', function() {
    return new MySQLOrderRepository();
});

$container->register('emailService', function() {
    // Decidir qual usar em tempo de inicialização
    if (get_option('email_provider') === 'postmark') {
        return new PostmarkEmailService();
    }
    return new GmailEmailService();
});

$container->register('orderService', function($c) {
    return new OrderService(
        $c->get('orderRepository'),
        $c->get('emailService')
    );
});

// Usar
$orderService = $container->get('orderService');
$order = $orderService->createOrder([
    'customer_id' => 1,
    'total' => 100.00
]);

// Para testes, trocar implementações é trivial
class FakeOrderRepository implements OrderRepository {
    private array $orders = [];
    
    public function save(Order $order): Order {
        $this->orders[$order->getId()] = $order;
        return $order;
    }
    
    public function findById(int $id): ?Order {
        return $this->orders[$id] ?? null;
    }
}

class FakeEmailService implements EmailService {
    public array $sentEmails = [];
    
    public function sendConfirmation(Order $order): bool {
        $this->sentEmails[] = $order;
        return true;
    }
}

// Teste é fácil
public function testCreateOrder() {
    $repository = new FakeOrderRepository();
    $emailService = new FakeEmailService();
    $service = new OrderService($repository, $emailService);
    
    $order = $service->createOrder(['customer_id' => 1, 'total' => 100]);
    
    // Assertions
    $this->assertNotNull($repository->findById($order->getId()));
    $this->assertCount(1, $emailService->sentEmails);
}
```

**Benefícios:**
- ✅ Testabilidade excelente - use mocks/fakes
- ✅ Flexibilidade - trocar implementações em tempo de inicialização
- ✅ Desacoplamento total - mudanças em uma classe não afetam outras
- ✅ Código reutilizável em diferentes contextos

---

## 13.2 Domain-Driven Design (DDD)

Domain-Driven Design é uma filosofia arquitetural que coloca a lógica de negócio no centro da aplicação.

### 13.2.1 Estrutura Básica DDD

```
src/
├── Domain/                      # Núcleo - Lógica de negócio pura
│   ├── Product/
│   │   ├── Entity/
│   │   │   └── Product.php
│   │   ├── ValueObject/
│   │   │   ├── ProductId.php
│   │   │   ├── ProductName.php
│   │   │   ├── Price.php
│   │   │   └── Inventory.php
│   │   ├── Repository/
│   │   │   └── ProductRepository.php
│   │   ├── Service/
│   │   │   └── PricingDomainService.php
│   │   └── Event/
│   │       ├── ProductCreatedEvent.php
│   │       ├── PriceChangedEvent.php
│   │       └── InventoryUpdatedEvent.php
│   │
│   ├── Order/
│   │   ├── Entity/
│   │   │   ├── Order.php
│   │   │   └── OrderItem.php
│   │   ├── ValueObject/
│   │   │   ├── OrderId.php
│   │   │   ├── OrderStatus.php
│   │   │   └── Money.php
│   │   ├── Repository/
│   │   │   └── OrderRepository.php
│   │   └── Service/
│   │       └── OrderCalculationService.php
│   │
│   └── Shared/
│       ├── ValueObject/
│       │   ├── Uuid.php
│       │   ├── Email.php
│       │   └── DateTime.php
│       └── Exception/
│           ├── DomainException.php
│           └── AggregateNotFoundException.php
│
├── Application/                 # Orquestração de casos de uso
│   ├── Product/
│   │   ├── CreateProductUseCase.php
│   │   ├── UpdateProductUseCase.php
│   │   └── ProductDTO.php
│   │
│   ├── Order/
│   │   ├── CreateOrderUseCase.php
│   │   ├── CompleteOrderUseCase.php
│   │   └── OrderDTO.php
│   │
│   └── EventSubscriber/
│       ├── OnProductCreatedSendEmailSubscriber.php
│       └── OnOrderCompletedUpdateInventorySubscriber.php
│
└── Infrastructure/              # Detalhes técnicos
    ├── Persistence/
    │   ├── ProductRepositoryWordPress.php
    │   └── OrderRepositoryWordPress.php
    │
    ├── Notification/
    │   ├── EmailNotificationService.php
    │   └── SMSNotificationService.php
    │
    └── EventPublisher/
        └── WordPressEventPublisher.php
```

### 13.2.2 Entities vs Value Objects

**Entities - Identidade própria:**

```php
<?php
/**
 * Entity - Tem identidade única mesmo se atributos mudam
 */

class Product {
    private ProductId $id;
    private ProductName $name;
    private Price $price;
    private Inventory $inventory;
    
    public function __construct(
        ProductId $id,
        ProductName $name,
        Price $price,
        Inventory $inventory
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->inventory = $inventory;
    }
    
    public function getId(): ProductId {
        return $this->id;
    }
    
    public function updatePrice(Price $newPrice): void {
        $this->price = $newPrice;
    }
    
    public function decreaseInventory(int $quantity): void {
        $this->inventory = $this->inventory->decrease($quantity);
    }
    
    // Igualdade por identidade (ID)
    public function equals(self $other): bool {
        return $this->id->equals($other->id);
    }
}
```

**Value Objects - Sem identidade, imutáveis:**

```php
<?php
/**
 * Value Object - Sem identidade, comparados por valor
 */

final class Price {
    private float $amount;
    private string $currency;
    
    public function __construct(float $amount, string $currency = 'BRL') {
        if ($amount < 0) {
            throw new InvalidPriceException('Preço não pode ser negativo');
        }
        
        $this->amount = $amount;
        $this->currency = $currency;
    }
    
    public function getAmount(): float {
        return $this->amount;
    }
    
    public function getCurrency(): string {
        return $this->currency;
    }
    
    // Imutável - criar novo ao invés de modificar
    public function increase(float $percentage): self {
        $newAmount = $this->amount * (1 + $percentage / 100);
        return new self($newAmount, $this->currency);
    }
    
    public function decrease(float $percentage): self {
        $newAmount = $this->amount * (1 - $percentage / 100);
        return new self($newAmount, $this->currency);
    }
    
    // Igualdade por valor
    public function equals(self $other): bool {
        return $this->amount === $other->amount 
            && $this->currency === $other->currency;
    }
    
    public function __toString(): string {
        return "{$this->currency} " . number_format($this->amount, 2);
    }
}

final class ProductId {
    private string $value;
    
    public function __construct(string $value) {
        if (empty($value)) {
            throw new InvalidProductIdException('ID não pode estar vazio');
        }
        $this->value = $value;
    }
    
    public function value(): string {
        return $this->value;
    }
    
    public function equals(self $other): bool {
        return $this->value === $other->value;
    }
    
    public function __toString(): string {
        return $this->value;
    }
}

final class Inventory {
    private int $quantity;
    
    public function __construct(int $quantity) {
        if ($quantity < 0) {
            throw new InvalidInventoryException('Estoque não pode ser negativo');
        }
        $this->quantity = $quantity;
    }
    
    public function quantity(): int {
        return $this->quantity;
    }
    
    public function decrease(int $amount): self {
        if ($amount > $this->quantity) {
            throw new InsufficientInventoryException('Estoque insuficiente');
        }
        return new self($this->quantity - $amount);
    }
    
    public function increase(int $amount): self {
        return new self($this->quantity + $amount);
    }
    
    public function isAvailable(int $requiredAmount): bool {
        return $this->quantity >= $requiredAmount;
    }
}
```

### 13.2.3 Repositories

```php
<?php
/**
 * Repository Pattern - Abstrai persistência
 */

interface ProductRepository {
    public function save(Product $product): void;
    
    public function findById(ProductId $id): ?Product;
    
    public function findAll(): array;
    
    /**
     * @return Product[]
     */
    public function findByPriceRange(Price $min, Price $max): array;
    
    public function delete(ProductId $id): void;
}

/**
 * Implementação WordPress
 */
class ProductRepositoryWordPress implements ProductRepository {
    public function save(Product $product): void {
        $post_id = wp_insert_post([
            'post_type' => 'product',
            'post_title' => $product->getName(),
            'post_status' => 'publish'
        ]);
        
        if (is_wp_error($post_id)) {
            throw new RepositoryException('Falha ao salvar produto');
        }
        
        // Salvar em metadados
        update_post_meta($post_id, '_product_id', (string)$product->getId());
        update_post_meta($post_id, '_price', $product->getPrice()->getAmount());
        update_post_meta($post_id, '_inventory', $product->getInventory()->quantity());
    }
    
    public function findById(ProductId $id): ?Product {
        $args = [
            'post_type' => 'product',
            'meta_query' => [
                [
                    'key' => '_product_id',
                    'value' => (string)$id,
                ]
            ]
        ];
        
        $posts = get_posts($args);
        
        if (empty($posts)) {
            return null;
        }
        
        return $this->mapToEntity($posts[0]);
    }
    
    public function findAll(): array {
        $posts = get_posts([
            'post_type' => 'product',
            'numberposts' => -1
        ]);
        
        return array_map([$this, 'mapToEntity'], $posts);
    }
    
    public function findByPriceRange(Price $min, Price $max): array {
        $args = [
            'post_type' => 'product',
            'meta_query' => [
                'relation' => 'AND',
                [
                    'key' => '_price',
                    'value' => $min->getAmount(),
                    'compare' => '>=',
                    'type' => 'NUMERIC'
                ],
                [
                    'key' => '_price',
                    'value' => $max->getAmount(),
                    'compare' => '<=',
                    'type' => 'NUMERIC'
                ]
            ]
        ];
        
        $posts = get_posts($args);
        return array_map([$this, 'mapToEntity'], $posts);
    }
    
    public function delete(ProductId $id): void {
        $product = $this->findById($id);
        if (!$product) {
            throw new ProductNotFoundException('Produto não encontrado');
        }
        wp_delete_post($product->getPostId(), true);
    }
    
    private function mapToEntity($post): Product {
        $id = new ProductId(get_post_meta($post->ID, '_product_id', true));
        $name = new ProductName($post->post_title);
        $price = new Price(
            (float)get_post_meta($post->ID, '_price', true)
        );
        $inventory = new Inventory(
            (int)get_post_meta($post->ID, '_inventory', true)
        );
        
        return new Product($id, $name, $price, $inventory);
    }
}
```

### 13.2.4 Domain Services

```php
<?php
/**
 * Domain Service - Lógica de negócio que envolve múltiplas entidades
 */

class PricingDomainService {
    public function calculateDiscount(
        Product $product,
        Customer $customer,
        int $quantity
    ): Price {
        $basePrice = $product->getPrice();
        
        // Desconto por volume
        if ($quantity >= 100) {
            $basePrice = $basePrice->decrease(10); // 10% desconto
        } elseif ($quantity >= 50) {
            $basePrice = $basePrice->decrease(5); // 5% desconto
        }
        
        // Desconto para cliente VIP
        if ($customer->isVIP()) {
            $basePrice = $basePrice->decrease(5); // 5% desconto adicional
        }
        
        return $basePrice;
    }
    
    public function calculateTax(Price $price, string $region): Price {
        $taxRate = $this->getTaxRateForRegion($region);
        return $price->increase($taxRate);
    }
    
    private function getTaxRateForRegion(string $region): float {
        return match($region) {
            'SP' => 18,
            'RJ' => 20,
            'BA' => 19,
            default => 15
        };
    }
}

// Uso no Application Layer
class CreateOrderUseCase {
    private OrderRepository $orderRepository;
    private ProductRepository $productRepository;
    private PricingDomainService $pricingService;
    private EventPublisher $eventPublisher;
    
    public function __construct(
        OrderRepository $orderRepository,
        ProductRepository $productRepository,
        PricingDomainService $pricingService,
        EventPublisher $eventPublisher
    ) {
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
        $this->pricingService = $pricingService;
        $this->eventPublisher = $eventPublisher;
    }
    
    public function execute(CreateOrderDTO $dto): OrderDTO {
        // Buscar entidades
        $product = $this->productRepository->findById(
            new ProductId($dto->productId)
        );
        
        if (!$product) {
            throw new ProductNotFoundException('Produto não encontrado');
        }
        
        // Usar Domain Service para lógica de negócio
        $finalPrice = $this->pricingService->calculateDiscount(
            $product,
            new Customer($dto->customerId),
            $dto->quantity
        );
        
        // Aplicar imposto
        $priceWithTax = $this->pricingService->calculateTax(
            $finalPrice,
            $dto->region
        );
        
        // Criar entidade
        $order = Order::create(
            new OrderId(uniqid()),
            new CustomerId($dto->customerId),
            $product,
            $dto->quantity,
            $priceWithTax
        );
        
        // Persistir
        $this->orderRepository->save($order);
        
        // Publicar evento de domínio
        $this->eventPublisher->publish(
            new OrderCreatedEvent($order)
        );
        
        return OrderDTO::fromEntity($order);
    }
}
```

### 13.2.5 Domain Events

```php
<?php
/**
 * Domain Event - Algo importante aconteceu no domínio
 */

abstract class DomainEvent {
    protected DateTime $occurredAt;
    
    public function __construct() {
        $this->occurredAt = new DateTime('now');
    }
    
    public function getOccurredAt(): DateTime {
        return $this->occurredAt;
    }
}

class OrderCreatedEvent extends DomainEvent {
    private Order $order;
    
    public function __construct(Order $order) {
        parent::__construct();
        $this->order = $order;
    }
    
    public function getOrder(): Order {
        return $this->order;
    }
}

class OrderCompletedEvent extends DomainEvent {
    private Order $order;
    
    public function __construct(Order $order) {
        parent::__construct();
        $this->order = $order;
    }
    
    public function getOrder(): Order {
        return $this->order;
    }
}

// Event Publisher
interface EventPublisher {
    public function publish(DomainEvent $event): void;
    
    public function subscribe(string $eventClass, callable $handler): void;
}

class WordPressEventPublisher implements EventPublisher {
    private array $subscribers = [];
    
    public function publish(DomainEvent $event): void {
        $eventClass = get_class($event);
        
        if (!isset($this->subscribers[$eventClass])) {
            return;
        }
        
        foreach ($this->subscribers[$eventClass] as $handler) {
            do_action('domain_event_' . $eventClass, $event);
            call_user_func($handler, $event);
        }
    }
    
    public function subscribe(string $eventClass, callable $handler): void {
        $this->subscribers[$eventClass][] = $handler;
    }
}

// Event Subscribers (Handlers)
class OnOrderCreatedSendEmailSubscriber {
    private EmailService $emailService;
    
    public function __construct(EmailService $emailService) {
        $this->emailService = $emailService;
    }
    
    public function handle(OrderCreatedEvent $event): void {
        $order = $event->getOrder();
        
        $this->emailService->sendOrderConfirmation($order);
    }
}

class OnOrderCompletedUpdateInventorySubscriber {
    private ProductRepository $productRepository;
    
    public function __construct(ProductRepository $productRepository) {
        $this->productRepository = $productRepository;
    }
    
    public function handle(OrderCompletedEvent $event): void {
        $order = $event->getOrder();
        
        // Atualizar inventário
        foreach ($order->getItems() as $item) {
            $product = $this->productRepository->findById(
                $item->getProductId()
            );
            
            $product->decreaseInventory($item->getQuantity());
            
            $this->productRepository->save($product);
        }
    }
}

// Registrar subscribers (geralmente em bootstrap/initialization)
$eventPublisher = new WordPressEventPublisher();

$eventPublisher->subscribe(
    OrderCreatedEvent::class,
    function(OrderCreatedEvent $event) {
        $emailService = new EmailService();
        $subscriber = new OnOrderCreatedSendEmailSubscriber($emailService);
        $subscriber->handle($event);
    }
);

$eventPublisher->subscribe(
    OrderCompletedEvent::class,
    function(OrderCompletedEvent $event) {
        $productRepository = new ProductRepositoryWordPress();
        $subscriber = new OnOrderCompletedUpdateInventorySubscriber($productRepository);
        $subscriber->handle($event);
    }
);
```

---

## 13.3 Service Layer Pattern

```php
<?php
/**
 * Service Layer - Orquestra lógica de negócio
 */

interface CreateOrderService {
    public function execute(CreateOrderDTO $dto): OrderDTO;
}

class CreateOrderApplicationService implements CreateOrderService {
    private OrderRepository $orderRepository;
    private ProductRepository $productRepository;
    private PricingDomainService $pricingService;
    private EventPublisher $eventPublisher;
    private Logger $logger;
    
    public function __construct(
        OrderRepository $orderRepository,
        ProductRepository $productRepository,
        PricingDomainService $pricingService,
        EventPublisher $eventPublisher,
        Logger $logger
    ) {
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
        $this->pricingService = $pricingService;
        $this->eventPublisher = $eventPublisher;
        $this->logger = $logger;
    }
    
    public function execute(CreateOrderDTO $dto): OrderDTO {
        try {
            $this->logger->info('Iniciando criação de pedido', ['dto' => $dto]);
            
            // Validar DTO
            $this->validateDTO($dto);
            
            // Buscar produto
            $product = $this->productRepository->findById(
                new ProductId($dto->productId)
            );
            
            if (!$product) {
                throw new ProductNotFoundException('Produto não encontrado');
            }
            
            // Verificar disponibilidade
            if (!$product->getInventory()->isAvailable($dto->quantity)) {
                throw new InsufficientInventoryException('Estoque insuficiente');
            }
            
            // Calcular preço
            $finalPrice = $this->pricingService->calculateDiscount(
                $product,
                new Customer($dto->customerId),
                $dto->quantity
            );
            
            // Criar entidade
            $order = Order::create(
                new OrderId(wp_generate_uuid4()),
                new CustomerId($dto->customerId),
                $product,
                $dto->quantity,
                $finalPrice
            );
            
            // Persistir
            $this->orderRepository->save($order);
            
            // Publicar evento
            $this->eventPublisher->publish(
                new OrderCreatedEvent($order)
            );
            
            $this->logger->info('Pedido criado com sucesso', ['orderId' => $order->getId()]);
            
            return OrderDTO::fromEntity($order);
            
        } catch (DomainException $e) {
            $this->logger->error('Erro de domínio ao criar pedido', [
                'error' => $e->getMessage(),
                'dto' => $dto
            ]);
            throw $e;
        } catch (\Exception $e) {
            $this->logger->error('Erro inesperado ao criar pedido', [
                'error' => $e->getMessage()
            ]);
            throw new UnexpectedErrorException('Falha ao criar pedido');
        }
    }
    
    private function validateDTO(CreateOrderDTO $dto): void {
        if (empty($dto->productId)) {
            throw new InvalidOrderDataException('ID do produto é obrigatório');
        }
        
        if ($dto->quantity < 1) {
            throw new InvalidOrderDataException('Quantidade deve ser >= 1');
        }
        
        if (empty($dto->customerId)) {
            throw new InvalidOrderDataException('ID do cliente é obrigatório');
        }
    }
}
```

---

## 13.4 Repository Pattern

Já coberto em profundidade na seção 13.2.3

---

## 13.5 Dependency Injection Container

```php
<?php
/**
 * Dependency Injection Container - Gerencia instâncias
 */

class ServiceContainer {
    private array $services = [];
    private array $singletons = [];
    private array $aliases = [];
    
    /**
     * Registrar serviço com factory
     */
    public function register(string $id, callable $factory, bool $singleton = false): void {
        $this->services[$id] = [
            'factory' => $factory,
            'singleton' => $singleton
        ];
    }
    
    /**
     * Registrar instância singleton
     */
    public function singleton(string $id, $instance): void {
        $this->singletons[$id] = $instance;
    }
    
    /**
     * Resolver serviço
     */
    public function get(string $id) {
        // Se já resolvido como singleton, retornar
        if (isset($this->singletons[$id])) {
            return $this->singletons[$id];
        }
        
        // Se é alias, resolver o alias
        if (isset($this->aliases[$id])) {
            return $this->get($this->aliases[$id]);
        }
        
        // Se não registrado, lançar erro
        if (!isset($this->services[$id])) {
            throw new ServiceNotFoundException("Serviço '$id' não foi registrado");
        }
        
        // Resolver via factory
        $definition = $this->services[$id];
        $instance = $definition['factory']($this);
        
        // Se é singleton, cache
        if ($definition['singleton']) {
            $this->singletons[$id] = $instance;
        }
        
        return $instance;
    }
    
    /**
     * Criar alias para serviço
     */
    public function alias(string $alias, string $id): void {
        $this->aliases[$alias] = $id;
    }
    
    /**
     * Verificar se serviço está registrado
     */
    public function has(string $id): bool {
        return isset($this->services[$id]) || isset($this->aliases[$id]) || isset($this->singletons[$id]);
    }
}

// Configurar container
$container = new ServiceContainer();

// Registrar repositórios como singletons
$container->register('productRepository', function() {
    return new ProductRepositoryWordPress();
}, singleton: true);

$container->register('orderRepository', function() {
    return new OrderRepositoryWordPress();
}, singleton: true);

// Registrar domain services como singletons
$container->register('pricingDomainService', function() {
    return new PricingDomainService();
}, singleton: true);

// Registrar serviços de aplicação
$container->register('createOrderService', function($c) {
    return new CreateOrderApplicationService(
        $c->get('orderRepository'),
        $c->get('productRepository'),
        $c->get('pricingDomainService'),
        $c->get('eventPublisher'),
        $c->get('logger')
    );
});

// Registrar services de infraestrutura
$container->register('logger', function() {
    return new WPLogger();
}, singleton: true);

$container->register('emailService', function() {
    return new GmailEmailService();
}, singleton: true);

$container->register('eventPublisher', function() {
    return new WordPressEventPublisher();
}, singleton: true);

// Criar aliases
$container->alias(CreateOrderService::class, 'createOrderService');

// Usar container
$createOrderService = $container->get(CreateOrderService::class);
$orderDTO = $createOrderService->execute($dto);
```

---

## 13.6 Event-Driven Architecture

Já coberto em profundidade na seção 13.2.5

---

## 13.7 MVC em WordPress

```php
<?php
/**
 * MVC Pattern em WordPress
 */

// ======== MODEL ========

class PostModel {
    private PostRepository $repository;
    
    public function __construct(PostRepository $repository) {
        $this->repository = $repository;
    }
    
    public function getPost(int $id): ?Post {
        return $this->repository->findById($id);
    }
    
    public function getPosts(array $filters = []): array {
        return $this->repository->search($filters);
    }
    
    public function savePost(Post $post): void {
        $this->repository->save($post);
    }
}

// ======== VIEW ========

class PostView {
    public function render(Post $post): string {
        ob_start();
        ?>
        <article class="post">
            <h1><?php echo esc_html($post->getTitle()); ?></h1>
            <div class="content">
                <?php echo wp_kses_post($post->getContent()); ?>
            </div>
            <footer class="meta">
                <span class="date">
                    <?php echo esc_html($post->getPublishedAt()->format('d/m/Y')); ?>
                </span>
                <span class="author">
                    <?php echo esc_html($post->getAuthor()); ?>
                </span>
            </footer>
        </article>
        <?php
        return ob_get_clean();
    }
    
    public function renderList(array $posts): string {
        ob_start();
        ?>
        <ul class="posts">
            <?php foreach ($posts as $post): ?>
                <li>
                    <a href="<?php echo esc_url($post->getPermalink()); ?>">
                        <?php echo esc_html($post->getTitle()); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php
        return ob_get_clean();
    }
}

// ======== CONTROLLER ========

class PostController {
    private PostModel $model;
    private PostView $view;
    
    public function __construct(PostModel $model, PostView $view) {
        $this->model = $model;
        $this->view = $view;
    }
    
    public function showPost(int $id): string {
        $post = $this->model->getPost($id);
        
        if (!$post) {
            wp_die('Post não encontrado', 404);
        }
        
        return $this->view->render($post);
    }
    
    public function showList(array $filters = []): string {
        $posts = $this->model->getPosts($filters);
        return $this->view->renderList($posts);
    }
}

// ======== INTEGRAÇÃO COM WORDPRESS ========

add_filter('the_content', function($content) {
    if (is_single()) {
        $repository = new PostRepositoryWordPress();
        $model = new PostModel($repository);
        $view = new PostView();
        $controller = new PostController($model, $view);
        
        return $controller->showPost(get_the_ID());
    }
    
    return $content;
});

add_shortcode('posts_list', function($atts) {
    $repository = new PostRepositoryWordPress();
    $model = new PostModel($repository);
    $view = new PostView();
    $controller = new PostController($model, $view);
    
    return $controller->showList(['limit' => 10]);
});
```

---

## 13.8 Adapter Pattern para APIs Externas

```php
<?php
/**
 * Adapter Pattern - Abstrair integrações com serviços externos
 */

// Interface que define o contrato
interface PaymentGatewayAdapter {
    public function charge(float $amount, string $customerId): PaymentResult;
    public function refund(string $transactionId, float $amount): PaymentResult;
}

// Adaptador para Stripe
class StripeAdapter implements PaymentGatewayAdapter {
    private $stripe;
    
    public function __construct() {
        require_once 'stripe-php/init.php';
        $this->stripe = new \Stripe\StripeClient(get_option('stripe_secret_key'));
    }
    
    public function charge(float $amount, string $customerId): PaymentResult {
        try {
            $result = $this->stripe->charges->create([
                'amount' => (int)($amount * 100), // Stripe usa centavos
                'currency' => 'brl',
                'customer' => $customerId,
                'description' => 'Pagamento via WordPress'
            ]);
            
            return new PaymentResult(
                $result->id,
                PaymentStatus::SUCCESS,
                $amount
            );
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new PaymentException('Falha ao processar pagamento: ' . $e->getMessage());
        }
    }
    
    public function refund(string $transactionId, float $amount): PaymentResult {
        try {
            $result = $this->stripe->refunds->create([
                'charge' => $transactionId,
                'amount' => (int)($amount * 100)
            ]);
            
            return new PaymentResult(
                $result->id,
                PaymentStatus::REFUNDED,
                $amount
            );
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new PaymentException('Falha ao reembolsar: ' . $e->getMessage());
        }
    }
}

// Adaptador para PagarMé
class PagarMeAdapter implements PaymentGatewayAdapter {
    private string $apiKey;
    
    public function __construct() {
        $this->apiKey = get_option('pagarme_api_key');
    }
    
    public function charge(float $amount, string $customerId): PaymentResult {
        $response = wp_remote_post('https://api.pagar.me/core/v5/charges', [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($this->apiKey . ':'),
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode([
                'amount' => (int)($amount * 100),
                'currency' => 'BRL',
                'customer_id' => $customerId,
                'description' => 'Pagamento via WordPress'
            ])
        ]);
        
        if (is_wp_error($response)) {
            throw new PaymentException($response->get_error_message());
        }
        
        $data = json_decode(wp_remote_retrieve_body($response), true);
        
        if ($data['status'] !== 'paid') {
            throw new PaymentException('Falha ao processar pagamento');
        }
        
        return new PaymentResult(
            $data['id'],
            PaymentStatus::SUCCESS,
            $amount
        );
    }
    
    public function refund(string $transactionId, float $amount): PaymentResult {
        $response = wp_remote_post(
            "https://api.pagar.me/core/v5/charges/{$transactionId}/refunds",
            [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($this->apiKey . ':'),
                    'Content-Type' => 'application/json'
                ],
                'body' => json_encode([
                    'amount' => (int)($amount * 100)
                ])
            ]
        );
        
        if (is_wp_error($response)) {
            throw new PaymentException($response->get_error_message());
        }
        
        $data = json_decode(wp_remote_retrieve_body($response), true);
        
        return new PaymentResult(
            $data['id'],
            PaymentStatus::REFUNDED,
            $amount
        );
    }
}

// Usar adaptador sem conhecer implementação específica
class OrderPaymentService {
    private PaymentGatewayAdapter $adapter;
    
    public function __construct(PaymentGatewayAdapter $adapter) {
        $this->adapter = $adapter;
    }
    
    public function processOrderPayment(Order $order): void {
        $result = $this->adapter->charge(
            $order->getTotal(),
            $order->getCustomerId()
        );
        
        $order->markAsPaid($result->getTransactionId());
    }
}

// Escolher implementação em tempo de inicialização
$provider = get_option('payment_provider'); // 'stripe' ou 'pagarme'

$adapter = match($provider) {
    'stripe' => new StripeAdapter(),
    'pagarme' => new PagarMeAdapter(),
    default => throw new Exception('Provedor de pagamento inválido')
};

$service = new OrderPaymentService($adapter);
```

---

## 13.9 Strategy Pattern

```php
<?php
/**
 * Strategy Pattern - Diferentes estratégias em runtime
 */

interface DiscountStrategy {
    public function calculate(Order $order): float;
}

class NoDiscountStrategy implements DiscountStrategy {
    public function calculate(Order $order): float {
        return 0;
    }
}

class PercentageDiscountStrategy implements DiscountStrategy {
    private float $percentage;
    
    public function __construct(float $percentage) {
        $this->percentage = $percentage;
    }
    
    public function calculate(Order $order): float {
        return $order->getTotal() * ($this->percentage / 100);
    }
}

class BulkDiscountStrategy implements DiscountStrategy {
    public function calculate(Order $order): float {
        $total = $order->getTotal();
        
        return match(true) {
            $total >= 1000 => $total * 0.10, // 10%
            $total >= 500 => $total * 0.05,  // 5%
            $total >= 100 => $total * 0.02,  // 2%
            default => 0
        };
    }
}

class CustomerLoyaltyDiscountStrategy implements DiscountStrategy {
    private CustomerRepository $customerRepository;
    
    public function __construct(CustomerRepository $customerRepository) {
        $this->customerRepository = $customerRepository;
    }
    
    public function calculate(Order $order): float {
        $customer = $this->customerRepository->findById(
            $order->getCustomerId()
        );
        
        if (!$customer) {
            return 0;
        }
        
        // Cliente VIP: 15% de desconto
        if ($customer->isVIP()) {
            return $order->getTotal() * 0.15;
        }
        
        // Cliente regular com muitas compras: 5% de desconto
        if ($customer->getOrderCount() > 10) {
            return $order->getTotal() * 0.05;
        }
        
        return 0;
    }
}

class ChainedDiscountStrategy implements DiscountStrategy {
    private array $strategies = [];
    
    public function addStrategy(DiscountStrategy $strategy): void {
        $this->strategies[] = $strategy;
    }
    
    public function calculate(Order $order): float {
        $totalDiscount = 0;
        
        foreach ($this->strategies as $strategy) {
            $totalDiscount += $strategy->calculate($order);
        }
        
        return $totalDiscount;
    }
}

// Usar strategy
class OrderPricingService {
    private DiscountStrategy $discountStrategy;
    
    public function __construct(DiscountStrategy $discountStrategy) {
        $this->discountStrategy = $discountStrategy;
    }
    
    public function calculateFinalPrice(Order $order): float {
        $discount = $this->discountStrategy->calculate($order);
        return max(0, $order->getTotal() - $discount);
    }
}

// Configurar em tempo de execução
$order = new Order(['total' => 250]);

// Usar desconto em percentual
$service = new OrderPricingService(
    new PercentageDiscountStrategy(10)
);
$finalPrice = $service->calculateFinalPrice($order); // 225

// Trocar para desconto por volume
$service = new OrderPricingService(
    new BulkDiscountStrategy()
);
$finalPrice = $service->calculateFinalPrice($order); // 245 (2% de desconto)

// Combinar múltiplas estratégias
$chained = new ChainedDiscountStrategy();
$chained->addStrategy(new PercentageDiscountStrategy(5));
$chained->addStrategy(new BulkDiscountStrategy());
$chained->addStrategy(new CustomerLoyaltyDiscountStrategy($customerRepo));

$service = new OrderPricingService($chained);
$finalPrice = $service->calculateFinalPrice($order);
```

---

## 13.10 Factory Pattern

```php
<?php
/**
 * Factory Pattern - Criar objetos complexos de forma centralizada
 */

interface PaymentGatewayFactory {
    public function create(): PaymentGateway;
}

class PaymentGatewayFactoryRegistry {
    private array $factories = [];
    
    public function register(string $type, PaymentGatewayFactory $factory): void {
        $this->factories[$type] = $factory;
    }
    
    public function create(string $type): PaymentGateway {
        if (!isset($this->factories[$type])) {
            throw new UnknownPaymentGatewayException("Gateway '$type' não registrado");
        }
        
        return $this->factories[$type]->create();
    }
}

// Factories específicas
class StripePaymentGatewayFactory implements PaymentGatewayFactory {
    public function create(): PaymentGateway {
        $apiKey = get_option('stripe_api_key');
        
        if (empty($apiKey)) {
            throw new ConfigurationException('Stripe API key não configurada');
        }
        
        return new StripePaymentGateway($apiKey);
    }
}

class PagarMePaymentGatewayFactory implements PaymentGatewayFactory {
    public function create(): PaymentGateway {
        $apiKey = get_option('pagarme_api_key');
        
        if (empty($apiKey)) {
            throw new ConfigurationException('PagarMé API key não configurada');
        }
        
        return new PagarMePaymentGateway($apiKey);
    }
}

class PixPaymentGatewayFactory implements PaymentGatewayFactory {
    public function create(): PaymentGateway {
        $accountKey = get_option('pix_account_key');
        
        if (empty($accountKey)) {
            throw new ConfigurationException('PIX account key não configurada');
        }
        
        return new PixPaymentGateway($accountKey);
    }
}

// Factory com parâmetros
class StorageBackendFactory {
    public static function create(string $type, array $config): StorageBackend {
        return match($type) {
            'local' => new LocalStorage($config['path']),
            's3' => new S3Storage(
                $config['client'],
                $config['bucket'],
                $config['region']
            ),
            'gcs' => new GoogleCloudStorage(
                $config['client'],
                $config['bucket']
            ),
            default => throw new InvalidStorageTypeException("Tipo '$type' inválido")
        };
    }
}

// Factory com builder
class OrderFactory {
    private OrderRepository $repository;
    private ProductRepository $productRepository;
    private CustomerRepository $customerRepository;
    
    public function __construct(
        OrderRepository $repository,
        ProductRepository $productRepository,
        CustomerRepository $customerRepository
    ) {
        $this->repository = $repository;
        $this->productRepository = $productRepository;
        $this->customerRepository = $customerRepository;
    }
    
    public function create(CreateOrderDTO $dto): Order {
        // Validar dados
        $this->validateDTO($dto);
        
        // Buscar entidades relacionadas
        $product = $this->productRepository->findById(
            new ProductId($dto->productId)
        );
        
        if (!$product) {
            throw new ProductNotFoundException('Produto não encontrado');
        }
        
        $customer = $this->customerRepository->findById(
            new CustomerId($dto->customerId)
        );
        
        if (!$customer) {
            throw new CustomerNotFoundException('Cliente não encontrado');
        }
        
        // Criar entidade
        return Order::create(
            new OrderId(wp_generate_uuid4()),
            $customer,
            $product,
            $dto->quantity
        );
    }
    
    private function validateDTO(CreateOrderDTO $dto): void {
        if ($dto->quantity < 1) {
            throw new InvalidOrderDataException('Quantidade deve ser >= 1');
        }
    }
}

// Usar factory
$registry = new PaymentGatewayFactoryRegistry();
$registry->register('stripe', new StripePaymentGatewayFactory());
$registry->register('pagarme', new PagarMePaymentGatewayFactory());
$registry->register('pix', new PixPaymentGatewayFactory());

$provider = get_option('payment_provider'); // stripe, pagarme ou pix
$gateway = $registry->create($provider);

// Storage factory
$storage = StorageBackendFactory::create('s3', [
    'client' => $s3Client,
    'bucket' => 'my-uploads',
    'region' => 'us-east-1'
]);

// Order factory
$orderFactory = new OrderFactory(
    new OrderRepositoryWordPress(),
    new ProductRepositoryWordPress(),
    new CustomerRepositoryWordPress()
);

$order = $orderFactory->create($dto);
```

---

## 📊 Resumo Comparativo dos Padrões

| Padrão | Propósito | Quando Usar |
|--------|-----------|------------|
| **SOLID** | Princípios fundamentais | Sempre |
| **DDD** | Design orientado ao domínio | Projetos complexos |
| **Service Layer** | Orquestração de lógica | Toda aplicação |
| **Repository** | Abstração de dados | Projetos médios+ |
| **DI Container** | Gestão de dependências | Projetos profissionais |
| **Event-Driven** | Desacoplamento via eventos | Arquitetura complexa |
| **MVC** | Separação de responsabilidades | Toda aplicação |
| **Adapter** | Integração com sistemas externos | APIs externas |
| **Strategy** | Diferentes algoritmos | Múltiplas variações |
| **Factory** | Criação de objetos | Criação complexa |

---

## 🚀 Checklist de Implementação

- [ ] SOLID Principles implementados
- [ ] DDD aplicado ao domínio do projeto
- [ ] Service Layer coordenando use cases
- [ ] Repository Pattern abstraindo dados
- [ ] DI Container gerenciando dependências
- [ ] Event-Driven Architecture para acoplamento
- [ ] MVC separando responsabilidades
- [ ] Adapters para integrações externas
- [ ] Strategy Patterns para variações
- [ ] Factory Pattern para criação complexa

---

**Data de Conclusão:** Janeiro 2026  
**Nível:** Especialista Sênior  
**Próximo Passo:** FASE 14 - Deployment e DevOps
