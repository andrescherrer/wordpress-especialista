# 07 – WP-CLI Fundamentos

**Foco: prática.** Teoria só quando precisar, sempre à mão.

---

## Comece pela prática

| # | Arquivo | O que você vai fazer |
|---|---------|----------------------|
| 1 | [01-comando-customizado.php](pratica/01-comando-customizado.php) | Comando principal: cleanup, status, reset com WP_CLI::add_command |
| 2 | [02-subcomandos.php](pratica/02-subcomandos.php) | Subcomando `db`: check, repair, optimize (estrutura hierárquica) |
| 3 | [03-output-formatacao.php](pratica/03-output-formatacao.php) | log, success, warning, error, table, progress bar e cores |
| 4 | [04-comando-interativo.php](pratica/04-comando-interativo.php) | setup com prompt, confirm e validação |
| 5 | [05-comando-crud-data.php](pratica/05-comando-crud-data.php) | Subcomando `data`: list, create, update, delete (CRUD) |
| 6 | [06-comandos-essenciais.md](pratica/06-comandos-essenciais.md) | Referência rápida: core, plugin, theme, post, user, db |
| 7 | [07-boas-praticas.md](pratica/07-boas-praticas.md) | Checklist, estrutura de projeto e registro no plugin |

**Como usar:** copie as classes para um plugin e registre com `WP_CLI::add_command()`. Carregue os arquivos apenas quando `defined('WP_CLI') && WP_CLI`. Detalhes em [pratica/README.md](pratica/README.md).

---

## Teoria quando precisar

- **No código:** cada arquivo `.php` tem no topo um bloco **REFERÊNCIA RÁPIDA** com a sintaxe daquele tópico.
- **Uma página:** [REFERENCIA-RAPIDA.md](REFERENCIA-RAPIDA.md) – comandos customizados, output, subcomandos (Ctrl+F).
- **Fonte completa:** [007-WordPress-Fase-7-WP-CLI-Fundamentos.md](../../007-WordPress-Fase-7-WP-CLI-Fundamentos.md) na raiz do repositório.

---

## Objetivos da Fase 7

- Usar comandos WP-CLI para core, plugins, temas, posts, usuários e database
- Criar comandos customizados com `WP_CLI::add_command()` e PHPDoc (OPTIONS, EXAMPLES)
- Construir subcomandos hierárquicos (ex.: `wp meu-plugin db check`)
- Implementar output formatado (log, success, warning, table, progress) e comandos interativos (prompt, confirm)
- Aplicar boas práticas: sanitizar entrada, `$wpdb->prepare()`, confirmação antes de deletar
