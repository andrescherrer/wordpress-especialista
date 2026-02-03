# Error handling e retry em async jobs

Fonte: **016-WordPress-Fase-16-Jobs-Assincronos-Background.md** (Error Handling).

---

## Retry com exponential backoff

- **Fórmula:** `delay = BASE_DELAY * 2^(attempt - 1)` (ex.: 60s, 120s, 240s).
- **Fluxo:** No handler, try/catch; em erro temporário (TransientException), se attempt < MAX_RETRIES reagendar com `as_schedule_single_action(time() + $delay, 'hook', [..., 'attempt' => $attempt + 1], 'group')`; senão mover para DLQ.
- **Erro permanente (PermanentException):** Não retentar; mover para DLQ ou logar e descartar.
- **Erro inesperado:** Logar; mover para DLQ ou reagendar conforme política.

---

## Dead Letter Queue (DLQ)

- **O que é:** Armazenar jobs que falharam após todas as tentativas (ou falha permanente).
- **Uso:** Análise de falhas, reprocessamento manual, auditoria. Não é para retry automático.
- **Implementação:** Tabela ou option separada; ao falhar definitivamente, gravar job + payload + mensagem de erro + timestamp; interface ou WP-CLI para listar/reprocessar.

---

## Circuit breaker

- **Objetivo:** Evitar sobrecarga em serviço externo que está falhando.
- **Estados:** closed (chamadas normais) → após N falhas → open (não chama, falha rápido) → após timeout → half-open (poucas chamadas de teste) → sucesso fecha, falha reabre.
- **Uso no job:** Antes de chamar API externa, verificar estado; se open, reagendar job para mais tarde (ex.: 5 min) em vez de tentar agora.

---

## Compensação (saga-style)

- Se o job faz várias etapas (reservar estoque, cobrar, enviar email) e uma falha, executar ações inversas na ordem reversa (liberar estoque, reembolsar). Manter “compensation stack” no job e em falha executar cada passo de compensação.
