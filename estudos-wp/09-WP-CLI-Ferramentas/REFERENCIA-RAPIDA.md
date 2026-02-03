# Referência rápida – WP-CLI Ferramentas

Uma página. Use Ctrl+F. Fonte: **009-WordPress-Fase-9-WP-CLI-Ferramentas.md**. Requer Fase 7 (fundamentos).

---

## Comandos avançados

- **Progress bar:** `$progress = \WP_CLI\Utils\make_progress_bar( 'Label', $total );` → `$progress->tick();` no loop → `$progress->finish();`
- **--dry-run:** simular sem alterar; usar `isset( $assoc_args['dry-run'] )`.
- **Try/catch:** em processamento em lote, capturar exceções e usar `WP_CLI::warning()` ou continuar.
- **check-integrity:** verificar tabelas (SHOW TABLES), opções obrigatórias, arquivos críticos; reportar com success/warning.

---

## Subcomando db (init, export, reset)

- **init:** `require_once ABSPATH . 'wp-admin/includes/upgrade.php';` + `dbDelta( $sql );` (CREATE TABLE IF NOT EXISTS).
- **export:** `$wpdb->get_results( ..., ARRAY_A );` → JSON (wp_json_encode) ou CSV (fputcsv em php://memory); `file_put_contents( $output, $content );`
- **reset:** pedir `--confirm`; DROP TABLE das tabelas do plugin; chamar init de novo.
- **format_items:** `\WP_CLI\Utils\format_items( 'table', $items, array( 'col1', 'col2' ) );` para tabela no output.

---

## Import wizard

- **WP_CLI::prompt( 'Pergunta', $default );** para caminho do arquivo e opções.
- **Validação:** loop enquanto valor inválido; ou callable em prompt (Fase 7).
- **Confirmar antes de importar:** `WP_CLI::confirm()` ou prompt 's/n'.
- **Progress:** barra com total de linhas/registros; tick a cada item; insert em lote se possível.

---

## Scaffolding

- **Gerar arquivo:** template com placeholders `{CLASS_NAME}`; `str_replace`; `file_put_contents( $path, $content );`
- **Criar diretório:** `if ( ! is_dir( $dir ) ) { mkdir( $dir, 0755, true ); }`
- **Nome da classe:** sanitizar (ucwords, remover hífens); nome do arquivo: snake_case ou kebab-case.

---

## Migrations

- **Listar migrações:** arquivos em `migrations/*.php`; ordenar; comparar com opção `meu_plugin_migrations` (array de executadas).
- **Executar:** require do arquivo; classe `Migration_Nome` com método `up()`; após rodar, adicionar nome à opção.
- **Rollback:** última migração na opção; require; método `down()`; remover da opção.
- **--step=N:** executar apenas N migrações pendentes (array_slice).

---

## Debug (report, performance, clear)

- **report:** montar string com versão WP, PHP, opções do plugin, plugins ativos; `file_put_contents` se --output.
- **performance:** microtime antes/depois de blocos (queries, cache, hooks); logar tempo.
- **clear:** DELETE de tabela de logs de debug; WP_CLI::success.

---

## Automação e CI/CD

- **Script bash:** `wp db export`, `wp meu-plugin test`, `wp meu-plugin migrate`, `wp cache flush`; `set -e` para falhar no primeiro erro.
- **GitHub Actions:** job com MySQL service; setup PHP + WP-CLI; wp core download + config create + core install; copiar plugin; wp plugin activate; wp meu-plugin test e check-integrity. Deploy (rsync/SSH) em branch main após test.

---

## Boas práticas

- **PHPDoc:** ## DESCRIPTION, ## OPTIONS, ## EXAMPLES, @when after_wp_load.
- **Erros:** validar argumentos; WP_CLI::error() para falha; try/catch com WP_CLI::warning ou rethrow.
- **Segurança:** sanitize entrada; $wpdb->prepare em queries.
- **Performance:** processar em lotes (array_chunk); wp_suspend_cache_invalidation(true) se útil.
