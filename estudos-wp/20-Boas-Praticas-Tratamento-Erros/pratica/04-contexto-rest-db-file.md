# Error handling por contexto

Resumo: REST API, operações de banco, operações de arquivo. Fonte: **020-WordPress-Fase-20-Boas-Praticas-Tratamento-Erros.md**.

---

## REST API

- **Padrões:** `WP_Error` para erros esperados; try-catch para exceções; status HTTP adequado (400 validação, 401 não autenticado, 403 sem permissão, 404 não encontrado, 500 erro interno).
- **Não expor detalhes em produção:** retornar mensagem genérica no corpo; nunca `$e->getTraceAsString()` em resposta.
- **Detalhes:** Fase 2 (2.12.1) e Fase 3 (3.6) – error handling completo na REST API.

---

## Database (transações)

```php
global $wpdb;
$wpdb->query('START TRANSACTION');
try {
    $result = $operation();
    $wpdb->query('COMMIT');
    return $result;
} catch (Exception $e) {
    $wpdb->query('ROLLBACK');
    error_log('Transaction failed: ' . $e->getMessage());
    throw new DatabaseException('Database operation failed', 0, $e);
}
```

- Sempre **ROLLBACK** em catch; logar e re-lançar (ou retornar Result).

---

## File operations

- **Antes de ler:** `file_exists()`, `is_readable()`; lançar `FileNotFoundException` ou `FilePermissionException`.
- **Após ler:** `file_get_contents()` pode retornar `false`; checar e lançar `FileReadException`.
- **Antes de escrever:** diretório existe? `wp_mkdir_p()` se necessário; `is_writable($dir)`; lançar exceções específicas.
- **Após escrever:** `file_put_contents(..., LOCK_EX)` retorna `false` em falha; lançar `FileWriteException`.

Exceções sugeridas: `FileNotFoundException`, `FilePermissionException`, `FileReadException`, `FileWriteException`, `DirectoryCreationException`.

---

## Background jobs

- Retry com backoff, Dead Letter Queue, circuit breaker, compensation (Saga); ver **Fase 16**.
