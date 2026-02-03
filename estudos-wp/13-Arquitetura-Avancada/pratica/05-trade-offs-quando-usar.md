# Trade-offs: quando NÃO usar SOLID

Fonte: **013-WordPress-Fase-13-Arquitetura-Avancada.md** (seção 13.11).

---

## Quando NÃO usar SOLID

- **Projeto muito pequeno** (< 500 linhas): plugins simples, temas simples.
- **Scripts temporários ou de migração:** uso único, não precisa extensibilidade.
- **Protótipos e MVP:** foco em funcionalidade primeiro; refatorar depois.
- **Performance crítica (hot path):** código que roda milhões de vezes; abstrações têm custo.
- **Código simples que não vai mudar:** ex.: helper que só chama `get_option()`.
- **Wrappers desnecessários sobre WordPress:** não criar adapter para `get_post`/`get_the_title` quando o uso direto é claro.

---

## Quando USAR SOLID

- Projeto com **mais de ~1000 linhas**.
- Código precisa ser **testável** (mocks, fakes).
- **Múltiplos desenvolvedores** no mesmo código.
- Necessidade de **extensibilidade** (novos gateways, estratégias, repositórios).
- Performance não é crítica para esse trecho.
- Projeto com **longa vida** e evolução contínua.

---

## Regra prática

```
Se complexidade da solução > complexidade do problema  → over-engineering; simplifique.
Se complexidade da solução ≈ complexidade do problema  → SOLID apropriado.
Se complexidade da solução < complexidade do problema  → pode precisar de mais abstração.
```

---

## WordPress core vs custom code

- **Core** não segue SOLID rigorosamente; usar funções globais (`get_post`, `add_action`) é aceitável.
- **Não** criar wrappers complexos só para “ficar SOLID” em cima de APIs simples do WordPress.
- Em **seu** plugin/tema: aplicar SOLID onde o domínio é rico e a manutenção se beneficia.

---

## Exemplos rápidos

| Cenário                    | Complexidade | Abordagem recomendada        |
|---------------------------|-------------|------------------------------|
| Buscar uma opção          | Baixa       | `get_option($key)` direto    |
| Múltiplos gateways de pagamento, testes, extensão | Alta | Interfaces, DI, Adapter/Strategy |
| Transformar array de posts | Baixa      | Função ou closure direta    |
