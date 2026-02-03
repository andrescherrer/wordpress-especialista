# Prática – Como usar

1. **Plugin:** use um plugin existente ou crie um. Menu em **admin_menu**, registro de settings em **admin_init**.
2. **Copie** os trechos para o plugin. O arquivo **01** monta a página completa; **02** detalha campos e sanitize; **03** e **04** são independentes (meta boxes, notices/scripts).
3. **Form de configuração:** action deve ser **options.php**; use **settings_fields( $option_group )** e **do_settings_sections( $page_slug )**.

**Arquivos 08–11:** tabs (08), tipos de campo (09), admin AJAX (10), tabela sanitize (11).

**Teoria rápida:** no topo de cada `.php` há um bloco **REFERÊNCIA RÁPIDA**. Tudo em uma página: [../REFERENCIA-RAPIDA.md](../REFERENCIA-RAPIDA.md).
