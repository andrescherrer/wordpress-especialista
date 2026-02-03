# 10 – Testes, Debug e Implantação

**Foco: prática.** PHPUnit, testes unitários e de integração, mocking, debug e deploy.

---

## Comece pela prática

| # | Arquivo | O que você vai fazer |
|---|---------|----------------------|
| 1 | [01-phpunit-config.md](pratica/01-phpunit-config.md) | Configuração: phpunit.xml, bootstrap.php, composer |
| 2 | [02-unit-test-basico.php](pratica/02-unit-test-basico.php) | Teste unitário simples: assertEquals, assertTrue, expectException |
| 3 | [03-testes-classe-service.php](pratica/03-testes-classe-service.php) | Teste de classe (setUp, instância, vários métodos) |
| 4 | [04-mocking-stubs.php](pratica/04-mocking-stubs.php) | createMock, expects/with; createStub, willReturn |
| 5 | [05-data-providers.php](pratica/05-data-providers.php) | @dataProvider com array de casos de teste |
| 6 | [06-integration-debug-deploy.md](pratica/06-integration-debug-deploy.md) | Integração, Query Monitor, Sentry, checklist e scripts de deploy |
| 7 | [07-boas-praticas.md](pratica/07-boas-praticas.md) | Checklist: testes, qualidade, deploy, monitoramento |
| 8 | [08-bootstrap-wp-unit.md](pratica/08-bootstrap-wp-unit.md) | Bootstrap e WP_UnitTestCase (integração WordPress) |
| 9 | [09-testes-hooks.php](pratica/09-testes-hooks.php) | Testar action e filter (assert chamada, valor) |
| 10 | [10-debug-ferramentas.md](pratica/10-debug-ferramentas.md) | WP_DEBUG, SCRIPT_DEBUG, Query Monitor |

**Como usar:** arquivos `.php` de teste vão em `tests/Unit/` (ou `tests/`) do seu plugin e são executados com `./vendor/bin/phpunit`. Detalhes em [pratica/README.md](pratica/README.md).

---

## Teoria quando precisar

- **No código:** cada arquivo de teste tem no topo um bloco **REFERÊNCIA RÁPIDA**.
- **Uma página:** [REFERENCIA-RAPIDA.md](REFERENCIA-RAPIDA.md) – PHPUnit, asserts, mock, data provider, coverage, deploy (Ctrl+F).
- **Fonte completa:** [010-WordPress-Fase-10-Testes-Debug-Implantacao.md](../../010-WordPress-Fase-10-Testes-Debug-Implantacao.md) na raiz do repositório.

---

## Objetivos da Fase 10

- Configurar PHPUnit (phpunit.xml, bootstrap) e rodar testes com Composer
- Escrever testes unitários (asserts, expectException) e testes de classes (setUp)
- Usar mocking (createMock, expects/with) e stubs (createStub, willReturn)
- Aplicar data providers para múltiplos casos
- Conhecer testes de integração, Query Monitor e Sentry
- Aplicar checklist de deploy e referências de scripts/CI/CD
