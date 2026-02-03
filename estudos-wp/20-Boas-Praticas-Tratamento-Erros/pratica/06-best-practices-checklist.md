# Best practices e checklist – Error handling

Fonte: **020-WordPress-Fase-20-Boas-Praticas-Tratamento-Erros.md**.

---

## DO

1. **Sempre logue erros com contexto suficiente**
   - Ex.: `order_id`, `user_id`, mensagem da exceção.

2. **Use tipos específicos de exceção**
   - `ValidationException`, `NotFoundException`, `PermissionException` em vez de `Exception` genérica.

3. **Trate erros no nível apropriado**
   - Controller: validação / resposta HTTP.
   - Service: regras de negócio.
   - Repository: falhas de dados.

4. **Sempre limpe recursos em `finally`**
   - Ex.: `fclose($handle)` em bloco `finally` após `fopen`.

5. **Documente exceções em PHPDoc**
   - `@throws ValidationException Se dados inválidos`
   - `@throws DatabaseException Se erro de banco`

---

## DON'T

1. **Não ignore exceções silenciosamente** (catch vazio).

2. **Não exponha detalhes internos em produção**
   - Não retornar `$e->getTraceAsString()` ou stack em resposta/HTML.

3. **Não use exceções para controle de fluxo**
   - Preferir verificação explícita (ex.: valor nulo ou flag) em vez de try/catch para fluxo normal.

4. **Não capture Exception genérica sem re-lançar**
   - Após logar, fazer `throw $e` para o caller poder tratar.

5. **Não misture WP_Error e Exceptions no mesmo fluxo**
   - Escolher um padrão (só WP_Error ou só Exceptions) e manter consistência.

---

## Checklist de error handling

- [ ] Todos os erros são logados com contexto suficiente
- [ ] Exceções específicas são usadas (não Exception genérica)
- [ ] Erros são tratados no nível apropriado
- [ ] Recursos são limpos em finally blocks
- [ ] Exceções são documentadas em PHPDoc
- [ ] Erros não expõem detalhes internos em produção
- [ ] Retry logic é usado para erros temporários
- [ ] Dead Letter Queue é usado para erros permanentes (async)
- [ ] Monitoramento está configurado (Sentry, etc.)
- [ ] Error handlers centralizados estão registrados
