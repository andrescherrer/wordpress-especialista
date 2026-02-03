# Boas práticas em testes

Fonte: **017-WordPress-Fase-17-Testes-Em-Toda-Fase.md** (Boas Práticas).

---

## Nomenclatura

- **Bom:** `test_user_cannot_access_admin_without_permission`, `test_validation_rejects_invalid_data`
- **Ruim:** `test_user`, `test1`
- Padrão: `test_<o_que>_<condição>_<resultado_esperado>` ou equivalente legível.

---

## Arrange-Act-Assert (AAA)

```php
public function test_example(): void {
    // Arrange (preparar dados, mocks)
    $user = $this->factory->user->create();

    // Act (executar o que está sendo testado)
    $result = do_something($user);

    // Assert (verificar resultado)
    $this->assertEquals('expected', $result);
}
```

---

## Um assertion por teste (quando possível)

- Um conceito por teste facilita localizar falhas.
- Vários assertions para o mesmo conceito (ex.: criação de usuário com nome e email) é aceitável.
- Evite testar comportamentos não relacionados no mesmo método.

---

## Testes independentes

- Cada teste deve poder rodar sozinho e em qualquer ordem.
- `setUp`: estado limpo; `tearDown`: remover hooks, limpar options/transients se necessário.
- Não depender de dados criados em outro teste.

---

## Mocking apropriado

- Mockar dependências externas (API, repositório quando testando service).
- Não mockar tudo; preferir objetos reais quando forem rápidos e determinísticos.
- Usar `createMock`/`createStub`; `expects($this->once())`, `willReturn`, `with($arg)`.

---

## Testes rápidos

- Unitários: muito rápidos (milissegundos).
- Integração: podem ser mais lentos; manter tempo razoável; evitar I/O desnecessário.
- Testes lentos desencorajam execução frequente.

---

## Cobertura de código

```bash
./vendor/bin/phpunit --coverage-html coverage/
```

- Focar em código crítico (regras de negócio, segurança, fluxos principais).
- Alvo 80%+ onde fizer sentido; não perseguir número à custa de testes úteis.

---

## Testes de regressão

- Quando corrigir um bug, adicionar um teste que falhava antes da correção e passa depois.
- No docblock: `@test Regressão: bug #123 - usuário acessava admin sem permissão`.
