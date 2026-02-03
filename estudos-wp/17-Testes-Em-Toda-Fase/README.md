# 17 – Testes em Toda Fase (Testing Throughout)

**Foco: prática.** Integrar testes a cada tópico: setup PHPUnit, hooks, REST API, CPT/Settings, arquitetura, async jobs e boas práticas.

---

## Comece pela prática

| # | Arquivo | O que você vai fazer |
|---|---------|----------------------|
| 1 | [01-phpunit-setup-bootstrap.md](pratica/01-phpunit-setup-bootstrap.md) | Estrutura de pastas, phpunit.xml, bootstrap e comandos |
| 2 | [02-testando-hooks.php](pratica/02-testando-hooks.php) | Testes de actions e filters (prioridade, remoção, múltiplos callbacks) |
| 3 | [03-testando-rest-api.php](pratica/03-testando-rest-api.php) | REST: status, autenticação, validação, sanitização, capabilities |
| 4 | [04-testando-cpt-settings-shortcode.md](pratica/04-testando-cpt-settings-shortcode.md) | Resumo: testes de CPT, Settings API, shortcodes e blocks |
| 5 | [05-testando-arquitetura-async.php](pratica/05-testando-arquitetura-async.php) | Repository/Service com mock; Action Scheduler (agendado, cancelado) |
| 6 | [06-boas-praticas-testes.md](pratica/06-boas-praticas-testes.md) | Nomenclatura, AAA, um assertion, testes independentes, mocking, coverage |
| 7 | [07-resumo-recursos.md](pratica/07-resumo-recursos.md) | Resumo do que foi aprendido e recursos |

**Como usar:** testes de integração exigem WordPress test suite (WP_TESTS_DIR). Unitários com mocks podem rodar só com PHPUnit. Detalhes em [pratica/README.md](pratica/README.md).

---

## Teoria quando precisar

- **No código:** cada `.php` de teste tem no topo um bloco **REFERÊNCIA RÁPIDA**.
- **Uma página:** [REFERENCIA-RAPIDA.md](REFERENCIA-RAPIDA.md) – setup, hooks, REST, CPT, arquitetura, async, boas práticas (Ctrl+F).
- **Fonte completa:** [017-WordPress-Fase-17-Testes-Em-Toda-Fase.md](../../017-WordPress-Fase-17-Testes-Em-Toda-Fase.md) na raiz do repositório.

---

## Objetivos da Fase 17

- Entender por que escrever testes junto com cada tópico (testing throughout)
- Configurar PHPUnit e bootstrap para testes WordPress (unit + integration)
- Testar hooks (actions e filters), REST API (status, auth, validação, sanitização)
- Testar CPT, Settings, shortcodes; arquitetura (Repository, Service, DI) com mocks
- Testar Action Scheduler (agendado, executado, cancelado)
- Aplicar boas práticas: AAA, nomenclatura, testes independentes, mocking e coverage
