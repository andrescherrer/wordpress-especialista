# Referência rápida – WP-CLI Fundamentos

Uma página. Use Ctrl+F. Fonte: **007-WordPress-Fase-7-WP-CLI-Fundamentos.md**.

---

## Registrar comando

- **if (!defined('WP_CLI') || !WP_CLI) return;** – carregar arquivo só quando WP-CLI estiver disponível.
- **WP_CLI::add_command( 'nome-comando', 'Nome_Classe' );** – comando principal (métodos = subcomandos).
- **WP_CLI::add_command( 'meu-plugin db', 'Classe_DB' );** – subcomando: `wp meu-plugin db check`.

---

## Estrutura do comando

- Classe com métodos **public**; cada método = um subcomando: `cleanup` → `wp meu-plugin cleanup`.
- Assinatura: **function nome( $args, $assoc_args )** – $args = argumentos posicionais, $assoc_args = opções (--days=30).
- PHPDoc: **## OPTIONS**, **## EXAMPLES**, **@when after_wp_load** (recomendado).

---

## Parâmetros (PHPDoc)

- **&lt;param&gt;** – argumento obrigatório (posicional).
- **[--option=&lt;value&gt;]** – opção; use **default:** e **options:** quando fizer sentido.
- Exemplo: `[--days=<days>]` com `default: 30`; `[--dry-run]` para simulação.

---

## Output

- **WP_CLI::log( 'texto' );** – mensagem neutra.
- **WP_CLI::success( 'texto' );** – sucesso (verde).
- **WP_CLI::warning( 'texto' );** – aviso (amarelo).
- **WP_CLI::error( 'texto', $exit );** – erro; segundo parâmetro false = não encerra execução.
- **WP_CLI::line( '%Gverde%n %Rvermelho%n %Yamarelo%n' );** – cores.
- **WP_CLI::table( $headers, $rows );** – tabela (array de arrays).
- **WP_CLI::confirm( 'Continuar?' );** – pede Y/n; sai com erro se não confirmar.
- **WP_CLI::prompt( 'Pergunta', $default );** – lê linha; default pode ser string ou array (menu).
- **WP_CLI::prompt( 'Pergunta', $default, $validator );** – validação (callable que retorna bool).
- **\WP_CLI\Utils\make_progress_bar( 'Label', $total );** – progress bar; tick() e finish().

---

## Comandos nativos (resumo)

- **Core:** wp core download|install|update|version; wp config create.
- **Plugins:** wp plugin list|install|activate|deactivate|update|delete.
- **Temas:** wp theme list|install|activate|update|delete.
- **Posts:** wp post list|create|update|delete|get.
- **Usuários:** wp user list|create|update|delete|get; wp role list.
- **DB:** wp db export|import|query|optimize|repair|reset|tables.
- **Cache:** wp cache flush; wp transient delete --all.
- **Search-replace:** wp search-replace 'old' 'new' [--dry-run] [--all-tables].

---

## Boas práticas

- Sanitizar entradas; usar **$wpdb->prepare()** em todas as queries.
- Pedir **WP_CLI::confirm()** antes de operações destrutivas (delete, reset).
- Oferecer **--dry-run** em comandos que alteram dados.
- Usar progress bar em loops longos; documentar com ## EXAMPLES.
