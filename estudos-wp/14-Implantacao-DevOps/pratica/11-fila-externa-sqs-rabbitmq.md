# Fila externa (RabbitMQ, SQS)

Quando **Action Scheduler** não basta (throughput, múltiplos workers); padrão: **publicar mensagem na fila**; **worker consome**; exemplo de integração mínima (enviar para SQS no lugar de as_enqueue_async_action).

---

## Quando usar fila externa

- **Action Scheduler** roda no próprio WordPress (cron ou daemon); limite de throughput e de workers na mesma instância.
- **Fila externa** (RabbitMQ, AWS SQS, Redis Queue): vários **workers** em outras máquinas consomem a fila; escala horizontal; desacopla o WordPress do processamento pesado.

---

## Padrão

1. **Produtor (WordPress):** ao precisar processar algo (ex.: enviar email, gerar relatório), publica uma **mensagem** na fila (payload: job_id, dados, etc.).
2. **Worker (processo externo):** consome mensagens da fila; processa; remove da fila (ou marca como processada).
3. **Retry/DLQ:** falhas podem reenviar a mensagem para retry ou para uma dead-letter queue (DLQ).

---

## Exemplo conceitual – SQS

- **WordPress:** usar AWS SDK (ou API REST); ao invés de `as_enqueue_async_action(...)`, chamar `$sqs->sendMessage(['QueueUrl' => '...', 'MessageBody' => json_encode([...])])`.
- **Worker:** script (PHP, Node, etc.) ou Lambda que polla a fila, processa e deleta a mensagem.
- Payload pode incluir URL de callback do WordPress (REST) para marcar conclusão ou atualizar status.

---

## Exemplo conceitual – RabbitMQ

- **WordPress:** conectar ao RabbitMQ (php-amqplib); publicar em uma exchange/queue.
- **Worker:** consumir da queue; processar; ack.
- Retry: rejeitar sem ack e configurar dead-letter ou TTL para reenfileirar.

---

## Integração mínima no WordPress

- Criar função helper que envia job para a fila externa (ex.: `estudos_wp_queue_job('nome', $args)`).
- O worker, ao processar, pode chamar um **endpoint REST** do WordPress para atualizar post_meta ou notificar conclusão; ou escrever em banco/Redis compartilhado.
- Manter **Action Scheduler** para jobs leves e usar fila externa só para os pesados ou de alto volume.

---

## Recursos

- AWS SQS, RabbitMQ, Redis (Lists como fila); documentação de cada serviço.
- Ver **08-docker-worker-cron.md**, **12-banco-read-replica.md**, **13-escalabilidade-checklist.md**; estudos-wp/16 (Jobs assíncronos).
