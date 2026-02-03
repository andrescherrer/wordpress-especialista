# Tabela – REST vs GraphQL (WordPress)

Quando usar **REST** vs **GraphQL** no contexto WordPress; prós e contras.

---

| Critério | REST (WP REST API) | GraphQL (WPGraphQL) |
|----------|---------------------|---------------------|
| **Modelo** | Recursos por URL; métodos HTTP (GET, POST, etc.). | Uma URL (/graphql); query/mutation define recurso e campos. |
| **Campos** | O servidor define o que vem na resposta (ou parâmetros como _fields). | O **cliente** escolhe exatamente os campos e relações. |
| **Requests** | Várias URLs para montar uma tela (posts, autor, comentários). | Uma query pode trazer posts + autores + featured image. |
| **Over-fetch / under-fetch** | Comum: resposta traz mais ou menos do que o cliente precisa. | Reduzido: o cliente pede só o que precisa. |
| **Cache (HTTP)** | Cache por URL e método; bem suportado por CDN/proxy. | POST único; cache mais no cliente (Apollo, etc.). |
| **Ferramentas** | Postman, Insomnia; documentação OpenAPI/Swagger. | GraphiQL, Playground; schema introspectável. |
| **WordPress nativo** | REST API no core; muitos plugins expõem REST. | Requer plugin (WPGraphQL); schema gerado a partir do WP. |
| **Headless** | Muito usado; fácil para CRUD e integração com outros sistemas. | Muito usado em front React/Next; uma request por tela. |
| **Quando preferir** | Integração simples; cache HTTP forte; equipe acostumada a REST. | Front complexo que precisa de muitos dados relacionados em uma chamada; evitar over-fetch. |

---

## Resumo

- **REST:** padrão do WordPress; bom para integrações, APIs públicas e quando você quer cache por URL.
- **GraphQL:** bom para headless com front React/Vue/Next quando você quer uma única request com exatamente os campos e relações necessários; requer WPGraphQL.

Recursos: [WPGraphQL](https://www.wpgraphql.com/), [WP REST API Handbook](https://developer.wordpress.org/rest-api/).
