# 🏗️ FASE 13: Arquitetura Avançada em WordPress

**Versão:** 1.0  
**Data:** Janeiro 2026  
**Nível:** Especialista Sênior em PHP  
**Objetivo:** Dominar padrões de arquitetura enterprise e design patterns avançados em WordPress

---

**Navegação:** [Índice](000-WordPress-Indice-Topicos.md) | [← Fase 12](012-WordPress-Fase-12-Seguranca-Boas-Praticas.md) | [Fase 14 →](014-WordPress-Fase-14-Implantacao-DevOps.md)

---

## 📑 Índice

1. [13.1 - SOLID Principles em WordPress](#131-solid-principles-em-wordpress)
2. [13.2 - Domain-Driven Design (DDD)](#132-domain-driven-design-ddd)
   - [13.2.2 - DDD Ubiquitous Language](#1322-ddd-ubiquitous-language)
3. [13.3 - Service Layer Pattern](#133-service-layer-pattern)
4. [13.4 - Repository Pattern](#134-repository-pattern)
5. [13.5 - Dependency Injection Container](#135-dependency-injection-container)
   - [13.5.2 - DI Container Implementação Completa com Pimple](#1352-di-container-implementação-completa-com-pimple)
6. [13.6 - Event-Driven Architecture](#136-event-driven-architecture)
7. [13.7 - MVC em WordPress](#137-mvc-em-wordpress)
8. [13.8 - Adapter Pattern para APIs Externas](#138-adapter-pattern-para-apis-externas)
9. [13.9 - Strategy Pattern](#139-strategy-pattern)
10. [13.10 - Factory Pattern](#1310-factory-pattern)
11. [13.11 - Quando NÃO Usar SOLID (Trade-offs)](#1311-quando-não-usar-solid-trade-offs)

---

## 🎯 Objetivos de Aprendizado

Ao final desta fase, você será capaz de:

1. ✅ Aplicar princípios SOLID (SRP, OCP, LSP, ISP, DIP) no desenvolvimento WordPress
2. ✅ Implementar Domain-Driven Design (DDD) com linguagem ubíqua
3. ✅ Criar padrões de service layer para orquestrar lógica de negócio
4. ✅ Usar padrão Repository para abstração de acesso a dados
5. ✅ Implementar containers de dependency injection (Pimple, containers customizados)
6. ✅ Projetar arquiteturas event-driven usando hooks do WordPress
7. ✅ Aplicar padrões de design (Adapter, Strategy, Factory) apropriadamente
8. ✅ Reconhecer quando NÃO usar SOLID (trade-offs e super-engenharia)

## 📝 Autoavaliação

Teste seu entendimento:

- [ ] O que é o Princípio de Responsabilidade Única e como se aplica ao WordPress?
- [ ] Como o Princípio de Inversão de Dependência ajuda com testes e manutenibilidade?
- [ ] Qual é a diferença entre Repository Pattern e acesso direto ao banco de dados?
- [ ] Quando você deve usar um Dependency Injection Container vs gerenciamento manual de dependências?
- [ ] Como você implementa Domain-Driven Design no contexto WordPress?
- [ ] Qual é o trade-off entre abstração e performance?
- [ ] Quando é apropriado NÃO seguir princípios SOLID?
- [ ] Como você equilibra pureza arquitetural com convenções do core WordPress?

## 🛠️ Projeto Prático

**Construir:** Plugin de Arquitetura Enterprise

Crie um plugin que demonstre:
- Princípios SOLID aplicados corretamente
- Service layer para lógica de negócio
- Repository pattern para acesso a dados
- Container de dependency injection
- Arquitetura event-driven
- Múltiplos padrões de design (Adapter, Strategy, Factory)
- Documento de análise de trade-offs explicando decisões arquiteturais

**Tempo estimado:** 20-25 horas  
**Dificuldade:** Avançado

---

## ❌ Equívocos Comuns

### Equívoco 1: "Princípios SOLID sempre melhoram código"
**Realidade:** Princípios SOLID melhoram manutenibilidade e testabilidade, mas podem adicionar complexidade e overhead de performance. Use-os quando benefícios superam custos.

**Por que é importante:** Aplicar SOLID excessivamente em código simples cria complexidade desnecessária. Equilíbrio é fundamental.

**Como lembrar:** SOLID = ferramenta, não dogma. Use quando ajuda, pule quando prejudica.

### Equívoco 2: "Dependency Injection requer um container"
**Realidade:** Dependency Injection é passar dependências, não usar um container. Você pode injetar manualmente ou usar um container. Ambos são válidos.

**Por que é importante:** Entender DI vs DI Container ajuda a escolher a abordagem certa para o tamanho do seu projeto.

**Como lembrar:** DI = padrão. DI Container = ferramenta para DI. Você pode fazer DI sem container.

### Equívoco 3: "Repository Pattern sempre melhora performance"
**Realidade:** Repository Pattern melhora testabilidade e manutenibilidade, mas adiciona camadas de abstração que podem impactar performance. Meça antes de otimizar.

**Por que é importante:** Abstração tem custos. Use repositories quando benefícios (testes, manutenibilidade) superam custos.

**Como lembrar:** Repository = abstração = testabilidade + manutenibilidade - alguma performance.

### Equívoco 4: "Arquitetura Event-Driven é sempre melhor"
**Realidade:** Arquitetura event-driven melhora desacoplamento mas torna debugging mais difícil e pode obscurecer fluxo de controle. Use quando desacoplamento é valioso.

**Por que é importante:** Eventos tornam código mais difícil de rastrear. Use eventos para necessidades reais de desacoplamento, não em todos os lugares.

**Como lembrar:** Eventos = desacoplamento + debugging mais difícil. Use quando desacoplamento importa.

### Equívoco 5: "WordPress não suporta arquitetura moderna"
**Realidade:** WordPress suporta padrões modernos (SOLID, DDD, DI) mas requer adaptá-los a convenções do WordPress (hooks, filters, globals).

**Por que é importante:** Você pode usar arquitetura moderna no WordPress, mas deve trabalhar com WordPress, não contra ele.

**Como lembrar:** Arquitetura moderna + convenções WordPress = combinação poderosa.

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

### 13.2.2 DDD Ubiquitous Language

**Conceito:** Ubiquitous Language (Linguagem Ubíqua) é a linguagem compartilhada entre desenvolvedores e especialistas do domínio (domain experts) para descrever o domínio do negócio.

**Por Que É Importante:**

1. **Comunicação Clara:** Todos falam a mesma linguagem
2. **Código Expressivo:** Código reflete o domínio do negócio
3. **Menos Traduções:** Não precisa traduzir entre "linguagem de negócio" e "linguagem técnica"
4. **Modelo Rico:** O modelo de domínio fica mais rico e expressivo

**Processo de Identificação:**

#### Passo 1: Entender o Domínio

```php
<?php
/**
 * Exemplo: E-commerce de Produtos
 * 
 * Entrevista com Domain Expert:
 * 
 * Domain Expert: "Quando um cliente adiciona um produto ao carrinho,
 *                 verificamos se há estoque disponível. Se sim, reservamos
 *                 o estoque por 15 minutos. Se o cliente não finalizar
 *                 a compra nesse tempo, liberamos o estoque."
 * 
 * Termos identificados:
 * - Cliente (Customer)
 * - Produto (Product)
 * - Carrinho (Cart)
 * - Estoque (Inventory)
 * - Reserva de Estoque (Stock Reservation)
 * - Finalizar Compra (Checkout)
 * - Liberar Estoque (Release Stock)
 */
```

#### Passo 2: Criar Entidades e Value Objects com Nomes do Domínio

```php
<?php
/**
 * ✅ CORRETO: Usar termos do domínio
 */

// Termo do domínio: "Reserva de Estoque"
class StockReservation {
    private ProductId $productId;
    private int $quantity;
    private DateTime $reservedAt;
    private DateTime $expiresAt;
    
    public function __construct(ProductId $productId, int $quantity) {
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->reservedAt = new DateTime();
        $this->expiresAt = (new DateTime())->modify('+15 minutes');
    }
    
    public function isExpired(): bool {
        return new DateTime() > $this->expiresAt;
    }
    
    public function release(): void {
        // "Liberar estoque" - termo do domínio
    }
}

// Termo do domínio: "Finalizar Compra"
class Checkout {
    private Cart $cart;
    private Customer $customer;
    private PaymentMethod $paymentMethod;
    
    public function finalize(): Order {
        // "Finalizar compra" - termo do domínio
        // Verificar reservas de estoque
        // Processar pagamento
        // Criar pedido
    }
}
```

#### Passo 3: Evitar Traduções Técnicas

```php
<?php
/**
 * ❌ ERRADO: Termos técnicos ao invés de termos do domínio
 */
class DatabaseRecord {
    public function insert() { } // ❌ "insert" é termo técnico
    public function update() { } // ❌ "update" é termo técnico
}

/**
 * ✅ CORRETO: Termos do domínio
 */
class Product {
    public function register(): void { } // ✅ "register" é termo do domínio
    public function updatePrice(Price $newPrice): void { } // ✅ "updatePrice" é termo do domínio
}
```

#### Passo 4: Documentar Glossário do Domínio

```php
<?php
/**
 * GLOSSÁRIO DO DOMÍNIO - E-commerce
 * 
 * Termos e Significados:
 * 
 * **Cliente (Customer)**
 * - Pessoa que compra produtos
 * - Pode ter múltiplos endereços de entrega
 * - Tem histórico de pedidos
 * 
 * **Produto (Product)**
 * - Item vendido na loja
 * - Tem preço, estoque, descrição
 * - Pode estar ativo ou inativo
 * 
 * **Carrinho (Cart)**
 * - Coleção temporária de produtos que cliente quer comprar
 * - Expira após 30 dias de inatividade
 * - Pode ser salvo para depois
 * 
 * **Reserva de Estoque (Stock Reservation)**
 * - Quando produto é adicionado ao carrinho, estoque é reservado
 * - Reserva expira após 15 minutos se compra não for finalizada
 * - Impede que outro cliente compre último item disponível
 * 
 * **Finalizar Compra (Checkout)**
 * - Processo de converter carrinho em pedido
 * - Inclui: validar estoque, processar pagamento, criar pedido
 * - Pode falhar se estoque foi esgotado durante processo
 * 
 * **Pedido (Order)**
 * - Compra finalizada pelo cliente
 * - Tem status: pendente, pago, enviado, entregue, cancelado
 * - Não pode ser modificado após criação
 */
```

#### Passo 5: Usar Ubiquitous Language em Código

```php
<?php
/**
 * Exemplo completo usando Ubiquitous Language
 */

// Domain Expert disse: "Quando cliente adiciona produto ao carrinho,
// verificamos estoque e reservamos por 15 minutos"

class CartService {
    private ProductRepository $productRepository;
    private StockReservationRepository $reservationRepository;
    
    /**
     * "Adicionar produto ao carrinho" - termo do domínio
     */
    public function addProductToCart(CartId $cartId, ProductId $productId, int $quantity): void {
        // "Verificar estoque disponível" - termo do domínio
        $product = $this->productRepository->findById($productId);
        $availableStock = $product->getAvailableStock();
        
        if (!$availableStock->hasEnough($quantity)) {
            throw new InsufficientStockException('Estoque insuficiente');
        }
        
        // "Reservar estoque" - termo do domínio
        $reservation = new StockReservation($productId, $quantity);
        $this->reservationRepository->save($reservation);
        
        // Adicionar ao carrinho
        $cart = $this->cartRepository->findById($cartId);
        $cart->addItem($productId, $quantity);
        $this->cartRepository->save($cart);
    }
    
    /**
     * "Finalizar compra" - termo do domínio
     */
    public function checkout(CartId $cartId, PaymentMethod $paymentMethod): Order {
        $cart = $this->cartRepository->findById($cartId);
        
        // Verificar se reservas ainda são válidas
        foreach ($cart->getItems() as $item) {
            $reservation = $this->reservationRepository->findByProductAndCart(
                $item->getProductId(),
                $cartId
            );
            
            if ($reservation->isExpired()) {
                throw new ReservationExpiredException('Reserva de estoque expirou');
            }
        }
        
        // Processar pagamento
        $payment = $this->paymentService->process($cart->getTotal(), $paymentMethod);
        
        // Criar pedido
        $order = Order::createFromCart($cart, $payment);
        $this->orderRepository->save($order);
        
        // "Liberar reservas" - termo do domínio (ou converter em alocação permanente)
        foreach ($cart->getItems() as $item) {
            $reservation = $this->reservationRepository->findByProductAndCart(
                $item->getProductId(),
                $cartId
            );
            $reservation->convertToAllocation($order->getId());
        }
        
        // Limpar carrinho
        $this->cartRepository->delete($cartId);
        
        return $order;
    }
}
```

**Checklist para Ubiquitous Language:**

- [ ] Termos do código correspondem aos termos usados por domain experts?
- [ ] Evitamos termos técnicos genéricos (insert, update, delete)?
- [ ] Glossário do domínio está documentado?
- [ ] Código é legível por não-desenvolvedores familiarizados com o domínio?
- [ ] Mudanças no domínio são refletidas no código?

**Exemplo Prático: Identificando Ubiquitous Language**

```php
<?php
/**
 * Cenário: Sistema de Biblioteca
 * 
 * Entrevista com bibliotecário (Domain Expert):
 * 
 * "Quando um membro quer pegar um livro emprestado, verificamos se o livro
 *  está disponível. Se estiver, criamos um empréstimo (loan) por 14 dias.
 *  Se o membro não devolver no prazo, aplicamos uma multa (fine) de R$ 2,00
 *  por dia de atraso. Quando o livro é devolvido, verificamos se está em
 *  bom estado. Se estiver danificado, cobramos uma taxa de reparo."
 * 
 * Termos identificados:
 * - Membro (Member) - pessoa que pode pegar livros emprestados
 * - Livro (Book) - item que pode ser emprestado
 * - Empréstimo (Loan) - quando membro pega livro emprestado
 * - Devolução (Return) - quando membro devolve livro
 * - Multa (Fine) - penalidade por atraso
 * - Taxa de Reparo (Repair Fee) - cobrança por dano
 * - Disponibilidade (Availability) - se livro está disponível para empréstimo
 * 
 * Implementação:
 */

class LibraryDomain {
    // ✅ Usar termos do domínio
    public function borrowBook(MemberId $memberId, BookId $bookId): Loan {
        // "Pegar livro emprestado" - termo do domínio
    }
    
    public function returnBook(LoanId $loanId): Return {
        // "Devolver livro" - termo do domínio
    }
    
    public function calculateFine(Loan $loan): Fine {
        // "Calcular multa" - termo do domínio
    }
    
    public function assessDamage(Book $book): DamageAssessment {
        // "Avaliar dano" - termo do domínio
    }
}
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

### 13.5.2 DI Container Implementação Completa com Pimple

**Instalação:**

```bash
composer require pimple/pimple
```

**Implementação Completa:**

```php
<?php
use Pimple\Container;

/**
 * Configuração completa do DI Container usando Pimple
 */
class ServiceContainer {
    
    private Container $container;
    
    public function __construct() {
        $this->container = new Container();
        $this->registerServices();
    }
    
    private function registerServices() {
        // ========== REPOSITORIES (Singletons) ==========
        
        $this->container['product.repository'] = function($c) {
            return new ProductRepositoryWordPress();
        };
        
        $this->container['order.repository'] = function($c) {
            return new OrderRepositoryWordPress();
        };
        
        $this->container['customer.repository'] = function($c) {
            return new CustomerRepositoryWordPress();
        };
        
        // ========== DOMAIN SERVICES (Singletons) ==========
        
        $this->container['pricing.service'] = function($c) {
            return new PricingDomainService();
        };
        
        $this->container['inventory.service'] = function($c) {
            return new InventoryDomainService(
                $c['product.repository']
            );
        };
        
        // ========== APPLICATION SERVICES ==========
        
        $this->container['create.product.service'] = function($c) {
            return new CreateProductService(
                $c['product.repository'],
                $c['event.publisher'],
                $c['logger']
            );
        };
        
        $this->container['create.order.service'] = function($c) {
            return new CreateOrderService(
                $c['order.repository'],
                $c['product.repository'],
                $c['customer.repository'],
                $c['pricing.service'],
                $c['inventory.service'],
                $c['event.publisher'],
                $c['logger']
            );
        };
        
        // ========== INFRASTRUCTURE SERVICES ==========
        
        $this->container['logger'] = function($c) {
            return new WPLogger();
        };
        
        $this->container['email.service'] = function($c) {
            return new EmailService(
                get_option('smtp_host'),
                get_option('smtp_port'),
                get_option('smtp_user'),
                get_option('smtp_pass')
            );
        };
        
        $this->container['event.publisher'] = function($c) {
            return new WordPressEventPublisher();
        };
        
        $this->container['cache.service'] = function($c) {
            return new CacheService();
        };
        
        // ========== FACTORIES ==========
        
        $this->container['payment.gateway.factory'] = function($c) {
            return new PaymentGatewayFactory([
                'stripe' => function() {
                    return new StripeGateway(get_option('stripe_api_key'));
                },
                'paypal' => function() {
                    return new PayPalGateway(
                        get_option('paypal_client_id'),
                        get_option('paypal_secret')
                    );
                },
            ]);
        };
        
        // ========== ALIASES ==========
        
        // Permitir acesso por nome de classe também
        $this->container[CreateProductService::class] = $this->container->factory(function($c) {
            return $c['create.product.service'];
        });
        
        $this->container[CreateOrderService::class] = $this->container->factory(function($c) {
            return $c['create.order.service'];
        });
        
        $this->container[ProductRepository::class] = $this->container->factory(function($c) {
            return $c['product.repository'];
        });
    }
    
    /**
     * Obter serviço do container
     */
    public function get(string $id) {
        if (!isset($this->container[$id])) {
            throw new ServiceNotFoundException("Service '$id' not found");
        }
        
        return $this->container[$id];
    }
    
    /**
     * Verificar se serviço existe
     */
    public function has(string $id): bool {
        return isset($this->container[$id]);
    }
    
    /**
     * Obter container Pimple diretamente (para casos avançados)
     */
    public function getContainer(): Container {
        return $this->container;
    }
}

// ========== USO DO CONTAINER ==========

// Criar instância global do container
$GLOBALS['service_container'] = new ServiceContainer();

// Helper function para acesso fácil
function container(): ServiceContainer {
    return $GLOBALS['service_container'];
}

// Usar em código
$createOrderService = container()->get('create.order.service');
// ou
$createOrderService = container()->get(CreateOrderService::class);

$order = $createOrderService->execute($orderDTO);
```

**Auto-wiring Básico:**

```php
<?php
/**
 * Auto-wiring simples usando Reflection
 */
class AutoWiringContainer extends Container {
    
    public function autoWire(string $className) {
        if (isset($this[$className])) {
            return $this[$className];
        }
        
        $reflection = new ReflectionClass($className);
        $constructor = $reflection->getConstructor();
        
        if (!$constructor) {
            // Sem construtor, criar instância simples
            return $this[$className] = new $className();
        }
        
        $parameters = $constructor->getParameters();
        $dependencies = [];
        
        foreach ($parameters as $parameter) {
            $type = $parameter->getType();
            
            if (!$type || $type->isBuiltin()) {
                // Tipo primitivo ou sem tipo, não pode auto-wire
                throw new AutoWiringException(
                    "Cannot auto-wire parameter '{$parameter->getName()}' in {$className}"
                );
            }
            
            $dependencyClass = $type->getName();
            $dependencies[] = $this->autoWire($dependencyClass);
        }
        
        return $this[$className] = new $className(...$dependencies);
    }
}

// Uso
$container = new AutoWiringContainer();

// Auto-wire automaticamente resolve dependências
$service = $container->autoWire(CreateOrderService::class);
// Cria CreateOrderService, que precisa de OrderRepository,
// que é criado automaticamente, etc.
```

**Service Provider Pattern:**

```php
<?php
/**
 * Service Provider para organizar registros
 */
interface ServiceProvider {
    public function register(Container $container): void;
}

class RepositoryServiceProvider implements ServiceProvider {
    public function register(Container $container): void {
        $container['product.repository'] = function($c) {
            return new ProductRepositoryWordPress();
        };
        
        $container['order.repository'] = function($c) {
            return new OrderRepositoryWordPress();
        };
        
        $container['customer.repository'] = function($c) {
            return new CustomerRepositoryWordPress();
        };
    }
}

class ApplicationServiceProvider implements ServiceProvider {
    public function register(Container $container): void {
        $container['create.product.service'] = function($c) {
            return new CreateProductService(
                $c['product.repository'],
                $c['event.publisher'],
                $c['logger']
            );
        };
        
        $container['create.order.service'] = function($c) {
            return new CreateOrderService(
                $c['order.repository'],
                $c['product.repository'],
                $c['pricing.service'],
                $c['event.publisher']
            );
        };
    }
}

class InfrastructureServiceProvider implements ServiceProvider {
    public function register(Container $container): void {
        $container['logger'] = function($c) {
            return new WPLogger();
        };
        
        $container['event.publisher'] = function($c) {
            return new WordPressEventPublisher();
        };
        
        $container['email.service'] = function($c) {
            return new EmailService();
        };
    }
}

// Registrar providers
$container = new Container();

$providers = [
    new InfrastructureServiceProvider(),
    new RepositoryServiceProvider(),
    new ApplicationServiceProvider(),
];

foreach ($providers as $provider) {
    $provider->register($container);
}
```

**Lazy Loading:**

```php
<?php
/**
 * Lazy loading com Pimple
 */
$container['heavy.service'] = $container->factory(function($c) {
    // Esta função só é executada quando serviço é acessado
    return new HeavyService(
        $c['dependency1'],
        $c['dependency2']
    );
});

// Serviço não é criado até ser acessado
// $container['heavy.service'] ainda não executou a factory

// Agora sim, cria a instância
$service = $container['heavy.service'];
```

**Service Container Customizado para WordPress:**

```php
<?php
/**
 * Container customizado integrado com WordPress
 */
class WordPressServiceContainer extends Container {
    
    public function __construct() {
        parent::__construct();
        $this->registerWordPressServices();
    }
    
    private function registerWordPressServices() {
        // Registrar serviços WordPress como factories
        $this['wpdb'] = function($c) {
            global $wpdb;
            return $wpdb;
        };
        
        $this['wp_query'] = function($c) {
            global $wp_query;
            return $wp_query;
        };
        
        // Registrar hooks do WordPress
        $this['hook.manager'] = function($c) {
            return new WordPressHookManager();
        };
        
        // Registrar cache
        $this['cache'] = function($c) {
            return new WordPressCacheService();
        };
    }
    
    /**
     * Registrar serviço como singleton
     */
    public function singleton(string $id, callable $factory): void {
        $this[$id] = function($c) use ($factory) {
            static $instance = null;
            if ($instance === null) {
                $instance = $factory($c);
            }
            return $instance;
        };
    }
    
    /**
     * Registrar serviço como factory (nova instância sempre)
     */
    public function factory(string $id, callable $factory): void {
        $this[$id] = $this->factory($factory);
    }
}

// Uso
$container = new WordPressServiceContainer();

$container->singleton('product.repository', function($c) {
    return new ProductRepositoryWordPress($c['wpdb']);
});

$container->factory('order.service', function($c) {
    return new OrderService(
        $c['order.repository'],
        $c['product.repository']
    );
});
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

## 13.11 Quando NÃO Usar SOLID (Trade-offs)

**Importante:** SOLID não é uma religião. Há situações onde aplicar SOLID rigorosamente pode ser over-engineering.

### 13.11.1 Trade-offs de Performance

**Problema:** Abstrações e injeção de dependências têm custo de performance.

```php
<?php
/**
 * ❌ OVER-ENGINEERING: Muitas abstrações para código simples
 */
interface CalculatorInterface {
    public function add(int $a, int $b): int;
}

class Calculator implements CalculatorInterface {
    public function add(int $a, int $b): int {
        return $a + $b;
    }
}

class MathService {
    private CalculatorInterface $calculator;
    
    public function __construct(CalculatorInterface $calculator) {
        $this->calculator = $calculator;
    }
    
    public function sum(array $numbers): int {
        $result = 0;
        foreach ($numbers as $num) {
            $result = $this->calculator->add($result, $num);
        }
        return $result;
    }
}

// Uso: Muitas camadas para operação simples
$calculator = new Calculator();
$service = new MathService($calculator);
$result = $service->sum([1, 2, 3]);

/**
 * ✅ SIMPLES E EFICIENTE: Para código simples, não precisa de abstrações
 */
function sum(array $numbers): int {
    return array_sum($numbers);
}

// Uso direto
$result = sum([1, 2, 3]);
```

**Quando Não Usar:**

- **Operações matemáticas simples** - Não precisa de abstração
- **Funções utilitárias** - Diretas são mais eficientes
- **Código que roda milhões de vezes** - Performance > Abstração
- **Scripts temporários** - Não vale o esforço

### 13.11.2 Projetos Pequenos

**Problema:** SOLID adiciona complexidade que pode não ser necessária.

```php
<?php
/**
 * ❌ OVER-ENGINEERING: Para plugin simples de 200 linhas
 */
interface PostDisplayInterface {
    public function render(Post $post): string;
}

class PostDisplay implements PostDisplayInterface {
    private PostFormatterInterface $formatter;
    private PostValidatorInterface $validator;
    
    public function __construct(
        PostFormatterInterface $formatter,
        PostValidatorInterface $validator
    ) {
        $this->formatter = $formatter;
        $this->validator = $validator;
    }
    
    public function render(Post $post): string {
        if (!$this->validator->validate($post)) {
            return '';
        }
        return $this->formatter->format($post);
    }
}

/**
 * ✅ SIMPLES: Para projeto pequeno, código direto é melhor
 */
function display_post($post_id) {
    $post = get_post($post_id);
    if (!$post) {
        return '';
    }
    
    echo '<h2>' . esc_html($post->post_title) . '</h2>';
    echo '<div>' . wp_kses_post($post->post_content) . '</div>';
}
```

**Quando Não Usar:**

- **Plugins simples** (< 500 linhas)
- **Temas simples** - Não precisa de arquitetura complexa
- **Scripts de migração** - Uma vez só, não precisa ser extensível
- **Protótipos** - Foque em funcionalidade primeiro

### 13.11.3 Quando Complexidade Não Compensa

**Regra de Ouro:** Se adicionar SOLID torna código mais difícil de entender sem benefício claro, não use.

```php
<?php
/**
 * ❌ COMPLEXIDADE DESNECESSÁRIA
 * 
 * Para um helper simples, criar interface + implementação + DI
 * é mais complexo que o problema que resolve
 */
interface ConfigHelperInterface {
    public function get(string $key, $default = null);
}

class ConfigHelper implements ConfigHelperInterface {
    public function get(string $key, $default = null) {
        return get_option($key, $default);
    }
}

class MyService {
    private ConfigHelperInterface $config;
    
    public function __construct(ConfigHelperInterface $config) {
        $this->config = $config;
    }
    
    public function doSomething() {
        $value = $this->config->get('my_option');
        // ...
    }
}

/**
 * ✅ SIMPLES E CLARO
 * 
 * Direto ao ponto, fácil de entender
 */
class MyService {
    public function doSomething() {
        $value = get_option('my_option');
        // ...
    }
}
```

### 13.11.4 WordPress Core vs Custom Code

**Problema:** WordPress core não segue SOLID rigorosamente, e isso é OK.

```php
<?php
/**
 * WordPress core usa funções globais - não é SOLID, mas funciona
 */
$post = get_post(123);
$title = get_the_title($post);
$content = get_the_content($post);

/**
 * Tentar "corrigir" WordPress core com SOLID pode ser contraproducente
 * 
 * ❌ EVITAR: Criar wrappers complexos para funções WordPress simples
 */
class WordPressPostAdapter {
    private int $postId;
    private ?WP_Post $post = null;
    
    public function __construct(int $postId) {
        $this->postId = $postId;
    }
    
    private function getPost(): WP_Post {
        if ($this->post === null) {
            $this->post = get_post($this->postId);
        }
        return $this->post;
    }
    
    public function getTitle(): string {
        return get_the_title($this->getPost());
    }
    
    public function getContent(): string {
        return get_the_content(null, false, $this->getPost());
    }
}

// Uso: Mais complexo que necessário
$adapter = new WordPressPostAdapter(123);
$title = $adapter->getTitle();

/**
 * ✅ ACEITÁVEL: Usar funções WordPress diretamente quando apropriado
 */
$post = get_post(123);
$title = get_the_title($post);
```

**Quando Não Usar:**

- **Integração com WordPress core** - Use funções WordPress diretamente
- **Hooks do WordPress** - Não precisa abstrair `add_action`, `add_filter`
- **Queries simples** - `get_posts()`, `WP_Query` direto é OK

### 13.11.5 Prototipagem e MVP

**Problema:** SOLID pode atrasar entrega de MVP.

```php
<?php
/**
 * ❌ MVP: Não precisa de arquitetura completa desde o início
 * 
 * Foque em funcionalidade primeiro, refatore depois
 */
class MVPFeature {
    public function process($data) {
        // Código direto, sem abstrações
        $validated = $this->validate($data);
        $processed = $this->processData($validated);
        $this->save($processed);
        $this->sendEmail($processed);
        
        return $processed;
    }
    
    private function validate($data) {
        // Validação simples inline
        if (empty($data['email'])) {
            throw new Exception('Email required');
        }
        return $data;
    }
    
    private function processData($data) {
        // Processamento direto
        return [
            'email' => sanitize_email($data['email']),
            'name' => sanitize_text_field($data['name']),
        ];
    }
    
    private function save($data) {
        // Salvar direto
        update_option('mvp_data', $data);
    }
    
    private function sendEmail($data) {
        // Enviar email direto
        wp_mail($data['email'], 'Welcome', 'Thanks!');
    }
}

/**
 * ✅ DEPOIS: Refatorar para SOLID quando necessário
 * 
 * Quando código cresce e precisa de testes, extensibilidade, etc.
 */
```

**Estratégia:**

1. **MVP:** Código direto, funcional
2. **Crescimento:** Refatorar gradualmente para SOLID
3. **Produção:** SOLID onde faz sentido

### 13.11.6 Decisão: Quando Usar SOLID?

**Use SOLID quando:**

- ✅ Projeto tem mais de 1000 linhas
- ✅ Código precisa ser testável
- ✅ Múltiplos desenvolvedores trabalham no código
- ✅ Código precisa ser extensível
- ✅ Performance não é crítica (não roda milhões de vezes)
- ✅ Projeto vai durar anos

**NÃO use SOLID quando:**

- ❌ Projeto muito pequeno (< 500 linhas)
- ❌ Script temporário ou de migração
- ❌ Prototipagem rápida
- ❌ Performance crítica (hot path)
- ❌ Código simples que não vai mudar
- ❌ Wrapper desnecessário sobre funções WordPress simples

**Regra Prática:**

```
Complexidade do Problema vs Complexidade da Solução

Se Complexidade da Solução > Complexidade do Problema:
    → Over-engineering, simplifique

Se Complexidade da Solução ≈ Complexidade do Problema:
    → SOLID apropriado

Se Complexidade da Solução < Complexidade do Problema:
    → Pode precisar de mais abstração
```

**Exemplo Prático:**

```php
<?php
/**
 * Cenário 1: Helper simples
 * 
 * Problema: Buscar configuração
 * Complexidade: Baixa
 * Solução: Função direta ✅
 */
function get_config($key) {
    return get_option($key);
}

/**
 * Cenário 2: Sistema de pagamento
 * 
 * Problema: Múltiplos gateways, testes, extensibilidade
 * Complexidade: Alta
 * Solução: SOLID com interfaces, DI ✅
 */
interface PaymentGatewayInterface {
    public function process(Payment $payment): PaymentResult;
}

class PaymentService {
    private PaymentGatewayInterface $gateway;
    
    public function __construct(PaymentGatewayInterface $gateway) {
        $this->gateway = $gateway;
    }
    
    public function processPayment(Payment $payment): PaymentResult {
        return $this->gateway->process($payment);
    }
}

/**
 * Cenário 3: Processamento de dados simples
 * 
 * Problema: Transformar array
 * Complexidade: Baixa
 * Solução: Função direta ✅ (não precisa de classe)
 */
function transform_data($data) {
    return array_map(function($item) {
        return [
            'id' => $item['ID'],
            'name' => $item['post_title'],
        ];
    }, $data);
}
```

---

## 13.12 Error Handling em Arquitetura Avançada

### 13.12.1 Exception Handling com Domain Exceptions

```php
<?php
/**
 * Domain Exceptions - Exceções específicas do domínio
 */
namespace App\Domain\Exceptions;

class DomainException extends \Exception {
    protected $context = [];
    
    public function __construct(string $message, array $context = [], int $code = 0, \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }
    
    public function getContext(): array {
        return $this->context;
    }
}

class InvalidUserDataException extends DomainException {}
class UserNotFoundException extends DomainException {}
class InsufficientPermissionsException extends DomainException {}
class BusinessRuleViolationException extends DomainException {}

/**
 * Uso em Domain Services
 */
class UserService {
    public function createUser(array $data): User {
        // Validação de regras de negócio
        if (empty($data['email'])) {
            throw new InvalidUserDataException(
                'Email é obrigatório',
                ['field' => 'email', 'data' => $data]
            );
        }
        
        if (!is_email($data['email'])) {
            throw new InvalidUserDataException(
                'Email inválido',
                ['field' => 'email', 'value' => $data['email']]
            );
        }
        
        // Verificar regra de negócio
        if ($this->userExists($data['email'])) {
            throw new BusinessRuleViolationException(
                'Usuário com este email já existe',
                ['email' => $data['email']]
            );
        }
        
        // Criar usuário
        return $this->userRepository->create($data);
    }
}
```

### 13.12.2 Error Handling em Service Layer

```php
<?php
/**
 * Service Layer com tratamento de erros robusto
 */
class OrderService {
    private OrderRepository $orderRepository;
    private PaymentService $paymentService;
    private InventoryService $inventoryService;
    private LoggerInterface $logger;
    
    public function __construct(
        OrderRepository $orderRepository,
        PaymentService $paymentService,
        InventoryService $inventoryService,
        LoggerInterface $logger
    ) {
        $this->orderRepository = $orderRepository;
        $this->paymentService = $paymentService;
        $this->inventoryService = $inventoryService;
        $this->logger = $logger;
    }
    
    /**
     * Processar pedido com tratamento de erros completo
     */
    public function processOrder(Order $order): OrderResult {
        try {
            // 1. Validar estoque
            $this->validateInventory($order);
            
            // 2. Processar pagamento
            $payment_result = $this->paymentService->process($order->getPayment());
            
            if (!$payment_result->isSuccessful()) {
                throw new PaymentFailedException(
                    'Pagamento falhou',
                    ['order_id' => $order->getId(), 'payment_result' => $payment_result]
                );
            }
            
            // 3. Reservar estoque
            $this->inventoryService->reserve($order->getItems());
            
            // 4. Criar pedido
            $saved_order = $this->orderRepository->save($order);
            
            // 5. Confirmar pagamento
            $this->paymentService->confirm($payment_result->getTransactionId());
            
            return OrderResult::success($saved_order);
            
        } catch (InsufficientInventoryException $e) {
            // Erro de estoque - não processar pagamento
            $this->logger->warning('Insufficient inventory', [
                'order_id' => $order->getId(),
                'exception' => $e,
            ]);
            
            return OrderResult::failure('Estoque insuficiente', $e);
            
        } catch (PaymentFailedException $e) {
            // Erro de pagamento - liberar estoque se reservado
            if (isset($saved_order)) {
                $this->inventoryService->release($order->getItems());
            }
            
            $this->logger->error('Payment failed', [
                'order_id' => $order->getId(),
                'exception' => $e,
            ]);
            
            return OrderResult::failure('Pagamento falhou', $e);
            
        } catch (Exception $e) {
            // Erro inesperado - rollback completo
            $this->rollbackOrder($order);
            
            $this->logger->error('Unexpected error processing order', [
                'order_id' => $order->getId(),
                'exception' => $e,
            ]);
            
            return OrderResult::failure('Erro ao processar pedido', $e);
        }
    }
    
    /**
     * Rollback de operações em caso de erro
     */
    private function rollbackOrder(Order $order): void {
        try {
            // Liberar estoque
            if ($order->hasReservedInventory()) {
                $this->inventoryService->release($order->getItems());
            }
            
            // Cancelar pagamento se processado
            if ($order->hasProcessedPayment()) {
                $this->paymentService->cancel($order->getPaymentTransactionId());
            }
            
            // Remover pedido se criado
            if ($order->getId()) {
                $this->orderRepository->delete($order->getId());
            }
            
        } catch (Exception $e) {
            // Log erro no rollback mas não lançar
            $this->logger->critical('Rollback failed', [
                'order_id' => $order->getId(),
                'exception' => $e,
            ]);
        }
    }
}
```

### 13.12.3 Error Handling em Repository Pattern

```php
<?php
/**
 * Repository com tratamento de erros específico
 */
class PostRepository implements PostRepositoryInterface {
    private $wpdb;
    private $logger;
    
    public function findById(int $id): ?Post {
        try {
            $post = get_post($id);
            
            if (!$post) {
                return null; // Não é erro, apenas não encontrado
            }
            
            return $this->mapToDomain($post);
            
        } catch (Exception $e) {
            $this->logger->error('Error finding post', [
                'id' => $id,
                'exception' => $e,
            ]);
            
            throw new RepositoryException(
                'Erro ao buscar post',
                ['id' => $id],
                0,
                $e
            );
        }
    }
    
    public function save(Post $post): Post {
        try {
            global $wpdb;
            
            $wpdb->query('START TRANSACTION');
            
            try {
                if ($post->getId()) {
                    $this->update($post);
                } else {
                    $this->insert($post);
                }
                
                $wpdb->query('COMMIT');
                
                return $post;
                
            } catch (Exception $e) {
                $wpdb->query('ROLLBACK');
                throw $e;
            }
            
        } catch (Exception $e) {
            $this->logger->error('Error saving post', [
                'post_id' => $post->getId(),
                'exception' => $e,
            ]);
            
            throw new RepositoryException(
                'Erro ao salvar post',
                ['post' => $post],
                0,
                $e
            );
        }
    }
    
    private function insert(Post $post): void {
        $post_id = wp_insert_post([
            'post_title' => $post->getTitle(),
            'post_content' => $post->getContent(),
            'post_status' => $post->getStatus(),
        ]);
        
        if (is_wp_error($post_id)) {
            throw new RepositoryException(
                'Falha ao inserir post',
                ['error' => $post_id]
            );
        }
        
        $post->setId($post_id);
    }
}
```

### 13.12.4 Error Handling em Event-Driven Architecture

```php
<?php
/**
 * Event handlers com tratamento de erros
 */
class OrderPlacedHandler {
    private LoggerInterface $logger;
    private DeadLetterQueue $dlq;
    
    public function handle(OrderPlacedEvent $event): void {
        try {
            // Processar evento
            $this->sendConfirmationEmail($event->getOrder());
            $this->updateInventory($event->getOrder());
            $this->notifyWarehouse($event->getOrder());
            
        } catch (EmailException $e) {
            // Erro de email não deve bloquear outros processamentos
            $this->logger->warning('Email failed', [
                'order_id' => $event->getOrder()->getId(),
                'exception' => $e,
            ]);
            
            // Continuar processamento
            
        } catch (Exception $e) {
            // Erro crítico - mover para DLQ
            $this->logger->error('Event handling failed', [
                'event' => $event,
                'exception' => $e,
            ]);
            
            $this->dlq->enqueue($event, $e);
            
            // Re-lançar para que o sistema saiba que falhou
            throw $e;
        }
    }
}

/**
 * Event dispatcher com retry logic
 */
class EventDispatcher {
    private RetryableOperation $retry;
    
    public function dispatch(Event $event): void {
        $this->retry->execute(function() use ($event) {
            foreach ($this->getHandlers($event) as $handler) {
                $handler->handle($event);
            }
        }, function($error) {
            // Retentar apenas erros temporários
            return !($error instanceof BusinessRuleViolationException);
        });
    }
}
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
