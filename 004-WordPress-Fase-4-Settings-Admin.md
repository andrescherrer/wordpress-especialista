# ⚙️ FASE 4: WordPress Settings API e Admin Pages

**Versão:** 1.0  
**Data:** Janeiro 2026  
**Nível:** Especialista em PHP  
**Objetivo:** Dominar a Settings API do WordPress e criar páginas administrativas profissionais

---

**Navegação:** [Índice](000-WordPress-Topicos-Index.md) | [← Fase 3](003-WordPress-Fase-3-REST-API-Advanced.md) | [Fase 5 →](005-WordPress-Fase-5-Custom-Post-Types-Taxonomies.md)

---

## 📑 Índice

1. [Fundamentos da Settings API](#fundamentos-da-settings-api)
2. [Criar Páginas de Configuração](#criar-páginas-de-configuração)
3. [Registrar Settings](#registrar-settings)
4. [Admin Styling e Scripts](#admin-styling-e-scripts)
5. [Meta Boxes](#meta-boxes)
6. [Admin Notices](#admin-notices)
7. [Validação e Sanitização](#validação-e-sanitização)
8. [Admin Forms Avançado](#admin-forms-avançado)

---

## 🎯 Objetivos de Aprendizado

Ao final desta fase, você será capaz de:

1. ✅ Usar a Settings API do WordPress para registrar settings, seções e campos
2. ✅ Criar páginas admin customizadas e páginas de submenu
3. ✅ Implementar validação e sanitização adequadas para formulários admin
4. ✅ Criar e gerenciar meta boxes para posts e custom post types
5. ✅ Exibir admin notices (sucesso, erro, aviso, info) apropriadamente
6. ✅ Estilizar páginas admin usando CSS admin do WordPress e estilos customizados
7. ✅ Tratar submissões de formulários com segurança usando nonces e verificações de capability
8. ✅ Construir interfaces admin complexas com abas, seções e fieldsets

## 📝 Autoavaliação

Teste seu entendimento:

- [ ] Qual é a diferença entre `register_setting()`, `add_settings_section()`, e `add_settings_field()`?
- [ ] Como você sanitiza adequadamente diferentes tipos de inputs de formulário (text, email, URL, number)?
- [ ] Qual é o propósito de nonces em formulários admin e como você os verifica?
- [ ] Como você cria campos condicionais que aparecem/ocultam baseados em valores de outros campos?
- [ ] Qual capability você deve verificar antes de permitir que usuários acessem páginas de configurações?
- [ ] Como você salva configurações no banco de dados com segurança?
- [ ] Qual é a diferença entre `add_meta_box()` e `add_action('add_meta_boxes')`?
- [ ] Como você trata uploads de arquivos em formulários admin?

## 🛠️ Projeto Prático

**Construir:** Gerenciador de Configurações de Plugin

Crie um plugin com uma página de configurações abrangente que:
- Tenha múltiplas abas e seções
- Inclua vários tipos de campos (text, textarea, select, checkbox, radio, file upload)
- Implemente validação e sanitização adequadas
- Mostre admin notices para estados de sucesso/erro
- Inclua uma meta box para posts com campos customizados
- Siga padrões de UI/UX admin do WordPress

**Tempo estimado:** 8-10 horas  
**Dificuldade:** Intermediário

---

## ❌ Equívocos Comuns

### Equívoco 1: "Settings API salva dados automaticamente"
**Realidade:** Settings API fornece a estrutura, mas você precisa tratar a submissão do formulário e chamar `update_option()` ou usar `register_setting()` com callbacks adequados.

**Por que é importante:** Sem tratamento adequado de salvamento, as configurações não persistirão. Entender o fluxo previne confusão.

**Como lembrar:** Settings API = estrutura + validação. Você ainda precisa salvar os dados.

### Equívoco 2: "Nonces são opcionais para formulários admin"
**Realidade:** Nonces são essenciais para proteção CSRF. O WordPress não salvará configurações sem nonces válidos em muitos casos.

**Por que é importante:** Sem nonces, formulários são vulneráveis a ataques CSRF onde sites maliciosos podem submeter formulários em nome dos usuários.

**Como lembrar:** Nonce = "Number used once" = proteção CSRF. Sempre inclua em formulários.

### Equívoco 3: "Todas as configurações devem usar a mesma sanitização"
**Realidade:** Diferentes tipos de campos precisam de sanitização diferente. Text precisa de `sanitize_text_field()`, URLs precisam de `esc_url_raw()`, emails precisam de `sanitize_email()`.

**Por que é importante:** Usar sanitização errada pode corromper dados ou deixar vulnerabilidades de segurança.

**Como lembrar:** Combine sanitização com tipo de dado. Text → sanitização de texto, URL → sanitização de URL.

### Equívoco 4: "Meta boxes só funcionam para posts"
**Realidade:** Meta boxes podem ser adicionadas a qualquer post type, incluindo custom post types, usando o hook `add_meta_boxes` com o parâmetro do post type.

**Por que é importante:** Entender isso permite adicionar campos customizados a qualquer tipo de conteúdo, não apenas posts.

**Como lembrar:** Meta boxes = "Caixas para qualquer post type", não apenas tipo "post".

---

## 🔧 Fundamentos da Settings API

### O que é Settings API?

A **Settings API** é um conjunto de funções do WordPress que facilitam a criação de páginas de configuração com segurança integrada.

**Vantagens:**
- ✅ Validação automática de dados
- ✅ Sanitização built-in
- ✅ Nonces automáticos para CSRF protection
- ✅ Interface consistente com WordPress
- ✅ Segurança reforçada
- ✅ Compatibilidade com multisite

**Componentes principais:**
- `register_setting()` - Registrar uma setting
- `add_settings_section()` - Agrupar campos relacionados
- `add_settings_field()` - Adicionar um campo individual
- `settings_fields()` - Renderizar nonces e hidden fields
- `do_settings_sections()` - Renderizar todas as seções

### Fluxo Completo

```
1. register_setting()     → Registrar a setting
2. add_settings_section() → Criar seção (agrupamento)
3. add_settings_field()   → Adicionar campos
4. Renderizar form        → settings_fields() + do_settings_sections()
5. Submit                 → WordPress salva/valida automaticamente
```

---

## 🏗️ Criar Páginas de Configuração

### Estrutura Básica Profissional

```php
<?php
class Meu_Plugin_Settings {
    
    private $option_group = 'meu_plugin_settings_group';
    private $option_name = 'meu_plugin_settings';
    private $page_slug = 'meu-plugin-settings';
    
    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
    }
    
    /**
     * Adicionar menu no admin
     */
    public function add_admin_menu() {
        // Menu principal
        add_menu_page(
            'Meu Plugin',                    // Page title (aba do navegador)
            'Meu Plugin',                    // Menu title (texto no menu)
            'manage_options',                // Capability (quem pode acessar)
            $this->page_slug,                // Menu slug (URL)
            [$this, 'render_settings_page'], // Callback (função que renderiza)
            'dashicons-admin-generic',       // Icon (ícone no menu)
            100                              // Position (posição no menu)
        );
        
        // Submenu - Configurações
        add_submenu_page(
            $this->page_slug,
            'Configurações',
            'Configurações',
            'manage_options',
            $this->page_slug,
            [$this, 'render_settings_page']
        );
        
        // Submenu - Relatórios
        add_submenu_page(
            $this->page_slug,
            'Relatórios',
            'Relatórios',
            'manage_options',
            'meu-plugin-reports',
            [$this, 'render_reports_page']
        );
        
        // Submenu em menu existente (Configurações do WordPress)
        add_options_page(
            'Meu Plugin',
            'Meu Plugin',
            'manage_options',
            'meu-plugin-options',
            [$this, 'render_settings_page']
        );
    }
    
    /**
     * Renderizar página de configurações
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <!-- Mostrar erros/sucesso de settings -->
            <?php settings_errors($this->option_name); ?>
            
            <form method="post" action="options.php">
                <!-- Nonce e hidden fields de segurança -->
                <?php settings_fields($this->option_group); ?>
                
                <!-- Renderizar todas as seções e campos -->
                <?php do_settings_sections($this->page_slug); ?>
                
                <!-- Botão de submit -->
                <?php submit_button('Salvar Configurações'); ?>
            </form>
            
            <!-- Ações adicionais -->
            <hr>
            <h2>Ações Adicionais</h2>
            <form method="post">
                <?php wp_nonce_field('meu_plugin_reset', 'reset_nonce'); ?>
                <input type="hidden" name="action" value="reset_settings">
                <?php submit_button('Resetar Configurações', 'secondary', 'reset_button', false); ?>
            </form>
        </div>
        <?php
    }
    
    public function render_reports_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1>Relatórios</h1>
            <p>Conteúdo da página de relatórios...</p>
        </div>
        <?php
    }
}

// Inicializar
add_action('plugins_loaded', function() {
    new Meu_Plugin_Settings();
});
?>
```

---

## 📝 Registrar Settings

### Registrar, Adicionar Seções e Campos

```php
<?php
public function register_settings() {
    // Registrar a setting (opção do WordPress)
    register_setting(
        'meu_plugin_settings_group',  // Option group (mesmo de settings_fields)
        'meu_plugin_settings',         // Option name (salvo no banco)
        [
            'type'              => 'array',
            'sanitize_callback' => [$this, 'sanitize_settings'],
            'show_in_rest'      => true // Expor em REST API
        ]
    );
    
    // ========== SEÇÃO 1: Configurações Gerais ==========
    add_settings_section(
        'meu_plugin_general_section',  // Seção ID
        'Configurações Gerais',        // Seção title
        [$this, 'render_general_section'],  // Callback (descrição)
        'meu-plugin-settings'          // Page slug
    );
    
    // Campo: API Key
    add_settings_field(
        'meu_plugin_api_key',          // Field ID
        'Chave da API',                // Field title
        [$this, 'render_api_key_field'],    // Callback (renderizar campo)
        'meu-plugin-settings',         // Page slug
        'meu_plugin_general_section'   // Section ID
    );
    
    // Campo: Habilitado
    add_settings_field(
        'meu_plugin_enabled',
        'Plugin Habilitado',
        [$this, 'render_enabled_field'],
        'meu-plugin-settings',
        'meu_plugin_general_section'
    );
    
    // ========== SEÇÃO 2: Configurações de Email ==========
    add_settings_section(
        'meu_plugin_email_section',
        'Configurações de Email',
        [$this, 'render_email_section'],
        'meu-plugin-settings'
    );
    
    // Campo: Email padrão
    add_settings_field(
        'meu_plugin_email',
        'Email Padrão',
        [$this, 'render_email_field'],
        'meu-plugin-settings',
        'meu_plugin_email_section'
    );
    
    // Campo: Ativar notificações
    add_settings_field(
        'meu_plugin_notifications',
        'Ativar Notificações',
        [$this, 'render_notifications_field'],
        'meu-plugin-settings',
        'meu_plugin_email_section'
    );
}

// ========== CALLBACKS - Renderização de Seções ==========

public function render_general_section() {
    echo '<p>Configurações gerais do plugin.</p>';
}

public function render_email_section() {
    echo '<p>Configure as opções de email para notificações.</p>';
}

// ========== CALLBACKS - Renderização de Campos ==========

public function render_api_key_field() {
    $options = get_option('meu_plugin_settings');
    $api_key = isset($options['api_key']) ? $options['api_key'] : '';
    ?>
    <input type="text" 
           id="meu_plugin_api_key" 
           name="meu_plugin_settings[api_key]" 
           value="<?php echo esc_attr($api_key); ?>"
           class="regular-text">
    <p class="description">Insira sua chave de API obtida no painel de controle.</p>
    <?php
}

public function render_enabled_field() {
    $options = get_option('meu_plugin_settings');
    $enabled = isset($options['enabled']) ? $options['enabled'] : 0;
    ?>
    <input type="checkbox" 
           id="meu_plugin_enabled" 
           name="meu_plugin_settings[enabled]" 
           value="1"
           <?php checked(1, $enabled); ?>>
    <label for="meu_plugin_enabled">Ativar o plugin</label>
    <?php
}

public function render_email_field() {
    $options = get_option('meu_plugin_settings');
    $email = isset($options['email']) ? $options['email'] : '';
    ?>
    <input type="email" 
           id="meu_plugin_email" 
           name="meu_plugin_settings[email]" 
           value="<?php echo esc_attr($email); ?>"
           class="regular-text">
    <?php
}

public function render_notifications_field() {
    $options = get_option('meu_plugin_settings');
    $notifications = isset($options['notifications']) ? $options['notifications'] : 1;
    ?>
    <input type="checkbox" 
           id="meu_plugin_notifications" 
           name="meu_plugin_settings[notifications]" 
           value="1"
           <?php checked(1, $notifications); ?>>
    <label for="meu_plugin_notifications">Enviar notificações por email</label>
    <?php
}

// ========== Sanitização ==========

public function sanitize_settings($input) {
    if (!is_array($input)) {
        return [];
    }
    
    $sanitized = [];
    
    // Sanitizar API Key
    if (isset($input['api_key'])) {
        $sanitized['api_key'] = sanitize_text_field($input['api_key']);
    }
    
    // Sanitizar checkbox
    $sanitized['enabled'] = isset($input['enabled']) ? 1 : 0;
    
    // Sanitizar email
    if (isset($input['email'])) {
        $sanitized['email'] = sanitize_email($input['email']);
    }
    
    // Sanitizar checkbox
    $sanitized['notifications'] = isset($input['notifications']) ? 1 : 0;
    
    return $sanitized;
}
?>
```

---

## 🎨 Admin Styling e Scripts

### Enfileirar Assets para Admin

```php
<?php
class Meu_Plugin_Admin_Assets {
    
    public function __construct() {
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
    }
    
    public function enqueue_scripts($hook) {
        // Carregar apenas na página do plugin
        if ($hook !== 'toplevel_page_meu-plugin-settings') {
            return;
        }
        
        // ========== Bibliotecas do WordPress ==========
        
        // Color Picker (para campos de cor)
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        
        // Media Uploader (para upload de arquivos)
        wp_enqueue_media();
        
        // Datarange (para datas)
        wp_enqueue_style('jquery-ui-datepicker');
        wp_enqueue_script('jquery-ui-datepicker');
        
        // ========== CSS do Plugin ==========
        
        wp_enqueue_style(
            'meu-plugin-admin-css',
            MEU_PLUGIN_URL . 'admin/css/admin.css',
            [],
            MEU_PLUGIN_VERSION
        );
        
        // ========== JavaScript do Plugin ==========
        
        wp_enqueue_script(
            'meu-plugin-admin-js',
            MEU_PLUGIN_URL . 'admin/js/admin.js',
            ['jquery', 'wp-color-picker', 'jquery-ui-datepicker'],
            MEU_PLUGIN_VERSION,
            true  // Footer
        );
        
        // Passar dados para JavaScript
        wp_localize_script('meu-plugin-admin-js', 'meuPluginAdmin', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('meu_plugin_admin'),
            'pluginUrl' => MEU_PLUGIN_URL,
            'settings' => get_option('meu_plugin_settings')
        ]);
    }
}

add_action('plugins_loaded', function() {
    new Meu_Plugin_Admin_Assets();
});
?>
```

**Arquivo: admin/css/admin.css**
```css
/* Estilos para a página de configurações */

.meu-plugin-settings-wrapper {
    max-width: 800px;
    margin-top: 20px;
}

.meu-plugin-settings-section {
    background: #fff;
    padding: 20px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.meu-plugin-settings-section h2 {
    border-bottom: 2px solid #0073aa;
    padding-bottom: 10px;
    margin-bottom: 20px;
}

.meu-plugin-form-group {
    margin-bottom: 15px;
}

.meu-plugin-form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.meu-plugin-form-group input,
.meu-plugin-form-group textarea,
.meu-plugin-form-group select {
    width: 100%;
    max-width: 500px;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.meu-plugin-form-group input[type="checkbox"] {
    width: auto;
    max-width: none;
    margin-right: 10px;
}

.meu-plugin-form-group .description {
    display: block;
    margin-top: 5px;
    color: #666;
    font-size: 12px;
}
```

**Arquivo: admin/js/admin.js**
```javascript
(function($) {
    'use strict';
    
    $(function() {
        // Color Picker
        $('#meu_plugin_color').wpColorPicker();
        
        // Date Picker
        $('#meu_plugin_date').datepicker({
            dateFormat: 'yy-mm-dd'
        });
        
        // Media Uploader
        $('#meu-plugin-upload-button').click(function(e) {
            e.preventDefault();
            
            var file_frame = wp.media.frames.file_frame = wp.media({
                title: 'Selecione uma imagem',
                button: {
                    text: 'Usar esta imagem'
                },
                multiple: false
            });
            
            file_frame.on('select', function() {
                var attachment = file_frame.state().get('selection').first().toJSON();
                $('#meu_plugin_image_id').val(attachment.id);
                $('#meu_plugin_image_preview').html('<img src="' + attachment.url + '" style="max-width:200px;">');
            });
            
            file_frame.open();
        });
        
        // AJAX Submit
        $('#meu-plugin-settings-form').on('submit', function(e) {
            e.preventDefault();
            
            $.ajax({
                url: meuPluginAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'meu_plugin_save_settings',
                    nonce: meuPluginAdmin.nonce,
                    settings: $(this).serialize()
                },
                success: function(response) {
                    if (response.success) {
                        alert('Configurações salvas com sucesso!');
                    } else {
                        alert('Erro ao salvar: ' + response.data);
                    }
                }
            });
        });
    });
})(jQuery);
```

---

## 📦 Meta Boxes

### Criar Meta Boxes em Posts

```php
<?php
class Meu_Plugin_Meta_Boxes {
    
    public function __construct() {
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post', [$this, 'save_meta_box_data']);
    }
    
    /**
     * Adicionar meta boxes
     */
    public function add_meta_boxes() {
        add_meta_box(
            'meu_plugin_meta_box',          // Meta box ID
            'Informações Adicionais',       // Meta box title
            [$this, 'render_meta_box'],     // Callback (renderizar)
            'post',                         // Post type
            'normal',                       // Context (normal, side, advanced)
            'high'                          // Priority (high, default, low)
        );
        
        // Meta box no sidebar
        add_meta_box(
            'meu_plugin_sidebar_meta',
            'Configurações da Barra Lateral',
            [$this, 'render_sidebar_meta_box'],
            'post',
            'side',
            'high'
        );
    }
    
    /**
     * Renderizar meta box
     */
    public function render_meta_box($post) {
        // Nonce para segurança
        wp_nonce_field('meu_plugin_meta_box', 'meu_plugin_meta_box_nonce');
        
        // Pegar valor salvo
        $valor = get_post_meta($post->ID, '_meu_plugin_campo', true);
        $opcoes = get_post_meta($post->ID, '_meu_plugin_opcoes', true);
        ?>
        <div class="meu-plugin-meta-box">
            <p>
                <label for="meu_plugin_campo">Campo de Texto:</label><br>
                <input type="text" 
                       id="meu_plugin_campo" 
                       name="meu_plugin_campo" 
                       value="<?php echo esc_attr($valor); ?>"
                       style="width: 100%;">
            </p>
            
            <p>
                <label for="meu_plugin_opcoes">Selecionar Opção:</label><br>
                <select id="meu_plugin_opcoes" 
                        name="meu_plugin_opcoes"
                        style="width: 100%;">
                    <option value="">-- Selecione --</option>
                    <option value="opcao1" <?php selected($opcoes, 'opcao1'); ?>>Opção 1</option>
                    <option value="opcao2" <?php selected($opcoes, 'opcao2'); ?>>Opção 2</option>
                    <option value="opcao3" <?php selected($opcoes, 'opcao3'); ?>>Opção 3</option>
                </select>
            </p>
        </div>
        <?php
    }
    
    public function render_sidebar_meta_box($post) {
        wp_nonce_field('meu_plugin_sidebar_meta', 'meu_plugin_sidebar_nonce');
        
        $featured = get_post_meta($post->ID, '_meu_plugin_featured', true);
        ?>
        <p>
            <label for="meu_plugin_featured">
                <input type="checkbox" 
                       id="meu_plugin_featured" 
                       name="meu_plugin_featured" 
                       value="1"
                       <?php checked($featured, 1); ?>>
                Marcar como destaque
            </label>
        </p>
        <?php
    }
    
    /**
     * Salvar dados da meta box
     */
    public function save_meta_box_data($post_id) {
        // Verificação de segurança
        if (!isset($_POST['meu_plugin_meta_box_nonce'])) {
            return;
        }
        
        if (!wp_verify_nonce($_POST['meu_plugin_meta_box_nonce'], 'meu_plugin_meta_box')) {
            return;
        }
        
        // Não salvar em revisions
        if (wp_is_post_revision($post_id)) {
            return;
        }
        
        // Não salvar em autosaves
        if (wp_is_post_autosave($post_id)) {
            return;
        }
        
        // Verificar capability
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Salvar campo
        if (isset($_POST['meu_plugin_campo'])) {
            $valor = sanitize_text_field($_POST['meu_plugin_campo']);
            update_post_meta($post_id, '_meu_plugin_campo', $valor);
        }
        
        // Salvar select
        if (isset($_POST['meu_plugin_opcoes'])) {
            $opcao = sanitize_text_field($_POST['meu_plugin_opcoes']);
            update_post_meta($post_id, '_meu_plugin_opcoes', $opcao);
        }
        
        // Salvar checkbox
        $featured = isset($_POST['meu_plugin_featured']) ? 1 : 0;
        update_post_meta($post_id, '_meu_plugin_featured', $featured);
    }
}

add_action('plugins_loaded', function() {
    new Meu_Plugin_Meta_Boxes();
});
?>
```

---

## ⚠️ Admin Notices

### Adicionar Mensagens no Admin

```php
<?php
// Função auxiliar para adicionar notice
function meu_plugin_add_notice($message, $type = 'success') {
    add_action('admin_notices', function() use ($message, $type) {
        ?>
        <div class="notice notice-<?php echo esc_attr($type); ?> is-dismissible">
            <p><?php echo wp_kses_post($message); ?></p>
        </div>
        <?php
    });
}

// Adicionar erro
meu_plugin_add_notice('Ocorreu um erro!', 'error');

// Adicionar sucesso
meu_plugin_add_notice('Operação realizada com sucesso!', 'success');

// Adicionar aviso
meu_plugin_add_notice('Atenção: Esta ação é importante!', 'warning');

// Adicionar informação
meu_plugin_add_notice('Nova versão disponível.', 'info');
?>
```

---

## ✅ Validação e Sanitização

### Validar e Sanitizar Dados

```php
<?php
/**
 * Funções de Sanitização disponíveis
 */

// Texto simples (remove tags HTML)
$texto = sanitize_text_field('Olá <b>mundo</b>');
// Resultado: 'Olá mundo'

// Email
$email = sanitize_email('usuario@example.com');

// URL
$url = esc_url('https://example.com');

// HTML permitido
$html = wp_kses_post('<p>Olá <b>mundo</b></p>');

// SQL
$valor = $wpdb->prepare('SELECT * FROM wp_posts WHERE post_title = %s', $titulo);

/**
 * Validação em Settings API
 */
register_setting(
    'meu_plugin_group',
    'meu_plugin_opcoes',
    [
        'sanitize_callback' => function($input) {
            if (!is_array($input)) {
                add_settings_error(
                    'meu_plugin_opcoes',
                    'invalid_input',
                    'Formato inválido'
                );
                return get_option('meu_plugin_opcoes');
            }
            
            $output = [];
            
            // Validar email
            if (isset($input['email'])) {
                if (!is_email($input['email'])) {
                    add_settings_error(
                        'meu_plugin_opcoes',
                        'invalid_email',
                        'Email inválido!'
                    );
                } else {
                    $output['email'] = sanitize_email($input['email']);
                }
            }
            
            // Validar número
            if (isset($input['numero'])) {
                if (!is_numeric($input['numero'])) {
                    add_settings_error(
                        'meu_plugin_opcoes',
                        'invalid_number',
                        'Deve ser um número!'
                    );
                } else {
                    $output['numero'] = intval($input['numero']);
                }
            }
            
            return $output;
        }
    ]
);
?>
```

---

## 🎯 Admin Forms Avançado

### Campos Dinâmicos (Repeaters)

```php
<?php
class Meu_Plugin_Repeater_Fields {
    
    public function render_repeater_field() {
        $items = get_option('meu_plugin_repeater_items', []);
        ?>
        <div id="meu-plugin-repeater" class="meu-plugin-repeater">
            <?php foreach ($items as $index => $item): ?>
            <div class="repeater-item">
                <p>
                    <input type="text" 
                           name="meu_plugin_repeater_items[<?php echo $index; ?>][titulo]" 
                           value="<?php echo esc_attr($item['titulo'] ?? ''); ?>"
                           placeholder="Título">
                    <input type="text" 
                           name="meu_plugin_repeater_items[<?php echo $index; ?>][descricao]" 
                           value="<?php echo esc_attr($item['descricao'] ?? ''); ?>"
                           placeholder="Descrição">
                    <button type="button" class="button remove-item">Remover</button>
                </p>
            </div>
            <?php endforeach; ?>
        </div>
        
        <button type="button" id="add-repeater-item" class="button button-primary">
            Adicionar Item
        </button>
        
        <script>
        jQuery(function($) {
            $('#add-repeater-item').click(function() {
                var index = $('.repeater-item').length;
                var html = '<div class="repeater-item">' +
                    '<p>' +
                    '<input type="text" name="meu_plugin_repeater_items[' + index + '][titulo]" placeholder="Título">' +
                    '<input type="text" name="meu_plugin_repeater_items[' + index + '][descricao]" placeholder="Descrição">' +
                    '<button type="button" class="button remove-item">Remover</button>' +
                    '</p>' +
                    '</div>';
                
                $('#meu-plugin-repeater').append(html);
            });
            
            $(document).on('click', '.remove-item', function() {
                $(this).closest('.repeater-item').remove();
            });
        });
        </script>
        <?php
    }
}
?>
```

### Color Picker

```php
<?php
public function render_color_field() {
    $color = get_option('meu_plugin_color', '#0073aa');
    ?>
    <input type="text" 
           id="meu_plugin_color" 
           name="meu_plugin_color" 
           value="<?php echo esc_attr($color); ?>"
           class="meu-plugin-color-picker">
    <?php
}

// No admin_enqueue_scripts:
wp_enqueue_style('wp-color-picker');
wp_enqueue_script('wp-color-picker');

wp_enqueue_script('meu-plugin-admin-js', MEU_PLUGIN_URL . 'admin/js/admin.js', ['wp-color-picker']);
?>
```

---

## 🎓 Resumo da Fase 4

Ao dominar a **Fase 4**, você entenderá:

✅ **Settings API** - Função completa de registrar, validar e salvar configurações  
✅ **Admin Pages** - Criar menus e submenus profissionais  
✅ **Meta Boxes** - Adicionar campos customizados aos posts  
✅ **Admin Notices** - Mensagens de sucesso, erro e aviso  
✅ **Validação & Sanitização** - Proteger dados do usuário  
✅ **Admin Styling** - CSS e JS na área administrativa  
✅ **Campos Avançados** - Color picker, date picker, media uploader, repeaters  

**Próximo passo:** Fase 5 - Custom Post Types e Taxonomies

---

**Versão:** 1.0  
**Última atualização:** Janeiro 2026  
**Autor:** André | Especialista em PHP e WordPress
