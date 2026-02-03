# Referência rápida – Arquitetura Avançada

Uma página. Use Ctrl+F. Fonte: **013-WordPress-Fase-13-Arquitetura-Avancada.md**.

---

## SOLID

- **S (SRP):** Uma classe, uma responsabilidade. Separar validação, persistência, email, log em classes distintas; orquestrar com um Service.
- **O (OCP):** Aberto para extensão, fechado para modificação. Usar interface (ex.: PaymentGateway) e implementações (CreditCard, PIX, Boleto); adicionar novo gateway = nova classe.
- **L (LSP):** Subtipos substituíveis. Contrato da interface deve ser respeitado (ex.: StorageBackend save/get/delete; LocalStorage e S3Storage intercambiáveis).
- **I (ISP):** Interfaces pequenas e específicas. RefundablePaymentGateway, SubscriptionPaymentGateway em vez de uma interface gigante.
- **D (DIP):** Depender de abstrações. Injetar OrderRepository e EmailService no construtor; container resolve implementações.

---

## DDD (Domain-Driven Design)

- **Linguagem ubíqua:** Nomes no código = termos do domínio (StockReservation, Checkout, Loan, Fine).
- **Entity:** Identidade (ID); igualdade por ID. Ex.: Product com ProductId.
- **Value Object:** Sem identidade; imutável; igualdade por valor. Ex.: Price, ProductId (como VO), Inventory.
- **Repository (interface no domínio):** save, findById, delete, findBy*; implementação na infra (ex.: ProductRepositoryWordPress com get_posts/update_post_meta).
- **Domain Service:** Lógica que envolve mais de uma entidade (ex.: PricingDomainService – desconto por volume + VIP).
- **Domain Events:** Algo importante aconteceu (OrderCreatedEvent); publicar e inscrever handlers (email, atualizar estoque).

---

## Service Layer

- Orquestra: validar DTO → buscar entidades → aplicar domain services → persistir → publicar eventos.
- Uma classe por caso de uso (CreateOrderApplicationService); recebe repositórios e serviços por construtor.
- Tratar DomainException vs Exception; logar e re-lançar ou converter em resposta (REST/UI).

---

## Dependency Injection Container

- **register(id, factory, singleton):** factory recebe container, retorna instância.
- **get(id):** resolve dependências (ex.: createOrderService chama get('orderRepository'), get('pricingDomainService'), etc.).
- **singleton:** cache da primeira resolução.
- **alias:** id alternativo para mesmo serviço.
- Pimple: `$pimple['service'] = function($c) { return new Service($c['repo']); };` e `$pimple['service']` para resolver.

---

## Event-Driven

- **WordPress:** do_action('evento', $payload); add_action('evento', $handler).
- **Domain events:** EventPublisher com publish(DomainEvent) e subscribe(eventClass, handler); handlers podem enviar email, atualizar estoque, etc.
- Desacopla: quem publica não conhece quem consome.

---

## MVC em WordPress

- **Model:** acessa dados (ou usa Repository); ex.: PostModel com PostRepository.
- **View:** apenas renderização (esc_html, esc_url, wp_kses_post); sem lógica de negócio.
- **Controller:** recebe input, chama model, chama view, retorna output. Integrar via shortcode ou filter (ex.: the_content).

---

## Adapter (APIs externas)

- Interface única (ex.: PaymentGatewayAdapter com charge, refund).
- Implementações por provedor (StripeAdapter, PagarMeAdapter) adaptam API externa ao contrato.
- Serviço (OrderPaymentService) recebe o adapter; em bootstrap escolhe qual adapter instanciar (get_option('payment_provider')).

---

## Strategy

- Interface da estratégia (ex.: DiscountStrategy com calculate(Order)).
- Implementações: NoDiscount, PercentageDiscount, BulkDiscount, CustomerLoyaltyDiscount; possível ChainedDiscount.
- Serviço recebe a estratégia no construtor; trocar estratégia em runtime sem mudar o serviço.

---

## Factory

- Centralizar criação complexa (dependências, configuração). PaymentGatewayFactory create(); registry por tipo (stripe, pix).
- Reduz acoplamento no código que precisa do gateway.

---

## Quando NÃO usar SOLID

- Projeto muito pequeno (< 500 linhas), scripts temporários, prototipagem, performance crítica (hot path).
- Não criar wrappers desnecessários sobre get_post/get_option; usar WordPress diretamente quando for suficiente.
- Regra: se complexidade da solução > complexidade do problema → simplificar.

---

## Error handling

- **Domain exceptions:** InvalidUserDataException, ProductNotFoundException, BusinessRuleViolationException (com contexto).
- **Service layer:** catch DomainException → log e re-lançar; catch Exception → log e lançar UnexpectedErrorException ou converter para resposta.
- **Repository:** falhas de persistência → RepositoryException ou equivalente; não engolir exceções.
