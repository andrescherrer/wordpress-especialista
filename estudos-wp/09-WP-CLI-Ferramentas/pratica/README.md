# Prática – Como usar (WP-CLI Ferramentas)

1. **Pré-requisito:** concluir a Fase 7 (WP-CLI Fundamentos). Os arquivos aqui estendem comandos com queue, db init/export, import wizard, scaffolding, migrations e debug.
2. **Registro:** no plugin, carregue cada arquivo quando `defined('WP_CLI') && WP_CLI` e chame `WP_CLI::add_command()` para cada comando/subcomando.
3. **Migrations:** crie a pasta `migrations/` no plugin e arquivos PHP com classe `Meu_Plugin_Migration_Nome` e métodos `up()` e `down()`.
4. **Scaffold:** defina a constante ou path base do plugin para onde os arquivos serão gerados.

**Teoria rápida:** no topo de cada `.php` há um bloco **REFERÊNCIA RÁPIDA**. Tudo em uma página: [../REFERENCIA-RAPIDA.md](../REFERENCIA-RAPIDA.md).
