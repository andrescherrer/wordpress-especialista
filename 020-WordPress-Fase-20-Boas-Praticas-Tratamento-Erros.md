# 🛡️ Error Handling Best Practices - WordPress

**Versão:** 1.0  
**Data:** Fevereiro 2026  
**Nível:** Especialista em PHP  
**Objetivo:** Guia completo de tratamento de erros em WordPress

---

**Navegação:** [Índice](000-WordPress-Indice-Topicos.md) | [← Fase 19](019-WordPress-Fase-19-Anti-padroes-Seguranca.md)

---

## 📑 Índice

1. [Princípios Fundamentais](#princípios-fundamentais)
2. [Padrões de Error Handling](#padrões-de-error-handling)
3. [Error Handling por Contexto](#error-handling-por-contexto)
4. [Logging e Monitoramento](#logging-e-monitoramento)
5. [Error Recovery Strategies](#error-recovery-strategies)
6. [Best Practices](#best-practices)

---

## Princípios Fundamentais

### 1. Fail Fast, Fail Loud

**Princípio:** Erros devem ser detectados e reportados o mais cedo possível.

```php
<?php
// ❌ ERRADO: Erro silencioso
function get_user_email($user_id) {
    $user = get_userdata($user_id);
    return $user ? $user->user_email : ''; // Retorna string vazia em caso de erro
}

// ✅ CORRETO: Fail loud
function get_user_email($user_id) {
    $user = get_userdata($user_id);
    
    if (!$user) {
        throw new InvalidArgumentException("User with ID {$user_id} not found");
    }
    
    return $user->user_email;
}
```

### 2. Never Swallow Exceptions

**Princípio:** Nunca capturar exceções sem tratamento adequado.

```php
<?php
// ❌ ERRADO: Swallowing exception
try {
    process_payment($order_id);
} catch (Exception $e) {
    // Ignorar erro silenciosamente
}

// ✅ CORRETO: Tratar ou re-lançar
try {
    process_payment($order_id);
} catch (PaymentException $e) {
    error_log('Payment failed: ' . $e->getMessage());
    notify_admin($e);
    throw $e; // Re-lançar para que caller saiba
}
```

### 3. Use Appropriate Error Types

**Princípio:** Use tipos específicos de erro para diferentes situações.

```php
<?php
// Exceções específicas do domínio
class ValidationException extends DomainException {}
class NotFoundException extends DomainException {}
class PermissionException extends DomainException {}
class BusinessRuleException extends DomainException {}

// Uso
if (empty($email)) {
    throw new ValidationException('Email is required');
}

if (!user_exists($user_id)) {
    throw new NotFoundException("User {$user_id} not found");
}

if (!current_user_can('edit_post', $post_id)) {
    throw new PermissionException('Insufficient permissions');
}
```

---

## Padrões de Error Handling

### Padrão 1: Try-Catch com Contexto

```php
<?php
class OrderProcessor {
    
    public function processOrder(int $order_id): OrderResult {
        $context = [
            'order_id' => $order_id,
            'user_id' => get_current_user_id(),
            'timestamp' => time(),
        ];
        
        try {
            return $this->doProcessOrder($order_id);
            
        } catch (ValidationException $e) {
            $this->logger->warning('Order validation failed', array_merge($context, [
                'exception' => $e,
            ]));
            
            return OrderResult::failure('Validation failed', $e);
            
        } catch (PaymentException $e) {
            $this->logger->error('Payment processing failed', array_merge($context, [
                'exception' => $e,
            ]));
            
            return OrderResult::failure('Payment failed', $e);
            
        } catch (Exception $e) {
            $this->logger->critical('Unexpected error processing order', array_merge($context, [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]));
            
            return OrderResult::failure('Unexpected error', $e);
        }
    }
}
```

### Padrão 2: Error Result Objects

```php
<?php
/**
 * Result object para operações que podem falhar
 */
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

// Uso
$result = processOrder($order_id);

if ($result->isFailure()) {
    $error = $result->getError();
    // Tratar erro
} else {
    $order = $result->getData();
    // Processar sucesso
}
```

### Padrão 3: Error Handler Centralizado

```php
<?php
/**
 * Error Handler centralizado para WordPress
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
    
    /**
     * Tratar erros PHP
     */
    public static function handleError(
        int $errno,
        string $errstr,
        string $errfile,
        int $errline
    ): bool {
        // Não tratar se error_reporting está desabilitado
        if (!(error_reporting() & $errno)) {
            return false;
        }
        
        $context = [
            'type' => self::getErrorType($errno),
            'message' => $errstr,
            'file' => $errfile,
            'line' => $errline,
        ];
        
        // Logar erro
        error_log(sprintf(
            '[PHP Error] %s: %s in %s:%d',
            $context['type'],
            $errstr,
            $errfile,
            $errline
        ));
        
        // Enviar para monitoramento
        if (function_exists('Sentry\\captureMessage')) {
            Sentry\captureMessage($errstr, \Sentry\Severity::error(), [
                'extra' => $context,
            ]);
        }
        
        // Em desenvolvimento, mostrar erro
        if (WP_DEBUG) {
            return false; // Deixar WordPress tratar normalmente
        }
        
        // Em produção, suprimir erro
        return true;
    }
    
    /**
     * Tratar exceções não capturadas
     */
    public static function handleException(Throwable $exception): void {
        $context = [
            'exception' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
        ];
        
        error_log(sprintf(
            '[Uncaught Exception] %s: %s in %s:%d',
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        ));
        
        // Enviar para monitoramento
        if (function_exists('Sentry\\captureException')) {
            Sentry\captureException($exception);
        }
        
        // Em desenvolvimento, mostrar erro
        if (WP_DEBUG) {
            echo '<pre>';
            echo get_class($exception) . ': ' . $exception->getMessage() . "\n";
            echo $exception->getTraceAsString();
            echo '</pre>';
        } else {
            // Em produção, mostrar erro genérico
            status_header(500);
            wp_die('An error occurred. Please try again later.');
        }
    }
    
    /**
     * Tratar fatal errors no shutdown
     */
    public static function handleShutdown(): void {
        $error = error_get_last();
        
        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            self::handleError(
                $error['type'],
                $error['message'],
                $error['file'],
                $error['line']
            );
        }
    }
    
    private static function getErrorType(int $errno): string {
        $types = [
            E_ERROR => 'E_ERROR',
            E_WARNING => 'E_WARNING',
            E_PARSE => 'E_PARSE',
            E_NOTICE => 'E_NOTICE',
            E_CORE_ERROR => 'E_CORE_ERROR',
            E_CORE_WARNING => 'E_CORE_WARNING',
            E_COMPILE_ERROR => 'E_COMPILE_ERROR',
            E_COMPILE_WARNING => 'E_COMPILE_WARNING',
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

// Registrar no início do plugin
WordPressErrorHandler::register();
```

---

## Error Handling por Contexto

### REST API

```php
<?php
// Ver seção 2.12.1 e 3.6 para detalhes completos
// Padrões: WP_Error, try-catch, error handler centralizado
```

### Background Jobs

```php
<?php
// Ver Fase 15 - Error Handling em Async Jobs
// Padrões: Retry logic, Dead Letter Queue, Circuit Breaker
```

### Database Operations

```php
<?php
/**
 * Error handling em operações de banco de dados
 */
class DatabaseOperation {
    
    public function executeTransaction(callable $operation) {
        global $wpdb;
        
        $wpdb->query('START TRANSACTION');
        
        try {
            $result = $operation();
            $wpdb->query('COMMIT');
            
            return $result;
            
        } catch (Exception $e) {
            $wpdb->query('ROLLBACK');
            
            error_log('Transaction failed: ' . $e->getMessage());
            
            throw new DatabaseException(
                'Database operation failed',
                0,
                $e
            );
        }
    }
}
```

### File Operations

```php
<?php
/**
 * Error handling em operações de arquivo
 */
class FileOperation {
    
    public function readFile(string $filepath): string {
        if (!file_exists($filepath)) {
            throw new FileNotFoundException("File not found: {$filepath}");
        }
        
        if (!is_readable($filepath)) {
            throw new FilePermissionException("File not readable: {$filepath}");
        }
        
        $content = file_get_contents($filepath);
        
        if ($content === false) {
            throw new FileReadException("Failed to read file: {$filepath}");
        }
        
        return $content;
    }
    
    public function writeFile(string $filepath, string $content): void {
        $dir = dirname($filepath);
        
        if (!is_dir($dir)) {
            if (!wp_mkdir_p($dir)) {
                throw new DirectoryCreationException("Failed to create directory: {$dir}");
            }
        }
        
        if (!is_writable($dir)) {
            throw new FilePermissionException("Directory not writable: {$dir}");
        }
        
        $result = file_put_contents($filepath, $content, LOCK_EX);
        
        if ($result === false) {
            throw new FileWriteException("Failed to write file: {$filepath}");
        }
    }
}
```

---

## Logging e Monitoramento

### Structured Logging

```php
<?php
/**
 * Structured logging para WordPress
 */
class StructuredLogger {
    
    public function log(string $level, string $message, array $context = []): void {
        $log_entry = [
            'timestamp' => current_time('mysql'),
            'level' => $level,
            'message' => $message,
            'context' => $context,
            'user_id' => get_current_user_id(),
            'request_uri' => $_SERVER['REQUEST_URI'] ?? null,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
        ];
        
        // Log local
        error_log(json_encode($log_entry));
        
        // Enviar para serviço de monitoramento
        if (function_exists('Sentry\\captureMessage')) {
            Sentry\captureMessage($message, $this->mapLevelToSentry($level), [
                'extra' => $context,
            ]);
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
    
    private function mapLevelToSentry(string $level): \Sentry\Severity {
        $map = [
            'debug' => \Sentry\Severity::debug(),
            'info' => \Sentry\Severity::info(),
            'warning' => \Sentry\Severity::warning(),
            'error' => \Sentry\Severity::error(),
            'critical' => \Sentry\Severity::fatal(),
        ];
        
        return $map[$level] ?? \Sentry\Severity::error();
    }
}
```

---

## Error Recovery Strategies

### Retry Logic

```php
<?php
// Ver Fase 3 - Retry Logic para Operações Transientes
```

### Fallback Operations

```php
<?php
// Ver Fase 3 - Error Recovery e Fallbacks
```

### Compensation (Saga Pattern)

```php
<?php
// Ver Fase 15 - Error Recovery e Compensation
```

---

## Best Practices

### ✅ DO

1. **Sempre logue erros com contexto suficiente**
   ```php
   error_log(sprintf(
       'Order processing failed | order_id=%d | user_id=%d | error=%s',
       $order_id,
       get_current_user_id(),
       $e->getMessage()
   ));
   ```

2. **Use tipos específicos de exceção**
   ```php
   throw new ValidationException('Email is required');
   // Não: throw new Exception('Email is required');
   ```

3. **Trate erros no nível apropriado**
   ```php
   // Controller trata erros de validação
   // Service trata erros de negócio
   // Repository trata erros de dados
   ```

4. **Sempre limpe recursos em finally**
   ```php
   try {
       $file = fopen($path, 'r');
       // processar
   } finally {
       if (isset($file)) {
           fclose($file);
       }
   }
   ```

5. **Documente exceções que métodos podem lançar**
   ```php
   /**
    * @throws ValidationException Se dados inválidos
    * @throws DatabaseException Se erro de banco
    */
   public function createOrder(array $data): Order {}
   ```

### ❌ DON'T

1. **Não ignore exceções silenciosamente**
   ```php
   // ❌ ERRADO
   try {
       process();
   } catch (Exception $e) {
       // Ignorar
   }
   ```

2. **Não exponha detalhes internos em produção**
   ```php
   // ❌ ERRADO
   return new WP_Error('error', $e->getTraceAsString());
   
   // ✅ CORRETO
   return new WP_Error('error', 'An error occurred');
   ```

3. **Não use exceções para controle de fluxo**
   ```php
   // ❌ ERRADO
   try {
       return get_value();
   } catch (NotFoundException $e) {
       return null;
   }
   
   // ✅ CORRETO
   $value = get_value();
   return $value !== null ? $value : null;
   ```

4. **Não capture Exception genérica sem re-lançar**
   ```php
   // ❌ ERRADO
   catch (Exception $e) {
       log($e);
       // Não re-lança
   }
   
   // ✅ CORRETO
   catch (Exception $e) {
       log($e);
       throw $e; // Re-lançar
   }
   ```

5. **Não misture WP_Error e Exceptions**
   ```php
   // ❌ ERRADO: Misturar
   if (is_wp_error($result)) {
       throw new Exception($result->get_error_message());
   }
   
   // ✅ CORRETO: Escolher um padrão e seguir
   // Opção 1: Apenas WP_Error
   return new WP_Error('error', 'message');
   
   // Opção 2: Apenas Exceptions
   throw new DomainException('message');
   ```

---

## Checklist de Error Handling

- [ ] Todos os erros são logados com contexto suficiente
- [ ] Exceções específicas são usadas (não Exception genérica)
- [ ] Erros são tratados no nível apropriado
- [ ] Recursos são limpos em finally blocks
- [ ] Exceções são documentadas em PHPDoc
- [ ] Erros não expõem detalhes internos em produção
- [ ] Retry logic é usado para erros temporários
- [ ] Dead Letter Queue é usado para erros permanentes
- [ ] Monitoramento está configurado (Sentry, etc.)
- [ ] Error handlers centralizados estão registrados

---

---

**Navegação:** [Índice](000-WordPress-Indice-Topicos.md) | [← Fase 19](019-WordPress-Fase-19-Anti-padroes-Seguranca.md)

---

## Recursos Adicionais

- [Fase 2 - REST API Error Handling](002-WordPress-Fase-2-REST-API-Fundamentos.md#2121-error-handling-patterns-completos)
- [Fase 3 - Advanced Error Handling](003-WordPress-Fase-3-REST-API-Avancado.md#tratamento-de-erros)
- [Fase 13 - Architecture Error Handling](013-WordPress-Fase-13-Arquitetura-Avancada.md#1312-error-handling-em-arquitetura-avançada)
- [Fase 15 - Async Jobs Error Handling](016-WordPress-Fase-15-Jobs-Assincronos-Background.md#error-handling-em-async-jobs)
