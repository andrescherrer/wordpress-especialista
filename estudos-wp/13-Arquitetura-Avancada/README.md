# 13 – Arquitetura Avançada em WordPress

**Foco: prática.** SOLID, DDD, Service Layer, Repository, DI Container, Event-Driven, MVC, Adapter, Strategy, Factory, trade-offs e error handling.

---

## Comece pela prática

| # | Arquivo | O que você vai fazer |
|---|---------|----------------------|
| 1 | [01-solid-principles.php](pratica/01-solid-principles.php) | SRP, OCP, LSP, ISP, DIP com exemplos WordPress |
| 2 | [02-ddd-service-repository.php](pratica/02-ddd-service-repository.php) | DDD (Entity, Value Object, Repository), Service Layer |
| 3 | [03-di-container-events.php](pratica/03-di-container-events.php) | Container de DI (register/get), Event-Driven (domain events) |
| 4 | [04-mvc-adapter-strategy-factory.php](pratica/04-mvc-adapter-strategy-factory.php) | MVC, Adapter para APIs, Strategy, Factory |
| 5 | [05-trade-offs-quando-usar.md](pratica/05-trade-offs-quando-usar.md) | Quando NÃO usar SOLID; decisão e regra prática |
| 6 | [06-error-handling.md](pratica/06-error-handling.md) | Domain exceptions, tratamento em Service/Repository |
| 7 | [07-boas-praticas.md](pratica/07-boas-praticas.md) | Resumo dos padrões, checklist, equívocos comuns |

**Como usar:** copie classes/trechos para seu plugin. Use SOLID e padrões onde a complexidade do problema justifica; evite over-engineering em projetos pequenos. Detalhes em [pratica/README.md](pratica/README.md).

---

## Teoria quando precisar

- **No código:** cada `.php` tem no topo um bloco **REFERÊNCIA RÁPIDA**.
- **Uma página:** [REFERENCIA-RAPIDA.md](REFERENCIA-RAPIDA.md) – SOLID, DDD, Service, Repository, DI, Events, MVC, Adapter, Strategy, Factory (Ctrl+F).
- **Fonte completa:** [013-WordPress-Fase-13-Arquitetura-Avancada.md](../../013-WordPress-Fase-13-Arquitetura-Avancada.md) na raiz do repositório.

---

## Objetivos da Fase 13

- Aplicar princípios SOLID (SRP, OCP, LSP, ISP, DIP) no desenvolvimento WordPress
- Implementar DDD com linguagem ubíqua, entidades, value objects e repositórios
- Criar Service Layer para orquestrar lógica de negócio
- Usar Repository para abstração de acesso a dados
- Implementar container de dependency injection (custom ou Pimple)
- Projetar arquitetura event-driven com hooks/domain events
- Aplicar padrões Adapter, Strategy e Factory quando apropriado
- Reconhecer quando NÃO usar SOLID (trade-offs e super-engenharia)
- Tratar erros com domain exceptions e handlers consistentes
