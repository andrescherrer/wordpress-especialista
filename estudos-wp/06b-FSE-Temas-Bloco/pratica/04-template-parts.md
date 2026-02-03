# Template parts

O que é **template part**; pasta **parts/**; registro e uso no Site Editor.

---

## O que é template part

- **Template part** é um trecho reutilizável de layout (geralmente HTML de blocos), como header ou footer.
- Fica em arquivos na pasta **`parts/`** do tema (ex.: `parts/header.html`, `parts/footer.html`).
- É referenciado nos **templates** (ex.: index.html) pelo bloco "Template Part" ou pela estrutura do tema.
- No **Site Editor** o usuário pode editar cada part (Header, Footer) separadamente.

---

## Estrutura da pasta parts/

```
tema-block/
  style.css
  theme.json
  templates/
    index.html
    single.html
  parts/
    header.html
    footer.html
```

- **header.html** e **footer.html** contêm o HTML dos blocos (gerado pelo editor ou escrito à mão).
- Em **theme.json** você declara as parts em **templateParts**:

```json
"templateParts": [
  { "name": "header", "title": "Header", "area": "header" },
  { "name": "footer", "title": "Footer", "area": "footer" }
]
```

- **area** identifica a região (header, footer, etc.); o core e o Site Editor usam para listar e posicionar as parts.

---

## Conteúdo de um part (ex.: header.html)

Arquivo HTML com blocos, por exemplo:

```html
<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group">
  <!-- wp:site-title /-->
  <!-- wp:navigation /-->
</div>
<!-- /wp:group -->
```

Ou blocos mais simples; o importante é ser HTML válido de blocos Gutenberg.

---

## Uso nos templates

No **templates/index.html** (e em single, archive, etc.) você inclui a part com o bloco "Template Part" ou a estrutura equivalente, referenciando o nome/slug da part (ex.: header, footer). O Site Editor permite arrastar e configurar onde cada part aparece.

Ver **05-templates-index-single-archive.md** para a relação entre templates e **06-block-theme-minimo-passos.md** para um fluxo completo.
