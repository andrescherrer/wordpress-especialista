# Boas práticas – Multisite e Internacionalização

Checklist e equívocos. Fonte: **011-WordPress-Fase-11-Multisite-Internacionalizacao.md**.

---

## Checklist i18n

- [ ] Todas as strings visíveis usam funções de tradução (__(), _e(), esc_html_e(), etc.)
- [ ] Text domain único e consistente em todo o plugin
- [ ] Contexto usado onde há ambiguidade (_x(), _ex())
- [ ] Plurais tratados com _n() / _nx() e number_format_i18n() quando aplicável
- [ ] Variáveis em sprintf() para permitir ordem flexível nas traduções
- [ ] JavaScript: wp_localize_script ou wp_set_script_translations para strings no front
- [ ] load_plugin_textdomain() em plugins_loaded; path relativo ao plugin (ex.: /languages/)
- [ ] Arquivo POT gerado e atualizado; PO/MO para cada idioma suportado
- [ ] Header do plugin: Text Domain e Domain Path
- [ ] RTL: wp_style_add_data( 'rtl', 'replace' ) ou CSS condicional com is_rtl()

---

## Checklist Multisite

- [ ] Plugin testado em instalação Multisite
- [ ] Ativação: percorrer get_sites() e ativar por site (switch_to_blog / restore_current_blog)
- [ ] Hooks wp_initialize_site e wp_delete_site para novos sites e limpeza
- [ ] Opções de rede (get_site_option) vs opções de site (get_option) usadas corretamente
- [ ] Menu e telas de rede só em network_admin_menu e com capability manage_network
- [ ] Header `Network: true` se o plugin puder ser ativado em rede

---

## Equívocos comuns

1. **“Multisite é várias instalações WordPress”**  
   Multisite é uma única instalação com vários sites; compartilha core, usuários e (em muitos casos) temas/plugins. Tabelas de conteúdo são por site (wp_2_posts, etc.).

2. **“i18n e l10n são iguais”**  
   i18n = preparar o código para tradução (funções, text domain). l10n = traduzir para um locale (criar .po/.mo). Primeiro i18n, depois l10n.

3. **“__() traduz automaticamente”**  
   __() retorna a string traduzida **se** existir arquivo .mo para o locale. Sem .mo, retorna o texto original. É preciso gerar POT, criar PO e compilar MO.

4. **“get_option() no Multisite é igual”**  
   get_option() é sempre do site atual. get_site_option() é da rede (wp_sitemeta). Não trocar um pelo outro.
