# Headless: consumir GraphQL no front

Cliente (Apollo, fetch) no **Next/React**; **variáveis** e **cache**; boas práticas (evitar over-fetch).

---

## Cliente com fetch (minimal)

```javascript
const query = `
  query GetPosts($first: Int!) {
    posts(first: $first) {
      nodes {
        id
        title
        slug
        date
      }
    }
  }
`;

const res = await fetch('https://seusite.com/graphql', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    query,
    variables: { first: 10 },
  }),
});
const json = await res.json();
const posts = json.data?.posts?.nodes ?? [];
```

---

## Apollo Client (React)

- Instalar `@apollo/client` e `graphql`.
- Configurar **ApolloProvider** com a URI do GraphQL e (se necessário) header de autenticação.
- Usar **useQuery** com a query e variáveis; Apollo gerencia cache e refetch.

```javascript
import { useQuery, gql } from '@apollo/client';

const GET_POSTS = gql`
  query GetPosts($first: Int!) {
    posts(first: $first) {
      nodes { id title slug date }
    }
  }
`;

function PostList() {
  const { data, loading, error } = useQuery(GET_POSTS, { variables: { first: 10 } });
  if (loading) return <p>Carregando...</p>;
  if (error) return <p>Erro: {error.message}</p>;
  return (
    <ul>
      {data.posts.nodes.map((post) => (
        <li key={post.id}>{post.title}</li>
      ))}
    </ul>
  );
}
```

---

## Boas práticas

- **Evitar over-fetch:** pedir só os campos necessários na query.
- **Variáveis:** usar variáveis para first, cursor, filtros; facilita cache e reuso.
- **Cache:** Apollo normaliza por `__typename` e `id`; configurar política de fetch (cache-first, network-only, etc.) conforme a tela.
- **Autenticação:** enviar JWT (ou o que o WPGraphQL esperar) no header nas requisições que exigem login.

---

## Recursos

- [WPGraphQL](https://www.wpgraphql.com/), [WPGraphQL Docs](https://www.wpgraphql.com/docs/intro)
- Apollo Client: [documentação](https://www.apollographql.com/docs/react/)

Ver **17-wpgraphql-instalacao-config.md**, **18-graphql-query-basica.md**, **21-rest-vs-graphql.md**.
