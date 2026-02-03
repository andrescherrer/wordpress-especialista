# Prática – Como usar

1. **Shortcodes:** adicione no plugin/tema e use no editor: `[nome_shortcode]` ou `[nome attr="valor"]conteúdo[/nome]`.
2. **Widgets:** registre com register_widget no **widgets_init**; o widget aparece em Aparência → Widgets (ou no customizador).
3. **Blocos:** register_block_type no **init**; para bloco dinâmico só PHP, basta render_callback (sem JS de editor se quiser apenas placeholder no editor).

**Arquivos 08–11:** shortcode box (08), widget completo (09), block mínimo (10), quando usar qual (11).

**Arquivos 12–17 (blocos JS/React):** estrutura do bloco JS (12), block.json (13), edit.js (14), save.js (15), build @wordpress/scripts (16), checklist primeiro bloco JS (17).

**Teoria rápida:** no topo de cada `.php` há um bloco **REFERÊNCIA RÁPIDA**. Tudo em uma página: [../REFERENCIA-RAPIDA.md](../REFERENCIA-RAPIDA.md).
