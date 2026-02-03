# Escape e semântica

Uso de **esc_html**, **esc_attr**; elementos **semânticos** (`<nav>`, `<main>`, `<button>` vs `<div onclick>`); **labels** em formulários.

---

## Escape

- **esc_html()** para conteúdo de texto dentro de HTML; **esc_attr()** para atributos.
- Escape **sempre** que o conteúdo vier do usuário ou do banco; isso evita XSS e ajuda leitores de tela a interpretar o conteúdo corretamente.

```php
<label for="nome"><?php echo esc_html($label); ?></label>
<input type="text" id="nome" value="<?php echo esc_attr($valor); ?>">
```

---

## Elementos semânticos

| Uso | Preferir | Evitar |
|-----|----------|--------|
| Navegação | `<nav>` | `<div class="nav">` |
| Conteúdo principal | `<main>` | `<div id="main">` |
| Botão de ação | `<button type="button">` | `<div onclick="...">` ou `<a href="#">` sem role |
| Título de seção | `<h1>`–`<h6>` em ordem lógica | Pular níveis (h1 → h3) |
| Lista de itens | `<ul>`/`<li>` ou `<ol>` | Divs sem lista |

- **<button>** é focável por teclado e anuncia “botão” para leitores de tela; **div** com onclick não.
- **<nav>** e **<main>** permitem que o usuário pule por regiões (landmarks).

---

## Labels em formulários

- Cada campo deve ter um **label** associado:
  - `<label for="id_do_campo">Texto</label>` e `<input id="id_do_campo">`, ou
  - `aria-label="Texto"` no input (quando não for possível label visível).
- Mensagens de erro: usar **aria-describedby** apontando para o id da mensagem, ou **aria-invalid="true"** no campo.

Ver **03-aria-basico.md** para ARIA e **06-checklist-a11y-plugin.md** para o checklist completo.
