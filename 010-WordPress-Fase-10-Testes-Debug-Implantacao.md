# 🧪 FASE 10: Testing, Debugging Avançado e Deploy

**Versão:** 1.0  
**Data:** Janeiro 2026  
**Nível:** Especialista em PHP  
**Objetivo:** Dominar testes automatizados, debugging profissional e estratégias de deploy em produção

---

**Navegação:** [Índice](./000-WordPress-Indice-Topicos.md) | [← Fase 9](./009-WordPress-Fase-9-WP-CLI-Ferramentas.md) | [Fase 11 →](./011-WordPress-Fase-11-Multisite-Internacionalizacao.md)

---

## 📑 Índice

1. [Objetivos de Aprendizado](#objetivos-de-aprendizado)
2. [Fundamentos de Testing](#fundamentos-de-testing)
3. [PHPUnit Setup](#phpunit-setup)
4. [Unit Tests Básicos](#unit-tests-basicos)
5. [Testes de Classes](#testes-de-classes)
6. [Testes com Factory](#testes-com-factory)
7. [Mocking e Stubs](#mocking-e-stubs)
8. [Data Providers](#data-providers)
9. [Code Coverage](#code-coverage)
10. [Integration Tests](#integration-tests)
11. [Performance Tests](#performance-tests)
12. [Debugging com Xdebug](#debugging-com-xdebug)
13. [Query Monitor](#query-monitor)
14. [Sentry Integration](#sentry-integration)
15. [Deploy Strategies](#deploy-strategies)
16. [Deploy Checklist](#deploy-checklist)
17. [Scripts de Deploy](#scripts-de-deploy)
18. [Blue-Green Deploy](#blue-green-deploy)
19. [Canary Deploy](#canary-deploy)
20. [CI/CD Pipeline](#cicd-pipeline)
21. [Monitoring](#monitoring)
22. [Boas Práticas](#boas-praticas)
23. [Autoavaliação](#autoavaliacao)
24. [Projeto Prático](#projeto-pratico)
25. [Equívocos Comuns](#equivocos-comuns)
26. [Recursos Recomendados](#recursos-recomendados)

---

<a id="objetivos-de-aprendizado"></a>
## 🎯 Objetivos de Aprendizado

Ao final desta fase, você será capaz de:

1. ✅ Configurar ambiente de testes PHPUnit para WordPress
2. ✅ Escrever testes unitários, de integração e funcionais
3. ✅ Usar mocking e stubs para isolar código sob teste
4. ✅ Alcançar cobertura de código significativa e interpretar relatórios de cobertura
5. ✅ Debugar aplicações WordPress usando Xdebug e Query Monitor
6. ✅ Integrar rastreamento de erros com Sentry para monitoramento em produção
7. ✅ Implementar estratégias de deployment (blue-green, canary, rolling)
8. ✅ Criar checklists de deployment e scripts de automação

---

<a id="fundamentos-de-testing"></a>
## 🧪 Fundamentos de Testing

### Por que Testar?

1. **Confiabilidade** - Código testado é mais confiável
2. **Documentação** - Testes documentam o comportamento esperado
3. **Refactoring Seguro** - Mudanças sem quebrar funcionalidades
4. **Qualidade** - Menos bugs em produção
5. **Performance** - Testes de performance encontram gargalos
6. **Manutenibilidade** - Código mais fácil de manter

### Pirâmide de Testes

```
       E2E Tests (1%)
      /            \
    Integration Tests (10%)
   /                    \
Unit Tests (89%)
```

---

<a id="phpunit-setup"></a>
## 🔧 PHPUnit Setup

### Instalação

```bash
# Via Composer
composer require --dev phpunit/phpunit ^11.0

# Verificar instalação
./vendor/bin/phpunit --version
```

### Arquivo de Configuração (phpunit.xml)

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/11.0/phpunit.xsd"
         bootstrap="tests/bootstrap.php"
         cacheDirectory=".phpunit.cache"
         requireCoverageMetastasis="true"
         beStrictAboutCoverageMetadata="false"
         colors="true">
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Integration">
            <directory>tests/Integration</directory>
        </testsuite>
        <testsuite name="E2E">
            <directory>tests/E2E</directory>
        </testsuite>
    </testsuites>

    <coverage processUncoveredFiles="true">
        <include>
            <directory suffix=".php">includes</directory>
        </include>
        <exclude>
            <directory suffix="Test.php">includes</directory>
        </exclude>
        <report>
            <html outputDirectory="coverage"/>
            <clover outputFile="coverage.xml"/>
        </report>
    </coverage>

    <php>
        <ini name="display_errors" value="On"/>
        <ini name="error_reporting" value="-1"/>
        <ini name="date.timezone" value="UTC"/>
        <const name="ABSPATH" value="/var/www/html/"/>
        <const name="WP_PLUGIN_DIR" value="/var/www/html/wp-content/plugins/"/>
    </php>
</phpunit>
```

### Bootstrap File (tests/bootstrap.php)

```php
<?php
/**
 * Bootstrap arquivo para testes
 */

// Definir constantes
define('ABSPATH', '/var/www/html/');
define('WP_PLUGIN_DIR', ABSPATH . 'wp-content/plugins/');
define('PLUGIN_DIR', WP_PLUGIN_DIR . 'meu-plugin/');

// Carregar WordPress
require_once ABSPATH . 'wp-load.php';

// Carregar Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Disable WP cache durante testes
wp_cache_flush();
```

---

<a id="unit-tests-basicos"></a>
## 📝 Unit Tests Básicos

### Exemplo 1: Teste Simples de Função

```php
<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class UtilTest extends TestCase
{
    /**
     * @test
     */
    public function testaFormataDataCorretamente()
    {
        // Arrange
        $data = '2026-01-29';
        
        // Act
        $resultado = formata_data($data);
        
        // Assert
        $this->assertEquals('29/01/2026', $resultado);
    }

    /**
     * @test
     */
    public function testaCalculoTotalComImposto()
    {
        // Arrange
        $valor = 100.00;
        $imposto = 0.15;
        
        // Act
        $total = calcula_com_imposto($valor, $imposto);
        
        // Assert
        $this->assertEquals(115.00, $total);
        $this->assertIsFloat($total);
    }

    /**
     * @test
     */
    public function testaValidacaoEmail()
    {
        $this->assertTrue(valida_email('andre@example.com'));
        $this->assertFalse(valida_email('email-invalido'));
        $this->assertFalse(valida_email(''));
    }

    /**
     * @test
     */
    public function testaLancaExcecaoComDadosInvalidos()
    {
        $this->expectException(\InvalidArgumentException::class);
        
        processa_dados(null);
    }
}
```

---

<a id="testes-de-classes"></a>
## 🏗️ Testes de Classes

### Exemplo: Teste de Classe Service

```php
<?php
namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\UserService;
use App\Models\User;

class UserServiceTest extends TestCase
{
    private UserService $userService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userService = new UserService();
    }

    /**
     * @test
     */
    public function testaCriacaoDeUsuario()
    {
        // Arrange
        $dados = [
            'name' => 'André Silva',
            'email' => 'andre@example.com',
            'password' => 'senha123'
        ];

        // Act
        $usuario = $this->userService->criar($dados);

        // Assert
        $this->assertInstanceOf(User::class, $usuario);
        $this->assertEquals('André Silva', $usuario->name);
        $this->assertEquals('andre@example.com', $usuario->email);
        $this->assertTrue($usuario->id > 0);
    }

    /**
     * @test
     */
    public function testaValidacaoDeDadosInvalidos()
    {
        // Arrange
        $dados = [
            'name' => '',  // Inválido
            'email' => 'email-invalido',  // Inválido
        ];

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->userService->criar($dados);
    }

    /**
     * @test
     */
    public function testaBuscaDeUsuarioPorId()
    {
        // Arrange
        $usuarioId = 1;

        // Act
        $usuario = $this->userService->encontraPorId($usuarioId);

        // Assert
        $this->assertInstanceOf(User::class, $usuario);
        $this->assertEquals($usuarioId, $usuario->id);
    }

    /**
     * @test
     */
    public function testaAtualizacaoDeUsuario()
    {
        // Arrange
        $usuario = $this->userService->encontraPorId(1);
        $novoNome = 'André Silva (Atualizado)';

        // Act
        $usuario->name = $novoNome;
        $atualizado = $this->userService->atualizar($usuario);

        // Assert
        $this->assertTrue($atualizado);
        $this->assertEquals($novoNome, $usuario->name);
    }

    /**
     * @test
     */
    public function testaDeletarUsuario()
    {
        // Arrange
        $usuarioId = 1;

        // Act
        $deletado = $this->userService->deletar($usuarioId);

        // Assert
        $this->assertTrue($deletado);
        $this->assertNull($this->userService->encontraPorId($usuarioId));
    }
}
```

---

<a id="testes-com-factory"></a>
## 🏭 Testes com Factory

### Factory Pattern para Testes

```php
<?php
namespace Tests\Factories;

use App\Models\User;

class UserFactory
{
    private static int $counter = 0;

    public static function criar(array $atributos = []): User
    {
        self::$counter++;

        $padrao = [
            'id' => self::$counter,
            'name' => 'Usuário ' . self::$counter,
            'email' => 'user' . self::$counter . '@example.com',
            'password' => bcrypt('senha123'),
            'role' => 'subscriber',
            'created_at' => now(),
        ];

        return User::create(array_merge($padrao, $atributos));
    }

    public static function admin(array $atributos = []): User
    {
        return self::criar(array_merge(['role' => 'administrator'], $atributos));
    }

    public static function editor(array $atributos = []): User
    {
        return self::criar(array_merge(['role' => 'editor'], $atributos));
    }

    public static function count(int $quantidade, array $atributos = []): array
    {
        return array_map(
            fn() => self::criar($atributos),
            range(1, $quantidade)
        );
    }
}
```

### Usando Factory em Testes

```php
<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tests\Factories\UserFactory;

class UserAuthorizationTest extends TestCase
{
    /**
     * @test
     */
    public function testaAdminPodeEditarQualquerUsuario()
    {
        // Arrange
        $admin = UserFactory::admin();
        $usuario = UserFactory::criar();

        // Act
        $podeEditar = $admin->pode('editar', $usuario);

        // Assert
        $this->assertTrue($podeEditar);
    }

    /**
     * @test
     */
    public function testaUsuarioComumNaoPodeEditarOutro()
    {
        // Arrange
        $usuario1 = UserFactory::criar();
        $usuario2 = UserFactory::criar();

        // Act
        $podeEditar = $usuario1->pode('editar', $usuario2);

        // Assert
        $this->assertFalse($podeEditar);
    }

    /**
     * @test
     */
    public function testaCriarMultiplosUsuarios()
    {
        // Arrange
        $usuarios = UserFactory::count(5);

        // Act
        $total = count($usuarios);

        // Assert
        $this->assertEquals(5, $total);
        $this->assertCount(5, $usuarios);
    }
}
```

---

<a id="mocking-e-stubs"></a>
## 🎭 Mocking e Stubs

### Mock Objects

```php
<?php
namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\EmailService;
use App\Services\UserService;

class EmailNotificationTest extends TestCase
{
    /**
     * @test
     */
    public function testaEnviaEmailAoRegistrarUsuario()
    {
        // Arrange - Criar mock
        $emailServiceMock = $this->createMock(EmailService::class);
        
        // Definir expectativa
        $emailServiceMock
            ->expects($this->once())
            ->method('enviar')
            ->with(
                $this->stringContains('@example.com'),
                $this->stringContains('Bem-vindo'),
                $this->isType('array')
            );

        // Act
        $userService = new UserService($emailServiceMock);
        $userService->registrar([
            'name' => 'André',
            'email' => 'andre@example.com',
            'password' => 'senha123'
        ]);
    }

    /**
     * @test
     */
    public function testaRetentaEnviarEmailSeFalhar()
    {
        // Arrange
        $emailServiceMock = $this->createMock(EmailService::class);
        
        $emailServiceMock
            ->expects($this->exactly(3))  // Tenta 3 vezes
            ->method('enviar')
            ->willThrowException(new \Exception('Falha ao enviar'));

        // Act & Assert
        $this->expectException(\Exception::class);
        
        $userService = new UserService($emailServiceMock);
        $userService->registrarComRetentativa([
            'name' => 'André',
            'email' => 'andre@example.com'
        ]);
    }
}
```

### Stub Objects

```php
<?php
class PaymentGatewayTest extends TestCase
{
    /**
     * @test
     */
    public function testaProcessamentoDePagamento()
    {
        // Arrange - Criar stub
        $gatewayStub = $this->createStub(PaymentGateway::class);
        
        // Configurar comportamento
        $gatewayStub->method('processar')
            ->willReturn(true);

        // Act
        $resultado = $gatewayStub->processar(100.00, 'cart-123');

        // Assert
        $this->assertTrue($resultado);
    }

    /**
     * @test
     */
    public function testaRetornaValoresVariados()
    {
        // Arrange
        $gatewayStub = $this->createStub(PaymentGateway::class);
        
        $gatewayStub->method('obterSaldo')
            ->willReturnOnConsecutiveCalls(100, 50, 0);

        // Act & Assert
        $this->assertEquals(100, $gatewayStub->obterSaldo());
        $this->assertEquals(50, $gatewayStub->obterSaldo());
        $this->assertEquals(0, $gatewayStub->obterSaldo());
    }
}
```

---

<a id="data-providers"></a>
## 📊 Data Providers

### Exemplo com Data Provider

```php
<?php
namespace Tests\Unit\Validation;

use PHPUnit\Framework\TestCase;

class EmailValidationTest extends TestCase
{
    /**
     * @test
     * @dataProvider emailsValidos
     */
    public function testaEmailsValidos(string $email)
    {
        $this->assertTrue(valida_email($email));
    }

    /**
     * @test
     * @dataProvider emailsInvalidos
     */
    public function testaEmailsInvalidos(string $email)
    {
        $this->assertFalse(valida_email($email));
    }

    /**
     * Data provider para emails válidos
     */
    public static function emailsValidos(): array
    {
        return [
            'email simples' => ['andre@example.com'],
            'email com ponto' => ['andre.silva@example.com'],
            'email com numero' => ['andre123@example.com'],
            'email com subdomain' => ['andre@mail.example.com'],
            'email com hyphen' => ['andre-silva@example.com'],
        ];
    }

    /**
     * Data provider para emails inválidos
     */
    public static function emailsInvalidos(): array
    {
        return [
            'sem @' => ['andreexample.com'],
            'sem domínio' => ['andre@'],
            'sem usuario' => ['@example.com'],
            'espaços' => ['andre @example.com'],
            'vazio' => [''],
            'com caracteres especiais' => ['andre@exa mple.com'],
        ];
    }
}
```

### CSV Data Provider

```php
<?php
class CalculationTest extends TestCase
{
    /**
     * @test
     * @dataProvider calculos
     */
    public function testaCalculosComCSV(float $valor, float $taxa, float $esperado)
    {
        $resultado = calcula_com_taxa($valor, $taxa);
        $this->assertEqualsWithDelta($esperado, $resultado, 0.01);
    }

    public static function calculos(): array
    {
        $file = fopen(__DIR__ . '/fixtures/calculos.csv', 'r');
        $dados = [];
        
        while (($linha = fgetcsv($file)) !== false) {
            $dados[$linha[0]] = [
                (float)$linha[1],  // valor
                (float)$linha[2],  // taxa
                (float)$linha[3],  // esperado
            ];
        }
        
        fclose($file);
        return $dados;
    }
}
```

---

<a id="code-coverage"></a>
## 📈 Code Coverage

### Configurar Code Coverage

```xml
<!-- phpunit.xml -->
<coverage processUncoveredFiles="true">
    <include>
        <directory suffix=".php">includes</directory>
    </include>
    <exclude>
        <directory>includes/Admin</directory>
        <file>includes/deprecated.php</file>
    </exclude>
    <report>
        <html outputDirectory="coverage"/>
        <clover outputFile="coverage.xml"/>
        <text outputFile="php://stdout" showUncoveredFiles="true"/>
    </report>
</coverage>
```

### Executar com Coverage

```bash
# Gerar relatório HTML
./vendor/bin/phpunit --coverage-html coverage

# Gerar relatório Clover (para CI/CD)
./vendor/bin/phpunit --coverage-clover coverage.xml

# Mostrar coverage no terminal
./vendor/bin/phpunit --coverage-text
```

### Analisar Coverage

```bash
# Visualizar arquivo HTML (abrir em navegador)
open coverage/index.html

# Verificar coverage mínimo
./vendor/bin/phpunit --coverage-clover=coverage.xml \
    --coverage-clover-required-percentage=80
```

---

<a id="integration-tests"></a>
## 🔗 Integration Tests

### Teste com Banco de Dados

```php
<?php
namespace Tests\Integration;

use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase
{
    private \App\Repositories\UserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new \App\Repositories\UserRepository();
        
        // Limpar banco antes de cada teste
        wp_delete_all_users_for_blog();
    }

    /**
     * @test
     */
    public function testaSalvaERecuperaUsuario()
    {
        // Arrange
        $usuario = [
            'user_login' => 'testuser',
            'user_email' => 'test@example.com',
            'user_pass' => 'senha123'
        ];

        // Act
        $id = wp_insert_user($usuario);
        $usuarioRecuperado = $this->repository->findById($id);

        // Assert
        $this->assertNotWPError($id);
        $this->assertEquals('testuser', $usuarioRecuperado->user_login);
        $this->assertEquals('test@example.com', $usuarioRecuperado->user_email);
    }

    /**
     * @test
     */
    public function testaPermissoesDeUsuario()
    {
        // Arrange
        $usuarioId = $this->criaAdministrador();
        $usuario = get_user_by('id', $usuarioId);

        // Act
        $podeEditar = user_can($usuarioId, 'edit_posts');
        $podeAdministrar = user_can($usuarioId, 'manage_options');

        // Assert
        $this->assertTrue($podeEditar);
        $this->assertTrue($podeAdministrar);
    }

    private function criaAdministrador(): int
    {
        return wp_insert_user([
            'user_login' => 'admin',
            'user_email' => 'admin@example.com',
            'user_pass' => 'senha123',
            'role' => 'administrator'
        ]);
    }
}
```

---

<a id="performance-tests"></a>
## ⚡ Performance Tests

### Teste de Performance

```php
<?php
namespace Tests\Performance;

use PHPUnit\Framework\TestCase;

class QueryPerformanceTest extends TestCase
{
    /**
     * @test
     */
    public function testaQueryPerformance()
    {
        // Arrange
        global $wpdb;
        $inicio = microtime(true);
        $queries_antes = count($wpdb->queries);

        // Act
        $posts = get_posts([
            'posts_per_page' => 100,
            'post_type' => 'post'
        ]);

        // Assert
        $tempo = microtime(true) - $inicio;
        $queries_apos = count($wpdb->queries);
        $total_queries = $queries_apos - $queries_antes;

        // Deve executar em menos de 1 segundo
        $this->assertLessThan(1.0, $tempo);

        // Não deve fazer N+1 queries
        $this->assertLessThan(10, $total_queries);
    }

    /**
     * @test
     * @large
     */
    public function testaPerformanceComMuitsoDados()
    {
        $this->markTestSkipped(
            'Teste de carga ignorado por padrão. Usar apenas em análise de performance.'
        );

        // Criar 10000 posts
        for ($i = 0; $i < 10000; $i++) {
            wp_insert_post([
                'post_title' => 'Post ' . $i,
                'post_content' => 'Conteúdo ' . $i,
                'post_status' => 'publish'
            ]);
        }

        $inicio = microtime(true);
        $posts = get_posts(['posts_per_page' => -1]);
        $tempo = microtime(true) - $inicio;

        $this->assertLessThan(5.0, $tempo);
    }
}
```

---

<a id="debugging-com-xdebug"></a>
## 🐛 Debugging com Xdebug

### Configuração do Xdebug

```ini
# php.ini ou docker-compose.yml
[xdebug]
xdebug.mode = debug,coverage,profile
xdebug.start_with_request = yes
xdebug.client_host = host.docker.internal
xdebug.client_port = 9003
xdebug.log = /var/log/xdebug.log
xdebug.profiler_output_dir = /tmp/profiler
```

### VSCode Debug Configuration

```json
// .vscode/launch.json
{
    "version": "0.2.0",
    "configurations": [
        {
            "name": "Listen for Xdebug",
            "type": "php",
            "port": 9003,
            "pathMapping": {
                "/var/www/html": "${workspaceFolder}"
            }
        },
        {
            "name": "Launch PHPUnit",
            "type": "php",
            "request": "launch",
            "program": "${workspaceFolder}/vendor/bin/phpunit",
            "cwd": "${workspaceFolder}",
            "port": 9003
        }
    ]
}
```

---

<a id="query-monitor"></a>
## 📊 Query Monitor

### Usar Query Monitor para Debugging

```php
<?php
// functions.php do plugin

// Registrar query monitor
if (defined('QM_DB_QUERIES_CAPTURE') && QM_DB_QUERIES_CAPTURE) {
    add_action('wp_footer', function() {
        if (function_exists('do_action')) {
            do_action('qm/debug', 'Meu debug message');
        }
    });
}

// Em um hook
add_action('wp_query', function($query) {
    // Query Monitor mostrará isso no debug bar
    do_action('qm/debug', [
        'tipo' => 'wp_query',
        'query' => $query->request,
        'found_posts' => $query->found_posts
    ]);
});

// No seu código
function processa_dados() {
    $inicio = microtime(true);
    
    // ... seu código ...
    
    $tempo = microtime(true) - $inicio;
    
    do_action('qm/debug', [
        'funcao' => 'processa_dados',
        'tempo' => $tempo
    ]);
}
```

---

<a id="sentry-integration"></a>
## 🚨 Sentry Integration

### Configurar Sentry

```php
<?php
// Instalar via Composer
// composer require sentry/sentry-laravel

// wp-config.php ou functions.php

use Sentry\Level;
use function Sentry\captureException;
use function Sentry\captureMessage;

// Inicializar Sentry
Sentry\init([
    'dsn' => 'https://examplePublicKey@o0.ingest.sentry.io/0',
    'environment' => wp_get_environment_type(),
    'release' => '1.0.0',
    'traces_sample_rate' => 0.1,
    'profiles_sample_rate' => 0.1,
]);

// Capturar exceções automáticamente
set_exception_handler(function ($exception) {
    captureException($exception);
    wp_die('Erro interno do servidor');
});

// Capturar mensagens
add_action('wp_footer', function() {
    if (is_user_logged_in()) {
        captureMessage('User logged in: ' . get_current_user_id(), Level::Info);
    }
});

// Em try-catch
try {
    // seu código
} catch (Exception $e) {
    captureException($e);
    wp_die('Erro ao processar requisição');
}
```

---

<a id="deploy-strategies"></a>
## 🚀 Deploy Strategies

### Estratégias de Deploy

#### 1. **Basic Deploy (Simples)**
```
1. Upload files
2. Run migrations
3. Clear cache
```
Risco: ALTO (downtime, possíveis falhas)

#### 2. **Blue-Green Deploy**
```
1. Manter dois ambientes idênticos (Blue e Green)
2. Deploy em Green enquanto Blue está em produção
3. Testar Green completamente
4. Mudar tráfego para Green
5. Green vira produção, Blue vira staging
```
Risco: BAIXO (sem downtime)

#### 3. **Canary Deploy**
```
1. Deploy em pequena porcentagem de servidores (5%)
2. Monitorar métricas
3. Gradualmente aumentar para 25%, 50%, 100%
4. Rollback automático se detectar erros
```
Risco: MUITO BAIXO (rollback automático)

#### 4. **Rolling Deploy**
```
1. Deploy em um servidor por vez
2. Remover do load balancer
3. Deploy e testes
4. Reintroduzir no load balancer
5. Repetir para próximo servidor
```
Risco: BAIXO (sem downtime total)

---

<a id="deploy-checklist"></a>
## ✅ Deploy Checklist

### Pré-Deploy

```markdown
## Verificações de Código
- [ ] Todos os testes passando
- [ ] Code review aprovado
- [ ] Sem warnings/errors do linter
- [ ] Changelog atualizado
- [ ] Versão atualizada em header do plugin
- [ ] README.md atualizado

## Verificações de Compatibilidade
- [ ] Compatível com versão mínima do WordPress
- [ ] Compatível com versão mínima do PHP
- [ ] Testado em navegadores principais
- [ ] Verificado em mobile
- [ ] Performance acceptable

## Backup e Segurança
- [ ] Backup completo do banco de dados criado
- [ ] Backup completo de arquivos criado
- [ ] Backup armazenado remotamente
- [ ] Procedimento de rollback documentado
- [ ] Plano de contingência definido
```

### Durante o Deploy

```markdown
## Execução do Deploy
- [ ] Backup confirmado antes de começar
- [ ] Equipe notificada sobre deploy
- [ ] Site em modo de manutenção ativado
- [ ] Código deployado com sucesso
- [ ] Composer dependencies instaladas (--no-dev)
- [ ] npm build executado (se aplicável)
- [ ] Database migrations executadas
- [ ] Assets compilados corretamente
- [ ] Cache limpo completamente
```

### Pós-Deploy

```markdown
## Verificações Pós-Deploy
- [ ] Site respondendo normalmente
- [ ] Sem erros na barra de testes
- [ ] Funcionalidades principais testadas manualmente
- [ ] Performance dentro do esperado
- [ ] Nenhum erro nos logs
- [ ] Métricas normais (Sentry, New Relic, etc)
- [ ] Equipe pode acessar painel
- [ ] Tudo operacional

## Finalização
- [ ] Modo de manutenção removido
- [ ] Deploy confirmado para equipe
- [ ] Documentação de release criada
- [ ] Monitorar próximas 2 horas
- [ ] Estar pronto para rollback se necessário
```

---

<a id="scripts-de-deploy"></a>
## 📜 Scripts de Deploy

### Deploy Script Completo

```bash
#!/bin/bash

# bin/deploy-production.sh
# Script profissional de deploy para produção

set -e  # Exit on error

# ===== CONFIGURAÇÕES =====
REMOTE_HOST="production.exemplo.com"
REMOTE_USER="deploy"
REMOTE_PATH="/var/www/html/wp-content/plugins/meu-plugin"
WP_PATH="/var/www/html"
ENVIRONMENT="production"

# ===== CORES =====
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# ===== FUNÇÕES =====

log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1"
}

error() {
    echo -e "${RED}[ERROR]${NC} $1"
    exit 1
}

warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

# ===== 1. VERIFICAÇÕES LOCAIS =====

log "=== INICIANDO DEPLOY PARA $ENVIRONMENT ==="

# Verificar git
info "Verificando Git..."
BRANCH=$(git rev-parse --abbrev-ref HEAD)
if [ "$BRANCH" != "main" ] && [ "$BRANCH" != "master" ]; then
    error "Você deve estar em 'main' ou 'master'. Branch atual: $BRANCH"
fi

# Verificar se há mudanças não commitadas
if [ -n "$(git status --porcelain)" ]; then
    error "Há mudanças não commitadas. Execute: git status"
fi

# Sincronizar com remote
git fetch origin
LOCAL=$(git rev-parse HEAD)
REMOTE=$(git rev-parse origin/$BRANCH)

if [ "$LOCAL" != "$REMOTE" ]; then
    error "Branch desatualizada. Execute: git pull"
fi

# ===== 2. EXECUTAR TESTES =====

log "Executando testes..."
if ! ./vendor/bin/phpunit; then
    error "Testes falharam! Corrija os erros antes do deploy."
fi

# ===== 3. VERIFICAR CODE QUALITY =====

log "Verificando padrões de código..."
if ./vendor/bin/phpcs --standard=WordPress --extensions=php includes/ --severity=8; then
    log "Code standards OK"
else
    warning "Code standards com issues (não bloqueante)"
fi

# ===== 4. COMPILAR ASSETS =====

log "Compilando assets..."
if [ -f "package.json" ]; then
    npm install --production=false
    npm run build || error "Erro ao compilar assets"
fi

# ===== 5. BACKUP PRÉ-DEPLOY =====

log "Criando backup pré-deploy..."
BACKUP_FILE="backup-$(date +%Y%m%d-%H%M%S).sql"

ssh "$REMOTE_USER@$REMOTE_HOST" << ENDSSH || error "Erro ao criar backup"
    cd $WP_PATH
    mysqldump -u wordpress -p\$DB_PASSWORD wordpress > /backups/$BACKUP_FILE
    tar czf /backups/$BACKUP_FILE.tar.gz wp-content/plugins/meu-plugin
ENDSSH

info "Backup criado: $BACKUP_FILE"

# ===== 6. ATIVAR MODO MANUTENÇÃO =====

log "Ativando modo de manutenção..."
ssh "$REMOTE_USER@$REMOTE_HOST" << 'ENDSSH'
    cd /var/www/html
    touch .maintenance
    echo '<?php header("HTTP/1.1 503 Service Unavailable"); ?>' > maintenance.php
ENDSSH

# ===== 7. FAZER DEPLOY =====

log "Fazendo deploy dos arquivos..."
rsync -avz --delete \
    --exclude='.git' \
    --exclude='node_modules' \
    --exclude='.env.local' \
    --exclude='vendor' \
    ./ "$REMOTE_USER@$REMOTE_HOST:$REMOTE_PATH/" || error "Erro ao fazer rsync"

# ===== 8. INSTALAR DEPENDÊNCIAS =====

log "Instalando dependências..."
ssh "$REMOTE_USER@$REMOTE_HOST" << 'ENDSSH'
    cd $REMOTE_PATH
    composer install --no-dev --optimize-autoloader
ENDSSH

# ===== 9. EXECUTAR MIGRATIONS =====

log "Executando migrations..."
ssh "$REMOTE_USER@$REMOTE_HOST" << 'ENDSSH'
    cd $WP_PATH
    wp db query "SET FOREIGN_KEY_CHECKS=0;"
    wp db query "CALL migrate_tabelas();"
    wp db query "SET FOREIGN_KEY_CHECKS=1;"
ENDSSH

# ===== 10. LIMPAR CACHES =====

log "Limpando caches..."
ssh "$REMOTE_USER@$REMOTE_HOST" << 'ENDSSH'
    cd $WP_PATH
    wp cache flush
    wp transient delete --all
    wp rewrite flush
    wp core cache-flush
ENDSSH

# ===== 11. VERIFICAR INTEGRIDADE =====

log "Verificando integridade..."
ssh "$REMOTE_USER@$REMOTE_HOST" << 'ENDSSH'
    cd $WP_PATH
    wp core verify-checksums || warning "Checksums não verificados"
    wp plugin verify-checksums meu-plugin || warning "Plugin checksums não verificados"
ENDSSH

# ===== 12. DESATIVAR MODO MANUTENÇÃO =====

log "Desativando modo de manutenção..."
ssh "$REMOTE_USER@$REMOTE_HOST" << 'ENDSSH'
    cd /var/www/html
    rm -f .maintenance maintenance.php
ENDSSH

# ===== 13. TESTES PÓS-DEPLOY =====

log "Executando testes pós-deploy..."
if ! ./bin/smoke-tests.sh; then
    warning "Alguns testes pós-deploy falharam"
fi

# ===== 14. MONITORAMENTO =====

log "Ativando monitoramento..."
# Aqui você poderia enviar uma notificação para seu serviço de monitoramento

# ===== SUCESSO =====

log "✅ Deploy concluído com sucesso!"
log "Versão deployada: $(git describe --tags)"
log "Backup armazenado em: $BACKUP_FILE"
info "Monitorar próximas 30 minutos para detectar problemas"
```

---

<a id="blue-green-deploy"></a>
## 🔄 Blue-Green Deploy

### Implementação de Blue-Green

```bash
#!/bin/bash

# bin/deploy-blue-green.sh

set -e

LIVE_DIR="/var/www/live"
BLUE_DIR="/var/www/blue"
GREEN_DIR="/var/www/green"
REPO="origin/main"

log() {
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] $1"
}

# 1. Determinar qual é o ambiente ativo (Blue ou Green)
SYMLINK_TARGET=$(readlink $LIVE_DIR)

if [[ $SYMLINK_TARGET == *"blue"* ]]; then
    ACTIVE="blue"
    INACTIVE="green"
else
    ACTIVE="green"
    INACTIVE="blue"
fi

log "Ambiente ativo: $ACTIVE"
log "Ambiente inativo (será atualizado): $INACTIVE"

# 2. Deploy na versão inativa
log "Fazendo deploy em $INACTIVE..."
INACTIVE_FULL=$(eval echo \$$INACTIVE)

cd $INACTIVE_FULL
git fetch origin
git checkout $REPO
composer install --no-dev
npm install --production
npm run build

# 3. Executar testes
log "Executando testes..."
./vendor/bin/phpunit --coverage-clover coverage.xml

# 4. Database migrations (sem downtime)
log "Executando migrations..."
wp db query "ALTER TABLE tabela ADD COLUMN nova_coluna VARCHAR(255);"

# 5. Smoke tests
log "Executando smoke tests..."
curl -f http://localhost:$INACTIVE_PORT/health || error "Health check falhou"

# 6. Trocar o symlink
log "Alternando tráfego para $INACTIVE..."
ln -sfn $INACTIVE_FULL $LIVE_DIR

log "✅ Deploy de Blue-Green concluído!"
log "Ativo agora: $INACTIVE (era $ACTIVE)"
```

---

<a id="canary-deploy"></a>
## 📊 Canary Deploy

### Implementação de Canary

```bash
#!/bin/bash

# bin/deploy-canary.sh

set -e

SERVERS=(
    "server1.prod.local"
    "server2.prod.local"
    "server3.prod.local"
    "server4.prod.local"
    "server5.prod.local"
)

CANARY_PERCENTAGE=20

log() {
    echo "[$(date)] $1"
}

# 1. Deploy em 20% dos servidores (canary)
log "Iniciando Canary Deploy..."
CANARY_COUNT=$(( ${#SERVERS[@]} * CANARY_PERCENTAGE / 100 ))

log "Deployando em $CANARY_COUNT de ${#SERVERS[@]} servidores"

for i in $(seq 0 $((CANARY_COUNT - 1))); do
    log "Deployando em ${SERVERS[$i]}..."
    ssh deploy@${SERVERS[$i]} 'cd /var/www && ./deploy.sh'
done

# 2. Monitorar canary
log "Monitorando canary por 5 minutos..."
sleep 300

# 3. Verificar métricas
ERROR_RATE=$(curl -s http://monitoring.local/api/error_rate | jq .error_rate)

if (( $(echo "$ERROR_RATE > 1" | bc -l) )); then
    log "❌ Taxa de erro elevada ($ERROR_RATE%). Rollback!"
    for i in $(seq 0 $((CANARY_COUNT - 1))); do
        ssh deploy@${SERVERS[$i]} 'cd /var/www && ./rollback.sh'
    done
    exit 1
fi

log "✅ Canary OK. Expandindo para 100%..."

# 4. Deploy nos servidores restantes
for i in $(seq $CANARY_COUNT $((${#SERVERS[@]} - 1))); do
    log "Deployando em ${SERVERS[$i]}..."
    ssh deploy@${SERVERS[$i]} 'cd /var/www && ./deploy.sh'
    sleep 60  # Aguardar entre deploys
done

log "✅ Deploy Canary concluído com sucesso!"
```

---

<a id="cicd-pipeline"></a>
## 🔄 CI/CD Pipeline

### GitHub Actions Workflow

```yaml
# .github/workflows/deploy.yml

name: Deploy to Production

on:
  push:
    branches:
      - main
  workflow_dispatch:

jobs:
  test:
    runs-on: ubuntu-latest
    
    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_DATABASE: wordpress
          MYSQL_ROOT_PASSWORD: root
        options: >-
          --health-cmd="mysqladmin ping"
          --health-interval=10s
          --health-timeout=5s
          --health-retries=3

    steps:
      - uses: actions/checkout@v3
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: mysqli, curl, gd, mbstring
          tools: composer:v2, phpunit:latest
      
      - name: Install Composer dependencies
        run: composer install --no-interaction
      
      - name: Run PHPUnit tests
        run: ./vendor/bin/phpunit
      
      - name: Run PHP CodeSniffer
        run: ./vendor/bin/phpcs --standard=WordPress includes/
        continue-on-error: true
      
      - name: Install Node dependencies
        run: npm install
      
      - name: Build assets
        run: npm run build
      
      - name: Upload coverage
        uses: codecov/codecov-action@v3
        with:
          files: ./coverage/clover.xml

  deploy:
    needs: test
    runs-on: ubuntu-latest
    if: success()
    
    steps:
      - uses: actions/checkout@v3
      
      - name: Deploy to production
        run: |
          mkdir -p ~/.ssh
          echo "${{ secrets.DEPLOY_KEY }}" > ~/.ssh/deploy_key
          chmod 600 ~/.ssh/deploy_key
          
          ssh-keyscan -H ${{ secrets.DEPLOY_HOST }} >> ~/.ssh/known_hosts
          
          ssh -i ~/.ssh/deploy_key ${{ secrets.DEPLOY_USER }}@${{ secrets.DEPLOY_HOST }} << 'EOF'
            cd /var/www/html/wp-content/plugins/meu-plugin
            git fetch origin main
            git checkout origin/main
            composer install --no-dev
            npm install --production
            npm run build
            wp cache flush
          EOF
      
      - name: Notify Slack
        uses: slackapi/slack-github-action@v1
        with:
          payload: |
            {
              "text": "Deploy realizado: ${{ github.sha }}",
              "blocks": [
                {
                  "type": "section",
                  "text": {
                    "type": "mrkdwn",
                    "text": "✅ Deploy concluído em produção"
                  }
                }
              ]
            }
```

---

<a id="monitoring"></a>
## 📈 Monitoring

### Configurar Monitoramento

```php
<?php
// includes/Monitoring.php

namespace App;

class Monitoring
{
    /**
     * Inicializar monitoramento
     */
    public static function init()
    {
        // Sentry
        if (defined('SENTRY_DSN')) {
            \Sentry\init(['dsn' => SENTRY_DSN]);
        }

        // New Relic
        if (extension_loaded('newrelic')) {
            newrelic_add_custom_tracer('App\Process::executar');
        }

        // Hooks de monitoramento
        add_action('wp_footer', [self::class, 'registra_metricas']);
        add_action('shutdown', [self::class, 'registra_tempo_execucao']);
    }

    /**
     * Registrar métricas
     */
    public static function registra_metricas()
    {
        global $wpdb;
        
        $metricas = [
            'memory_used' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
            'queries' => count($wpdb->queries),
            'cache_hits' => wp_cache_get_stats()['hits'] ?? 0,
            'cache_misses' => wp_cache_get_stats()['misses'] ?? 0,
        ];

        // Enviar para serviço de monitoramento
        do_action('monitoring/metricas', $metricas);
    }

    /**
     * Registrar tempo de execução
     */
    public static function registra_tempo_execucao()
    {
        $tempo = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
        
        if (function_exists('newrelic_add_custom_metric')) {
            newrelic_add_custom_metric('Custom/PageLoadTime', $tempo);
        }
    }
}
```

---

<a id="boas-praticas"></a>
## 🎯 Boas Práticas

### Checklist Final

```markdown
## Testing
- [ ] Escrever testes para novo código
- [ ] Manter coverage > 80%
- [ ] Executar testes localmente antes de commit
- [ ] CI/CD deve passar em cada PR

## Code Quality
- [ ] Seguir WordPress Coding Standards
- [ ] Usar linters (PHPCS, ESLint)
- [ ] Code review antes de merge
- [ ] Documentar código complexo

## Performance
- [ ] Monitorar queries ao banco
- [ ] Implementar caching apropriado
- [ ] Otimizar imagens
- [ ] Minimizar CSS/JS

## Security
- [ ] Validar e sanitizar inputs
- [ ] Escapar outputs
- [ ] Usar prepared statements
- [ ] Verificar capabilities
- [ ] Manter plugins atualizados

## Deploy
- [ ] Ter plano de rollback
- [ ] Testar em staging antes
- [ ] Fazer backup pré-deploy
- [ ] Documentar procedimento
- [ ] Monitorar pós-deploy

## Monitoring
- [ ] Configurar error tracking (Sentry)
- [ ] Alertas para erros críticos
- [ ] Monitorar performance
- [ ] Logs estruturados
- [ ] Dashboards atualizados
```

---

<a id="autoavaliacao"></a>
## 📝 Autoavaliação

Teste seu entendimento:

- [ ] Qual é a diferença entre testes unitários e testes de integração?
- [ ] Como você mocka funções do WordPress em testes PHPUnit?
- [ ] O que é cobertura de código e qual porcentagem você deve buscar?
- [ ] Como você debuga queries do WordPress usando Query Monitor?
- [ ] Qual é a diferença entre deployments blue-green e canary?
- [ ] Como você trata migrações de banco de dados durante deployment?
- [ ] O que deve ser incluído em um checklist de deployment?
- [ ] Como você faz rollback de um deployment se algo der errado?

<a id="projeto-pratico"></a>
## 🛠️ Projeto Prático

**Construir:** Plugin Testado com Pipeline de Deployment

Crie um plugin que:
- Tenha cobertura de testes abrangente (testes unitários + de integração)
- Use mocking para dependências externas
- Inclua ferramentas de debugging e rastreamento de erros
- Tenha pipeline de deployment automatizado
- Inclua procedimentos de rollback
- Siga boas práticas de deployment

**Tempo estimado:** 15-20 horas  
**Dificuldade:** Avançado

---

<a id="equivocos-comuns"></a>
## ❌ Equívocos Comuns

### Equívoco 1: "100% de cobertura de código significa código sem bugs"
**Realidade:** Cobertura de código mede qual código é executado, não se está correto. Você pode ter 100% de cobertura com bugs.

**Por que é importante:** Cobertura é uma métrica, não um objetivo. Foque em testes significativos, não apenas porcentagem de cobertura.

**Como lembrar:** Cobertura = o que executa, não correção. Teste comportamento, não apenas linhas.

### Equívoco 2: "Testes unitários são suficientes"
**Realidade:** Testes unitários verificam componentes individuais. Testes de integração verificam interações entre componentes. Você precisa de ambos.

**Por que é importante:** Testes unitários perdem problemas de integração. Testes de integração capturam problemas do mundo real.

**Como lembrar:** Unitário = isolado. Integração = juntos. Ambos necessários.

### Equívoco 3: "Deployment é apenas copiar arquivos"
**Realidade:** Deployment inclui migrações de banco de dados, limpeza de cache, health checks, planos de rollback e monitoramento.

**Por que é importante:** Tratar deployment como cópia de arquivos leva a downtime, perda de dados e rollbacks difíceis.

**Como lembrar:** Deployment = arquivos + banco de dados + cache + monitoramento + plano de rollback.

### Equívoco 4: "Deployment blue-green é sempre melhor"
**Realidade:** Blue-green requer infraestrutura dupla. Deployments canary podem ser mais econômicos para rollouts graduais.

**Por que é importante:** Escolher a estratégia de deployment certa depende de infraestrutura, tolerância a risco e orçamento.

**Como lembrar:** Blue-green = troca instantânea, infraestrutura dupla. Canary = gradual, infraestrutura única.

---

<a id="recursos-recomendados"></a>
## 📚 Recursos Recomendados

- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [WordPress Testing Handbook](https://developer.wordpress.org/plugins/testing/)
- [GitHub Actions Docs](https://docs.github.com/actions)
- [Sentry Documentation](https://docs.sentry.io/)
- [New Relic PHP Agent](https://docs.newrelic.com/docs/apm/agents/php-agent/)

---

**Versão:** 1.0  
**Status:** Completo  
**Última atualização:** Janeiro 2026  
**Autor:** André | Senior Software Engineer & WordPress Specialist
