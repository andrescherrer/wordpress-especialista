# Prática – Como usar (Boas Práticas e Tratamento de Erros)

1. **Princípios:** Use os exemplos de fail fast (lançar em vez de retornar vazio), never swallow (logar + re-lançar) e exceções específicas (ValidationException, NotFoundException, PermissionException).
2. **Padrões:** Try-catch com array de contexto (order_id, user_id); Result::success/failure; tratar por tipo de exceção e retornar Result ou re-lançar.
3. **Handler centralizado:** Registrar no bootstrap do plugin: set_error_handler, set_exception_handler, register_shutdown_function; em produção não exibir trace.
4. **Contexto:** REST (WP_Error + status HTTP); DB (transações + ROLLBACK em catch); arquivos (exists, readable/writable, checagem de false).
5. **Logging:** Logger com nível, mensagem e contexto (IDs, user, URI); error_log + opcional Sentry.
6. **Checklist:** Revise código com a lista DO/DON'T e o checklist antes de merge.
7. **Recursos:** Consulte Fase 2 (REST errors), 3 (retry/fallback), 13 (arquitetura), 16 (async jobs).

**Teoria rápida:** [../REFERENCIA-RAPIDA.md](../REFERENCIA-RAPIDA.md).
