# Docker e monitoramento de workers

Fonte: **016-WordPress-Fase-16-Jobs-Assincronos-Background.md** (Integração Docker, Monitoramento).

---

## Workers em Docker

- **Ideia:** Container que executa o processamento da fila (Action Scheduler). O WordPress core usa `wp-cron.php`; com Action Scheduler, um worker pode rodar WP-CLI ou um script PHP que dispara o runner.
- **Exemplo:** Serviço no docker-compose que executa em loop: `wp action-scheduler run` (se existir comando WP-CLI do Action Scheduler) ou `php -r "require 'wp-load.php'; do_action('action_scheduler_run_queue');"` em intervalo.
- **Múltiplos workers:** Vários containers do mesmo tipo; garantir que o backend de fila (tabela do Action Scheduler) suporta concorrência (MySQL/Postgres).

---

## Supervisord

- Manter N processos worker ativos; restart automático se morrerem.
- Config: `[program:action_scheduler_worker] command=... numprocs=2 autostart=true autorestart=true`
- Útil em VM/servidor onde não se usa Docker para os workers.

---

## Health checks

- **Worker:** Endpoint ou script que verifica se há jobs travados (ex.: último job completado há mais de X minutos) ou se a tabela de ações está sendo processada.
- **Fila:** Métrica “tamanho da fila” (pending) e “falhas recentes”; alerta se pending > limite ou falhas subindo.
- **Container:** Healthcheck no compose (CMD que chama script de health do worker).

---

## Prometheus / Grafana

- **Métricas sugeridas:** total de ações pending, running, completed, failed; tempo médio de processamento; taxa de falha.
- Action Scheduler pode expor métricas via hook ou tabela; exporter ou script que lê a tabela e expõe em formato Prometheus.
- **Grafana:** Dashboard com gráficos de fila ao longo do tempo e alertas (ex.: pending > 1000, failure rate > 5%).
