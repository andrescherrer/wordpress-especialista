# Checklist – Escala horizontal

Cache persistente; sessões centralizadas; CDN; fila externa se necessário; monitoramento; referência a documentação de hospedagem (Kinsta, WP Engine, etc.).

---

- [ ] **Object cache persistente**  
  Redis ou Memcached configurado; WordPress usando object cache (plugin Redis Object Cache ou drop-in); transients e wp_cache indo para o cache persistente. Ver estudos-wp/08: **13-object-cache-redis-memcached.md**.

- [ ] **Sessões centralizadas**  
  Em ambiente com mais de um app server: sticky session ou sessão PHP em Redis (Memcached); evitar sessão em arquivo local. Ver **14-load-balancer-sessoes.md** (08).

- [ ] **CDN**  
  Assets (imagens, CSS, JS) servidos via CDN; invalidação ao publicar; full page cache na borda se aplicável. Ver **15-cdn-assets-paginas.md** (08).

- [ ] **Fila externa (se necessário)**  
  Se Action Scheduler não for suficiente (throughput, workers): fila externa (SQS, RabbitMQ); workers dedicados; retry/DLQ. Ver **11-fila-externa-sqs-rabbitmq.md**.

- [ ] **Banco read replica (se necessário)**  
  Leituras em réplica; escritas no primário; cuidado com replicação lag. Ver **12-banco-read-replica.md**.

- [ ] **Monitoramento**  
  Métricas de CPU, memória, disco; latência e erros no app; filas e workers; alertas.

- [ ] **Secrets e config**  
  Credenciais e URLs via variáveis de ambiente/secrets; não commitar em repositório. Ver **02-secrets-wpconfig.php**.

- [ ] **Documentação de hospedagem**  
  Kinsta, WP Engine, Pantheon etc. têm guias de scaling (Redis, CDN, múltiplos nós); seguir as recomendações do provedor.

---

## Recursos

- Documentação de Redis/Memcached para PHP; AWS SQS; guias de scaling WordPress (Kinsta, WP Engine).
- estudos-wp/08: **13-object-cache-redis-memcached.md**, **14-load-balancer-sessoes.md**, **15-cdn-assets-paginas.md**.
- estudos-wp/14: **11-fila-externa-sqs-rabbitmq.md**, **12-banco-read-replica.md**.
