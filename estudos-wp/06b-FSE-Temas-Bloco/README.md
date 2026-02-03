# 06b – Full Site Editing (FSE) e temas de bloco

Complemento à Fase 6: **temas de bloco** (block theme), **theme.json**, **template parts** e **Site Editor**.

---

## O que é FSE e block theme

- **Tema clássico:** templates em PHP (header.php, footer.php, index.php, etc.); o editor edita só o conteúdo do post/página.
- **Tema de bloco (block theme):** não usa PHP para templates; usa arquivos HTML (ou blocos) em `templates/` e `parts/`, estilos e configuração em **theme.json**. O **Site Editor** permite editar header, footer, arquivo e páginas de template.
- **Quando usar FSE:** projetos que querem edição visual completa do site (header, footer, arquivo) e design consistente via theme.json; temas novos podem ser 100% block theme.

---

## Conteúdo da pasta

| # | Arquivo | Conteúdo |
|---|---------|----------|
| 1 | [01-fse-block-theme.md](pratica/01-fse-block-theme.md) | O que é FSE e block theme; diferença para tema clássico; Site Editor |
| 2 | [02-theme-json-estrutura.md](pratica/02-theme-json-estrutura.md) | theme.json – version, settings, styles, templateParts |
| 3 | [03-theme-json-estilos-globais.md](pratica/03-theme-json-estilos-globais.md) | Paleta, tipografia, variáveis CSS geradas |
| 4 | [04-template-parts.md](pratica/04-template-parts.md) | Template parts – pasta parts/, registro e uso |
| 5 | [05-templates-index-single-archive.md](pratica/05-templates-index-single-archive.md) | Templates – index, single, archive; relação com hierarchy |
| 6 | [06-block-theme-minimo-passos.md](pratica/06-block-theme-minimo-passos.md) | Passo a passo: criar block theme mínimo |
| 7 | [07-recursos-fse.md](pratica/07-recursos-fse.md) | Recursos externos – Block Editor Handbook, theme.json |

---

## Recursos externos

- [Block Editor Handbook – Full Site Editing](https://developer.wordpress.org/block-editor/how-to-guides/themes/theme-json/)
- [theme.json reference](https://developer.wordpress.org/block-editor/reference-guides/theme-json/)
