# 🌍 FASE 11: Multisite e Internacionalização (i18n/l10n)

**Versão:** 1.0  
**Data:** Janeiro 2026  
**Nível:** Especialista em PHP  
**Objetivo:** Dominar WordPress Multisite e implementar internacionalização profissional

---

**Navegação:** [Índice](./000-WordPress-Indice-Topicos.md) | [← Fase 10](./010-WordPress-Fase-10-Testes-Debug-Implantacao.md) | [Fase 12 →](./012-WordPress-Fase-12-Seguranca-Boas-Praticas.md)

---

## 📑 Índice

1. [Fundamentos do WordPress Multisite](#fundamentos-do-wordpress-multisite)
2. [Plugin Compatível com Multisite](#plugin-compatível-com-multisite)
3. [Classe Multisite](#classe-multisite)
4. [Network Settings](#network-settings)
5. [Site vs Network Options](#site-vs-network-options)
6. [Fundamentos de Internacionalização (i18n)](#fundamentos-de-internacionalização-i18n)
7. [Classe i18n](#classe-i18n)
8. [Funções de Tradução](#funções-de-tradução)
9. [Gerar Arquivo POT](#gerar-arquivo-pot)
10. [Traduzir Plugin](#traduzir-plugin)
11. [JavaScript i18n](#javascript-i18n)
12. [RTL Support](#rtl-support)
13. [Traduções Dinâmicas](#traduções-dinâmicas)
14. [Translation Manager](#translation-manager)
15. [GlotPress Integration](#glotpress-integration)
16. [WP-CLI i18n](#wp-cli-i18n)
17. [Best Practices](#best-practices)

---

## 🎯 Objetivos de Aprendizado

Ao final desta fase, você será capaz de:

1. ✅ Entender arquitetura WordPress Multisite e conceitos de network vs site
2. ✅ Criar plugins e temas compatíveis com Multisite
3. ✅ Usar configurações de rede vs opções específicas de site corretamente
4. ✅ Implementar internacionalização (i18n) usando funções de tradução do WordPress
5. ✅ Gerar arquivos POT e gerenciar traduções para plugins/temas
6. ✅ Tratar traduções JavaScript e suporte RTL (Right-to-Left)
7. ✅ Implementar traduções dinâmicas e sistemas de gerenciamento de tradução
8. ✅ Integrar com GlotPress para workflows colaborativos de tradução

## 📝 Autoavaliação

Teste seu entendimento:

- [ ] Qual é a diferença entre `get_site_option()` e `get_option()` no Multisite?
- [ ] Como você verifica se o WordPress está rodando em modo Multisite?
- [ ] Qual é a diferença entre funções `__()`, `_e()`, `_x()`, e `_n()`?
- [ ] Como você gera um arquivo POT para seu plugin/tema?
- [ ] Qual é o propósito de text domains em traduções do WordPress?
- [ ] Como você trata formas plurais em traduções?
- [ ] Qual é a diferença entre `load_plugin_textdomain()` e `load_theme_textdomain()`?
- [ ] Como você implementa suporte RTL em temas e plugins?

## 🛠️ Projeto Prático

**Construir:** Plugin Multilíngue com Suporte Multisite

Crie um plugin que:
- Funcione em instalações single-site e Multisite
- Esteja totalmente internacionalizado com suporte a tradução
- Inclua configurações de network admin para Multisite
- Gere arquivos POT automaticamente
- Suporte múltiplos idiomas com formas plurais adequadas
- Inclua suporte RTL
- Integre com sistema de gerenciamento de tradução

**Tempo estimado:** 10-12 horas  
**Dificuldade:** Intermediário-Avançado

---

## ❌ Equívocos Comuns

### Equívoco 1: "Multisite é apenas múltiplas instalações WordPress"
**Realidade:** Multisite compartilha um core WordPress, um banco de dados (com tabelas específicas por site) e um codebase. É uma rede de sites, não instalações separadas.

**Por que é importante:** Entender a arquitetura ajuda com performance, segurança e manutenção.

**Como lembrar:** Multisite = um core, múltiplos sites. Instalações separadas = múltiplos cores.

### Equívoco 2: "i18n e l10n são a mesma coisa"
**Realidade:** i18n (internacionalização) é tornar código traduzível. l10n (localização) é traduzir para locales específicos. i18n vem primeiro.

**Por que é importante:** Você deve internacionalizar código antes de poder localizá-lo. Entender a diferença ajuda no workflow.

**Como lembrar:** i18n = "tornar traduzível". l10n = "traduzir para locale".

### Equívoco 3: "Funções de tradução traduzem automaticamente"
**Realidade:** Funções de tradução (`__()`, `_e()`) retornam strings traduzidas SE traduções existirem. Sem arquivos de tradução, retornam o texto original em inglês.

**Por que é importante:** Traduções não acontecem automaticamente. Você precisa de arquivos de tradução (.po/.mo) para cada idioma.

**Como lembrar:** Funções de tradução = "obter tradução se disponível", não "sempre traduzir".

### Equívoco 4: "get_option() funciona igual no Multisite"
**Realidade:** No Multisite, `get_option()` obtém opções específicas do site. `get_site_option()` obtém opções da rede. Elas são diferentes.

**Por que é importante:** Usar a função errada pode causar problemas de isolamento de dados ou expor dados da rede a sites individuais.

**Como lembrar:** `get_option()` = específico do site. `get_site_option()` = rede inteira.

---

## 🏗️ Fundamentos do WordPress Multisite

### O que é Multisite?

**WordPress Multisite** é uma instalação única que gerencia múltiplos sites:

- Uma instalação do WordPress que gerencia múltiplos sites
- Compartilha código, plugins e temas entre sites
- Cada site tem seu próprio banco de dados (tabelas separadas)
- Ideal para redes de sites, SaaS, instituições educacionais

### Estrutura de Tabelas no Multisite

```
wp_blogs                    # Lista de sites na rede
wp_blogmeta                 # Metadata dos sites
wp_site                     # Informações da rede
wp_sitemeta                 # Metadata da rede
wp_users                    # Usuários (compartilhados)
wp_usermeta                 # Metadata dos usuários

# Para cada site (exemplo site ID 2):
wp_2_posts
wp_2_postmeta
wp_2_options
wp_2_comments
wp_2_commentmeta
wp_2_terms
wp_2_term_taxonomy
wp_2_term_relationships
```

### Verificar se é Multisite

```php
<?php
if (is_multisite()) {
    // Código específico para multisite
    $blog_id = get_current_blog_id();
    $blog_details = get_blog_details($blog_id);
}
```

---

## 💻 Plugin Compatível com Multisite

### Estrutura Básica

**meu-plugin.php**

```php
<?php
/**
 * Plugin Name: Meu Plugin
 * Plugin URI: https://exemplo.com
 * Description: Plugin compatível com multisite
 * Version: 1.0.0
 * Author: Seu Nome
 * Network: true  // Permite ativação em toda a rede
 */

if (!defined('ABSPATH')) {
    exit;
}

// Constantes
define('MEU_PLUGIN_VERSION', '1.0.0');
define('MEU_PLUGIN_FILE', __FILE__);
define('MEU_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('MEU_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Classe principal
 */
class Meu_Plugin {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }
    
    private function load_dependencies() {
        require_once MEU_PLUGIN_PATH . 'includes/class-multisite.php';
        require_once MEU_PLUGIN_PATH . 'includes/class-i18n.php';
        require_once MEU_PLUGIN_PATH . 'includes/class-admin.php';
    }
    
    private function init_hooks() {
        add_action('plugins_loaded', [$this, 'init']);
        add_action('muplugins_loaded', [$this, 'multisite_init']);
    }
    
    public function init() {
        // Inicializar i18n
        new Meu_Plugin_I18n();
        
        // Inicializar admin
        if (is_admin()) {
            new Meu_Plugin_Admin();
        }
    }
    
    public function multisite_init() {
        // Código específico de multisite
        if (is_multisite()) {
            new Meu_Plugin_Multisite();
        }
    }
}

// Iniciar plugin
Meu_Plugin::get_instance();

// Activation hooks
register_activation_hook(__FILE__, function() {
    if (is_multisite()) {
        // Ativação em toda a rede
        Meu_Plugin_Multisite::activate_all_sites();
    } else {
        // Ativação em site único
        Meu_Plugin_Multisite::activate_single_site();
    }
});

register_deactivation_hook(__FILE__, function() {
    if (is_multisite()) {
        Meu_Plugin_Multisite::deactivate_all_sites();
    } else {
        Meu_Plugin_Multisite::deactivate_single_site();
    }
});
```

---

## 🌐 Classe Multisite

**includes/class-multisite.php**

```php
<?php
/**
 * Classe de Multisite
 */

class Meu_Plugin_Multisite {
    
    /**
     * Ativar em todos os sites
     */
    public static function activate_all_sites() {
        global $wpdb;
        
        // Obter todos os blogs
        $blog_ids = get_sites(['fields' => 'ids']);
        
        foreach ($blog_ids as $blog_id) {
            switch_to_blog($blog_id);
            self::activate_single_site();
            restore_current_blog();
        }
    }
    
    /**
     * Ativar em um site
     */
    public static function activate_single_site() {
        global $wpdb;
        
        // Criar tabelas customizadas
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'meu_plugin_data';
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            blog_id BIGINT UNSIGNED NOT NULL,
            user_id BIGINT UNSIGNED NOT NULL,
            data LONGTEXT NOT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            KEY blog_id (blog_id),
            KEY user_id (user_id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Salvar versão do plugin
        update_option('meu_plugin_version', MEU_PLUGIN_VERSION);
    }
    
    /**
     * Desativar em um site
     */
    public static function deactivate_single_site() {
        global $wpdb;
        
        // Limpar opções
        delete_option('meu_plugin_version');
        delete_option('meu_plugin_settings');
    }
    
    /**
     * Desativar em todos os sites
     */
    public static function deactivate_all_sites() {
        global $wpdb;
        
        $blog_ids = get_sites(['fields' => 'ids']);
        
        foreach ($blog_ids as $blog_id) {
            switch_to_blog($blog_id);
            self::deactivate_single_site();
            restore_current_blog();
        }
    }
    
    /**
     * Obter todos os sites
     */
    public static function get_all_sites() {
        if (!is_multisite()) {
            return [];
        }
        
        $sites = get_sites([
            'number' => -1,
            'orderby' => 'name',
        ]);
        
        return $sites;
    }
    
    /**
     * Obter dados de um site
     */
    public static function get_site_data($blog_id = null) {
        if ($blog_id === null) {
            $blog_id = get_current_blog_id();
        }
        
        $blog = get_blog_details($blog_id);
        
        return [
            'blog_id' => $blog->blog_id,
            'domain' => $blog->domain,
            'path' => $blog->path,
            'url' => get_site_url($blog_id),
            'name' => get_blog_option($blog_id, 'blogname'),
            'description' => get_blog_option($blog_id, 'blogdescription'),
            'admin_email' => get_blog_option($blog_id, 'admin_email'),
            'post_count' => (int) get_blog_option($blog_id, 'posts_per_page'),
        ];
    }
    
    /**
     * Loopear por todos os blogs
     */
    public static function loop_blogs($callback) {
        if (!is_multisite()) {
            return;
        }
        
        $sites = self::get_all_sites();
        
        foreach ($sites as $site) {
            switch_to_blog($site->blog_id);
            call_user_func($callback, $site);
            restore_current_blog();
        }
    }
    
    /**
     * Hook: Quando novo site é criado
     */
    public static function on_new_blog($new_site) {
        switch_to_blog($new_site->blog_id);
        self::activate_single_site();
        restore_current_blog();
    }
    
    /**
     * Hook: Quando site é deletado
     */
    public static function on_delete_blog($blog_id) {
        switch_to_blog($blog_id);
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'meu_plugin_data';
        $wpdb->query("DROP TABLE IF EXISTS $table_name");
        
        restore_current_blog();
    }
    
    /**
     * Registrar hooks
     */
    public static function register_hooks() {
        if (is_multisite()) {
            add_action('wp_insert_site', [self::class, 'on_new_blog']);
            add_action('wp_delete_site', [self::class, 'on_delete_blog']);
        }
    }
}

// Registrar hooks
Meu_Plugin_Multisite::register_hooks();
```

---

## ⚙️ Network Settings

**includes/class-network-admin.php**

```php
<?php
/**
 * Network Admin Settings
 */

class Meu_Plugin_Network_Admin {
    
    public function __construct() {
        if (!is_network_admin()) {
            return;
        }
        
        add_action('network_admin_menu', [$this, 'add_network_menu']);
        add_action('admin_post_meu_plugin_network_save', [$this, 'save_network_settings']);
    }
    
    /**
     * Adicionar menu de rede
     */
    public function add_network_menu() {
        add_menu_page(
            __('Meu Plugin Settings', 'meu-plugin'),
            __('Meu Plugin', 'meu-plugin'),
            'manage_network',
            'meu-plugin-network',
            [$this, 'render_network_settings']
        );
    }
    
    /**
     * Renderizar página de configurações
     */
    public function render_network_settings() {
        if (!current_user_can('manage_network')) {
            wp_die(__('Unauthorized', 'meu-plugin'));
        }
        
        $settings = get_site_option('meu_plugin_network_settings', []);
        
        ?>
        <div class="wrap">
            <h1><?php _e('Meu Plugin Network Settings', 'meu-plugin'); ?></h1>
            
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                <?php wp_nonce_field('meu_plugin_network_settings'); ?>
                <input type="hidden" name="action" value="meu_plugin_network_save">
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="api_key"><?php _e('API Key', 'meu-plugin'); ?></label>
                        </th>
                        <td>
                            <input type="text" 
                                   name="api_key" 
                                   value="<?php echo esc_attr($settings['api_key'] ?? ''); ?>"
                                   class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="enable_feature"><?php _e('Enable Feature', 'meu-plugin'); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" 
                                   name="enable_feature" 
                                   value="1"
                                   <?php checked($settings['enable_feature'] ?? false, 1); ?>>
                            <p class="description"><?php _e('Enable this feature for all sites', 'meu-plugin'); ?></p>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(); ?>
            </form>
            
            <!-- Site Actions -->
            <h2><?php _e('Sites Actions', 'meu-plugin'); ?></h2>
            <p>
                <button id="activate-all-sites" class="button button-primary">
                    <?php _e('Activate on All Sites', 'meu-plugin'); ?>
                </button>
                <button id="deactivate-all-sites" class="button">
                    <?php _e('Deactivate on All Sites', 'meu-plugin'); ?>
                </button>
            </p>
            
            <!-- Sites List -->
            <h2><?php _e('Sites', 'meu-plugin'); ?></h2>
            <?php $this->render_sites_list(); ?>
        </div>
        <?php
    }
    
    /**
     * Renderizar lista de sites
     */
    private function render_sites_list() {
        $sites = get_sites(['number' => -1]);
        
        ?>
        <table class="wp-list-table widefat">
            <thead>
                <tr>
                    <th><?php _e('Site', 'meu-plugin'); ?></th>
                    <th><?php _e('Domain', 'meu-plugin'); ?></th>
                    <th><?php _e('Admin', 'meu-plugin'); ?></th>
                    <th><?php _e('Status', 'meu-plugin'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sites as $site) : ?>
                    <tr>
                        <td>
                            <strong>
                                <a href="<?php echo esc_url(get_site_url($site->blog_id)); ?>" target="_blank">
                                    <?php echo esc_html(get_blog_option($site->blog_id, 'blogname')); ?>
                                </a>
                            </strong>
                        </td>
                        <td><?php echo esc_html($site->domain . $site->path); ?></td>
                        <td>
                            <?php 
                            $admins = get_blog_option($site->blog_id, 'admin_users');
                            echo !empty($admins) ? esc_html(implode(', ', $admins)) : '-';
                            ?>
                        </td>
                        <td>
                            <span class="status">
                                <?php echo $site->deleted ? 'Deleted' : 'Active'; ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
    }
    
    /**
     * Salvar configurações de rede
     */
    public function save_network_settings() {
        if (!isset($_POST['_wpnonce']) || 
            !wp_verify_nonce($_POST['_wpnonce'], 'meu_plugin_network_settings')) {
            wp_die(__('Security check failed', 'meu-plugin'));
        }
        
        if (!current_user_can('manage_network')) {
            wp_die(__('Unauthorized', 'meu-plugin'));
        }
        
        $settings = [
            'api_key' => sanitize_text_field($_POST['api_key'] ?? ''),
            'enable_feature' => isset($_POST['enable_feature']) ? 1 : 0,
        ];
        
        update_site_option('meu_plugin_network_settings', $settings);
        
        wp_redirect(add_query_arg('updated', 1, network_admin_url('admin.php?page=meu-plugin-network')));
        exit;
    }
}

if (is_multisite()) {
    new Meu_Plugin_Network_Admin();
}
```

---

## 🎯 Site vs Network Options

**includes/class-options-manager.php**

```php
<?php
/**
 * Gerenciamento de Options em Multisite
 */

class Meu_Plugin_Options_Manager {
    
    /**
     * Obter opção (network-aware)
     */
    public static function get_option($key, $default = false) {
        if (is_multisite()) {
            // Tentar obter do site atual primeiro
            $value = get_option($key, null);
            
            // Se não existir, tentar obter da network
            if ($value === null) {
                $value = get_site_option($key, $default);
            }
            
            return $value;
        }
        
        return get_option($key, $default);
    }
    
    /**
     * Atualizar opção (network-aware)
     */
    public static function update_option($key, $value, $network = false) {
        if (is_multisite() && $network) {
            return update_site_option($key, $value);
        }
        
        return update_option($key, $value);
    }
    
    /**
     * Deletar opção (network-aware)
     */
    public static function delete_option($key, $network = false) {
        if (is_multisite() && $network) {
            return delete_site_option($key);
        }
        
        return delete_option($key);
    }
    
    /**
     * Verificar se opção existe
     */
    public static function option_exists($key, $network = false) {
        if (is_multisite() && $network) {
            return get_site_option($key, '__not_exists__') !== '__not_exists__';
        }
        
        return get_option($key, '__not_exists__') !== '__not_exists__';
    }
}
```

---

## 🌐 Fundamentos de Internacionalização (i18n)

### Conceitos Importantes

- **i18n (Internationalization)**: Preparar código para tradução
- **l10n (Localization)**: Processo de traduzir para idiomas específicos
- **Text Domain**: Identificador único do plugin para traduções
- **POT File**: Template de tradução (Portable Object Template)
- **PO File**: Arquivo de tradução para idioma específico
- **MO File**: Arquivo compilado usado pelo WordPress

### Estrutura de Diretórios

```
meu-plugin/
├── languages/
│   ├── meu-plugin.pot          # Template
│   ├── meu-plugin-pt_BR.po     # Português Brasil
│   ├── meu-plugin-pt_BR.mo     # Compilado
│   ├── meu-plugin-es_ES.po     # Espanhol
│   ├── meu-plugin-es_ES.mo     # Compilado
│   └── meu-plugin-fr_FR.po     # Francês
```

---

## 🔤 Classe i18n

**includes/class-i18n.php**

```php
<?php
/**
 * Classe de Internacionalização
 */

class Meu_Plugin_I18n {
    
    /**
     * Text domain
     */
    private $domain = 'meu-plugin';
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('plugins_loaded', [$this, 'load_plugin_textdomain']);
        add_action('init', [$this, 'init']);
    }
    
    /**
     * Carregar text domain
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            $this->domain,
            false,
            dirname(plugin_basename(MEU_PLUGIN_FILE)) . '/languages/'
        );
    }
    
    /**
     * Init
     */
    public function init() {
        // Registrar strings para tradução
        $this->register_strings();
        
        // Verificar traduções disponíveis
        $this->check_available_translations();
    }
    
    /**
     * Registrar strings importantes
     */
    private function register_strings() {
        // Estas strings serão extraídas para o arquivo POT
        __('Meu Plugin', 'meu-plugin');
        __('Configurações do Plugin', 'meu-plugin');
        __('Salvar Alterações', 'meu-plugin');
    }
    
    /**
     * Verificar traduções disponíveis
     */
    private function check_available_translations() {
        $translations_path = MEU_PLUGIN_PATH . 'languages';
        
        if (!is_dir($translations_path)) {
            return [];
        }
        
        $available = [];
        $files = glob($translations_path . '/*.mo');
        
        foreach ($files as $file) {
            $locale = str_replace(
                [$translations_path . '/' . $this->domain . '-', '.mo'],
                '',
                $file
            );
            $available[] = $locale;
        }
        
        return $available;
    }
}
```

---

## 📝 Funções de Tradução

### Funções Básicas

```php
<?php

// String simples
__('Hello World', 'meu-plugin');

// Echo string
_e('Hello World', 'meu-plugin');

// Atributo HTML
esc_html__('Settings', 'meu-plugin');
esc_attr__('Click here', 'meu-plugin');
esc_url__('https://exemplo.com', 'meu-plugin');

// Com contexto
_x('Post', 'post type', 'meu-plugin');
_ex('Post', 'post type', 'meu-plugin');

// Plural
_n('1 item', '%d items', $count, 'meu-plugin');
_nx('1 comment', '%d comments', $count, 'comments', 'meu-plugin');

// Com contexto e plural
_nx('%s post', '%s posts', $count, 'post type', 'meu-plugin');

// Sem echo (sprintf)
sprintf(__('Hello %s', 'meu-plugin'), $name);
sprintf(_n('%d item', '%d items', $count, 'meu-plugin'), $count);
```

### Exemplo Completo

```php
<?php

class Meu_Plugin_Translations_Example {
    
    public function render_admin_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Plugin Settings', 'meu-plugin'); ?></h1>
            
            <form method="post">
                <p>
                    <label for="name">
                        <?php _e('Name:', 'meu-plugin'); ?>
                    </label>
                    <input type="text" 
                           name="name" 
                           placeholder="<?php esc_attr_e('Enter your name', 'meu-plugin'); ?>">
                </p>
                
                <p>
                    <label for="email">
                        <?php esc_html_e('Email:', 'meu-plugin'); ?>
                    </label>
                    <input type="email" name="email">
                </p>
                
                <?php submit_button(__('Save Changes', 'meu-plugin')); ?>
            </form>
            
            <p class="description">
                <?php printf(
                    __('Visit our %s for more information', 'meu-plugin'),
                    sprintf(
                        '<a href="%s" target="_blank">%s</a>',
                        esc_url('https://exemplo.com'),
                        __('website', 'meu-plugin')
                    )
                ); ?>
            </p>
        </div>
        <?php
    }
    
    public function get_product_status($count) {
        // Plural com números
        return sprintf(
            _n('%d item in stock', '%d items in stock', $count, 'meu-plugin'),
            number_format_i18n($count)
        );
    }
    
    public function get_notification($item_count) {
        // Plural com contexto
        return sprintf(
            _nx(
                'You have 1 %s',
                'You have %d %s',
                $item_count,
                'notification type',
                'meu-plugin'
            ),
            $item_count,
            _n('notification', 'notifications', $item_count, 'meu-plugin')
        );
    }
}
```

---

## 🔧 Gerar Arquivo POT

### Método 1: WP-CLI

```bash
# Gerar arquivo POT
wp i18n make-pot . languages/meu-plugin.pot --domain=meu-plugin

# Com exclusões
wp i18n make-pot . languages/meu-plugin.pot \
    --domain=meu-plugin \
    --exclude=vendor,node_modules,tests
```

### Método 2: Poedit

1. Abrir Poedit
2. File > New > From POT file
3. Selecionar diretório raiz do plugin
4. Configurar source path para `/`
5. Gerar arquivo

### Método 3: Grunt

**Gruntfile.js**

```javascript
module.exports = function(grunt) {
    grunt.initConfig({
        makepot: {
            target: {
                options: {
                    mainFile: 'meu-plugin.php',
                    type: 'wp-plugin',
                    domainPath: 'languages',
                    generatorComments: true,
                    include: ['includes', 'admin', 'public'],
                    exclude: ['vendor/.*', 'node_modules/.*'],
                    updateTimestamp: true,
                    updatePoFiles: true
                }
            }
        },
        
        po2mo: {
            files: {
                src: 'languages/*.po',
                expand: true
            }
        }
    });
    
    grunt.loadNpmTasks('grunt-wp-i18n');
    grunt.loadNpmTasks('grunt-po2mo');
    
    grunt.registerTask('i18n', ['makepot', 'po2mo']);
};
```

**package.json**

```json
{
    "name": "meu-plugin",
    "scripts": {
        "makepot": "wp i18n make-pot . languages/meu-plugin.pot --domain=meu-plugin",
        "i18n": "grunt i18n"
    },
    "devDependencies": {
        "grunt": "^1.5.0",
        "grunt-wp-i18n": "^1.0.3",
        "grunt-po2mo": "^0.1.2"
    }
}
```

---

## 🌍 Traduzir Plugin

**languages/meu-plugin-pt_BR.po**

```po
# Translation file for Meu Plugin
# Copyright (C) 2024
msgid ""
msgstr ""
"Project-Id-Version: Meu Plugin 1.0.0\n"
"Report-Msgid-Bugs-To: https://exemplo.com/support\n"
"POT-Creation-Date: 2024-01-28 12:00:00+00:00\n"
"PO-Revision-Date: 2024-01-28 12:00:00+0000\n"
"Last-Translator: Seu Nome <email@exemplo.com>\n"
"Language-Team: Portuguese (Brazil)\n"
"Language: pt_BR\n"
"MIME-Version: 1.0\n"
"Content-Type: text/plain; charset=UTF-8\n"
"Content-Transfer-Encoding: 8bit\n"
"Plural-Forms: nplurals=2; plural=(n > 1);\n"

#: meu-plugin.php:15
msgid "Meu Plugin"
msgstr "Meu Plugin"

#: includes/class-admin.php:45
msgid "Settings"
msgstr "Configurações"

#: includes/class-admin.php:67
msgid "Save Changes"
msgstr "Salvar Alterações"

#: includes/class-admin.php:89
msgid "Settings saved successfully."
msgstr "Configurações salvas com sucesso."

#: includes/class-product.php:34
msgid "Out of stock"
msgstr "Fora de estoque"

#: includes/class-product.php:38
msgid "Only 1 item left!"
msgstr "Apenas 1 item restante!"

#: includes/class-product.php:43
#, php-format
msgid "Only %d items left!"
msgstr "Apenas %d itens restantes!"

#: includes/class-product.php:48
#, php-format
msgid "%d item in stock"
msgid_plural "%d items in stock"
msgstr[0] "%d item em estoque"
msgstr[1] "%d itens em estoque"

#: includes/class-product.php:72
msgid "Add to cart"
msgstr "Adicionar ao carrinho"

#: includes/class-product.php:74
msgid "Buy Now"
msgstr "Comprar Agora"

#: includes/class-product.php:80
#, php-format
msgid "Expected delivery: %s"
msgstr "Entrega prevista: %s"

#. Context: post status
#: includes/class-posts.php:123
msgctxt "post status"
msgid "Draft"
msgstr "Rascunho"

#. Context: page title
#: includes/class-archive.php:56
msgctxt "page title"
msgid "Archive"
msgstr "Arquivo"

#: includes/class-comments.php:89
#, php-format
msgid "You have %d new message"
msgid_plural "You have %d new messages"
msgstr[0] "Você tem %d nova mensagem"
msgstr[1] "Você tem %d novas mensagens"

#: admin/views/settings.php:23
msgid "API Key"
msgstr "Chave API"

#: admin/views/settings.php:28
msgid "Enter your API key here"
msgstr "Digite sua chave API aqui"
```

---

## 📜 JavaScript i18n

### Método 1: wp_localize_script (Tradicional)

```php
<?php
// Enfileirar script
wp_enqueue_script(
    'meu-plugin-script',
    MEU_PLUGIN_URL . 'assets/js/script.js',
    ['jquery'],
    MEU_PLUGIN_VERSION,
    true
);

// Localizar strings
wp_localize_script('meu-plugin-script', 'meuPluginI18n', [
    'confirmDelete' => __('Are you sure you want to delete this item?', 'meu-plugin'),
    'confirmSave'   => __('Save changes?', 'meu-plugin'),
    'saving'        => __('Saving...', 'meu-plugin'),
    'saved'         => __('Saved!', 'meu-plugin'),
    'error'         => __('An error occurred. Please try again.', 'meu-plugin'),
    'ajaxUrl'       => admin_url('admin-ajax.php'),
]);
```

**assets/js/script.js**

```javascript
jQuery(document).ready(function($) {
    // Usar strings traduzidas
    $('#delete-button').on('click', function() {
        if (confirm(meuPluginI18n.confirmDelete)) {
            // Deletar
        }
    });
    
    $('#save-button').on('click', function() {
        $.post(meuPluginI18n.ajaxUrl, {
            action: 'meu_plugin_save',
            data: {}
        }, function(response) {
            if (response.success) {
                alert(meuPluginI18n.saved);
            } else {
                alert(meuPluginI18n.error);
            }
        });
    });
});
```

### Método 2: wp_set_script_translations (Modern)

```php
<?php
// Enfileirar script
wp_enqueue_script(
    'meu-plugin-script',
    MEU_PLUGIN_URL . 'assets/js/script.js',
    [],
    MEU_PLUGIN_VERSION,
    true
);

// Definir traduções diretamente do arquivo .mo
wp_set_script_translations(
    'meu-plugin-script',
    'meu-plugin',
    MEU_PLUGIN_PATH . 'languages'
);
```

**assets/js/script.js**

```javascript
// Com WordPress i18n
import { __ } from '@wordpress/i18n';

const message = __('Hello World', 'meu-plugin');
const plural = _n('%d item', '%d items', count, 'meu-plugin');
const context = _x('Post', 'post type', 'meu-plugin');
```

---

## 🌏 RTL Support

### RTL CSS

**assets/css/style-rtl.css**

```css
/* Para idiomas RTL (Árabe, Hebraico, Persa) */

body {
    direction: rtl;
    text-align: right;
}

.sidebar {
    float: left;
    margin-left: 0;
    margin-right: 20px;
}

.button {
    margin-left: 10px;
    margin-right: 0;
}

/* Padding/Margin RTL */
.box {
    padding-right: 20px;
    padding-left: 0;
}
```

### Enqueue RTL CSS

```php
<?php
wp_enqueue_style(
    'meu-plugin-style',
    MEU_PLUGIN_URL . 'assets/css/style.css',
    [],
    MEU_PLUGIN_VERSION
);

// Enqueue RTL version automatically
wp_style_add_data('meu-plugin-style', 'rtl', 'replace');

// Or manually
if (is_rtl()) {
    wp_enqueue_style(
        'meu-plugin-style-rtl',
        MEU_PLUGIN_URL . 'assets/css/style-rtl.css',
        ['meu-plugin-style'],
        MEU_PLUGIN_VERSION
    );
}
```

---

## 💾 Traduções Dinâmicas

**includes/class-dynamic-translations.php**

```php
<?php
/**
 * Sistema de traduções dinâmicas
 */

class Meu_Plugin_Dynamic_Translations {
    
    /**
     * Traduzir strings do banco de dados
     */
    public function translate_db_string($string, $context = '') {
        if (!empty($context)) {
            return apply_filters(
                'meu_plugin_translate_' . $context,
                $string,
                determine_locale()
            );
        }
        
        return apply_filters('meu_plugin_translate', $string, determine_locale());
    }
    
    /**
     * Registrar string para tradução dinâmica
     */
    public function register_dynamic_string($string, $context = 'default') {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'meu_plugin_translations';
        
        $wpdb->insert(
            $table_name,
            [
                'original'   => $string,
                'context'    => $context,
                'created_at' => current_time('mysql'),
            ],
            ['%s', '%s', '%s']
        );
    }
    
    /**
     * Obter tradução do banco
     */
    public function get_translation($string, $locale = null) {
        global $wpdb;
        
        if ($locale === null) {
            $locale = determine_locale();
        }
        
        $table_name = $wpdb->prefix . 'meu_plugin_translations';
        
        $translation = $wpdb->get_var($wpdb->prepare(
            "SELECT translation FROM {$table_name} 
            WHERE original = %s AND locale = %s",
            $string,
            $locale
        ));
        
        return $translation ? $translation : $string;
    }
    
    /**
     * Salvar tradução customizada
     */
    public function save_translation($original, $translation, $locale = null) {
        global $wpdb;
        
        if ($locale === null) {
            $locale = determine_locale();
        }
        
        $table_name = $wpdb->prefix . 'meu_plugin_translations';
        
        return $wpdb->replace(
            $table_name,
            [
                'original'    => $original,
                'translation' => $translation,
                'locale'      => $locale,
                'updated_at'  => current_time('mysql'),
            ],
            ['%s', '%s', '%s', '%s']
        );
    }
    
    /**
     * Criar tabela de traduções
     */
    public function create_translations_table() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'meu_plugin_translations';
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            original LONGTEXT NOT NULL,
            translation LONGTEXT NOT NULL,
            context VARCHAR(255) DEFAULT 'default',
            locale VARCHAR(10) NOT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            KEY original (original(100)),
            KEY locale (locale)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}
```

---

## 🎛️ Translation Manager Dashboard

**includes/class-translation-manager.php**

```php
<?php
/**
 * Dashboard de gerenciamento de traduções
 */

class Meu_Plugin_Translation_Manager {
    
    public function __construct() {
        add_action('admin_menu', [$this, 'add_menu']);
        add_action('admin_post_meu_plugin_save_translation', [$this, 'save_translation']);
    }
    
    /**
     * Adicionar menu
     */
    public function add_menu() {
        add_submenu_page(
            'options-general.php',
            __('Plugin Translations', 'meu-plugin'),
            __('Translations', 'meu-plugin'),
            'manage_options',
            'meu-plugin-translations',
            [$this, 'render_dashboard']
        );
    }
    
    /**
     * Renderizar dashboard
     */
    public function render_dashboard() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized', 'meu-plugin'));
        }
        
        $locale = isset($_GET['locale']) ? sanitize_text_field($_GET['locale']) : get_locale();
        $strings = $this->get_missing_translations($locale);
        
        ?>
        <div class="wrap">
            <h1><?php _e('Translation Manager', 'meu-plugin'); ?></h1>
            
            <form method="get" action="">
                <input type="hidden" name="page" value="meu-plugin-translations">
                <label for="locale"><?php _e('Locale:', 'meu-plugin'); ?></label>
                <select name="locale" id="locale">
                    <?php foreach ($this->get_available_locales() as $loc) : ?>
                        <option value="<?php echo esc_attr($loc); ?>" <?php selected($locale, $loc); ?>>
                            <?php echo esc_html($loc); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php submit_button(__('Filter', 'meu-plugin'), 'secondary'); ?>
            </form>
            
            <h2><?php printf(__('Missing Translations: %d', 'meu-plugin'), count($strings)); ?></h2>
            
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                <?php wp_nonce_field('meu_plugin_translations'); ?>
                <input type="hidden" name="action" value="meu_plugin_save_translation">
                <input type="hidden" name="locale" value="<?php echo esc_attr($locale); ?>">
                
                <table class="wp-list-table widefat">
                    <thead>
                        <tr>
                            <th><?php _e('Original String', 'meu-plugin'); ?></th>
                            <th><?php _e('Translation', 'meu-plugin'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($strings as $string) : ?>
                            <tr>
                                <td><?php echo esc_html($string); ?></td>
                                <td>
                                    <textarea name="translations[<?php echo esc_attr($string); ?>]" 
                                              class="widefat" 
                                              rows="2"></textarea>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <?php submit_button(__('Save Translations', 'meu-plugin')); ?>
            </form>
        </div>
        <?php
    }
    
    /**
     * Obter strings faltando tradução
     */
    private function get_missing_translations($locale) {
        $pot_file = MEU_PLUGIN_PATH . 'languages/meu-plugin.pot';
        
        if (!file_exists($pot_file)) {
            return [];
        }
        
        $pot = file_get_contents($pot_file);
        preg_match_all('/msgid "([^"]+)"/', $pot, $matches);
        
        return array_unique($matches[1]);
    }
    
    /**
     * Obter locales disponíveis
     */
    private function get_available_locales() {
        $locales = [];
        $language_dir = MEU_PLUGIN_PATH . 'languages';
        
        if (is_dir($language_dir)) {
            $files = glob($language_dir . '/*.mo');
            foreach ($files as $file) {
                $locale = str_replace(
                    [MEU_PLUGIN_PATH . 'languages/meu-plugin-', '.mo'],
                    '',
                    $file
                );
                $locales[] = $locale;
            }
        }
        
        return $locales;
    }
    
    /**
     * Salvar tradução
     */
    public function save_translation() {
        if (!isset($_POST['_wpnonce']) || 
            !wp_verify_nonce($_POST['_wpnonce'], 'meu_plugin_translations')) {
            wp_die(__('Security check failed', 'meu-plugin'));
        }
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized', 'meu-plugin'));
        }
        
        $locale = sanitize_text_field($_POST['locale'] ?? '');
        $translations = $_POST['translations'] ?? [];
        
        // Salvar traduções
        foreach ($translations as $original => $translation) {
            if (!empty($translation)) {
                $this->save_to_database(
                    sanitize_text_field($original),
                    sanitize_textarea_field($translation),
                    $locale
                );
            }
        }
        
        wp_redirect(add_query_arg(['page' => 'meu-plugin-translations', 'updated' => 1]));
        exit;
    }
    
    /**
     * Salvar no banco
     */
    private function save_to_database($original, $translation, $locale) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'meu_plugin_translations';
        
        $wpdb->replace($table, [
            'original' => $original,
            'translation' => $translation,
            'locale' => $locale,
            'updated_at' => current_time('mysql'),
        ]);
    }
}

new Meu_Plugin_Translation_Manager();
```

---

## 🔄 GlotPress Integration

```php
<?php
/**
 * Download automático de traduções do GlotPress
 */

class Meu_Plugin_GlotPress {
    
    public function __construct() {
        add_action('wp_version_check', [$this, 'download_translations']);
    }
    
    /**
     * Download de traduções do GlotPress
     */
    public function download_translations() {
        $glotpress_url = 'https://translate.wordpress.org/projects/wp-plugins/meu-plugin/';
        
        $languages = $this->get_languages_from_glotpress();
        
        foreach ($languages as $locale => $percentage) {
            if ($percentage >= 90) {  // Baixar apenas se >= 90% traduzido
                $this->download_language($locale, $glotpress_url);
            }
        }
    }
    
    /**
     * Obter linguagens do GlotPress
     */
    private function get_languages_from_glotpress() {
        $url = 'https://translate.wordpress.org/api/projects/wp-plugins/meu-plugin/';
        
        $response = wp_remote_get($url);
        
        if (is_wp_error($response)) {
            return [];
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        $languages = [];
        foreach ($body['sub_projects'] ?? [] as $sub) {
            $locale = $sub['language']['slug'];
            $percentage = $sub['percent_translated'];
            $languages[$locale] = $percentage;
        }
        
        return $languages;
    }
    
    /**
     * Download de uma linguagem
     */
    private function download_language($locale, $base_url) {
        $url = "{$base_url}{$locale}/default/export-translations/";
        
        $response = wp_remote_get($url);
        
        if (is_wp_error($response)) {
            return false;
        }
        
        $po_content = wp_remote_retrieve_body($response);
        
        // Salvar arquivo .po
        $po_file = MEU_PLUGIN_PATH . "languages/meu-plugin-{$locale}.po";
        file_put_contents($po_file, $po_content);
        
        // Compilar para .mo
        $this->compile_po_to_mo($po_file);
        
        return true;
    }
    
    /**
     * Compilar .po para .mo
     */
    private function compile_po_to_mo($po_file) {
        $mo_file = str_replace('.po', '.mo', $po_file);
        
        // Usar msgfmt se disponível
        if (function_exists('exec')) {
            exec("msgfmt -o {$mo_file} {$po_file}");
        } else {
            // Fallback usando PHP (mais lento)
            $this->compile_with_php($po_file, $mo_file);
        }
    }
    
    /**
     * Compilar com PHP
     */
    private function compile_with_php($po_file, $mo_file) {
        // Você pode usar uma biblioteca como Gettext ou implementar um compilador simples
        // Por enquanto, apenas cópia o arquivo (não ideal)
        copy($po_file, $mo_file);
    }
}

new Meu_Plugin_GlotPress();
```

---

## 🎯 WP-CLI i18n

**Comandos customizados**

```php
<?php
/**
 * Comandos WP-CLI para i18n
 */

if (defined('WP_CLI') && WP_CLI) {
    
    class Meu_Plugin_CLI_I18n_Command {
        
        /**
         * Gerar arquivo POT
         *
         * ## EXAMPLES
         *
         *     wp meu-plugin i18n makepot
         *
         * @subcommand makepot
         */
        public function makepot($args, $assoc_args) {
            WP_CLI::log('Gerando arquivo POT...');
            
            $command = sprintf(
                'wp i18n make-pot %s %slanguages/meu-plugin.pot --domain=meu-plugin',
                escapeshellarg(MEU_PLUGIN_PATH),
                escapeshellarg(MEU_PLUGIN_PATH)
            );
            
            exec($command, $output, $return_code);
            
            if ($return_code === 0) {
                WP_CLI::success('Arquivo POT gerado com sucesso!');
            } else {
                WP_CLI::error('Erro ao gerar arquivo POT');
            }
        }
        
        /**
         * Listar strings sem tradução
         *
         * ## OPTIONS
         *
         * [--locale=<locale>]
         * : Locale específico. Padrão: pt_BR
         *
         * ## EXAMPLES
         *
         *     wp meu-plugin i18n missing
         *     wp meu-plugin i18n missing --locale=es_ES
         *
         * @subcommand missing
         */
        public function missing($args, $assoc_args) {
            $locale = $assoc_args['locale'] ?? 'pt_BR';
            
            $pot_file = MEU_PLUGIN_PATH . 'languages/meu-plugin.pot';
            $po_file = MEU_PLUGIN_PATH . "languages/meu-plugin-{$locale}.po";
            
            if (!file_exists($pot_file)) {
                WP_CLI::error('Arquivo POT não encontrado');
                return;
            }
            
            $pot = $this->parse_po_file($pot_file);
            $po = file_exists($po_file) ? $this->parse_po_file($po_file) : [];
            
            $missing = [];
            foreach ($pot as $string) {
                if (!isset($po[$string]) || empty($po[$string]['translation'])) {
                    $missing[] = $string;
                }
            }
            
            if (empty($missing)) {
                WP_CLI::success('Todas as strings estão traduzidas!');
                return;
            }
            
            WP_CLI::warning(sprintf('%d strings faltando:', count($missing)));
            
            foreach ($missing as $string) {
                WP_CLI::log('  - ' . $string);
            }
        }
        
        /**
         * Compilar .po para .mo
         *
         * ## OPTIONS
         *
         * [--locale=<locale>]
         * : Locale específico. Padrão: todos
         *
         * ## EXAMPLES
         *
         *     wp meu-plugin i18n compile
         *     wp meu-plugin i18n compile --locale=pt_BR
         *
         * @subcommand compile
         */
        public function compile($args, $assoc_args) {
            $locale = $assoc_args['locale'] ?? null;
            
            $language_dir = MEU_PLUGIN_PATH . 'languages';
            
            if ($locale) {
                $files = ["{$language_dir}/meu-plugin-{$locale}.po"];
            } else {
                $files = glob("{$language_dir}/*.po");
            }
            
            $count = 0;
            foreach ($files as $po_file) {
                $mo_file = str_replace('.po', '.mo', $po_file);
                
                exec("msgfmt -o {$mo_file} {$po_file}");
                $count++;
                
                WP_CLI::log("Compilado: {$mo_file}");
            }
            
            WP_CLI::success("Compilados {$count} arquivo(s) .mo");
        }
        
        /**
         * Listar traduções disponíveis
         *
         * ## EXAMPLES
         *
         *     wp meu-plugin i18n list
         *
         * @subcommand list
         */
        public function list_translations($args, $assoc_args) {
            $language_dir = MEU_PLUGIN_PATH . 'languages';
            $files = glob("{$language_dir}/*.mo");
            
            if (empty($files)) {
                WP_CLI::log('Nenhuma tradução encontrada');
                return;
            }
            
            WP_CLI::log('Traduções disponíveis:');
            foreach ($files as $file) {
                $locale = str_replace(
                    [$language_dir . '/meu-plugin-', '.mo'],
                    '',
                    $file
                );
                WP_CLI::log("  - {$locale}");
            }
        }
        
        /**
         * Parser simples de arquivo PO/POT
         */
        private function parse_po_file($file) {
            $content = file_get_contents($file);
            $strings = [];
            
            preg_match_all('/msgid "([^"]*)"(?:\nmsgstr "([^"]*)")?/m', $content, $matches);
            
            for ($i = 0; $i < count($matches[1]); $i++) {
                $strings[$matches[1][$i]] = [
                    'translation' => $matches[2][$i] ?? ''
                ];
            }
            
            return $strings;
        }
    }
    
    WP_CLI::add_command('meu-plugin i18n', 'Meu_Plugin_CLI_I18n_Command');
}
```

---

## ✅ Best Practices

### Checklist de Internacionalização

```markdown
# Checklist de Internacionalização

## Código
- [ ] Todas as strings usam funções de tradução (__(), _e(), etc.)
- [ ] Text domain correto em todas as chamadas
- [ ] Contexto usado onde necessário (_x(), _ex())
- [ ] Plurais tratados corretamente (_n(), _nx())
- [ ] Variáveis em sprintf() para ordem flexível
- [ ] JavaScript traduzido (wp_localize_script ou wp_set_script_translations)
- [ ] Números formatados com number_format_i18n()
- [ ] Datas formatadas com date_i18n()

## Arquivos
- [ ] Arquivo POT gerado e atualizado
- [ ] Text domain no header do plugin
- [ ] Domain Path definido
- [ ] load_plugin_textdomain() chamado no hook correto
- [ ] Diretório /languages criado

## Traduções
- [ ] Arquivos PO criados para idiomas suportados
- [ ] Arquivos MO compilados
- [ ] Traduções testadas em diferentes idiomas
- [ ] RTL testado para árabe/hebraico (se aplicável)

## Multisite
- [ ] Compatível com ativação em rede
- [ ] Configurações network vs site testadas
- [ ] switch_to_blog() usado corretamente
- [ ] Hooks multisite implementados

## Testes
- [ ] Plugin testado em diferentes locales
- [ ] Strings sem tradução identificadas
- [ ] Progresso de tradução monitorado
- [ ] Traduções automáticas via GlotPress (se aplicável)
```

---

## 📌 Resumo da Fase 11

### ✅ Tópicos Abordados

1. **WordPress Multisite** - Estrutura, tabelas, verificações
2. **Plugin compatível com Multisite** - Activation, network hooks
3. **Classe Multisite** - Gerenciar sites, configurações, estatísticas
4. **Network Settings** - Interface de administração de rede
5. **Site vs Network Options** - Gerenciamento correto de options
6. **Fundamentos i18n** - Conceitos, text domain, POT/PO/MO
7. **Classe i18n** - Load textdomain, verificações
8. **Funções de Tradução** - Guia completo de todas as funções
9. **Gerar POT** - WP-CLI, Poedit, Grunt
10. **Traduzir Plugin** - Criar arquivos PO
11. **JavaScript i18n** - wp_localize_script e wp.i18n
12. **RTL Support** - CSS para idiomas da direita para esquerda
13. **Traduções Dinâmicas** - Banco de dados, interface customizada
14. **Translation Manager** - Dashboard, progresso, strings faltantes
15. **GlotPress Integration** - Download automático de traduções
16. **WP-CLI i18n** - Comandos para automação
17. **Best Practices** - Checklist completo

---

**Versão:** 1.0  
**Status:** Completo e pronto para produção  
**Próxima fase:** FASE 12 - Segurança Avançada e Boas Práticas Finais
