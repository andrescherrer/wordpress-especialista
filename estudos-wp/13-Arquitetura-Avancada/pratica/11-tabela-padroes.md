# Tabela – Quando usar cada padrão (Repository, Service, Event, Factory)

Resumo para decisão de arquitetura. Fonte: **013-WordPress-Fase-13-Arquitetura-Avancada.md**.

---

## Repository

| Quando usar | Exemplo |
|-------------|---------|
| Acesso a dados (posts, options, custom tables) abstraído em um lugar | PostRepository::find(), findById(), save(), delete() |
| Trocar fonte de dados (WP vs API) sem mudar orquestração | Implementar interface PostRepositoryInterface |
| Múltiplas queries complexas reutilizáveis | findPublishedByAuthor($author_id) |

---

## Service

| Quando usar | Exemplo |
|-------------|---------|
| Orquestrar lógica de negócio (validação + repository + notificação) | PostService::create($data) → validate → repository->save() → dispatch event |
| Um fluxo que usa mais de um repositório ou dependência | OrderService usa OrderRepository + PaymentGateway |
| Regras de negócio que não cabem no modelo de dados | Calcular preço, aplicar cupom |

---

## Event (Event-Driven)

| Quando usar | Exemplo |
|-------------|---------|
| Desacoplar ações secundárias (log, email, cache) após um fato | do_action('post_created', $post_id); listener envia email |
| Múltiplos consumidores do mesmo fato | post_created → log, notificar, invalidar cache |
| Integração com outros sistemas sem acoplar | Webhook ao criar pedido |

---

## Factory

| Quando usar | Exemplo |
|-------------|---------|
| Criação de objetos complexos ou com lógica | OrderFactory::fromCart($cart) |
| Diferentes implementações conforme contexto | GatewayFactory::create('stripe') retorna StripeGateway |
| Objetos que precisam de muitas dependências injetadas | Container + factory para montar controller REST |
