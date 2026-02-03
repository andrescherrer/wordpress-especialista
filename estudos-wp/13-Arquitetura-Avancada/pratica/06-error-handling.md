# Error handling em arquitetura avançada

Fonte: **013-WordPress-Fase-13-Arquitetura-Avancada.md** (seção 13.12).

---

## Domain exceptions

- Exceções **específicas do domínio** (não genéricas): `InvalidUserDataException`, `ProductNotFoundException`, `BusinessRuleViolationException`, `InsufficientStockException`.
- Podem carregar **contexto** (array com field, value, etc.) para log e resposta à API.
- Herdar de uma base `DomainException` (que estende `\Exception`) com `getContext()` opcional.

Exemplo:

```php
class InvalidUserDataException extends DomainException {}
class ProductNotFoundException extends DomainException {}

// No domain/service:
if (empty($data['email'])) {
    throw new InvalidUserDataException('Email é obrigatório', ['field' => 'email']);
}
if (!$product) {
    throw new ProductNotFoundException('Produto não encontrado', ['id' => $id]);
}
```

---

## Service layer

- **Try/catch** no método do caso de uso (ex.: `CreateOrderService::execute()`).
- **DomainException:** logar (com contexto) e **re-lançar** para o chamador (controller/REST) decidir status e mensagem.
- **Exception genérica:** logar e lançar **UnexpectedErrorException** (ou similar) para não vazar detalhes internos; ou converter em resposta 500 com mensagem genérica.
- **Validação de entrada (DTO):** lançar exceções de domínio ou `InvalidArgumentException` no início do execute.

---

## Repository

- Falhas de **persistência** (wp_insert_post retorna WP_Error, wpdb erro): lançar **RepositoryException** (ou equivalente) com mensagem segura.
- **Não** engolir exceções; deixar subir para o service layer tratar e logar.
- Métodos como `findById` podem retornar `null` em “não encontrado” em vez de exceção, dependendo do contrato; “não encontrado” como regra de negócio pode ser exceção no service.

---

## Event-driven

- Handlers de eventos devem tratar exceções **internamente** (try/catch + log) para um handler quebrado não impedir os demais.
- Se usar um dispatcher customizado, considerar **try/catch por handler** e continuar executando os próximos.

---

## Checklist

- [ ] Domain exceptions com nomes e contexto claros.
- [ ] Service: catch DomainException → log + re-lançar; catch Exception → log + UnexpectedErrorException ou resposta 500.
- [ ] Repository: falhas de persistência → RepositoryException; não engolir erros.
- [ ] Event handlers: não deixar exceção escapar e quebrar outros handlers; logar falhas.
