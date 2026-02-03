# Prática – Como usar (Arquitetura Avançada)

1. **SOLID:** Uma responsabilidade por classe (SRP); estender por novas classes, não alterar existentes (OCP); respeitar contrato ao substituir (LSP); interfaces pequenas (ISP); depender de interfaces e injetar dependências (DIP).
2. **DDD:** Usar linguagem do domínio nos nomes; separar Entity (identidade) e Value Object (imutável, valor); Repository na interface no domínio, implementação na infra; Domain Services para lógica que cruza entidades; Domain Events para desacoplar efeitos.
3. **Service Layer:** Um serviço de aplicação por caso de uso; orquestra validação, repositórios e domain services; publica eventos; trata exceções e log.
4. **DI Container:** Registrar factories e singletons; resolver com get(); usar para montar serviços de aplicação e repositórios sem new espalhado.
5. **Event-Driven:** Publicar eventos de domínio após ações importantes; inscrever handlers (email, estoque); usar do_action/add_action quando for suficiente.
6. **MVC:** Model (dados), View (renderização com escape), Controller (fluxo); integrar com shortcodes/filters.
7. **Adapter/Strategy/Factory:** Adapter para APIs externas; Strategy para trocar algoritmo (desconto); Factory para criação centralizada.
8. **Trade-offs:** Não aplicar SOLID onde não compensa (projeto pequeno, MVP, performance crítica). Preferir simplicidade quando a solução ficar mais complexa que o problema.

**Teoria rápida:** no topo de cada `.php` há um bloco **REFERÊNCIA RÁPIDA**. Tudo em uma página: [../REFERENCIA-RAPIDA.md](../REFERENCIA-RAPIDA.md).
