# ARIA básico

**aria-label**, **aria-describedby**, **role**, **aria-expanded**; quando usar; exemplo em botão de menu e modal.

---

## Atributos ARIA comuns

| Atributo | Uso |
|----------|-----|
| **aria-label** | Nome acessível para o elemento quando o texto visível não basta (ex.: botão com só ícone). |
| **aria-describedby** | ID do elemento que descreve este (ex.: mensagem de erro ou hint). |
| **role** | Papel do elemento (button, dialog, menu, etc.) quando o HTML nativo não comunica (ex.: div que age como botão). |
| **aria-expanded** | true/false para controles que expandem/colapsam (menu, accordion). |
| **aria-hidden** | true para esconder do leitor de tela (decorativo). |

---

## Quando usar

- **aria-label:** botão com apenas ícone (ex.: “Fechar”, “Menu”).
- **aria-describedby:** input com texto de ajuda ou erro (id do parágrafo da mensagem).
- **role="button":** só se não puder usar `<button>` (ex.: div que dispara ação; garantir teclado: keydown Enter/Space).
- **aria-expanded:** menu dropdown, accordion; atualizar ao abrir/fechar.

---

## Exemplo – botão de menu

```html
<button type="button" aria-label="Abrir menu" aria-expanded="false" aria-controls="menu-principal" id="btn-menu">
  <span aria-hidden="true">☰</span>
</button>
<nav id="menu-principal" aria-label="Menu principal" hidden>
  ...
</nav>
```

- Ao abrir o menu: `aria-expanded="true"`, remover `hidden` do nav.
- Ao fechar: `aria-expanded="false"`, adicionar `hidden`.

---

## Exemplo – modal

```html
<div role="dialog" aria-modal="true" aria-labelledby="titulo-modal" aria-describedby="desc-modal">
  <h2 id="titulo-modal">Título</h2>
  <p id="desc-modal">Descrição.</p>
  <button type="button" aria-label="Fechar">×</button>
</div>
```

- Foco deve ir para o modal ao abrir e ficar preso (trap) até fechar; ao fechar, devolver foco ao elemento que abriu.

Ver **06-checklist-a11y-plugin.md** e **07-recursos-a11y.md** para mais referências.
