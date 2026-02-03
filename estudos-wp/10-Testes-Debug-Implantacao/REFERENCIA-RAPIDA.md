# Referência rápida – Testes, Debug e Implantação

Uma página. Use Ctrl+F. Fonte: **010-WordPress-Fase-10-Testes-Debug-Implantacao.md**.

---

## PHPUnit

- **Instalar:** `composer require --dev phpunit/phpunit ^11`
- **Rodar:** `./vendor/bin/phpunit` ou `./vendor/bin/phpunit tests/Unit`
- **phpunit.xml:** bootstrap, testsuites (Unit, Integration, E2E), coverage (include/exclude), php/const (ABSPATH, etc.)
- **Bootstrap:** definir ABSPATH, WP_PLUGIN_DIR; opcionalmente require wp-load.php e vendor/autoload; wp_cache_flush para testes limpos

---

## Testes unitários

- **Estender:** `PHPUnit\Framework\TestCase`
- **Método de teste:** `public function testAlgo()` ou qualquer método com `@test` no docblock
- **Asserts:** `assertEquals( $esperado, $real )`, `assertTrue( $cond )`, `assertFalse`, `assertSame`, `assertInstanceOf( Class, $obj )`, `assertCount( $n, $array )`
- **Exceção:** `$this->expectException( \InvalidArgumentException::class );` antes do código que deve lançar
- **setUp():** `protected function setUp(): void { parent::setUp(); ... }` – roda antes de cada teste

---

## Mock e Stub

- **Mock:** `$mock = $this->createMock( Classe::class );`  
  `$mock->expects( $this->once() )->method( 'nome' )->with( $arg1, $arg2 );` – verifica chamada e argumentos
- **Stub:** `$stub = $this->createStub( Classe::class );`  
  `$stub->method( 'nome' )->willReturn( $valor );` – retorna valor fixo sem verificar chamadas
- **willReturnOnConsecutiveCalls( $a, $b, $c )** – retornos diferentes por chamada
- **willThrowException( new \Exception() )** – simular falha

---

## Data Providers

- **@dataProvider nomeMetodo** no docblock do teste; método estático que retorna array de argumentos
- **Formato:** `return [ 'cenário 1' => [ $arg1, $arg2 ], 'cenário 2' => [ ... ], ];`
- O teste recebe os argumentos na ordem: `public function testAlgo( $arg1, $arg2 ) { ... }`

---

## Code Coverage

- **Gerar HTML:** `./vendor/bin/phpunit --coverage-html coverage`
- **Clover (CI):** `./vendor/bin/phpunit --coverage-clover coverage.xml`
- **Terminal:** `./vendor/bin/phpunit --coverage-text`
- No phpunit.xml: `<coverage><include>`, `<exclude>`, `<report>` (html, clover, text)

---

## Integração, Query Monitor, Sentry

- **Integração:** testes que usam WP (wp_insert_user, get_user_by, $wpdb); setUp com limpeza; às vezes WP_UnitTestCase do WordPress
- **Query Monitor:** `do_action( 'qm/debug', $mensagem );` para aparecer na barra do plugin
- **Sentry:** `Sentry\init( [ 'dsn' => '...', 'environment' => ... ] );`; `captureException( $e );`; `captureMessage( $msg, Level::Info );`

---

## Deploy

- **Checklist:** testes passando, backup (DB + arquivos), modo manutenção, migrations, cache flush, health check, rollback documentado
- **Script:** set -e; testes; backup remoto; rsync/ssh; composer install --no-dev; wp cache flush; migrations
- **CI/CD:** job test (PHP, MySQL, composer, phpunit, phpcs); job deploy (se test ok); secrets para SSH/key
- **Blue-Green:** dois ambientes; deploy no inativo; trocar symlink/tráfego
- **Canary:** deploy em % dos servidores; monitorar; expandir ou rollback
