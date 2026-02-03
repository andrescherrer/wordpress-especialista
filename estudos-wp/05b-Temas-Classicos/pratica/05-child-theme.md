# Child theme

Criar **child theme**; **@import** ou **wp_enqueue_style** do parent; **override** de templates e functions; quando usar.

---

## Estrutura mínima

- Pasta: `meu-tema-child/`
- **style.css** com cabeçalho que declara o **Template** (nome da pasta do tema pai):

```css
/*
Theme Name: Meu Tema Child
Template: meu-tema-pai
Version: 1.0
*/
@import url("../meu-tema-pai/style.css");
```

- **functions.php** (opcional): enqueue do style do parent e do child (recomendado em vez de @import para performance):

```php
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('child-style', get_stylesheet_uri(), ['parent-style']);
});
```

---

## Override de templates

- Copiar um template do tema pai para o child (ex.: **header.php**, **single.php**).
- O WordPress carrega primeiro do **child**; se existir no child, usa esse; senão usa do parent.
- Hierarquia continua igual; apenas a origem dos arquivos passa a ser a pasta do child quando o arquivo existir lá.

---

## Override de functions

- O **functions.php** do child é carregado **depois** do do parent.
- Para “desfazer” algo do parent: remover hooks com **remove_action** / **remove_filter** (precisa da mesma prioridade) ou adicionar filtros/ações com prioridade maior que sobrescrevam o comportamento.

---

## Quando usar

- **Customizar** um tema sem alterar os arquivos do tema pai (atualizações do parent não sobrescrevem suas mudanças).
- **Sites específicos** que precisam de alterações de layout ou comportamento mantendo a base do tema pai.
- **Starter theme** (ex.: Underscores) como parent e child para o projeto real.

Ver **06-starter-theme-underscores.md** e **07-plugin-vs-tema.md**; [Child Themes](https://developer.wordpress.org/themes/advanced-topics/child-themes/).
