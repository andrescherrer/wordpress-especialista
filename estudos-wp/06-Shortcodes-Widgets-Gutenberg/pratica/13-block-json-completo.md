# block.json completo – exemplo mínimo

Campos essenciais para um bloco que usa **editorScript** (edit.js) e **style** (CSS no front).

---

## Campos principais

| Campo | Descrição |
|-------|-----------|
| **`apiVersion`** | Versão da API do bloco (2 ou 3). |
| **`name`** | Namespace + nome, ex.: `meu-plugin/meu-bloco`. |
| **`title`** | Nome exibido no inserter. |
| **`category`** | `text`, `media`, `design`, `widgets`, `theme`, `embed`. |
| **`icon`** | Nome do ícone Dashicons ou objeto SVG. |
| **`attributes`** | Atributos do bloco (tipo, default, source para salvar no HTML). |
| **`supports`** | Suportes do bloco: `align`, `anchor`, `color`, `typography`, etc. |
| **`editorScript`** | Handle do script do editor (edit.js compilado). |
| **`editorStyle`** | Handle do CSS do editor. |
| **`style`** | Handle do CSS do front. |

---

## Exemplo mínimo copiável (block.json)

```json
{
  "$schema": "https://schemas.wp.org/trunk/block.json",
  "apiVersion": 3,
  "name": "meu-plugin/meu-bloco",
  "version": "1.0.0",
  "title": "Meu bloco",
  "category": "text",
  "icon": "editor-paragraph",
  "description": "Descrição curta do bloco.",
  "attributes": {
    "content": {
      "type": "string",
      "default": ""
    },
    "align": {
      "type": "string",
      "default": "none"
    }
  },
  "supports": {
    "align": true
  },
  "textdomain": "meu-plugin",
  "editorScript": "file:./build/index.js",
  "editorStyle": "file:./build/index.css",
  "style": "file:./build/style-index.css"
}
```

- **`file:./build/...`** indica que o asset é o arquivo gerado pelo build (quando usa `register_block_type_from_metadata` com o caminho do `block.json`).
- **attributes**: `content` pode ser salvo no HTML via `source: "html"` e `selector` em blocos estáticos; em blocos com save.js você controla o que vai no HTML.

---

## Atributo com source no HTML (salvo no markup)

```json
"attributes": {
  "title": {
    "type": "string",
    "source": "attribute",
    "selector": ".titulo",
    "attribute": "data-titulo",
    "default": ""
  }
}
```

Assim o valor é lido/escrito no elemento com classe `.titulo` e atributo `data-titulo`.

---

## Referência rápida

- [block.json (Block Editor Handbook)](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/)
- Ver **12-bloco-js-estrutura.md** para o papel de cada arquivo e **17-checklist-bloco-js.md** para o fluxo completo.
