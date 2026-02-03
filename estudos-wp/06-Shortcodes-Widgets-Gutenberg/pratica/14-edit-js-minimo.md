# edit.js mínimo (React) – bloco Gutenberg

Exemplo mínimo de **edit.js** usando APIs do Block Editor (sem build externo pode usar script direto; com `@wordpress/scripts` usa import).

---

## Dependências do WordPress (global no editor)

- **`wp.element`** – React (createElement) e React hooks.
- **`wp.blocks.useBlockProps`** – atributos de wrapper do bloco (classes, etc.).
- **`wp.components`** – TextControl, RichText, Button, etc.
- **`wp.blockEditor`** – useBlockProps, RichText (às vezes vem daqui).

---

## Exemplo mínimo (edit.js)

```javascript
(function (wp) {
  var el = wp.element.createElement;
  var useBlockProps = wp.blockEditor.useBlockProps;
  var RichText = wp.blockEditor.RichText;

  wp.blocks.registerBlockType('meu-plugin/meu-bloco', {
    edit: function (props) {
      var blockProps = useBlockProps();
      var content = props.attributes.content || '';

      return el(
        'div',
        blockProps,
        el(RichText, {
          tagName: 'p',
          value: content,
          onChange: function (newContent) {
            props.setAttributes({ content: newContent });
          },
          placeholder: 'Digite aqui…'
        })
      );
    },
    save: function (props) {
      return wp.element.createElement(
        'p',
        useBlockProps.save(),
        wp.element.createElement(wp.blockEditor.RichText.Content, {
          value: props.attributes.content
        })
      );
    }
  });
})(window.wp);
```

---

## Com @wordpress/scripts (ES modules)

```javascript
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { RichText } from '@wordpress/block-editor';

registerBlockType('meu-plugin/meu-bloco', {
  edit: function (props) {
    const blockProps = useBlockProps();
    const { attributes, setAttributes } = props;

    return (
      <div {...blockProps}>
        <RichText
          tagName="p"
          value={attributes.content}
          onChange={(content) => setAttributes({ content })}
          placeholder="Digite aqui…"
        />
      </div>
    );
  },
  save: function (props) {
    const blockProps = useBlockProps.save();
    return (
      <p {...blockProps}>
        <RichText.Content value={props.attributes.content} />
      </p>
    );
  }
});
```

---

## Leitura e atualização de attributes

- **Leitura:** `props.attributes.nomeDoAtributo`.
- **Atualização:** `props.setAttributes({ nomeDoAtributo: valor })`.
- O valor é persistido pelo core (no block comment ou no HTML, conforme `source` no block.json e o retorno de `save()`).

---

## Componentes úteis (wp.components / wp.blockEditor)

- **TextControl** – input de texto.
- **RichText** – conteúdo editável rico (bold, link, etc.).
- **SelectControl** – select.
- **CheckboxControl** – checkbox.
- **InspectorControls** – painel na sidebar (e.g. configurações do bloco).

Ver **15-save-js-serializacao.md** para o `save` e quando usar bloco estático vs dinâmico (render_callback em PHP).
