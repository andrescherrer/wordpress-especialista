# Referência rápida – Boas Práticas e Tratamento de Erros

Uma página. Use Ctrl+F. Fonte: **020-WordPress-Fase-20-Boas-Praticas-Tratamento-Erros.md**.

---

## Princípios fundamentais

- **Fail fast, fail loud:** detectar e reportar erros o mais cedo possível; não retornar valor “vazio” escondendo falha.
- **Never swallow exceptions:** nunca `catch` sem logar, notificar ou re-lançar; tratar ou re-lançar.
- **Use appropriate error types:** exceções específicas do domínio (ValidationException, NotFoundException, PermissionException, BusinessRuleException) em vez de Exception genérica.

---

## Padrões de error handling

- **Try-catch com contexto:** incluir order_id, user_id, timestamp no contexto; logar por tipo (ValidationException → warning, PaymentException → error, Exception → critical); retornar Result ou re-lançar.
- **Result object:** Result::success($data), Result::failure($exception, $data); isSuccess(), isFailure(), getData(), getError(), getOrThrow().
- **Error handler centralizado:** set_error_handler (PHP errors), set_exception_handler (exceções não capturadas), register_shutdown_function (fatal); logar + opcional Sentry; em produção não expor trace.

---

## Error handling por contexto

- **REST API:** WP_Error, try-catch, status HTTP adequado (400, 401, 403, 404, 500); ver Fase 2 e 3.
- **Background jobs:** retry, DLQ, circuit breaker, compensation; ver Fase 16.
- **Database:** transações (START TRANSACTION / COMMIT / ROLLBACK); em catch fazer ROLLBACK e re-lançar ou retornar Result.
- **File:** verificar exists, is_readable/is_writable; file_get_contents/file_put_contents com checagem de false; exceções específicas (FileNotFoundException, FilePermissionException, FileReadException).

---

## Logging e monitoramento

- **Structured logging:** nível (debug, info, warning, error, critical), mensagem, contexto (IDs, user_id, request_uri, IP), timestamp; error_log(json_encode($entry)); integração Sentry (captureMessage/captureException) opcional.
- **Contexto suficiente:** sempre incluir IDs (order_id, user_id) e mensagem do erro para reprodução.

---

## Error recovery strategies

- **Retry logic:** operações transientes; ver Fase 3.
- **Fallback:** operação alternativa quando primária falha; ver Fase 3.
- **Compensation (Saga):** desfazer efeitos em caso de falha em fluxo distribuído; ver Fase 16.

---

## Best practices (DO)

1. Sempre logar erros com contexto suficiente (IDs, user, mensagem).
2. Usar tipos específicos de exceção (ValidationException, etc.).
3. Tratar erros no nível apropriado (Controller → validação; Service → negócio; Repository → dados).
4. Limpar recursos em `finally` (fclose, etc.).
5. Documentar exceções em PHPDoc: `@throws ValidationException`, `@throws DatabaseException`.

---

## Don'ts

1. Não ignorar exceções silenciosamente (catch vazio).
2. Não expor detalhes internos em produção (trace, stack).
3. Não usar exceções para controle de fluxo (preferir verificação explícita).
4. Não capturar Exception genérica sem re-lançar após log.
5. Não misturar WP_Error e Exceptions no mesmo fluxo; escolher um padrão.

---

## Checklist de error handling

- [ ] Todos os erros logados com contexto suficiente
- [ ] Exceções específicas (não Exception genérica)
- [ ] Erros tratados no nível apropriado
- [ ] Recursos limpos em finally
- [ ] Exceções documentadas em PHPDoc
- [ ] Produção: não expor detalhes internos
- [ ] Retry para erros temporários; DLQ para permanentes
- [ ] Monitoramento configurado (Sentry, etc.)
- [ ] Error handlers centralizados registrados
