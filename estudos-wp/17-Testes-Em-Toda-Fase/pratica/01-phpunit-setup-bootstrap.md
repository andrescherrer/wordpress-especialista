# Setup PHPUnit e bootstrap

Fonte: **017-WordPress-Fase-17-Testes-Em-Toda-Fase.md** (Setup Básico de Testes).

---

## Instalação

```bash
composer require --dev phpunit/phpunit ^11.0
./vendor/bin/phpunit --version
```

---

## Estrutura de diretórios

```
plugin-name/
├── src/
├── tests/
│   ├── bootstrap.php
│   ├── Unit/
│   │   └── HookSystemTest.php
│   └── Integration/
│       └── RestApiTest.php
└── phpunit.xml
```

---

## phpunit.xml (exemplo)

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/11.0/phpunit.xsd"
         bootstrap="tests/bootstrap.php"
         colors="true">
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Integration">
            <directory>tests/Integration</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

---

## tests/bootstrap.php (WordPress)

Para testes de integração que usam WordPress (WP_UnitTestCase, rest_get_server, factory):

- Definir `WP_TESTS_DIR` (ambiente ou phpunit.xml).
- `require getenv('WP_TESTS_DIR') . '/includes/functions.php';`
- `tests_add_filter('muplugins_loaded', function() { require dirname(__DIR__) . '/plugin-name.php'; });`
- `require getenv('WP_TESTS_DIR') . '/includes/bootstrap.php';`

Opcional: `WP_TESTS_PHPUNIT_POLYFILLS_PATH` para polyfills do Yoast.

---

## Comandos

```bash
# Todos os testes
./vendor/bin/phpunit

# Apenas unit
./vendor/bin/phpunit tests/Unit

# Um arquivo
./vendor/bin/phpunit tests/Unit/HookSystemTest.php

# Com cobertura
./vendor/bin/phpunit --coverage-html coverage/
```

Testes que estendem WP_UnitTestCase precisam da WordPress test suite instalada (WP_TESTS_DIR apontando para o clone de desenvolvimento do core com testes).
