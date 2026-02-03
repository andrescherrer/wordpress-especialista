# Query básica (posts, usuário)

Query **posts**, **post(id)**, **users**; campos retornados; **fragmentos**; diferença em relação a REST (uma request, campos sob demanda).

---

## Posts

```graphql
query {
  posts(first: 5) {
    nodes {
      id
      databaseId
      title
      date
      excerpt
      slug
      author {
        node {
          name
          email
        }
      }
    }
    pageInfo {
      hasNextPage
      endCursor
    }
  }
}
```

- **first:** quantidade; **nodes:** lista de posts; **pageInfo:** paginação (cursor).
- Você escolhe **apenas os campos** que precisa (evita over-fetch).

---

## Post por ID ou slug

```graphql
query {
  post(id: "post-id-123", idType: DATABASE_ID) {
    title
    content
    date
    featuredImage {
      node {
        sourceUrl
        altText
      }
    }
  }
}
```

Ou por slug: `post(id: "meu-post", idType: SLUG)`.

---

## Usuários

```graphql
query {
  users(first: 10) {
    nodes {
      id
      name
      email
      username
    }
  }
}
```

(Requer permissões adequadas; usuários podem ser restritos conforme configuração do WPGraphQL.)

---

## Fragmentos

Reutilizar conjunto de campos:

```graphql
fragment PostSummary on Post {
  id
  title
  slug
  date
}

query {
  posts(first: 5) {
    nodes {
      ...PostSummary
    }
  }
}
```

---

## REST vs GraphQL

- **REST:** uma URL por recurso; o servidor define os campos retornados (ou parâmetros limitados); várias requests para montar uma tela.
- **GraphQL:** uma request; o **cliente** define exatamente os campos e relações; uma query pode trazer posts + autores + featured image em uma única chamada.

Ver **17-wpgraphql-instalacao-config.md**, **19-graphql-cpt-mutations.md**, **21-rest-vs-graphql.md**.
