# Estrutura de um bloco Gutenberg em JavaScript/React

Este documento descreve a estrutura de pastas e arquivos de um bloco que usa **edit.js** (React) e **save.js**, com build via npm.

---

## Pastas e arquivos

| Pasta/Arquivo | Papel |
|---------------|--------|
| **`src/`** | Código fonte do bloco (edit.js, save.js, block.json pode ficar aqui ou na raiz do bloco). |
| **`build/`** | Saída do build (gerada por `@wordpress/scripts`); não versionar ou versionar conforme fluxo do projeto. |
| **`block.json`** | Metadados do bloco: nome, título, atributos, scripts e estilos; o editor e o front carregam a partir daqui. |
| **`src/edit.js`** | Componente React do **editor**: UI de edição, leitura/atualização de `attributes`, `setAttributes`. |
| **`src/save.js`** | Componente que **serializa** o bloco para o HTML salvo no banco; retorno de elemento React. |
| **`style.css`** | Estilos no **front** (site); enfileirado via `style` em block.json. |
| **`editor.css`** | Estilos só no **editor**; enfileirado via `editorStyle` em block.json. |

---

## Ordem de carregamento

1. O WordPress carrega **block.json** (nome, atributos, suportes).
2. **editorScript** e **editorStyle** são enfileirados no editor; **script** e **style** no front.
3. No editor: React monta o componente **edit**; alterações em `setAttributes` atualizam o estado e o preview.
4. No save: o conteúdo é serializado pelo **save** (ou por `render_callback` em bloco dinâmico).

---

## Onde fica cada coisa

- **Lógica de UI (editor)** → `edit.js` (React).
- **Atributos e valor salvo** → definidos em `block.json` (`attributes`); persistidos no HTML ou no block comment.
- **Apresentação no front** → `save.js` (bloco estático) ou **PHP** `render_callback` (bloco dinâmico).
- **Build** → `npm run build` gera os arquivos em `build/`; no PHP você registra o bloco apontando para `build/block.json` (ou para a raiz se o build copiar o block.json).

---

## Próximos passos

- Ver **13-block-json-completo.md** para um `block.json` mínimo copiável.
- Ver **14-edit-js-minimo.md** e **15-save-js-serializacao.md** para exemplos de edit/save.
- Ver **16-build-wordpress-scripts.md** para npm e enqueue no PHP.
- Ver **17-checklist-bloco-js.md** para o passo a passo “Meu primeiro bloco JS”.
