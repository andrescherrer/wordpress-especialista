# Load balancer e sessões

Problema: **sessões PHP** por servidor; solução: **sticky session** ou armazenar sessão em **Redis**; configuração Nginx/HAProxy como exemplo.

---

## Problema

- Com **vários servidores** atrás de um load balancer, cada requisição pode cair em um servidor diferente.
- **Sessões PHP** (session_start(), $_SESSION) ficam no servidor que atendeu a requisição; na próxima, outro servidor não tem essa sessão → usuário “deslogado” ou perde estado.

---

## Soluções

### 1. Sticky session (affinity)

- O load balancer **fixa** o cliente em um servidor (por cookie ou IP).
- Prós: simples; sessão PHP continua em arquivo/local.
- Contras: desbalanceamento se alguns usuários geram muito mais carga; se um servidor cair, as sessões daquele servidor se perdem.

### 2. Sessão em Redis (ou Memcached)

- Configurar PHP para usar **Redis** (ou Memcached) como armazenamento de sessão.
- **php.ini** (exemplo para Redis):  
  `session.save_handler = redis`  
  `session.save_path = "tcp://127.0.0.1:6379"`  
  (ou conforme extensão: `session.save_path = "tcp://redis:6379?auth=senha"`).
- Todas as instâncias PHP leem/escrevem a mesma sessão no Redis; o load balancer pode distribuir livremente.

---

## Nginx (sticky)

Exemplo com **ip_hash** (sticky por IP):

```nginx
upstream backend {
    ip_hash;
    server 10.0.1.1:80;
    server 10.0.1.2:80;
}
server {
    location / {
        proxy_pass http://backend;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
    }
}
```

Ou usar módulo **sticky** (cookie) se disponível.

---

## HAProxy (sticky)

Exemplo com cookie:

```haproxy
backend wp
    balance roundrobin
    cookie SERVERID insert indirect nocache
    server s1 10.0.1.1:80 cookie s1
    server s2 10.0.1.2:80 cookie s2
```

---

## Resumo

- **Sticky:** rápido de implementar; aceitável para tráfego não muito desbalanceado.
- **Sessão em Redis:** escalável; todas as instâncias compartilham o mesmo estado de sessão; requer Redis e configuração do PHP.

Ver **13-object-cache-redis-memcached.md**, **15-cdn-assets-paginas.md**; estudos-wp/14: **11-fila-externa-sqs-rabbitmq.md**, **12-banco-read-replica.md**, **13-escalabilidade-checklist.md**.
