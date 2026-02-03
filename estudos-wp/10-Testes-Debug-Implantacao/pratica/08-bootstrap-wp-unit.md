# Bootstrap e WP_UnitTestCase – Integração com WordPress

Resumo: carregar WordPress no PHPUnit para testes de integração. Fonte: **010-WordPress-Fase-10-Testes-Debug-Implantacao.md**.

---

## Bootstrap

- Definir **WP_TESTS_DIR** (caminho para WordPress Develop ou instalação de testes).
- Incluir `includes/functions.php` e `tests/bootstrap.php` (ou `includes/bootstrap.php`).
- Usar `tests_add_filter('muplugins_loaded', function () { require 'plugin.php'; });` para carregar o plugin.

---

## phpunit.xml

```xml
<phpunit bootstrap="tests/bootstrap.php">
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

## WP_UnitTestCase

- Estender **WP_UnitTestCase** quando precisar de `$this->factory->post`, `$this->factory->user`, `get_post()`, `wp_insert_post()`, etc.
- O WordPress é carregado uma vez por suite; limpar dados em `tearDown()` se necessário (truncate, delete posts de teste).

---

## Comando

- Testes unitários (sem WP): `./vendor/bin/phpunit tests/Unit`
- Testes de integração (com WP): exigir `WP_TESTS_DIR` no bootstrap e rodar `./vendor/bin/phpunit tests/Integration`
