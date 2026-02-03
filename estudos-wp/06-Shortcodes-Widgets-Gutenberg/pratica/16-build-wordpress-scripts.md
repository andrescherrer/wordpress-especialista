# Build com npm e @wordpress/scripts

Guia mínimo para construir o bloco com **@wordpress/scripts** e enfileirar no PHP.

---

## Comandos

| Comando | Uso |
|---------|-----|
| **`npm run build`** | Build de produção; saída em `build/`. |
| **`npm run start`** | Modo desenvolvimento (watch); recompila ao salvar. |

---

## Setup

```bash
cd wp-content/plugins/meu-plugin-bloco
npm init -y
npm install --save-dev @wordpress/scripts
```

No **package.json**:

```json
{
  "scripts": {
    "build": "wp-scripts build",
    "start": "wp-scripts start"
  }
}
```

Por padrão o script espera **src/index.js** como entrada e gera **build/index.js** (e CSS se houver import de .css). Para múltiplos blocos ou entrada com outro nome, use **webpack.config.js** ou opções do wp-scripts.

---

## Estrutura típica

- **src/index.js** – importa e registra o bloco (registerBlockType) ou apenas exporta edit/save; pode importar **block.json** com import metadata.
- **block.json** na raiz ou em **src/**; o build pode copiar para build/.
- **src/edit.js**, **src/save.js** – importados por index.js.
- **style.css** / **editor.css** – podem ser importados em index.js (`import './style.css'`) para o build gerar **build/index.css** e **build/style-index.css**.

---

## webpack.config.js (customização)

Geralmente não é necessário; @wordpress/scripts já usa Webpack. Se precisar:

```javascript
const defaultConfig = require('@wordpress/scripts/config/webpack.config');
module.exports = {
  ...defaultConfig,
  entry: './src/index.js',
  output: {
    path: __dirname + '/build',
    filename: 'index.js'
  }
};
```

---

## Enqueue no PHP

Registrar o bloco **a partir do block.json** (recomendado):

```php
register_block_type(
  plugin_dir_path(__FILE__) . 'build',
  array(
    'render_callback' => 'meu_bloco_render', // só se for dinâmico
  )
);
```

Ou passando o caminho do diretório que contém **block.json**; o core lê `editorScript`, `editorStyle`, `style` do block.json e enfileira. Os handles são derivados do nome do bloco.

Se o block.json estiver na raiz do plugin e os arquivos compilados em **build/**:

```php
register_block_type_from_metadata(
  plugin_dir_path(__FILE__) . 'block.json',
  array(
    'render_callback' => 'meu_bloco_render',
  )
);
```

E no block.json os caminhos devem apontar para os arquivos em build, por exemplo `"editorScript": "file:./build/index.js"`.

---

## Resumo

1. **npm run build** → gera `build/`.
2. **block.json** com `editorScript`, `editorStyle`, `style` apontando para `file:./build/...`.
3. **register_block_type** (ou **register_block_type_from_metadata**) com o caminho do block.json ou do diretório com block.json.
4. No editor e no front o core enfileira os assets definidos no block.json.

Ver **17-checklist-bloco-js.md** para o passo a passo completo “Meu primeiro bloco JS”.
