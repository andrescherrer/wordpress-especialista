# Configuração PHPUnit – phpunit.xml e bootstrap

Referência. Fonte: **010-WordPress-Fase-10-Testes-Debug-Implantacao.md**. Coloque na **raiz do plugin**.

---

## Instalação

```bash
composer require --dev phpunit/phpunit ^11.0
./vendor/bin/phpunit --version
```

---

## phpunit.xml (exemplo)

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/11.0/phpunit.xsd"
         bootstrap="tests/bootstrap.php"
         cacheDirectory=".phpunit.cache"
         colors="true">
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Integration">
            <directory>tests/Integration</directory>
        </testsuite>
    </testsuites>

    <coverage processUncoveredFiles="true">
        <include>
            <directory suffix=".php">includes</directory>
        </include>
        <exclude>
            <directory>vendor</directory>
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
        <const name="ABSPATH" value="/caminho/para/wordpress/"/>
    </php>
</phpunit>
```

Ajuste `ABSPATH` para o caminho real do WordPress (ou use variável de ambiente no CI).

---

## tests/bootstrap.php (exemplo)

```php
<?php
/**
 * Bootstrap para PHPUnit
 */

define( 'ABSPATH', getenv( 'WP_ROOT' ) ?: '/var/www/html/' );
define( 'WP_PLUGIN_DIR', ABSPATH . 'wp-content/plugins/' );

// Autoload do Composer
require_once dirname( __DIR__ ) . '/vendor/autoload.php';

// Opcional: carregar WordPress (para testes de integração)
// require_once ABSPATH . 'wp-load.php';
// wp_cache_flush();
```

Para testes **unitários puros** (sem WP), não é obrigatório carregar `wp-load.php`. Para **integração**, descomente e ajuste o caminho.

---

## Comandos úteis

```bash
# Todos os testes
./vendor/bin/phpunit

# Apenas Unit
./vendor/bin/phpunit tests/Unit

# Um arquivo
./vendor/bin/phpunit tests/Unit/MeuTesteTest.php

# Com cobertura HTML
./vendor/bin/phpunit --coverage-html coverage

# Clover (CI)
./vendor/bin/phpunit --coverage-clover coverage.xml
```
