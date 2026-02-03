# Instalação e configuração WPGraphQL

Instalar o plugin **WPGraphQL**; endpoint **/graphql**; teste com **GraphiQL** (interface no admin); autenticação (JWT ou header).

---

## Instalação

1. Instalar o plugin **WPGraphQL** (Plugins > Adicionar novo > buscar “WPGraphQL” ou instalar via ZIP).
2. Ativar o plugin.
3. O endpoint fica em **`https://seusite.com/graphql`** (ou conforme configuração de permalinks).

---

## GraphiQL (interface no admin)

- Com WPGraphQL ativo, acesse **GraphQL > GraphiQL** no menu do admin (ou a URL indicada pelo plugin).
- Na interface você escreve **queries** e **mutations** e vê a resposta em tempo real; o schema é explorável (documentação e autocomplete).

---

## Autenticação

- **Leitura pública:** muitas queries (posts, páginas) funcionam sem autenticação.
- **Mutations e dados privados:** exigem autenticação. Opções:
  - **JWT:** configurar plugin de JWT (ex.: WPGraphQL JWT Authentication) e enviar header `Authorization: Bearer <token>`.
  - **Application Password:** em alguns setups, Basic Auth ou header custom pode ser usado.
  - **Cookie:** em requisições no mesmo domínio (admin/front), o cookie de login do WP pode ser usado (conforme configuração do WPGraphQL).

Ver documentação do WPGraphQL para a versão instalada.

---

## Teste rápido

Na GraphiQL, execute:

```graphql
query {
  posts(first: 3) {
    nodes {
      id
      title
      date
    }
  }
}
```

Deve retornar os 3 primeiros posts com id, title e date.

---

## Recursos

- [WPGraphQL](https://www.wpgraphql.com/)
- [WPGraphQL Docs](https://www.wpgraphql.com/docs/intro)

Ver **18-graphql-query-basica.md**, **19-graphql-cpt-mutations.md**, **20-headless-consumir-graphql.md** e **21-rest-vs-graphql.md**.
