# Automação, CI/CD e boas práticas – WP-CLI Ferramentas

Referência rápida. Fonte: **009-WordPress-Fase-9-WP-CLI-Ferramentas.md**.

---

## Script de deploy (bash)

```bash
#!/bin/bash
set -e

# Backup
wp db export backup_$(date +%Y%m%d_%H%M%S).sql

# Testes
wp meu-plugin test || { echo "Testes falharam!"; exit 1; }

# Migrações
wp meu-plugin migrate

# Cache
wp cache flush
wp transient delete --all

# Integridade
wp meu-plugin check-integrity
```

Execute na **raiz do WordPress** (onde está o `wp-config.php`).

---

## CI/CD – GitHub Actions (resumo)

- **Job de teste:** Ubuntu + serviço MySQL; checkout; setup PHP (shivammathur/setup-php); instalar WP-CLI (curl phar, chmod, mv para /usr/local/bin/wp).
- **WordPress:** `wp core download --path=/tmp/wordpress`; `wp config create`; `wp core install`.
- **Plugin:** copiar repositório para `wp-content/plugins/meu-plugin`; `wp plugin activate meu-plugin`.
- **Comandos:** `wp meu-plugin test`; `wp meu-plugin check-integrity`.
- **Deploy (opcional):** job que depende do test; só em `refs/heads/main`; usar secrets (DEPLOY_KEY, SERVER_HOST, PLUGIN_PATH); rsync ou SSH para servidor.

---

## Documentação de comandos (PHPDoc)

- **## DESCRIPTION** – descrição detalhada.
- **## OPTIONS** – `<arg>` obrigatório; `[--option=<value>]` opcional; `[--flag]` booleano; use `---` e `default:` quando fizer sentido.
- **## EXAMPLES** – exemplos de uso (wp meu-plugin comando ...).
- **@when after_wp_load** – garantir que o WordPress está carregado.

---

## Tratamento de erros

- Validar argumentos no início: `if ( empty( $args[0] ) ) { WP_CLI::error( '...' ); }`
- Operações perigosas: exigir `--confirm` ou `WP_CLI::confirm()`
- Try/catch em processamento em lote: `WP_CLI::warning( $e->getMessage() );` ou continuar
- `WP_CLI::error( $msg, false );` – segundo parâmetro false = não encerra execução (útil para listar vários erros)

---

## Output e progresso

- **format_items:** `\WP_CLI\Utils\format_items( 'table', $items, array( 'col1', 'col2' ) );` ou `'json'`, `'csv'`
- **Progress bar:** `make_progress_bar` → `tick()` no loop → `finish()`
- **success / warning / log:** usar de forma consistente para resultado final e avisos

---

## Segurança e performance

- **Sanitize:** `sanitize_text_field`, `absint` em opções e argumentos
- **Queries:** sempre `$wpdb->prepare()` para dados do usuário
- **Lotes:** processar em chunks com `array_chunk( $items, 100 )` para evitar timeout e memória
- **Cache:** `wp_suspend_cache_invalidation( true );` durante operações em massa pode ajudar; restaurar com `false` depois
