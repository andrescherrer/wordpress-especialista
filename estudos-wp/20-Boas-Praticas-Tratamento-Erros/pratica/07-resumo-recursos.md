# Resumo e recursos – Boas Práticas e Tratamento de Erros

---

## Resumo da Fase 20

- **Princípios:** Fail fast (reportar cedo, não esconder com valor vazio), never swallow (logar/notificar/re-lançar), exceções específicas do domínio.
- **Padrões:** Try-catch com contexto (IDs, user, timestamp); Result object (success/failure); error handler centralizado (set_error_handler, set_exception_handler, shutdown).
- **Contexto:** REST (WP_Error + status HTTP); DB (transações + ROLLBACK); arquivos (exists, readable/writable, checagem de retorno); async jobs (retry, DLQ, compensation).
- **Logging:** Estruturado com nível, mensagem e contexto; integração opcional com Sentry.
- **Checklist:** Log com contexto, exceções específicas, tratamento no nível certo, finally para recursos, PHPDoc, sem expor detalhes em prod, retry/DLQ, monitoramento, handlers registrados.

---

## Recursos adicionais (material do curso)

- **Fase 2 – REST API Error Handling:** [002-WordPress-Fase-2-REST-API-Fundamentos.md](../../002-WordPress-Fase-2-REST-API-Fundamentos.md) – seção 2.12.1 (error handling patterns).
- **Fase 3 – Advanced Error Handling:** [003-WordPress-Fase-3-REST-API-Avancado.md](../../003-WordPress-Fase-3-REST-API-Avancado.md) – retry logic, fallbacks.
- **Fase 13 – Architecture Error Handling:** [013-WordPress-Fase-13-Arquitetura-Avancada.md](../../013-WordPress-Fase-13-Arquitetura-Avancada.md) – error handling em arquitetura avançada.
- **Fase 16 – Async Jobs Error Handling:** [016-WordPress-Fase-16-Jobs-Assincronos-Background.md](../../016-WordPress-Fase-16-Jobs-Assincronos-Background.md) – retry, DLQ, circuit breaker, compensation.

---

## Arquivos desta pasta

| Arquivo | Conteúdo |
|--------|----------|
| 01-principios-fail-fast-excecoes.php | Fail fast, never swallow, exceções específicas |
| 02-padroes-try-catch-result.php | Result object, try-catch com contexto |
| 03-error-handler-centralizado.php | set_error_handler, set_exception_handler, shutdown |
| 04-contexto-rest-db-file.md | REST, DB (transações), arquivos |
| 05-logging-estruturado.php | StructuredLogger com contexto e níveis |
| 06-best-practices-checklist.md | DO/DON'T e checklist |
| 07-resumo-recursos.md | Este arquivo |
