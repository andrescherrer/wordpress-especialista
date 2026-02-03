# Referência rápida – Jobs Assíncronos e Background

Uma página. Use Ctrl+F. Fonte: **016-WordPress-Fase-16-Jobs-Assincronos-Background.md**.

---

## Por que async jobs

- **Problema:** Operações longas (processar imagem, email, API externa) bloqueiam a requisição → timeout, UX ruim.
- **Solução:** Enfileirar com Action Scheduler; responder imediatamente; processar em background.
- **Quando usar:** mídia, emails em bulk, sincronização com API, arquivos grandes, webhooks, operações > ~1s.
- **Quando não:** validações rápidas, operações que precisam de resposta imediata, contexto da requisição HTTP.

---

## WP-Cron – limitações

- **Não é cron real:** dispara em requisições HTTP; sem tráfego = não executa.
- **Produção:** definir `DISABLE_WP_CRON`, true e usar cron do sistema (`*/15 * * * * curl ... wp-cron.php`).
- **Ainda assim:** não adequado para async one-time, retry, prioridade, DLQ. Preferir Action Scheduler.

---

## Action Scheduler

- **Instalação:** `composer require woocommerce/action-scheduler` ou plugin.
- **Async (one-time, imediato):** `as_enqueue_async_action('hook', [$arg1, $arg2], 'group');`
- **Single (com delay):** `as_schedule_single_action(time() + 30 * MINUTE_IN_SECONDS, 'hook', [$arg], 'group');`
- **Recurring:** `as_schedule_recurring_action(time(), 6 * HOUR_IN_SECONDS, 'hook', [$arg], 'group');` — verificar antes com `as_has_scheduled_action('hook', $args, 'group')`.
- **Cancelar:** `as_unschedule_action('hook', $args, 'group');` ou por action_id.
- **Handler:** `add_action('hook', callable, 10, N);` com mesmo número de argumentos.
- **Status/monitoramento:** Action Scheduler armazena ações na tabela; interface em Tools > Scheduled Actions (se disponível).

---

## Queue patterns

- **Simple (FIFO):** jobs em option/DB; `as_enqueue_async_action('process_queue_job', ['queue_name','job_id'])`; handler processa um job por vez; status pending/processing/completed/failed.
- **Priority:** job com campo priority; ao processar, ordenar por prioridade (maior primeiro).
- **Dead Letter Queue (DLQ):** jobs que falharam após máximo de retries; mover para tabela/option separada; análise e reprocessamento manual; não é para retry automático.

---

## Webhook receiver

- **Verificar assinatura:** HMAC-SHA256 do body com secret; comparar com `hash_equals(expected, header)` (constant-time).
- **Idempotency key:** header X-Idempotency-Key; se já processado (option/transient), retornar 200 com mesmo resultado; senão enfileirar e retornar 202 Accepted.
- **Resposta:** 202 + webhook_id/status; processar em handler assíncrono (`as_enqueue_async_action('process_webhook', [...])`); nunca processar no request.
- **Rota REST:** POST; permission_callback pode ser `__return_true` (autenticação via assinatura).

---

## Error handling

- **Retry com exponential backoff:** delay = BASE_DELAY * 2^(attempt-1); reagendar com `as_schedule_single_action(time() + $delay, 'hook', [...])`; após MAX_RETRIES mover para DLQ.
- **TransientException vs PermanentException:** retentar só erros temporários; permanentes vão direto para DLQ.
- **Circuit breaker:** após N falhas, abrir (não chamar serviço); após timeout, half-open; sucesso fecha.
- **Compensação:** em falha, executar ações inversas (release inventory, refund) na ordem reversa.

---

## Docker e monitoramento

- **Workers:** container que roda WP-CLI ou script PHP que dispara Action Scheduler (ou cron que chama wp-cron.php).
- **Supervisord:** manter N processos worker; restart on exit.
- **Health check:** endpoint ou comando que verifica se fila está sendo processada / não travada.
- **Prometheus/Grafana:** métricas (tamanho da fila, jobs concluídos/falhados, latência); alertas.
