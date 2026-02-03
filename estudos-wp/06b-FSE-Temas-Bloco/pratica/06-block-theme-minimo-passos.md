# Criar um block theme mínimo – passo a passo

Estrutura de pastas; **theme.json** mínimo; templates e parts mínimos; ativar e preview no Site Editor.

---

## 1. Estrutura de pastas

```
meu-block-theme/
  style.css          (obrigatório – header do tema + estilos opcionais)
  theme.json         (obrigatório para block theme)
  templates/
    index.html       (obrigatório – fallback)
    single.html
    archive.html
  parts/
    header.html
    footer.html
```

---

## 2. style.css (cabeçalho do tema)

```css
/*
Theme Name: Meu Block Theme
Theme URI: https://example.com
Description: Tema de bloco mínimo.
Version: 1.0
Requires at least: 6.0
Tested up to: 6.4
Requires PHP: 7.4
License: GPL v2 or later
*/
```

---

## 3. theme.json mínimo

- **version**: 2.
- **settings**: paleta (ex.: primary, secondary), fontSizes (ex.: small, medium, large).
- **styles**: opcional – cor de fundo e texto usando os presets.
- **templateParts**: header e footer (name, title, area).

Ver **02-theme-json-estrutura.md** e **03-theme-json-estilos-globais.md** para exemplos completos.

---

## 4. templates/index.html

Conteúdo mínimo: uma template part de header, conteúdo do post, template part de footer. Exemplo:

```html
<!-- wp:template-part {"slug":"header","tagName":"header"} /-->
<!-- wp:post-content {"layout":{"type":"constrained"}} /-->
<!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->
```

---

## 5. parts/header.html e parts/footer.html

- **header.html**: blocos para logo/título e navegação (ex.: wp:site-title, wp:navigation).
- **footer.html**: blocos para copyright ou links.

Os slugs (header, footer) devem bater com os declarados em **theme.json** em **templateParts** (name/slug).

---

## 6. single.html e archive.html

- **single.html**: semelhante ao index, com post-content (e opcionalmente post-title, post-date).
- **archive.html**: query-loop ou post-content conforme a necessidade; template part header/footer iguais.

---

## 7. Ativar e testar

1. Copiar a pasta do tema para **wp-content/themes/meu-block-theme**.
2. Em **Aparência → Temas**, ativar “Meu Block Theme”.
3. Abrir **Aparência → Editor** (Site Editor); editar templates e parts; visualizar no front.

---

## Recursos

- **01-fse-block-theme.md** – conceitos FSE e block theme.
- **07-recursos-fse.md** – links oficiais.
