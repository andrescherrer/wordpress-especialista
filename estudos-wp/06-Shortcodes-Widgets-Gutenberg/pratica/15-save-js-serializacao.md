# save.js e serialização do bloco

O **save** retorna o elemento React que vira o HTML salvo no conteúdo do post. Atributos podem ser salvos no HTML (data-*) ou apenas no block comment.

---

## Retorno de save() – elemento React

```javascript
save: function (props) {
  const blockProps = useBlockProps.save();
  return (
    <p {...blockProps}>
      <RichText.Content value={props.attributes.content} />
    </p>
  );
}
```

- **useBlockProps.save()** – classes e atributos de wrapper (align, etc.).
- **RichText.Content** – imprime o HTML do conteúdo rico sem ser editável.

---

## Atributos salvos no HTML (data-*)

Para que o core leia o atributo do HTML ao carregar o bloco, use **source** no block.json:

```json
"attributes": {
  "titulo": {
    "type": "string",
    "source": "attribute",
    "selector": ".bloco-titulo",
    "attribute": "data-titulo"
  }
}
```

No save:

```javascript
<p {...blockProps} className="bloco-titulo" data-titulo={attributes.titulo}>
  ...
</p>
```

Assim o valor fica no markup e é restaurado ao abrir o post no editor.

---

## Bloco estático vs dinâmico

| Tipo | Quando usar | Quem gera o HTML no front |
|------|-------------|----------------------------|
| **Estático** | Conteúdo fixo no momento do save (texto, opções escolhidas no editor). | save.js (HTML salvo no post). |
| **Dinâmico** | Conteúdo que muda (dados do banco, data atual, listas). | PHP `render_callback`; save pode salvar só um placeholder ou nada. |

Para **dinâmico**: em block.json use `"save": { "source": "null" }` ou retorne `null` no save; no PHP registre o bloco com `render_callback`. O front sempre chama o PHP; o editor usa edit.js para UI e pode mostrar um placeholder no preview.

---

## Exemplo save com data-*

```javascript
save: function (props) {
  const { attributes } = props;
  const blockProps = useBlockProps.save({
    className: 'meu-bloco',
    'data-titulo': attributes.titulo,
    'data-ativo': attributes.ativo ? '1' : '0'
  });
  return (
    <div {...blockProps}>
      <h3>{attributes.titulo}</h3>
      <RichText.Content value={attributes.content} tagName="div" />
    </div>
  );
}
```

Ver **12-bloco-js-estrutura.md** e **17-checklist-bloco-js.md** para o fluxo completo; **04-bloco-dinamico-php.php** para render_callback em PHP.
