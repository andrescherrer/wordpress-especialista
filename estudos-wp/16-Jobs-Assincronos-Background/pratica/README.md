# Prática – Como usar (Jobs Assíncronos e Background)

1. **Action Scheduler:** Use `as_enqueue_async_action` para trabalho imediato em background; `as_schedule_single_action` para delay; `as_schedule_recurring_action` para recorrente (evite duplicar com `as_has_scheduled_action`). Sempre registrar o handler com `add_action`.
2. **Filas:** Implemente FIFO ou prioridade em cima do Action Scheduler (job payload em option; handler processa um item por ação). Após N falhas, mova para DLQ para análise.
3. **Webhooks:** Valide assinatura HMAC; use X-Idempotency-Key para evitar processamento duplicado; responda 202 e processe em async.
4. **Erros:** Retry com exponential backoff; distinga erros temporários (retentar) de permanentes (DLQ); considere circuit breaker para APIs externas.
5. **WP-Cron:** Em produção use `DISABLE_WP_CRON` e cron real; para jobs assíncronos e filas use Action Scheduler.

**Teoria rápida:** no topo de cada `.php` há **REFERÊNCIA RÁPIDA**; uma página: [../REFERENCIA-RAPIDA.md](../REFERENCIA-RAPIDA.md).
