# 🎯 FASE 1: Fundamentos do WordPress Core

**Versão:** 1.0  
**Data:** Janeiro 2026  
**Nível:** Especialista em PHP  
**Objetivo:** Dominar os fundamentos essenciais do WordPress

---

## 📑 Índice

1. [Arquitetura e Estrutura](#arquitetura-e-estrutura)
2. [Hook System](#hook-system-actions-e-filters)
3. [Estrutura do Banco de Dados](#estrutura-do-banco-de-dados)
4. [WordPress Database API](#wordpress-database-api-wpdb)
5. [Posts, Pages e Custom Content](#posts-pages-e-custom-content)
6. [Template Hierarchy](#template-hierarchy)
7. [The Loop](#the-loop)
8. [WordPress Coding Standards](#wordpress-coding-standards)

---

## 🏗️ Arquitetura e Estrutura

### 1.1 Estrutura de Diretórios do WordPress

```
wordpress/
├── wp-admin/              # Interface administrativo (painel)
│   ├── css/              # Estilos do admin
│   ├── js/               # Scripts do admin
│   ├── images/           # Imagens do admin
│   ├── includes/         # Funções do admin
│   ├── network/          # Código específico do multisite
│   ├── api/              # REST API admin
│   └── ...
├── wp-content/           # Conteúdo do usuário
│   ├── plugins/          # Plugins instalados
│   ├── themes/           # Temas instalados
│   ├── uploads/          # Arquivos de mídia
│   ├── languages/        # Traduções
│   └── index.php         # Segurança
├── wp-includes/          # Arquivos core do WordPress
│   ├── js/              # Scripts core
│   ├── css/             # Estilos core
│   ├── images/          # Imagens core
│   ├── classes/         # Classes principais
│   ├── rest-api/        # REST API core
│   ├── block-editor/    # Gutenberg/Block Editor
│   └── ...
├── wp-config.php         # Configurações principais (NUNCA versionar)
├── wp-settings.php       # Bootstrap do WordPress
├── wp-load.php          # Load básico
├── wp-blog-header.php   # Header do blog
├── index.php            # Entry point
├── .htaccess            # Rewrite rules (Apache)
└── web.config           # Rewrite rules (IIS)
```

### 1.2 Arquivos Core Essenciais

| Arquivo | Descrição |
|---------|-----------|
| `wp-config.php` | Configurações do site (DB, salts, constants) |
| `wp-settings.php` | Inicialização do WordPress |
| `wp-load.php` | Load do WordPress (sem output) |
| `wp-blog-header.php` | Load com setup de tema |
| `index.php` | Entry point de todas as requisições |
| `wp-admin/index.php` | Dashboard |
| `wp-login.php` | Página de login |

### 1.3 Ordem de Carregamento do WordPress

```
1. index.php (entry point)
   ↓
2. wp-blog-header.php
   ↓
3. wp-load.php
   ↓
4. wp-config.php (conexão com DB, definição de constants)
   ↓
5. wp-settings.php (inicialização principal)
   ├─ Define funções core
   ├─ Conecta ao banco
   ├─ Carrega plugins (load todas as actions/filters)
   ├─ Carrega tema ativo (child theme se existir)
   └─ Dispara hooks: plugins_loaded, init, wp_loaded
   ↓
6. Query de URL (rewrite rules)
   ↓
7. Execução de hooks e filters por ponto
   ↓
8. Carregamento de template (template hierarchy)
   ↓
9. The Loop (iteração de posts)
   ↓
10. Output (header, content, footer)
```

### 1.4 Constantes Importantes

```php
<?php
// Path constants
define('ABSPATH', dirname(__FILE__) . '/');           // /home/user/public_html/
define('WPINC', 'wp-includes');                       // Nome da pasta core
define('WPCONTENDIR', 'wp-content');                  // Nome da pasta conteúdo

// URL constants (definidas em wp-config.php)
define('WP_HOME', 'https://seusite.com');            // URL do site
define('WP_SITEURL', 'https://seusite.com');         // URL do WordPress

// Directory constants
define('WP_CONTENT_DIR', ABSPATH . 'wp-content');    // Caminho completo
define('WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins');
define('WP_THEME_DIR', WP_CONTENT_DIR . '/themes');
define('WPMU_PLUGIN_DIR', WP_CONTENT_DIR . '/mu-plugins');

// Database
define('DB_NAME', 'wordpress_db');
define('DB_USER', 'user');
define('DB_PASSWORD', 'password');
define('DB_HOST', 'localhost');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', 'utf8mb4_unicode_ci');

// Segurança (authentication unique keys & salts)
define('AUTH_KEY', '...');
define('SECURE_AUTH_KEY', '...');
define('LOGGED_IN_KEY', '...');
define('NONCE_KEY', '...');
// ... AUTH_SALT, SECURE_AUTH_SALT, etc

// Debug
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
define('SCRIPT_DEBUG', true);
define('SAVEQUERIES', true);

// Performance
define('EMPTY_TRASH_DAYS', 30);
define('AUTO_SAVE_INTERVAL', 60);
define('WP_POST_REVISIONS', 5);
define('DISABLE_FILE_EDIT', true);

// Segurança
define('DISALLOW_FILE_EDIT', true);
define('DISALLOW_FILE_MODS', false);
define('FORCE_SSL_ADMIN', true);
define('FORCE_SSL_LOGIN', true);
?>
```

---

## 🔌 Hook System (Actions e Filters)

### 2.1 Fundamentos de Actions

**Actions** executam funções em pontos específicos do código WordPress.

```php
<?php
// Registrar uma action (adicionar função a um hook)
add_action('hook_name', 'funcao_callback', $priority, $accepted_args);

/**
 * @param string $hook_name     - Nome do hook (ex: 'init', 'wp_head')
 * @param callable $callback    - Função a executar
 * @param int $priority         - Ordem (padrão: 10) - menor = executa primeiro
 * @param int $accepted_args    - Número de argumentos
 */

// Exemplo simples
add_action('wp_footer', function() {
    echo '<!-- Site desenvolvido por André -->';
});

// Com prioridade
add_action('wp_footer', 'primeiro_script', 5);
add_action('wp_footer', 'segundo_script', 10);  // Padrão
add_action('wp_footer', 'terceiro_script', 15);
// Resultado: primeiro_script → segundo_script → terceiro_script

// Com argumentos
add_action('save_post', 'meu_callback', 10, 2);

function meu_callback($post_id, $post) {
    // $post_id é o ID do post
    // $post é o objeto WP_Post
}

// Disparar uma action (executar todas as funções registradas)
do_action('meu_hook_customizado', $arg1, $arg2);

// Remover uma action
remove_action('wp_head', 'wp_generator');
remove_action('hook_name', 'funcao_callback', $priority);

// Verificar se uma action já foi disparada
if (did_action('wp_footer')) {
    // wp_footer já foi executado
}

// Hooks condicionais
if (is_admin()) {
    add_action('admin_menu', 'meu_menu_admin');
} else {
    add_action('wp_footer', 'meu_rodape');
}

// Hooks dinâmicos
foreach (['post', 'page', 'product'] as $post_type) {
    add_action("publish_{$post_type}", 'ao_publicar');
}
?>
```

### 2.2 Fundamentos de Filters

**Filters** modificam e retornam valores. Sempre retornam algo.

```php
<?php
// Registrar um filter
add_filter('hook_name', 'funcao_callback', $priority, $accepted_args);

/**
 * @param string $hook_name     - Nome do filter
 * @param callable $callback    - Função que modifica o valor
 * @param int $priority         - Ordem de execução (padrão: 10)
 * @param int $accepted_args    - Número de argumentos que a função aceita
 */

// Exemplo simples
add_filter('the_title', function($title) {
    return strtoupper($title); // Converter para maiúsculas
});

// Aplicar um filter (retorna valor modificado)
$conteudo = apply_filters('the_content', $conteudo_original);

// Função filter com lógica
function remover_emojis($conteudo) {
    // Remove emojis do conteúdo
    $conteudo = preg_replace('~[^\p{L}\p{N}\s]~u', '', $conteudo);
    return $conteudo;
}
add_filter('the_content', 'remover_emojis', 10, 1);

// Múltiplos filters no mesmo hook (ordem importa)
add_filter('the_content', 'primeira_transformacao', 5);   // Executa primeiro
add_filter('the_content', 'segunda_transformacao', 10);   // Depois
add_filter('the_content', 'terceira_transformacao', 15);  // Por último

// Remover um filter
remove_filter('the_content', 'wpautop');
remove_filter('hook_name', 'funcao_callback', $priority);

// Verificar se um filter foi aplicado
has_filter('the_title', 'remover_emojis');  // Retorna priority ou false

// Obter todos os callbacks de um filter
$callbacks = $GLOBALS['wp_filter']['the_title']->callbacks;
?>
```

### 2.3 Diferença entre Actions e Filters

| Aspecto | Actions | Filters |
|--------|---------|---------|
| **Objetivo** | Executar código em um ponto específico | Modificar e retornar um valor |
| **Retorno** | Não retornam nada | Devem retornar um valor |
| **Uso** | `do_action()` | `apply_filters()` |
| **Exemplo** | Salvar post, enviar email | Modificar conteúdo, validar dados |
| **Função** | Ação/Efeito colateral | Transformação |

### 2.4 Hook Priority (Ordem de Execução)

```php
<?php
// Valor padrão: 10
// Menor valor = executa antes
// Maior valor = executa depois

// Exemplo com 3 callbacks
add_action('init', function() {
    echo '1. Primeira (prioridade 5)';
}, 5);

add_action('init', function() {
    echo '2. Segunda (prioridade 10 - padrão)';
}); // Sem prioridade = 10

add_action('init', function() {
    echo '3. Terceira (prioridade 15)';
}, 15);

// Saída:
// 1. Primeira (prioridade 5)
// 2. Segunda (prioridade 10 - padrão)
// 3. Terceira (prioridade 15)

// Caso prático: Enfileirar CSS/JS
add_action('wp_enqueue_scripts', 'enfileirar_jquery', 1);      // Muito cedo
add_action('wp_enqueue_scripts', 'enfileirar_css_tema', 10);   // Normal
add_action('wp_enqueue_scripts', 'enfileirar_js_tema', 15);    // Depois
?>
```

### 2.5 Hooks Essenciais por Contexto

#### **Inicialização e Setup**

```php
<?php
// plugins_loaded (9)
// Dispara depois que todos os plugins ativos são carregados
add_action('plugins_loaded', function() {
    // Hooks específicos do plugin aqui
    // Seguro aqui: plugins estão carregados
});

// init (10)
// Inicialização geral do WordPress
// Aqui você registra: post types, taxonomies, rewrite rules
add_action('init', function() {
    register_post_type('produto', [...]);
    register_taxonomy('categoria_produto', 'produto', [...]);
});

// wp_loaded (11)
// Depois que wp-load.php completo (query pronta, usuário autenticado)
add_action('wp_loaded', function() {
    // Usuário está pronto, query foi executada
});

// after_setup_theme
// Depois que o tema foi carregado
add_action('after_setup_theme', function() {
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form']);
});
?>
```

#### **Frontend (Tema)**

```php
<?php
// wp_enqueue_scripts
// Enfileirar CSS/JS no frontend
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('meu-css', get_template_directory_uri() . '/style.css');
    wp_enqueue_script('meu-js', get_template_directory_uri() . '/script.js', ['jquery']);
});

// wp_head (1-999)
// Executado dentro de <head>
add_action('wp_head', function() {
    echo '<meta name="custom" content="value">';
}, 10);

// wp_footer (10-999)
// Executado antes de </body>
add_action('wp_footer', function() {
    echo '<!-- Google Analytics -->';
});

// template_redirect
// Antes de carregar o template
// Útil para redirecionamentos condicionais
add_action('template_redirect', function() {
    if (is_single()) {
        // Fazer algo específico para posts únicos
    }
});

// the_content
// Filter para modificar conteúdo do post
add_filter('the_content', function($content) {
    return '<p>Aviso: ' . $content . '</p>';
});
?>
```

#### **Admin (Painel)**

```php
<?php
// admin_init
// Inicialização do admin
add_action('admin_init', function() {
    register_setting('meu_plugin', 'meu_opcao');
});

// admin_menu
// Registrar menus do admin
add_action('admin_menu', function() {
    add_menu_page(
        'Meu Plugin',          // Título da página
        'Meu Plugin',          // Título do menu
        'manage_options',      // Capability
        'meu-plugin',          // Slug
        'pagina_meu_plugin'    // Callback
    );
});

// admin_enqueue_scripts
// Enfileirar CSS/JS no admin
add_action('admin_enqueue_scripts', function($hook) {
    if ($hook === 'toplevel_page_meu-plugin') {
        wp_enqueue_style('meu-admin-css', plugin_dir_url(__FILE__) . 'admin.css');
        wp_enqueue_script('meu-admin-js', plugin_dir_url(__FILE__) . 'admin.js');
    }
});

// add_meta_boxes
// Registrar meta boxes no editor de posts
add_action('add_meta_boxes', function() {
    add_meta_box(
        'meu-meta-box',
        'Informações Adicionais',
        'renderizar_meta_box',
        'post'
    );
});

// save_post
// Disparado ao salvar um post
add_action('save_post', function($post_id) {
    if (wp_is_post_autosave($post_id)) return;
    if (wp_is_post_revision($post_id)) return;
    // Processar salvar
}, 10, 1);
?>
```

### 2.6 Named Functions vs Anonymous Functions

```php
<?php
// ✅ Named Function (melhor para debugging)
function meu_callback_init() {
    echo 'Init executado';
}
add_action('init', 'meu_callback_init');

// ❌ Anonymous Function (não aparece em stack trace)
add_action('init', function() {
    echo 'Init executado';
});

// ✅ Anonymous com contexto (classe)
class Meu_Plugin {
    public function __construct() {
        add_action('init', [$this, 'init']);
    }
    
    public function init() {
        echo 'Init da classe';
    }
}
new Meu_Plugin();

// ✅ Anonymous com static
class Meu_Plugin_Static {
    public static function init() {
        echo 'Init static';
    }
}
add_action('init', ['Meu_Plugin_Static', 'init']);
?>
```

---

## 🗄️ Estrutura do Banco de Dados

### 3.1 Tabelas Principais do WordPress

```sql
-- Posts e conteúdo
wp_posts          -- Posts, páginas, attachments, revisões
wp_postmeta       -- Metadados dos posts

-- Taxonomias (categorias, tags, etc)
wp_terms          -- Termos (ex: "PHP", "WordPress")
wp_term_taxonomy  -- Tipo de taxonomia (ex: "category", "post_tag")
wp_term_relationships -- Relação entre posts e termos
wp_termmeta       -- Metadados dos termos

-- Usuários
wp_users          -- Informações dos usuários
wp_usermeta       -- Metadados dos usuários

-- Comentários
wp_comments       -- Comentários dos posts
wp_commentmeta    -- Metadados dos comentários

-- Opções do site
wp_options        -- Configurações e opções do site

-- Outros (raramente usados)
wp_links          -- Blogroll (deprecated)
wp_term_meta      -- Adicionado em 4.4
```

### 3.2 Estrutura de wp_posts

```sql
CREATE TABLE wp_posts (
    ID bigint(20) PRIMARY KEY AUTO_INCREMENT,
    post_author bigint(20),              -- ID do usuário que criou
    post_date datetime,                  -- Data de criação
    post_date_gmt datetime,              -- Data GMT
    post_content longtext,               -- Conteúdo principal
    post_title text,                     -- Título
    post_excerpt text,                   -- Resumo/Excerpt
    post_status varchar(20),             -- publish, draft, pending, etc
    comment_status varchar(20),          -- open, closed
    ping_status varchar(20),             -- open, closed
    post_password varchar(255),          -- Senha (se privado)
    post_name varchar(200),              -- Slug (URL)
    to_ping text,                        -- URLs para pingback
    pinged text,                         -- URLs já pingadas
    post_modified datetime,              -- Última modificação
    post_modified_gmt datetime,          -- Última modificação GMT
    post_content_filtered longtext,      -- Filtrado (interno)
    post_parent bigint(20),              -- ID do post pai (hierarchical)
    guid varchar(255),                   -- GUID único
    menu_order int(11),                  -- Ordem de exibição
    post_type varchar(20),               -- post, page, attachment, etc
    post_mime_type varchar(100),         -- MIME type (para attachments)
    comment_count bigint(20),            -- Contagem de comentários
    
    KEY post_name (post_name),
    KEY type_status_date (post_type, post_status, post_date),
    KEY post_parent (post_parent),
    KEY post_author (post_author)
);
```

### 3.3 Estrutura de wp_postmeta

```sql
CREATE TABLE wp_postmeta (
    meta_id bigint(20) PRIMARY KEY AUTO_INCREMENT,
    post_id bigint(20),                  -- ID do post (FK)
    meta_key varchar(255),               -- Nome da meta (ex: '_thumbnail_id')
    meta_value longtext,                 -- Valor da meta (pode ser serialized)
    
    KEY post_id (post_id),
    KEY meta_key (meta_key)
);

-- Exemplo de dados:
-- post_id | meta_key           | meta_value
-- 123     | _thumbnail_id      | 456
-- 123     | _price             | 99.90
-- 123     | _stock             | 50
-- 123     | acf_campo1         | {"nome": "valor"}
```

### 3.4 Estrutura de Taxonomias

```sql
CREATE TABLE wp_terms (
    term_id bigint(20) PRIMARY KEY AUTO_INCREMENT,
    name varchar(200),                   -- Nome do termo
    slug varchar(200),                   -- URL-friendly
    term_group bigint(10),               -- Grupo de termos (usuário pode agrupar)
    
    KEY slug (slug),
    KEY name (name)
);

CREATE TABLE wp_term_taxonomy (
    term_taxonomy_id bigint(20) PRIMARY KEY AUTO_INCREMENT,
    term_id bigint(20),                  -- ID do termo
    taxonomy varchar(32),                -- category, post_tag, custom_tax
    description longtext,                -- Descrição
    parent bigint(20),                   -- ID do termo pai (hierarchical)
    count bigint(20),                    -- Contagem de posts
    
    KEY term_id (term_id),
    KEY taxonomy (taxonomy)
);

CREATE TABLE wp_term_relationships (
    object_id bigint(20),                -- ID do post
    term_taxonomy_id bigint(20),         -- ID do termo taxonomy
    term_order int(11),                  -- Ordem de exibição
    
    PRIMARY KEY (object_id, term_taxonomy_id)
);
```

### 3.5 Relacionamentos (ER Diagram)

```
wp_posts (1) ──────── (N) wp_postmeta
   |
   | (N)
   ├──────> wp_term_relationships (N:N junction)
   |            |
   |            | (1)
   |            v
   |        wp_term_taxonomy (1)
   |            |
   |            | (1)
   |            v
   |        wp_terms
   |
   └──────> wp_comments (1:N)
                |
                | (1)
                v
            wp_commentmeta (1:N)

wp_users (1) ──────── (N) wp_posts (post_author)
   |
   └──────── (N) wp_usermeta
```

### 3.6 Entendendo Prefixos

```php
<?php
global $wpdb;

// Prefixo padrão é 'wp_' mas pode variar
echo $wpdb->prefix;  // wp_ (ou outro se customizado)

// Usar sempre o prefixo ao consultar
$posts = $wpdb->get_results(
    "SELECT * FROM {$wpdb->prefix}posts"
);

// Não é recomendado:
// $posts = $wpdb->get_results("SELECT * FROM wp_posts"); // Hardcoded!

// Multisite tem prefixes dinâmicos por site
// Blog 1: wp_posts, wp_postmeta
// Blog 2: wp_2_posts, wp_2_postmeta
?>
```

---

## 🛠️ WordPress Database API ($wpdb)

### 4.1 Global $wpdb

```php
<?php
// Usar a global $wpdb
global $wpdb;

// Ou usar a função wrapper
$wpdb = $GLOBALS['wpdb'];

// Propriedades úteis
echo $wpdb->prefix;           // wp_ (ou customizado)
echo $wpdb->posts;            // Nome completo: wp_posts
echo $wpdb->postmeta;         // wp_postmeta
echo $wpdb->users;            // wp_users
echo $wpdb->usermeta;         // wp_usermeta
echo $wpdb->comments;         // wp_comments
echo $wpdb->commentmeta;      // wp_commentmeta
echo $wpdb->terms;            // wp_terms
echo $wpdb->term_taxonomy;    // wp_term_taxonomy
echo $wpdb->term_relationships; // wp_term_relationships
?>
```

### 4.2 Métodos de Query

```php
<?php
global $wpdb;

// get_results() - Retorna array de objetos
$results = $wpdb->get_results("SELECT * FROM {$wpdb->posts}");
foreach ($results as $post) {
    echo $post->ID . ' - ' . $post->post_title;
}

// get_row() - Retorna single row
$post = $wpdb->get_row("SELECT * FROM {$wpdb->posts} WHERE ID = 1");
echo $post->post_title;

// get_var() - Retorna single value
$count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts}");

// get_col() - Retorna array de valores de uma coluna
$ids = $wpdb->get_col("SELECT ID FROM {$wpdb->posts}");
?>
```

### 4.3 Prepared Statements (SEGURANÇA CRÍTICA)

```php
<?php
global $wpdb;

// ✅ CORRETO - Seguro contra SQL Injection
$post_id = intval($_GET['id']);
$post = $wpdb->get_row($wpdb->prepare(
    "SELECT * FROM {$wpdb->posts} WHERE ID = %d",
    $post_id
));

// ✅ CORRETO - Múltiplos placeholders
$posts = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM {$wpdb->posts} WHERE post_type = %s AND post_status = %s",
    'post',
    'publish'
));

// ✅ CORRETO - Array de valores
$types = ['post', 'page'];
$placeholders = implode(',', array_fill(0, count($types), '%s'));
$posts = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM {$wpdb->posts} WHERE post_type IN ($placeholders)",
        ...$types
    )
);

// ❌ ERRADO - NUNCA faça isso!
$post = $wpdb->get_row("SELECT * FROM {$wpdb->posts} WHERE ID = {$_GET['id']}");
// SQL Injection: https://site.com/?id=1 OR 1=1

// Placeholders:
// %d - Integer
// %f - Float
// %s - String
?>
```

### 4.4 Insert, Update, Delete

```php
<?php
global $wpdb;

// INSERT
$result = $wpdb->insert(
    $wpdb->posts,
    [
        'post_title'   => 'Novo Post',
        'post_content' => 'Conteúdo do post',
        'post_type'    => 'post',
        'post_status'  => 'publish',
        'post_author'  => 1
    ],
    ['%s', '%s', '%s', '%s', '%d'] // Tipos de cada valor
);

if ($result === false) {
    echo 'Erro: ' . $wpdb->last_error;
} else {
    $new_post_id = $wpdb->insert_id;
}

// UPDATE
$updated = $wpdb->update(
    $wpdb->posts,
    ['post_title' => 'Título Atualizado'],  // Dados a atualizar
    ['ID' => 123],                           // Condição WHERE
    ['%s'],                                  // Tipos de dados
    ['%d']                                   // Tipos da condição
);

// DELETE
$deleted = $wpdb->delete(
    $wpdb->posts,
    ['ID' => 123],
    ['%d']
);
?>
```

### 4.5 Transações

```php
<?php
global $wpdb;

try {
    $wpdb->query('START TRANSACTION');
    
    // Inserir post
    $wpdb->insert($wpdb->posts, [
        'post_title' => 'Post Transacional',
        'post_type'  => 'post',
        'post_status' => 'publish'
    ]);
    $post_id = $wpdb->insert_id;
    
    // Inserir meta
    $wpdb->insert($wpdb->postmeta, [
        'post_id'   => $post_id,
        'meta_key'  => 'preco',
        'meta_value' => '99.90'
    ]);
    
    $wpdb->query('COMMIT');
} catch (Exception $e) {
    $wpdb->query('ROLLBACK');
    echo 'Erro: ' . $e->getMessage();
}
?>
```

---

## 📄 Posts, Pages e Custom Content

### 5.1 Post Types Nativos

```
post          - Posts do blog
page          - Páginas estáticas
attachment    - Arquivos (imagens, vídeos, etc)
revision      - Revisões de posts
nav_menu_item - Itens de menu
custom_css    - CSS customizado
customize_changeset - Changeset do customizer
oembed_cache  - Cache de oEmbed
user_request  - Requisições GDPR
wp_block      - Blocos reutilizáveis
wp_template   - Templates (block themes)
wp_template_part - Partes de template
```

### 5.2 Post Status

```
publish       - Publicado (visível)
draft         - Rascunho (apenas autor)
pending       - Pendente (aguardando revisão)
private       - Privado (apenas autenticado)
scheduled     - Agendado (data futura)
trash         - Lixo (deletado, mas recuperável)
auto-draft    - Auto-salvo (nunca foi publicado)
inherit       - Herdado (attachments, revisões)
```

### 5.3 Funções Essenciais

```php
<?php
// Obter post
$post = get_post(123);                    // Por ID
$post = get_post($post_obj);              // Já é um post

// Criar post
$post_id = wp_insert_post([
    'post_title'   => 'Novo Post',
    'post_content' => 'Conteúdo',
    'post_type'    => 'post',
    'post_status'  => 'publish',
    'post_author'  => 1
]);

// Atualizar post
wp_update_post([
    'ID'           => 123,
    'post_title'   => 'Novo Título'
]);

// Deletar post
wp_delete_post(123);                      // Manda pra lixo
wp_delete_post(123, true);                // Deleta permanentemente

// Post meta (Custom Fields)
update_post_meta(123, 'preco', '99.90');
$preco = get_post_meta(123, 'preco', true);  // true = single value
delete_post_meta(123, 'preco');

// Verificar tipo
if (is_singular('post')) { ... }
if (is_singular(['post', 'page'])) { ... }
if (get_post_type() === 'product') { ... }

// Queryar posts
$args = [
    'post_type'      => 'post',
    'posts_per_page' => 10,
    'paged'          => 1
];
$posts = get_posts($args);
// Ou usar WP_Query para mais controle
?>
```

### 5.4 Revisões, Featured Images e Hierarchy

```php
<?php
// Revisões
wp_get_post_revisions(123);                        // Array de revisões
wp_restore_post_revision($revision_id);            // Restaurar

// Featured Images (Thumbnail)
set_post_thumbnail(123, $attachment_id);
$thumb_id = get_post_thumbnail_id(123);
$thumb_url = get_the_post_thumbnail_url(123);
the_post_thumbnail('large');                       // Exibir

// Hierarchy (Parent/Child)
$args = [
    'post_parent' => 123,                          // Posts filhos
    'post_type'   => 'page',
    'numberposts' => -1
];
$filhos = get_posts($args);

$parent = get_post_parent(123);                    // Post pai
?>
```

---

## 🎨 Template Hierarchy

### 6.1 Ordem de Resolução de Templates

```
Singular (Single Post):
1. single-{post_type}-{post_name}.php (ex: single-product-iphone.php)
2. single-{post_type}.php (ex: single-product.php)
3. single.php
4. index.php

Pages:
1. page-{page_name}.php (ex: page-sobre.php)
2. page-{page_id}.php (ex: page-123.php)
3. page.php
4. index.php

Archives:
1. archive-{post_type}.php (ex: archive-product.php)
2. archive.php
3. index.php

Categories:
1. category-{category_slug}.php (ex: category-tecnologia.php)
2. category-{category_id}.php (ex: category-5.php)
3. category.php
4. archive.php
5. index.php

Tags:
1. tag-{tag_slug}.php (ex: tag-wordpress.php)
2. tag-{tag_id}.php (ex: tag-3.php)
3. tag.php
4. archive.php
5. index.php

Taxonomies (Custom):
1. taxonomy-{taxonomy}-{term_slug}.php
2. taxonomy-{taxonomy}.php
3. archive.php
4. index.php

Search:
1. search.php
2. index.php

404 Not Found:
1. 404.php
2. index.php

Attachments:
1. {mime_type}.php (ex: image.php, video.php)
2. attachment.php
3. single.php
4. index.php

Homepage/Front Page:
1. front-page.php (se configurado como static front page)
2. home.php (blog page)
3. index.php
```

### 6.2 Identificar o Template Atual

```php
<?php
// No template:
global $template;
echo $template;  // Caminho completo do arquivo atual

// Tipo de página (conditional tags)
is_home()                    // Página de blog
is_front_page()              // Home
is_singular()                // Post ou page único
is_singular('post')          // Post único
is_singular(['post', 'page']) // Post ou page
is_page()                    // Page (não post)
is_page(123)                 // Page específica
is_archive()                 // Qualquer archive
is_category()                // Archive de categoria
is_tag()                     // Archive de tag
is_taxonomy()                // Archive de custom taxonomy
is_search()                  // Página de busca
is_404()                     // 404
is_attachment()              // Attachment

// Template tags
get_page_template_slug()     // Slug do template designado
is_page_template('full-width.php') // Verifica template específico
?>
```

---

## 🔄 The Loop

### 7.1 Conceito Básico

```php
<?php
// The Loop é a iteração sobre posts
if (have_posts()) {
    while (have_posts()) {
        the_post();  // Setup post global ($post)
        
        // Aqui você está dentro do contexto do post
        the_title();          // Título
        the_content();        // Conteúdo
        the_excerpt();        // Resumo
        get_the_ID();         // ID do post
        get_the_author();     // Autor
    }
} else {
    echo 'Nenhum post encontrado';
}

// Ao sair do loop, $post volta ao anterior
?>
```

### 7.2 Funções do Loop

```php
<?php
// Iteração
have_posts()              // bool - há mais posts?
the_post()                // Move para próximo post, setup globals

// Informações
get_the_ID()              // ID do post atual
the_ID()                  // Echo do ID
get_the_title()           // Título
the_title()               // Echo do título
get_the_content()         // Conteúdo (função, não template tag)
the_content()             // Echo do conteúdo
get_the_excerpt()         // Resumo
the_excerpt()             // Echo do resumo
get_the_author()          // Autor
the_author()              // Echo do autor
get_the_date()            // Data formatada
the_date()                // Echo da data
get_post_type()           // Tipo de post
get_post_status()         // Status

// Taxonomias
the_category(', ')        // Exibir categorias
get_the_tags()            // Array de tags
the_tags('Etiquetas: ')   // Exibir tags

// Templates
get_the_post_thumbnail()  // HTML da thumbnail
the_post_thumbnail()      // Echo da thumbnail

// Permissões/Lógica
current_user_can('edit_post', get_the_ID())  // Pode editar?
?>
```

### 7.3 Loops Aninhados (cuidado!)

```php
<?php
// ❌ ERRADO - Loop quebrado
if (have_posts()) {
    while (have_posts()) {
        the_post();
        echo get_the_title();
        
        // Loop aninhado sem resetar!
        $args = ['numberposts' => 5];
        $posts = get_posts($args);
        foreach ($posts as $p) {
            echo $p->post_title;  // $post global foi sobrescrito!
        }
        
        // Aqui $post não é mais o post original
        echo get_the_title();  // ERRADO!
    }
}

// ✅ CORRETO - Salvar e restaurar
if (have_posts()) {
    while (have_posts()) {
        the_post();
        echo get_the_title();
        
        $args = ['numberposts' => 5];
        $posts = get_posts($args);
        foreach ($posts as $p) {
            echo $p->post_title;
        }
        
        // Restaurar post global
        wp_reset_postdata();
        
        echo get_the_title();  // OK!
    }
}

// ✅ CORRETO - Usar WP_Query
$main_query = new WP_Query(['paged' => 1]);

if ($main_query->have_posts()) {
    while ($main_query->have_posts()) {
        $main_query->the_post();
        echo get_the_title();
        
        $sub_query = new WP_Query(['numberposts' => 5]);
        if ($sub_query->have_posts()) {
            while ($sub_query->have_posts()) {
                $sub_query->the_post();
                echo get_the_title();
            }
        }
        wp_reset_postdata();
    }
}
wp_reset_postdata();
?>
```

---

## 📐 WordPress Coding Standards

### 8.1 PHPDoc Padrão

```php
<?php
/**
 * Descrição breve da função em uma linha.
 *
 * Descrição mais detalhada explicando o que a função faz,
 * como ela funciona, e qualquer informação relevante.
 *
 * @since 1.0.0 Versão em que foi adicionada
 * @deprecated 2.0.0 Se foi descontinuada
 *
 * @param string $param1        Descrição do primeiro parâmetro
 * @param int    $param2        Descrição do segundo parâmetro
 * @param array  $param3        Descrição do terceiro parâmetro {
 *     @type string $chave1 Descrição
 *     @type int    $chave2 Descrição
 * }
 *
 * @return WP_Post|null Descrição do retorno
 *
 * @throws Exception Se algo der errado
 *
 * @see função_relacionada()
 * @link https://wordpress.org/documentation
 *
 * @example
 *   $resultado = minha_funcao('valor', 123, ['key' => 'value']);
 *   if ($resultado) {
 *       echo $resultado->post_title;
 *   }
 */
function minha_funcao($param1, $param2, $param3 = []) {
    // Implementação
}
?>
```

### 8.2 Naming Conventions

```php
<?php
// Classes - PascalCase
class My_Custom_Plugin { }
class Post_Meta_Handler { }

// Functions - snake_case com prefixo do plugin
function meu_plugin_init() { }
function meu_plugin_enqueue_scripts() { }
function meu_plugin_sanitize_input() { }

// Variables - snake_case
$my_variable = 'value';
$post_data = [];
$user_id = 123;

// Constants - UPPER_SNAKE_CASE
define('MEU_PLUGIN_VERSION', '1.0.0');
define('MEU_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('MEU_PLUGIN_URL', plugin_dir_url(__FILE__));

// Hooks - snake_case
// Actions
do_action('meu_plugin_init');
do_action('meu_plugin_depois_salvar_post');

// Filters
apply_filters('meu_plugin_titulo_post', $titulo);
apply_filters('meu_plugin_conteudo_post', $conteudo);

// Options - snake_case com prefixo
update_option('meu_plugin_opcoes', $data);
get_option('meu_plugin_api_key');

// Transients
set_transient('meu_plugin_cache_dados', $data, HOUR_IN_SECONDS);
?>
```

### 8.3 Code Formatting

```php
<?php
// Indentação: 4 espaços (não tabs)
function exemplo() {
    if ($condicao) {
        // 4 espaços
    }
}

// Espaços em volta de operadores
$x = 1 + 2;
$y = function_a() && function_b();

// Chaves na mesma linha
if ($condicao) {
    // Código
} else {
    // Código
}

// Array formatting
$args = [
    'post_type' => 'post',
    'numberposts' => 10,
    'orderby' => 'date',
    'order' => 'DESC'
];

// Function calls com múltiplos argumentos
$resultado = funcao_grande(
    $parametro_1,
    $parametro_2,
    $parametro_3
);

// Espaços após keywords
if ($condicao) { }
foreach ($array as $item) { }
while ($condicao) { }
for ($i = 0; $i < 10; $i++) { }

// Comentários
// Comentário de uma linha

/* 
 * Comentário de múltiplas linhas
 * explicando algo mais complexo
 */
?>
```

### 8.4 Plugin Header

```php
<?php
/**
 * Plugin Name: Meu Awesome Plugin
 * Plugin URI: https://seusite.com/meu-plugin
 * Description: Descrição breve do que o plugin faz
 * Version: 1.0.0
 * Author: Seu Nome
 * Author URI: https://seusite.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: meu-plugin
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * WC tested up to: 7.0
 * 
 * Meu Plugin é free software: você pode redistribuir e/ou modificar
 * sob os termos da GNU General Public License publicada pela Free
 * Software Foundation, ou versão 2 da licença, ou qualquer versão
 * posterior.
 */

// Prevenir acesso direto
if (!defined('ABSPATH')) {
    exit;
}

// Definir constantes
define('MEU_PLUGIN_VERSION', '1.0.0');
define('MEU_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('MEU_PLUGIN_URL', plugin_dir_url(__FILE__));

// Carregar o plugin
require_once MEU_PLUGIN_PATH . 'includes/class-plugin.php';

// Ativar
function meu_plugin_activate() {
    // Lógica de ativação
}
register_activation_hook(__FILE__, 'meu_plugin_activate');

// Desativar
function meu_plugin_deactivate() {
    // Lógica de desativação
}
register_deactivation_hook(__FILE__, 'meu_plugin_deactivate');

// Inicializar
add_action('plugins_loaded', function() {
    Meu_Plugin\Plugin::get_instance()->init();
});
?>
```

---

## 🎓 Resumo da Fase 1

Ao dominar a **Fase 1**, você entenderá:

✅ **Arquitetura do WordPress** - Como os arquivos estão organizados  
✅ **Hook System** - Actions e Filters, base de todo desenvolvimento  
✅ **Banco de Dados** - Tabelas, relacionamentos, queries seguras  
✅ **wpdb API** - Interagir com o banco de forma segura  
✅ **Posts e Content** - Core data structures  
✅ **Template Hierarchy** - Como os temas encontram templates  
✅ **The Loop** - Iteração de posts  
✅ **Coding Standards** - Código limpo e profissional  

**Próximo passo:** Fase 2 - REST API Fundamentals

---

**Versão:** 1.0  
**Última atualização:** Janeiro 2026  
**Autor:** André | Especialista em PHP e WordPress
