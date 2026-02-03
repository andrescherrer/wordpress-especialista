# Checklist – Meu primeiro bloco JS

Passos para criar um bloco Gutenberg com **edit.js** (React), **save.js** e build com **@wordpress/scripts**.

---

- [ ] **1. Criar o plugin**  
  Pasta em `wp-content/plugins/meu-bloco-exemplo` com arquivo PHP principal (header do plugin).

- [ ] **2. npm init**  
  Na raiz do plugin: `npm init -y`.

- [ ] **3. Instalar @wordpress/scripts**  
  `npm install --save-dev @wordpress/scripts`.

- [ ] **4. Configurar scripts no package.json**  
  `"build": "wp-scripts build"`, `"start": "wp-scripts start"`.

- [ ] **5. Criar block.json**  
  Nome, título, categoria, ícone, **attributes**, **editorScript**, **editorStyle**, **style** (apontando para `file:./build/...` após o build).

- [ ] **6. Criar src/index.js**  
  Registrar o bloco com `registerBlockType`; importar edit e save (ou defini-los no mesmo arquivo).

- [ ] **7. Criar edit.js**  
  Usar `useBlockProps`, componentes (ex.: RichText, TextControl), `props.attributes`, `props.setAttributes`.

- [ ] **8. Criar save.js**  
  Retornar elemento React com o markup a ser salvo; usar `useBlockProps.save()` e `RichText.Content` quando for conteúdo rico.

- [ ] **9. Registrar o bloco no PHP**  
  `register_block_type_from_metadata( plugin_dir_path(__FILE__) . 'build' )` (ou caminho do block.json) para carregar a partir do **build/**.

- [ ] **10. Rodar build**  
  `npm run build`; conferir se `build/index.js` e CSS foram gerados.

- [ ] **11. Testar no editor**  
  Ativar o plugin; abrir um post; inserir o bloco; editar atributos; publicar e ver o conteúdo no front.

---

## Recursos externos

- [Create Block (getting started)](https://developer.wordpress.org/block-editor/getting-started/create-block/)
- [Block Editor Handbook](https://developer.wordpress.org/block-editor/)
- [block.json reference](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/)

Documentos desta pasta: **12-bloco-js-estrutura.md**, **13-block-json-completo.md**, **14-edit-js-minimo.md**, **15-save-js-serializacao.md**, **16-build-wordpress-scripts.md**.
