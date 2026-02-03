# 16 – Jobs Assíncronos e Background (Async Jobs, Queues)

**Foco: prática.** Action Scheduler, filas (Simple/Priority/DLQ), webhooks com idempotência, Docker workers, error handling e monitoramento.

---

## Comece pela prática

| # | Arquivo | O que você vai fazer |
|---|---------|----------------------|
| 1 | [01-action-scheduler-basico.php](pratica/01-action-scheduler-basico.php) | Async, single e recurring actions; grupo; handler; verificar/cancelar |
| 2 | [02-queue-simple-priority.php](pratica/02-queue-simple-priority.php) | Fila FIFO com Action Scheduler; prioridade; retry e falha |
| 3 | [03-webhook-idempotency.php](pratica/03-webhook-idempotency.php) | Receiver com HMAC, idempotency key, enqueue e resposta 202 |
| 4 | [04-docker-monitoring.md](pratica/04-docker-monitoring.md) | Workers em Docker, Supervisord, health checks, Prometheus/Grafana |
| 5 | [05-error-handling-retry.md](pratica/05-error-handling-retry.md) | Retry com exponential backoff, DLQ, circuit breaker, compensação |
| 6 | [06-wpcron-dlq-equívocos.md](pratica/06-wpcron-dlq-equívocos.md) | Limitações do WP-Cron, DISABLE_WP_CRON, equívocos comuns |
| 7 | [07-boas-praticas.md](pratica/07-boas-praticas.md) | Resumo, próximos passos e recursos |
| 8 | [08-recurring-cancel.php](pratica/08-recurring-cancel.php) | Recurring (as_schedule_recurring_action) e cancelar série |
| 9 | [09-retry-no-callback.php](pratica/09-retry-no-callback.php) | Retry manual no callback (tentativas, backoff) |
| 10 | [10-webhook-receiver.php](pratica/10-webhook-receiver.php) | Webhook: assinatura HMAC e idempotência (X-Idempotency-Key) |

**Como usar:** requer Action Scheduler (`composer require woocommerce/action-scheduler` ou plugin). Copie classes/trechos para seu plugin; adapte hooks e grupos. Detalhes em [pratica/README.md](pratica/README.md).

---

## Teoria quando precisar

- **No código:** cada `.php` tem no topo um bloco **REFERÊNCIA RÁPIDA**.
- **Uma página:** [REFERENCIA-RAPIDA.md](REFERENCIA-RAPIDA.md) – Action Scheduler, filas, webhooks, DLQ, error handling (Ctrl+F).
- **Fonte completa:** [016-WordPress-Fase-16-Jobs-Assincronos-Background.md](../../016-WordPress-Fase-16-Jobs-Assincronos-Background.md) na raiz do repositório.

---

## Objetivos da Fase 16

- Entender quando usar async jobs (evitar bloqueio em requisições HTTP)
- Substituir WP-Cron por Action Scheduler para jobs em produção
- Implementar padrões de fila (Simple FIFO, Priority, Dead Letter Queue)
- Receber webhooks com verificação de assinatura e idempotency keys
- Configurar workers em Docker e monitorar filas (health, métricas)
- Tratar falhas com retry, exponential backoff e DLQ
