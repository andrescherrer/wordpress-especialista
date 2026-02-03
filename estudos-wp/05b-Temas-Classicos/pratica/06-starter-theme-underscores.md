# Starter theme (Underscores)

O que é **_s** (Underscores); como usar como base; estrutura de pastas; **template parts** (get_template_part).

---

## O que é Underscores (_s)

- **Starter theme** oficial do WordPress: [underscores.me](https://underscores.me/).
- Gera um tema mínimo com estrutura de pastas, template hierarchy, suporte a title-tag, post-thumbnails, menus, widgets, etc.
- Você baixa com um nome de tema customizado e desenvolve em cima.

---

## Estrutura de pastas (típica)

- **inc/** – funções auxiliares (template-tags.php, etc.).
- **template-parts/** – partes reutilizáveis (content.php, content-none.php, etc.).
- **js/**, **css/** (ou **assets/**) – scripts e estilos.
- **header.php**, **footer.php**, **sidebar.php**, **index.php**, **single.php**, **page.php**, **archive.php**, **404.php**, **search.php**, etc.
- **functions.php** – enqueue e suportes; pode incluir arquivos de **inc/**.

---

## get_template_part

- **get_template_part('template-parts/content', get_post_type());** – carrega `template-parts/content.php` ou `template-parts/content-{post-type}.php`.
- Evita repetir o mesmo bloco de HTML em vários templates; centraliza o “conteúdo do post” em um arquivo.

---

## Como usar como base

1. Gerar o tema em [underscores.me](https://underscores.me/) com o nome do projeto.
2. Ativar e desenvolver: adicionar estilos, ajustar templates, adicionar opções no Customizer.
3. Ou usar como **parent** e criar um **child theme** (ver **05-child-theme.md**) para o projeto.

---

## Recursos

- [Underscores](https://underscores.me/)
- [Theme Handbook](https://developer.wordpress.org/themes/)
- Ver **01-estrutura-minima-tema.md**, **05-child-theme.md**, **07-plugin-vs-tema.md**.
