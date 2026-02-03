# Boas práticas – Comandos WP-CLI

Checklist e estrutura de projeto. Fonte: **007-WordPress-Fase-7-WP-CLI-Fundamentos.md**.

---

## Checklist de qualidade

### Estrutura

- [ ] Carregar arquivos CLI apenas quando `defined('WP_CLI') && WP_CLI`
- [ ] Usar namespace/nome próprio para o comando (`meu-plugin`, não `wp`)
- [ ] Usar `@when after_wp_load` nas callbacks
- [ ] Documentar com PHPDoc: `## OPTIONS`, `## EXAMPLES`

### Parâmetros

- [ ] `<param>` para argumentos obrigatórios (posicionais)
- [ ] `[--option]` para opcionais
- [ ] Definir `default:` quando fizer sentido
- [ ] Usar `options:` para valores permitidos em opções

### Output

- [ ] `WP_CLI::success()` para sucesso
- [ ] `WP_CLI::warning()` para aviso
- [ ] `WP_CLI::error()` para erro (e encerrar quando for crítico)
- [ ] `WP_CLI::log()` para informação
- [ ] `WP_CLI::table()` para listagens

### Segurança

- [ ] Sanitizar toda entrada do usuário (`sanitize_text_field`, `absint`, etc.)
- [ ] Usar `$wpdb->prepare()` em todas as queries
- [ ] Pedir `WP_CLI::confirm()` antes de deletar ou resetar dados
- [ ] Oferecer `--dry-run` em comandos que alteram muitos dados

### Performance

- [ ] Usar progress bar em operações longas (loops)
- [ ] Executar queries em lotes para grandes volumes
- [ ] Considerar transações quando fizer várias writes seguidas

---

## Estrutura de projeto (plugin com CLI)

```
meu-plugin/
├── includes/
│   ├── class-plugin.php
│   ├── class-cli.php              # Comando principal (cleanup, status, reset)
│   └── cli/
│       ├── class-db-command.php   # wp meu-plugin db
│       ├── class-data-command.php # wp meu-plugin data
│       ├── class-setup-command.php
│       └── class-scaffold-command.php
├── src/
├── meu-plugin.php
```

---

## Registrar comandos no plugin

No arquivo principal do plugin (ex.: `meu-plugin.php`):

```php
if ( defined( 'WP_CLI' ) && WP_CLI ) {
    require_once MEU_PLUGIN_PATH . 'includes/class-cli.php';
    require_once MEU_PLUGIN_PATH . 'includes/cli/class-db-command.php';
    require_once MEU_PLUGIN_PATH . 'includes/cli/class-data-command.php';
    require_once MEU_PLUGIN_PATH . 'includes/cli/class-setup-command.php';

    WP_CLI::add_command( 'meu-plugin', 'Meu_Plugin_CLI_Command' );
    WP_CLI::add_command( 'meu-plugin db', 'Meu_Plugin_DB_CLI_Command' );
    WP_CLI::add_command( 'meu-plugin data', 'Meu_Plugin_Data_CLI_Command' );
    WP_CLI::add_command( 'meu-plugin setup', 'Meu_Plugin_Setup_CLI_Command' );
}
```

Cada arquivo em `includes/cli/` deve começar com:

```php
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
    return;
}
```

Assim, o código CLI só é carregado quando o WP-CLI está em uso.
