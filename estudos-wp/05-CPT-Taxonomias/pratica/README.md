# Prática – Como usar

1. **Plugin ou tema:** registre CPT e taxonomias no hook **init** (tema: functions.php ou include; plugin: no arquivo principal).
2. **Ordem:** registrar o **CPT antes** da taxonomia que o usa (ou no mesmo init).
3. **Flush rewrite:** após alterar slug/rewrite, vá em Configurações → Links permanentes e salve, ou use `flush_rewrite_rules()` na ativação do plugin.

**Teoria rápida:** no topo de cada `.php` há um bloco **REFERÊNCIA RÁPIDA**. Tudo em uma página: [../REFERENCIA-RAPIDA.md](../REFERENCIA-RAPIDA.md).
