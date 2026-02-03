# 09 – WP-CLI Ferramentas

**Foco: prática.** Automação, subcomandos avançados, migrations, scaffolding e CI/CD.

---

## Comece pela prática

| # | Arquivo | O que você vai fazer |
|---|---------|----------------------|
| 1 | [01-comando-avancado.php](pratica/01-comando-avancado.php) | cleanup, process-queue, check-integrity, repair (progress bar, dry-run) |
| 2 | [02-subcomando-db.php](pratica/02-subcomando-db.php) | Subcomando `db`: init, export, reset (dbDelta, CSV/JSON) |
| 3 | [03-import-wizard.php](pratica/03-import-wizard.php) | Wizard de importação interativo (CSV/JSON) com prompt e progress |
| 4 | [04-scaffold-module.php](pratica/04-scaffold-module.php) | Gerar módulos: class, service, controller (scaffolding) |
| 5 | [05-migrations.php](pratica/05-migrations.php) | migrate, rollback, list (sistema de migrations) |
| 6 | [06-debug-report.php](pratica/06-debug-report.php) | debug report, performance, clear (ferramentas de debug) |
| 7 | [07-automacao-boas-praticas.md](pratica/07-automacao-boas-praticas.md) | Scripts de deploy, CI/CD (GitHub Actions), documentação e erros |
| 8 | [08-progress-bar.php](pratica/08-progress-bar.php) | Progress bar (make_progress_bar, tick, finish) |
| 9 | [09-db-export-import.md](pratica/09-db-export-import.md) | db export/import no deploy |
| 10 | [10-subcomandos-tabela.md](pratica/10-subcomandos-tabela.md) | Tabela subcomandos (list, get, create, delete) |

**Como usar:** copie as classes para seu plugin; carregue apenas quando `defined('WP_CLI') && WP_CLI`. Requer Fase 7 (fundamentos). Detalhes em [pratica/README.md](pratica/README.md).

---

## Teoria quando precisar

- **No código:** cada `.php` tem no topo um bloco **REFERÊNCIA RÁPIDA**.
- **Uma página:** [REFERENCIA-RAPIDA.md](REFERENCIA-RAPIDA.md) – subcomandos, migrations, scaffolding, debug (Ctrl+F).
- **Fonte completa:** [009-WordPress-Fase-9-WP-CLI-Ferramentas.md](../../009-WordPress-Fase-9-WP-CLI-Ferramentas.md) na raiz do repositório.

---

## Objetivos da Fase 9

- Criar comandos avançados com process-queue, check-integrity e repair (progress bar, try/catch)
- Implementar subcomando `db`: init (dbDelta), export (JSON/CSV), reset com confirmação
- Construir wizard de importação interativo (prompt, validação, progress)
- Gerar código com scaffolding (module class/service/controller)
- Implementar migrations (migrate, rollback, list) com opção de versionamento
- Oferecer ferramentas de debug (relatório, performance, limpeza de logs)
- Integrar WP-CLI em scripts de deploy e CI/CD (GitHub Actions)
