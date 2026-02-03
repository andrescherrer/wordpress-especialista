# Estrutura de diretórios e constantes – WordPress Core

Resumo para consulta. Fonte: **001-WordPress-Fase-1-Fundamentos-Core.md**.

---

## Estrutura principal

| Pasta / arquivo | Uso |
|-----------------|-----|
| **wp-admin/** | Painel administrativo (admin), includes de admin |
| **wp-includes/** | Core: funções, classes, hooks, REST, etc. |
| **wp-content/** | Conteúdo e extensões: themes, plugins, uploads |
| **wp-content/themes/** | Temas (twentytwentyfourteen, etc.) |
| **wp-content/plugins/** | Plugins |
| **wp-content/uploads/** | Mídia (por ano/mês) |
| **wp-config.php** | Configuração (DB, keys, debug, etc.) – na raiz |
| **index.php** | Ponto de entrada na raiz |

---

## Constantess úteis

| Constante | Descrição |
|-----------|-----------|
| **ABSPATH** | Caminho absoluto da raiz do WordPress (com trailing slash) |
| **WP_CONTENT_DIR** | Caminho do diretório wp-content |
| **WP_PLUGIN_DIR** | Caminho do diretório de plugins |
| **WPINC** | 'wp-includes' |
| **WP_DEBUG** | true para exibir erros (nunca true em produção) |
| **WP_DEBUG_LOG** | true para gravar em wp-content/debug.log |
| **DISALLOW_FILE_EDIT** | true para desabilitar edição de arquivos no admin |
| **WP_HOME** / **WP_SITEURL** | URLs do site (quando diferentes da instalação) |

---

## Ordem de carregamento (resumo)

1. `wp-config.php` (constantes, DB)
2. `wp-settings.php` → carrega `wp-includes/load.php`, `default-filters.php`, etc.
3. Hooks: `muplugins_loaded` → `plugins_loaded` → `setup_theme` → `init` → `wp` → `template_redirect` → …

Plugins e temas são carregados a partir de `wp-content`; o tema ativo é definido pela opção `template` / `stylesheet`.
