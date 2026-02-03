# Boas práticas – Arquitetura avançada

Fonte: **013-WordPress-Fase-13-Arquitetura-Avancada.md**.

---

## Resumo dos padrões

| Padrão          | Propósito                         | Quando usar                    |
|-----------------|-----------------------------------|--------------------------------|
| **SOLID**       | Princípios fundamentais           | Sempre que compensar           |
| **DDD**         | Design orientado ao domínio       | Projetos com domínio rico      |
| **Service Layer** | Orquestração de lógica          | Casos de uso de aplicação      |
| **Repository**  | Abstração de persistência         | Projetos médios em diante      |
| **DI Container**| Gestão de dependências           | Projetos com muitas dependências |
| **Event-Driven** | Desacoplamento via eventos     | Múltiplos efeitos colaterais   |
| **MVC**         | Separação Model/View/Controller  | UI com lógica de dados         |
| **Adapter**     | Integração com sistemas externos  | APIs de terceiros              |
| **Strategy**    | Algoritmos intercambiáveis        | Várias estratégias (ex.: desconto) |
| **Factory**     | Criação centralizada de objetos   | Objetos complexos ou por tipo  |

---

## Checklist de implementação

- [ ] **SRP:** Cada classe com uma responsabilidade; orquestração em Service.
- [ ] **OCP:** Novos comportamentos por novas classes (interfaces); evitar `if (tipo)` para extensão.
- [ ] **LSP:** Implementações substituíveis sem quebrar contrato.
- [ ] **ISP:** Interfaces pequenas; segregar quando um cliente não usa todos os métodos.
- [ ] **DIP:** Depender de interfaces; injetar via construtor; container para resolver.
- [ ] **DDD:** Linguagem ubíqua; Entity vs Value Object; Repository no domínio, implementação na infra.
- [ ] **Service Layer:** Um serviço por caso de uso; validar → domínio → persistir → eventos.
- [ ] **Erros:** Domain exceptions com contexto; tratamento consistente no service e repository.
- [ ] **Trade-offs:** Evitar over-engineering em projetos pequenos e hot paths.

---

## Equívocos comuns

1. **Aplicar SOLID em tudo:** helpers simples e scripts únicos não precisam de interfaces e DI.
2. **Wrappers desnecessários:** encapsular `get_post()`/`get_option()` em várias camadas sem ganho real.
3. **Eventos para tudo:** usar domain events ou hooks só onde há desacoplamento real (múltiplos consumidores ou efeitos laterais).
4. **Repository para uma query:** se for uma consulta pontual e simples, `get_posts()` ou `WP_Query` podem ser suficientes.
5. **Ignorar performance:** em loops muito quentes, preferir código direto a muitas indireções.
6. **MVP com arquitetura pesada:** começar simples; introduzir camadas e padrões quando a complexidade do problema justificar.

---

## Referência rápida

- Uma página: [../REFERENCIA-RAPIDA.md](../REFERENCIA-RAPIDA.md).
- Teoria completa: [../../013-WordPress-Fase-13-Arquitetura-Avancada.md](../../013-WordPress-Fase-13-Arquitetura-Avancada.md).
