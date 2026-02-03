# Boas práticas – Jobs assíncronos e background

Fonte: **016-WordPress-Fase-16-Jobs-Assincronos-Background.md** (Resumo e Próximos Passos).

---

## O que você aprendeu

- **Por que async jobs** – Evitar bloqueio em requisições HTTP; resposta rápida e processamento em background.
- **Limitações do WP-Cron** – Depende de tráfego; em produção usar `DISABLE_WP_CRON` e cron real; para filas usar Action Scheduler.
- **Action Scheduler** – Async, single e recurring; grupos; verificar e cancelar; adequado para produção.
- **Queue patterns** – Simple (FIFO), Priority, Dead Letter Queue para falhas definitivas.
- **Webhooks** – Assinatura HMAC; idempotency key; responder 202 e processar em async.
- **Docker e monitoramento** – Workers em container; Supervisord; health checks; métricas (Prometheus/Grafana).
- **Error handling** – Retry com exponential backoff; DLQ; circuit breaker; compensação quando fizer sentido.

---

## Próximos passos

1. Implementar Action Scheduler no projeto (Composer ou plugin).
2. Criar workers (Docker ou cron) que rodem a fila.
3. Implementar monitoramento (tamanho da fila, falhas, health).
4. Testar em staging (carga, falhas, retry, DLQ).
5. Documentar padrões e runbooks (o que fazer quando a fila cresce ou falha).

---

## Recursos

- [Action Scheduler](https://actionscheduler.org/)
- [WooCommerce Action Scheduler (GitHub)](https://github.com/woocommerce/action-scheduler)
- [WP-CLI Action Scheduler Commands](https://github.com/woocommerce/action-scheduler-cli)

---

## Referência rápida

- Uma página: [../REFERENCIA-RAPIDA.md](../REFERENCIA-RAPIDA.md).
- Teoria completa: [../../016-WordPress-Fase-16-Jobs-Assincronos-Background.md](../../016-WordPress-Fase-16-Jobs-Assincronos-Background.md).
