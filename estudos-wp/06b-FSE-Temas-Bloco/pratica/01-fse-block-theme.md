# O que é FSE e block theme

Diferença entre **tema clássico** e **tema de bloco**; papel do **Site Editor**; quando usar FSE.

---

## Tema clássico vs tema de bloco

| Aspecto | Tema clássico | Tema de bloco (FSE) |
|---------|----------------|----------------------|
| **Templates** | PHP (header.php, footer.php, index.php, single.php, etc.) | HTML em `templates/` e `parts/` (blocos) |
| **Estilos globais** | style.css, CSS customizado | **theme.json** (paleta, tipografia, espaçamento) |
| **Header/Footer** | Editados em PHP ou Customizer | Editados no **Site Editor** (blocos) |
| **Hierarquia** | Template hierarchy em PHP | `templates/index.html`, `single.html`, `archive.html`, etc. |
| **Obrigatório** | style.css + index.php | style.css + theme.json + templates (ex.: index.html) |

---

## Site Editor

- Acesso: **Aparência → Editor** (ou **Site Editor** no admin).
- Permite editar: **templates** (página inicial, single, archive, 404…), **template parts** (header, footer), **estilos** (quando o tema expõe via theme.json).
- O usuário arrasta e configura blocos para header, footer e layout do site; as alterações são salvas como parte do tema (ou do banco, conforme a versão).

---

## Quando usar FSE

- **Usar tema de bloco** quando: o projeto quer edição visual completa do site (header, footer, arquivo) sem tocar em PHP; design system via theme.json; temas novos “block-first”.
- **Manter tema clássico** quando: tema legado; necessidade de lógica PHP pesada nos templates; cliente ainda não usa o Site Editor.

---

## Próximos passos

- **02-theme-json-estrutura.md** – estrutura do theme.json.
- **06-block-theme-minimo-passos.md** – criar um block theme mínimo do zero.
