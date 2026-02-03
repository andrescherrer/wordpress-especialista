# 20 – Boas Práticas e Tratamento de Erros (Error Handling)

**Foco: prática.** Princípios (fail fast, never swallow, tipos de exceção), padrões (try-catch com contexto, Result object, handler centralizado), contexto (REST, DB, arquivos), logging estruturado e checklist.

---

## Comece pela prática

| # | Arquivo | O que você vai fazer |
|---|---------|----------------------|
| 1 | [01-principios-fail-fast-excecoes.php](pratica/01-principios-fail-fast-excecoes.php) | Fail fast, never swallow exceptions, exceções específicas do domínio |
| 2 | [02-padroes-try-catch-result.php](pratica/02-padroes-try-catch-result.php) | Try-catch com contexto, Result object, tratamento por tipo de exceção |
| 3 | [03-error-handler-centralizado.php](pratica/03-error-handler-centralizado.php) | set_error_handler, set_exception_handler, register_shutdown_function |
| 4 | [04-contexto-rest-db-file.md](pratica/04-contexto-rest-db-file.md) | Error handling por contexto: REST API, DB (transações), operações de arquivo |
| 5 | [05-logging-estruturado.php](pratica/05-logging-estruturado.php) | Structured logging com contexto, níveis e integração com monitoramento |
| 6 | [06-best-practices-checklist.md](pratica/06-best-practices-checklist.md) | DO/DON'T e checklist de error handling |
| 7 | [07-resumo-recursos.md](pratica/07-resumo-recursos.md) | Resumo e links para Fase 2, 3, 13, 16 |
| 8 | [08-wp-error-rest.php](pratica/08-wp-error-rest.php) | WP_Error em REST + try/catch convertendo para WP_Error |
| 9 | [09-finally-recursos.php](pratica/09-finally-recursos.php) | finally (fechar handle, limpar recurso) em try/catch |
| 10 | [10-log-contexto-exemplo.php](pratica/10-log-contexto-exemplo.php) | Log com contexto (order_id, user_id) em JSON |

**Como usar:** execute os trechos PHP em ambiente de desenvolvimento (WP_DEBUG); use os .md para revisão e checklist. Detalhes em [pratica/README.md](pratica/README.md).

---

## Teoria quando precisar

- **No código:** cada `.php` tem no topo um bloco **REFERÊNCIA RÁPIDA**.
- **Uma página:** [REFERENCIA-RAPIDA.md](REFERENCIA-RAPIDA.md) – princípios, padrões, contexto, logging, recovery, checklist (Ctrl+F).
- **Fonte completa:** [020-WordPress-Fase-20-Boas-Praticas-Tratamento-Erros.md](../../020-WordPress-Fase-20-Boas-Praticas-Tratamento-Erros.md) na raiz do repositório.

---

## Objetivos da Fase 20

- Aplicar princípios: fail fast, never swallow exceptions, exceções específicas
- Usar padrões: try-catch com contexto, Result object, error handler centralizado
- Tratar erros por contexto (REST, DB, arquivos) e logar com contexto suficiente
- Conhecer estratégias de recovery (retry, fallback, compensation) e checklist de revisão
