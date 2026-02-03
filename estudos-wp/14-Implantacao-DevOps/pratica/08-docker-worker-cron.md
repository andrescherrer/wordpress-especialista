# Docker – Worker e cron (fila e agendamento)

Resumo: adicionar serviço de worker ou cron ao docker-compose. Fonte: **014-WordPress-Fase-14-Implantacao-DevOps.md**.

---

## Worker (processar fila)

- Serviço que roda o mesmo código PHP/WordPress e executa jobs (ex.: Action Scheduler).
- Exemplo de serviço no docker-compose:

```yaml
worker:
  build: .
  command: php -r "require 'wp-load.php'; do_action('action_scheduler_run_queue');"  # ou loop infinito
  depends_on:
    - wordpress
    - db
  env_file: .env
```

- Ou usar um script `run-worker.sh` que em loop chama `wp action-scheduler run` ou inclui wp-load e processa uma fila custom.

---

## Cron (agendamento)

- Serviço que executa cron a cada minuto (ou intervalo):

```yaml
cron:
  image: wordpress:cli
  volumes:
    - .:/var/www/html
  entrypoint: /bin/sh -c "while true; do wp cron event run --due-now; sleep 60; done"
  depends_on:
    - wordpress
```

- Em produção: `DISABLE_WP_CRON=true` no WordPress e cron real (servidor ou container) chamando `wp cron event run --due-now` ou a URL de cron.

---

## Action Scheduler

- Se usar Action Scheduler, o worker pode rodar `wp action-scheduler run` em loop ou ser acionado por um cron que chama a URL de processamento da fila.
