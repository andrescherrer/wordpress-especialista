# Prática – Como usar (Testes, Debug, Implantação)

1. **PHPUnit:** instale com `composer require --dev phpunit/phpunit ^11`. Coloque `phpunit.xml` na raiz do plugin e `tests/bootstrap.php` conforme 01-phpunit-config.md.
2. **Testes:** os arquivos `.php` de exemplo (02 a 05) devem ser copiados para `tests/Unit/` do seu plugin. Ajuste namespaces e paths. As funções/classes sob teste (ex.: `formata_data`, `calcula_com_imposto`) precisam existir no plugin ou serem carregadas no bootstrap.
3. **Executar:** na raiz do plugin, `./vendor/bin/phpunit` ou `./vendor/bin/phpunit tests/Unit/NomeDoArquivoTest.php`.
4. **Query Monitor / Sentry:** trechos em 06-integration-debug-deploy.md são para integrar em functions.php ou classe do plugin.

**Arquivos 08–10:** bootstrap WP_UnitTestCase (08), testes de hooks (09), debug ferramentas (10).

**Teoria rápida:** no topo de cada arquivo de teste há um bloco **REFERÊNCIA RÁPIDA**. Tudo em uma página: [../REFERENCIA-RAPIDA.md](../REFERENCIA-RAPIDA.md).
