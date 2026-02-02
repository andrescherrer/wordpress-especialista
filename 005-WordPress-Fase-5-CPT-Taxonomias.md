# 🎯 FASE 5: Custom Post Types e Taxonomies

**Versão:** 1.0  
**Data:** Janeiro 2026  
**Nível:** Especialista em PHP  
**Objetivo:** Dominar a criação de tipos de conteúdo customizados e taxonomias

---

**Navegação:** [Índice](./000-WordPress-Indice-Topicos.md) | [← Fase 4](./004-WordPress-Fase-4-Configuracoes-Admin.md) | [Fase 6 →](./006-WordPress-Fase-6-Shortcodes-Widgets-Gutenberg.md)

---

## 📑 Índice

1. [Fundamentos de Custom Post Types (CPT)](#fundamentos-de-custom-post-types-cpt)
2. [Registrar Custom Post Types](#registrar-custom-post-types)
3. [Custom Post Type Avançado](#custom-post-type-avançado)
4. [Fundamentos de Taxonomias](#fundamentos-de-taxonomias)
5. [Registrar Taxonomias Customizadas](#registrar-taxonomias-customizadas)
6. [Integração de CPT e Taxonomias](#integração-de-cpt-e-taxonomias)
7. [Funções Essenciais](#funções-essenciais)
8. [Boas Práticas](#boas-práticas)

---

## 🎯 Objetivos de Aprendizado

Ao final desta fase, você será capaz de:

1. ✅ Registrar custom post types com argumentos e capabilities apropriados
2. ✅ Criar taxonomias customizadas (hierárquicas e não-hierárquicas) e atribuí-las a CPTs
3. ✅ Configurar supports de CPT (title, editor, thumbnail, custom fields, etc.)
4. ✅ Implementar meta boxes customizadas e salvar meta data com segurança
5. ✅ Consultar CPTs usando `WP_Query` com taxonomias e meta fields customizados
6. ✅ Usar funções `register_post_type()` e `register_taxonomy()` corretamente
7. ✅ Tratar arquivos e templates single de CPTs no tema
8. ✅ Aplicar verificações de capability adequadas para ações de CPT (edit, delete, publish)

## 📝 Autoavaliação

Teste seu entendimento:

- [ ] Qual é a diferença entre taxonomias hierárquicas e não-hierárquicas?
- [ ] Como você consulta posts por termos de taxonomia customizada?
- [ ] Quais são as implicações de segurança de expor posts em rascunho na REST API?
- [ ] Como você adiciona colunas customizadas à tabela de listagem admin para um CPT?
- [ ] Qual é o propósito de `capability_type` em `register_post_type()`?
- [ ] Como você filtra queries de CPT usando `pre_get_posts`?
- [ ] Qual é a diferença entre `register_post_type()` e o filter `register_post_type_args`?
- [ ] Como você remove meta boxes padrão e adiciona customizadas?

## 🛠️ Projeto Prático

**Construir:** Sistema de Gerenciamento de Eventos

Crie um sistema completo de gerenciamento de eventos com:
- Custom Post Type "Event" com campos customizados (data, localização, preço)
- Taxonomia customizada "Event Category" (hierárquica)
- Taxonomia customizada "Event Tag" (não-hierárquica)
- Meta boxes customizadas para detalhes do evento
- Colunas admin customizadas mostrando informações-chave
- Templates de arquivo e single para eventos
- Suporte REST API para eventos

**Tempo estimado:** 10-12 horas  
**Dificuldade:** Intermediário

---

## ❌ Equívocos Comuns

### Equívoco 1: "Custom Post Types precisam de tabelas de banco de dados customizadas"
**Realidade:** CPTs usam as mesmas tabelas `wp_posts` e `wp_postmeta` que posts regulares. A coluna `post_type` os diferencia.

**Por que é importante:** Entender isso ajuda com queries e otimização de performance. Você não precisa de tabelas separadas.

**Como lembrar:** CPTs = mesmas tabelas, valor diferente de `post_type`.

### Equívoco 2: "Taxonomias são apenas categorias"
**Realidade:** Taxonomias são um sistema geral. Categorias e tags são taxonomias built-in. Você pode criar taxonomias customizadas para qualquer propósito.

**Por que é importante:** Taxonomias são poderosas para organizar qualquer tipo de conteúdo, não apenas posts.

**Como lembrar:** Taxonomia = sistema de classificação. Categorias/tags = exemplos de taxonomias.

### Equívoco 3: "Taxonomias hierárquicas são sempre melhores"
**Realidade:** Taxonomias hierárquicas (como categorias) são boas para relacionamentos pai-filho. Não-hierárquicas (como tags) são melhores para organização plana.

**Por que é importante:** Escolher o tipo errado torna a organização de conteúdo difícil e queries ineficientes.

**Como lembrar:** Hierárquica = estrutura de árvore (categorias). Não-hierárquica = lista plana (tags).

### Equívoco 4: "CPTs aparecem automaticamente na REST API"
**Realidade:** Você precisa definir `'show_in_rest' => true` ao registrar o CPT. Por padrão, custom post types não são expostos na REST API.

**Por que é importante:** Sem essa configuração, seu CPT não será acessível via REST API, limitando possibilidades de integração.

**Como lembrar:** REST API = recurso opt-in. Defina `show_in_rest` para habilitar.

### Equívoco 5: "register_post_type() deve ser chamado no hook init"
**Realidade:** Embora `init` seja o hook padrão, você pode usar `after_setup_theme` para CPTs baseados em tema. O importante é chamá-lo antes do WordPress precisar saber sobre o CPT.

**Por que é importante:** Entender quando registrar dá flexibilidade na arquitetura de plugin/tema.

**Como lembrar:** Registre antes do WordPress consultar conteúdo. `init` = padrão seguro.

---

## Fundamentos de Custom Post Types (CPT)

### 1.1 O que são Custom Post Types?

**Custom Post Types** são extensões do sistema de posts do WordPress que permitem criar tipos de conteúdo personalizados:

- **Exemplos**: Produtos, Portfólio, Eventos, Depoimentos, FAQ, Livros, Imóveis, Restaurantes
- **Funcionalidades**: Revisões, Taxonomias, Featured Image, Custom Fields, Permalink estruturado
- **Armazenamento**: Mesma tabela `wp_posts` (diferenciado por coluna `post_type`)
- **Isolamento**: Aparecem separados no menu admin e nas listagens

### 1.2 Hierarquia de Post Types no WordPress

```
post          - Posts do blog (padrão)
page          - Páginas estáticas (padrão)
attachment    - Arquivos de mídia (padrão)
revision      - Revisões (interno)
nav_menu_item - Itens de menu (interno)
custom_css    - CSS customizado (bloco themes)
customize_changeset - Changesets (customizer)
oembed_cache  - Cache de oEmbed (interno)
user_request  - Requisições GDPR (interno)
wp_block      - Blocos reutilizáveis (block editor)
```

### 1.3 Quando Usar CPT vs Post vs Page

| Situação | Tipo | Razão |
|----------|------|-------|
| Posts de blog normais | `post` | Padrão do WordPress |
| Página estática (sobre, contato) | `page` | Pode ter filhos (hierarquia) |
| Catálogo de produtos | CPT | Separado, listagem própria |
| Galeria de portfólio | CPT | Tipo único, estrutura específica |
| Eventos com data/local | CPT | Metadados especializados |
| FAQ com categorias | CPT + Taxonomy | Estrutura customizada |

---

## Registrar Custom Post Types

### 2.1 CPT Simples - Portfólio

```php
<?php
class Meu_Plugin_CPT {
    
    public function __construct() {
        add_action('init', [$this, 'register_post_types']);
        add_action('init', [$this, 'register_post_type_supports']);
    }
    
    /**
     * Exemplo 1: CPT Simples - Portfólio
     */
    public function register_portfolio_cpt() {
        $labels = [
            'name'                  => 'Portfólio',
            'singular_name'         => 'Item de Portfólio',
            'menu_name'             => 'Portfólio',
            'name_admin_bar'        => 'Portfólio',
            'archives'              => 'Arquivos de Portfólio',
            'attributes'            => 'Atributos',
            'parent_item_colon'     => 'Item Pai:',
            'all_items'             => 'Todos os Itens',
            'add_new_item'          => 'Adicionar Novo Item',
            'add_new'               => 'Adicionar Novo',
            'new_item'              => 'Novo Item',
            'edit_item'             => 'Editar Item',
            'update_item'           => 'Atualizar Item',
            'view_item'             => 'Ver Item',
            'view_items'            => 'Ver Itens',
            'search_items'          => 'Buscar Item',
            'not_found'             => 'Não encontrado',
            'not_found_in_trash'    => 'Não encontrado na lixeira',
            'featured_image'        => 'Imagem Destaque',
            'set_featured_image'    => 'Definir Imagem Destaque',
            'remove_featured_image' => 'Remover Imagem Destaque',
            'use_featured_image'    => 'Usar como Imagem Destaque',
        ];
        
        $args = [
            'label'             => 'Portfólio',
            'description'       => 'Items do Portfólio',
            'labels'            => $labels,
            'public'            => true,
            'publicly_queryable'=> true,
            'show_ui'           => true,
            'show_in_menu'      => true,
            'show_in_nav_menus' => true,
            'show_in_rest'      => true,           // Expor na REST API
            'query_var'         => 'portfolio',
            'rewrite'           => [
                'slug'       => 'portfolio',       // URL: /portfolio/item-name/
                'with_front' => true,
                'feeds'      => false,
                'pages'      => true
            ],
            'has_archive'       => 'portfolio',    // Página de arquivo: /portfolio/
            'hierarchical'      => false,          // Posts vs Pages
            'menu_position'     => 5,              // Posição no menu
            'menu_icon'         => 'dashicons-images-alt2',
            'supports'          => [
                'title',
                'editor',
                'excerpt',
                'thumbnail',
                'revisions',
                'custom-fields'
            ],
            'capabilities'      => [
                'edit_post'             => 'edit_portfolio',
                'read_post'             => 'read_portfolio',
                'delete_post'           => 'delete_portfolio',
                'edit_posts'            => 'edit_portfolios',
                'edit_others_posts'     => 'edit_others_portfolios',
                'publish_posts'         => 'publish_portfolios',
                'read_private_posts'    => 'read_private_portfolios',
                'delete_posts'          => 'delete_portfolios',
                'delete_private_posts'  => 'delete_private_portfolios',
                'delete_published_posts'=> 'delete_published_portfolios',
                'edit_private_posts'    => 'edit_private_portfolios',
                'edit_published_posts'  => 'edit_published_portfolios'
            ],
            'map_meta_cap'      => true,
        ];
        
        register_post_type('portfolio', $args);
    }
    
    /**
     * Exemplo 2: CPT com Suporte a Pai-Filho (Hierárquico)
     */
    public function register_service_cpt() {
        register_post_type('service', [
            'label'             => 'Serviços',
            'public'            => true,
            'show_ui'           => true,
            'show_in_rest'      => true,
            'rewrite'           => ['slug' => 'servicos'],
            'has_archive'       => 'servicos',
            'hierarchical'      => true,          // ✨ PERMITE HIERARQUIA (Pai-Filho)
            'supports'          => ['title', 'editor', 'thumbnail', 'page-attributes'],
            'menu_icon'         => 'dashicons-briefcase',
        ]);
    }
    
    /**
     * Adicionar suporte a features
     */
    public function register_post_type_supports() {
        // Adicionar suporte depois do registro
        add_post_type_support('portfolio', 'author');
        
        // Remover suporte se necessário
        // remove_post_type_support('portfolio', 'comments');
    }
}

// Instanciar
new Meu_Plugin_CPT();
?>
```

### 2.2 Argumentos Detalhados do register_post_type()

```php
<?php
$args = [
    // ========== LABELS E DESCRIÇÃO ==========
    'label'             => 'Etiqueta Plural',
    'description'       => 'Descrição do tipo de post',
    'labels'            => [/* array de labels */],
    
    // ========== VISIBILIDADE ==========
    'public'            => true,              // Visível no front-end
    'publicly_queryable'=> true,              // Pode ser queryado
    'show_ui'           => true,              // Mostra interface admin
    'show_in_menu'      => true,              // Mostra no menu admin
    'show_in_nav_menus' => true,              // Pode ser adicionado a menus
    'show_in_rest'      => true,              // Expõe na REST API
    
    // ========== URL E PERMALINK ==========
    'query_var'         => 'portfolio',       // Query var: ?portfolio=name
    'rewrite'           => [
        'slug'       => 'portfolio',
        'with_front' => true,    // Respeita front page base
        'feeds'      => false,
        'pages'      => true
    ],
    'has_archive'       => 'portfolio',       // /portfolio/ lista
    
    // ========== HIERARQUIA ==========
    'hierarchical'      => false,             // true = tipo página, false = tipo post
    
    // ========== MENU ==========
    'menu_position'     => 5,                 // Posição no menu (0-99)
    'menu_icon'         => 'dashicons-xxx',   // Ícone do menu
    
    // ========== FUNCIONALIDADES ==========
    'supports'          => [
        'title',           // Campo de título
        'editor',          // Editor WYSIWYG
        'excerpt',         // Resumo
        'thumbnail',       // Imagem destaque
        'revisions',       // Versionamento
        'custom-fields',   // Campo customizado (meta)
        'author',          // Seletor de autor
        'comments',        // Sistema de comentários
        'trackbacks',      // Trackbacks
        'page-attributes', // Suporta parent (se hierarchical=true)
        'post-formats'     // Formatos de post
    ],
    
    // ========== SEGURANÇA E CAPABILIDADES ==========
    'capabilities'      => [
        'edit_post'               => 'edit_portfolio',
        'read_post'               => 'read_portfolio',
        'delete_post'             => 'delete_portfolio',
        'edit_posts'              => 'edit_portfolios',
        'edit_others_posts'       => 'edit_others_portfolios',
        'edit_private_posts'      => 'edit_private_portfolios',
        'edit_published_posts'    => 'edit_published_portfolios',
        'publish_posts'           => 'publish_portfolios',
        'read_private_posts'      => 'read_private_portfolios',
        'delete_posts'            => 'delete_portfolios',
        'delete_private_posts'    => 'delete_private_portfolios',
        'delete_published_posts'  => 'delete_published_portfolios',
    ],
    'map_meta_cap'      => true,             // Mapear capabilidades meta
    
    // ========== TEMPLATE ==========
    'template'          => [                 // Template de blocos (block editor)
        ['core/paragraph', ['placeholder' => 'Conteúdo...']],
        ['core/image'],
    ],
    'template_lock'     => 'all',            // 'all', 'insert', false (sem lock)
];
?>
```

---

## Custom Post Type Avançado

### 3.1 CPT com Suporte Completo e Hooks

```php
<?php
class Advanced_CPT {
    
    private $post_type = 'product';
    
    public function __construct() {
        add_action('init', [$this, 'register']);
        add_filter('post_type_labels_' . $this->post_type, [$this, 'filter_labels']);
        add_filter('bulk_actions-edit-' . $this->post_type, [$this, 'add_bulk_actions']);
        add_action('admin_menu', [$this, 'modify_menu']);
    }
    
    public function register() {
        register_post_type($this->post_type, [
            'label'             => 'Produtos',
            'public'            => true,
            'show_ui'           => true,
            'show_in_rest'      => true,
            'rest_controller_class' => 'WP_REST_Posts_Controller', // Custom controller
            'rewrite'           => ['slug' => 'produtos'],
            'has_archive'       => 'produtos',
            'hierarchical'      => false,
            'supports'          => ['title', 'editor', 'thumbnail', 'custom-fields'],
            'menu_icon'         => 'dashicons-shopping-cart',
            'menu_position'     => 20,
        ]);
    }
    
    /**
     * Modificar labels dinamicamente
     */
    public function filter_labels($labels) {
        $labels->name = 'Catálogo de Produtos';
        return $labels;
    }
    
    /**
     * Adicionar ações em bulk (seleção múltipla)
     */
    public function add_bulk_actions($actions) {
        $actions['change_status'] = 'Mudar Status';
        $actions['send_email'] = 'Enviar Email';
        return $actions;
    }
    
    /**
     * Modificar menu do CPT
     */
    public function modify_menu() {
        global $submenu;
        
        // Mudar rótulo de "Add New"
        $submenu['edit.php?post_type=' . $this->post_type][10][0] = 'Novo Produto';
        
        // Adicionar submenu customizado
        add_submenu_page(
            'edit.php?post_type=' . $this->post_type,
            'Importar Produtos',
            'Importar',
            'manage_options',
            'import-products',
            [$this, 'import_page']
        );
    }
    
    public function import_page() {
        echo '<div class="wrap"><h1>Importar Produtos</h1></div>';
    }
}

new Advanced_CPT();
?>
```

---

## Fundamentos de Taxonomias

### 4.1 O que são Taxonomias?

**Taxonomias** são sistemas de categorização no WordPress:

| Nativa | Descrição |
|--------|-----------|
| `category` | Categorias de posts |
| `post_tag` | Tags de posts |
| `post_format` | Formatos de post (aside, gallery, link, etc) |

**Custom Taxonomies**: Você cria novas taxonomias para categorizar seus CPTs

### 4.2 Tipo: Hierárquica vs Não-Hierárquica

```
Hierárquica (como Categorias)      Não-Hierárquica (como Tags)
├── Eletrônicos                     #wordpress
│   ├── Computadores               #design
│   ├── Celulares                  #frontend
│   └── Acessórios
├── Livros
│   ├── Ficção
│   └── Não-ficção
└── Roupas
```

---

## Registrar Taxonomias Customizadas

### 5.1 Taxonomia Básica - Categoria de Produtos

```php
<?php
class Meu_Plugin_Taxonomies {
    
    public function __construct() {
        add_action('init', [$this, 'register_taxonomies']);
    }
    
    /**
     * Registrar Taxonomia Hierárquica: Categoria de Produtos
     */
    public function register_product_category_taxonomy() {
        $labels = [
            'name'                       => 'Categorias de Produtos',
            'singular_name'              => 'Categoria de Produto',
            'menu_name'                  => 'Categorias',
            'all_items'                  => 'Todas as Categorias',
            'parent_item'                => 'Categoria Pai',
            'parent_item_colon'          => 'Categoria Pai:',
            'new_item_name'              => 'Nome da Nova Categoria',
            'add_new_item'               => 'Adicionar Nova Categoria',
            'edit_item'                  => 'Editar Categoria',
            'update_item'                => 'Atualizar Categoria',
            'separate_items_with_commas' => 'Separar categorias com vírgula',
            'search_items'               => 'Buscar Categorias',
            'add_or_remove_items'        => 'Adicionar ou remover categorias',
            'choose_from_most_used'      => 'Escolher das mais usadas',
            'not_found'                  => 'Nenhuma categoria encontrada',
            'back_to_items'              => '← Voltar para Categorias',
        ];
        
        register_taxonomy('product_cat', 'product', [
            'labels'            => $labels,
            'description'       => 'Categorias do catálogo de produtos',
            'public'            => true,
            'publicly_queryable'=> true,
            'show_ui'           => true,
            'show_in_menu'      => true,
            'show_in_nav_menus' => true,
            'show_in_rest'      => true,
            'hierarchical'      => true,              // ✨ HIERÁRQUICA
            'rewrite'           => [
                'slug'       => 'categoria-produto',
                'with_front' => true,
                'hierarchical' => true               // URLs: /cat-pai/cat-filha/
            ],
            'rest_base'         => 'product-categories',
            'query_var'         => 'product-cat',
        ]);
    }
    
    /**
     * Registrar Taxonomia Não-Hierárquica: Tags de Portfólio
     */
    public function register_portfolio_tags_taxonomy() {
        register_taxonomy('portfolio-tag', 'portfolio', [
            'labels'            => [
                'name'                       => 'Tags',
                'singular_name'              => 'Tag',
                'search_items'               => 'Buscar Tags',
                'all_items'                  => 'Todas as Tags',
                'edit_item'                  => 'Editar Tag',
                'update_item'                => 'Atualizar Tag',
                'add_new_item'               => 'Adicionar Nova Tag',
            ],
            'public'            => true,
            'show_ui'           => true,
            'show_in_rest'      => true,
            'hierarchical'      => false,             // ✨ NÃO-HIERÁRQUICA
            'rewrite'           => ['slug' => 'portfolio-tag'],
        ]);
    }
}

new Meu_Plugin_Taxonomies();
?>
```

### 5.2 Argumentos Detalhados do register_taxonomy()

```php
<?php
$args = [
    // ========== LABELS ==========
    'labels'            => [/* array de labels */],
    'description'       => 'Descrição da taxonomia',
    
    // ========== VISIBILIDADE ==========
    'public'            => true,
    'publicly_queryable'=> true,
    'show_ui'           => true,
    'show_in_menu'      => true,
    'show_in_nav_menus' => true,
    'show_in_rest'      => true,
    
    // ========== TIPO ==========
    'hierarchical'      => true,       // true = categorias, false = tags
    
    // ========== URL E QUERY ==========
    'rewrite'           => [
        'slug'          => 'categoria',
        'with_front'    => true,
        'hierarchical'  => true        // URLs hierárquicas
    ],
    'query_var'         => 'categoria',
    'rest_base'         => 'categories',
    'rest_controller_class' => 'WP_REST_Terms_Controller',
    
    // ========== META ==========
    'show_admin_column' => true,       // Mostrar coluna na listagem
    'show_in_quick_edit'=> true,
];
?>
```

---

## Integração de CPT e Taxonomias

### 6.1 Queryar CPT com Taxonomia

```php
<?php
// Buscar produtos da categoria "eletrônicos"
$args = [
    'post_type'      => 'product',
    'posts_per_page' => 10,
    'tax_query'      => [
        [
            'taxonomy' => 'product_cat',
            'field'    => 'slug',         // slug, id, name
            'terms'    => 'eletronicos',
            'operator' => 'IN'            // IN, NOT IN, AND
        ]
    ]
];

$products = get_posts($args);

foreach ($products as $product) {
    echo $product->post_title;
    
    // Obter termos da taxonomia
    $categories = wp_get_post_terms($product->ID, 'product_cat');
    foreach ($categories as $cat) {
        echo $cat->name . ', ';
    }
}

// Múltiplas taxonomias (AND)
$args = [
    'post_type'      => 'product',
    'tax_query'      => [
        'relation' => 'AND',             // AND ou OR
        [
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => ['eletronicos', 'livros']
        ],
        [
            'taxonomy' => 'product-tag',
            'field'    => 'slug',
            'terms'    => ['promocao']
        ]
    ]
];
?>
```

### 6.2 REST API com CPT e Taxonomias

```php
<?php
// GET /wp-json/wp/v2/product
// GET /wp-json/wp/v2/product?product_cat=eletronicos
// GET /wp-json/wp/v2/product-categories
// GET /wp-json/wp/v2/product-categories/123

// Filtrar por múltiplas categorias
// GET /wp-json/wp/v2/product?product_cat=1,2,3
?>
```

---

## Funções Essenciais

### 7.1 Funções de Taxonomias

```php
<?php
// Obter termos de uma taxonomia
$terms = get_terms('product_cat', [
    'hide_empty' => false,
    'number'     => 10
]);

// Obter termo por ID
$term = get_term(1, 'product_cat');
echo $term->name;
echo $term->slug;
echo $term->term_id;

// Obter termos de um post
$terms = wp_get_post_terms($post_id, 'product_cat');

// Atribuir termo a um post
wp_set_post_terms($post_id, [1, 2, 3], 'product_cat');  // Sobrescreve
wp_add_object_terms($post_id, 1, 'product_cat');        // Adiciona

// Remover termo de um post
wp_remove_object_terms($post_id, 1, 'product_cat');

// Contar posts em um termo
$count = (int) get_term(1, 'product_cat')->count;

// Criar novo termo
wp_insert_term('Novo Produto', 'product_cat', [
    'description' => 'Descrição',
    'slug'        => 'novo-produto',
    'parent'      => 0
]);
?>
```

---

## Boas Práticas

### 8.1 Checklist

- ✅ Use nomes únicos com prefixo (meu_plugin_cpt, meu_plugin_tax)
- ✅ Sempre exponha na REST API (`show_in_rest` => true)
- ✅ Configure `map_meta_cap` para segurança
- ✅ Use labels array completo (melhor UX)
- ✅ Implemente `custom-fields` suporte para metadados
- ✅ Use rewrite rules apropriadas
- ✅ Teste com WP-CLI: `wp post-type list`, `wp taxonomy list`
- ✅ Documente tipos e taxonomias para outros devs

### 8.2 Debugging

```bash
# Listar todos os CPTs
wp post-type list

# Listar todas as taxonomias
wp taxonomy list

# Listar posts de um CPT
wp post list --post_type=portfolio

# Listar termos
wp term list product_cat
```

---

**Versão:** 1.0  
**Última atualização:** Janeiro 2026  
**Próxima fase:** Fase 6 - Shortcodes, Widgets e Gutenberg Blocks
