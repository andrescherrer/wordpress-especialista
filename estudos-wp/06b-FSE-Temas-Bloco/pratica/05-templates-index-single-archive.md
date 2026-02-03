# Templates – index, single, archive

Pasta **templates/**; **index.html** como fallback; **single.html**, **archive.html**; relação com a template hierarchy.

---

## Pasta templates/

Em um tema de bloco, os templates são arquivos **HTML** (com blocos) na pasta **`templates/`**:

- **index.html** – fallback; usado quando não houver template mais específico.
- **single.html** – post único.
- **archive.html** – listagem (arquivo).
- **single-{post-type}.html** – single para CPT (ex.: single-livro.html).
- **archive-{post-type}.html** – archive para CPT.
- **page.html** – página.
- **front-page.html** – página inicial (quando configurada como estática).
- **404.html** – não encontrado.

---

## Relação com a template hierarchy

O WordPress escolhe o template conforme a **template hierarchy** (post type, singular/archive, etc.). No tema de bloco:

- O core mapeia essa escolha para o arquivo correspondente em **templates/**.
- Se existir **single.html**, ele é usado para posts; **single-produto.html** para o CPT `produto`, se existir.
- Se não existir template específico, usa **index.html**.

---

## Conteúdo típico de um template

Cada arquivo é HTML de blocos. Exemplo **index.html** mínimo:

```html
<!-- wp:template-part {"slug":"header","tagName":"header"} /-->
<!-- wp:post-content {"layout":{"type":"constrained"}} /-->
<!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->
```

- **template-part** inclui a part pelo slug (header, footer).
- **post-content** exibe o conteúdo do post/página.

Para **single.html** e **archive.html** a estrutura é semelhante; você pode usar blocos como **post-title**, **post-date**, **post-excerpt**, **query-loop** (para archive), etc.

---

## Fallback

- **index.html** é obrigatório como fallback: se nenhum outro template for encontrado, o core usa index.html.
- Criar pelo menos **index.html**, **single.html** e **archive.html** cobre a maior parte dos sites.

Ver **06-block-theme-minimo-passos.md** para montar um block theme mínimo do zero.
