# Prática – Como usar (WP-CLI)

1. **Onde rodar:** na raiz do WordPress (onde está o `wp-config.php`). Use `wp meu-plugin status` etc.
2. **No plugin:** inclua os arquivos CLI apenas quando `defined('WP_CLI') && WP_CLI`; registre com `WP_CLI::add_command()`.
3. **Testar:** ative o plugin e execute `wp meu-plugin` (ou o nome do seu comando) para listar subcomandos; use `--help` em qualquer subcomando.

**Arquivos 08–10:** argumento posicional e assoc (08), when after_wp_load (09), tabela comandos úteis (10).

**Teoria rápida:** no topo de cada `.php` há um bloco **REFERÊNCIA RÁPIDA**. Tudo em uma página: [../REFERENCIA-RAPIDA.md](../REFERENCIA-RAPIDA.md).
