# Prática – Como usar (Multisite e i18n)

1. **Multisite:** os exemplos usam `is_multisite()`, `switch_to_blog()` e `restore_current_blog()`. Em ativação de plugin em rede, percorra `get_sites()` e ative por site; registre `wp_insert_site` para novos sites.
2. **Network:** menu e opções de rede usam `network_admin_menu` e `get_site_option` / `update_site_option`. Só aparecem quando o plugin está ativo na rede e o usuário está no admin da rede.
3. **i18n:** defina um text domain único (ex.: `meu-plugin`). Chame `load_plugin_textdomain()` em `plugins_loaded`. Todas as strings visíveis devem usar `__()`, `_e()`, etc. com o mesmo domain.
4. **POT/PO/MO:** gere o POT com `wp i18n make-pot`; crie/edite .po por idioma; compile para .mo (Poedit ou msgfmt).

**Arquivos 08–10:** switch_to_blog (08), i18n em template (09), fluxo POT/PO/MO (10).

**Teoria rápida:** no topo de cada `.php` há um bloco **REFERÊNCIA RÁPIDA**. Tudo em uma página: [../REFERENCIA-RAPIDA.md](../REFERENCIA-RAPIDA.md).
