# 🎯 FASE 6: Shortcodes, Widgets e Gutenberg Blocks

**Versão:** 1.0  
**Data:** Janeiro 2026  
**Nível:** Especialista em PHP  
**Objetivo:** Dominar a criação de Shortcodes, Widgets customizados e Gutenberg Blocks

---

**Navegação:** [Índice](./000-WordPress-Indice-Topicos.md) | [← Fase 5](./005-WordPress-Fase-5-CPT-Taxonomias.md) | [Fase 7 →](./007-WordPress-Fase-7-WP-CLI-Fundamentos.md)

---

## 📑 Índice

1. [Objetivos de Aprendizado](#objetivos-de-aprendizado)
2. [Autoavaliação](#autoavaliacao)
3. [Projeto Prático](#projeto-pratico)
4. [Equívocos Comuns](#equivocos-comuns)
5. [Fundamentos de Shortcodes](#fundamentos-de-shortcodes)
6. [Criar Shortcodes Básicos](#criar-shortcodes-basicos)
7. [Shortcodes Avançados](#shortcodes-avancados)
8. [Widgets API Clássica](#widgets-api-classica)
9. [Criar Widgets Customizados](#criar-widgets-customizados)
10. [Fundamentos de Gutenberg Blocks](#fundamentos-de-gutenberg-blocks)
11. [Criar Custom Blocks](#criar-custom-blocks)
12. [Dynamic Blocks e Block Patterns](#dynamic-blocks-e-block-patterns)
13. [Boas Práticas](#boas-praticas)
14. [Resumo da Fase 6](#resumo-da-fase-6)

---

<a id="objetivos-de-aprendizado"></a>
## 🎯 Objetivos de Aprendizado

Ao final desta fase, você será capaz de:

1. ✅ Criar shortcodes simples e avançados com atributos e tratamento de conteúdo
2. ✅ Construir widgets customizados usando a Widgets API
3. ✅ Entender os fundamentos do desenvolvimento de blocos Gutenberg
4. ✅ Criar blocos Gutenberg estáticos e dinâmicos usando `@wordpress/create-block`
5. ✅ Implementar atributos de bloco, componentes de edição e funções de salvamento
6. ✅ Usar Block Patterns e Block Variations efetivamente
7. ✅ Enfileirar scripts e estilos adequadamente para blocos
8. ✅ Aplicar boas práticas para desenvolvimento de shortcodes, widgets e blocos

<a id="autoavaliacao"></a>
## 📝 Autoavaliação

Teste seu entendimento:

- [ ] Qual é a diferença entre shortcodes auto-fechados e com conteúdo?
- [ ] Como você escapa adequadamente a saída em shortcodes para prevenir XSS?
- [ ] Qual é a diferença entre blocos Gutenberg estáticos e dinâmicos?
- [ ] Como você registra atributos de bloco e os usa em funções de edição e salvamento?
- [ ] Qual é o propósito de `register_block_type()` vs `@wordpress/create-block`?
- [ ] Como você cria block patterns reutilizáveis?
- [ ] Qual é a diferença entre Widgets API e blocos Gutenberg?
- [ ] Como você trata depreciações de blocos ao atualizar atributos de bloco?

<a id="projeto-pratico"></a>
## 🛠️ Projeto Prático

**Construir:** Plugin de Exibição de Conteúdo

Crie um plugin que inclua:
- Múltiplos shortcodes para exibir conteúdo (posts recentes, depoimentos, etc.)
- Widget customizado para exibir conteúdo em destaque
- Bloco Gutenberg para exibir itens de custom post type
- Bloco dinâmico que busca dados da REST API
- Block pattern para layouts comuns

**Tempo estimado:** 10-12 horas  
**Dificuldade:** Intermediário

---

<a id="equivocos-comuns"></a>
## ❌ Equívocos Comuns

### Equívoco 1: "Shortcodes executam código PHP diretamente"
**Realidade:** Shortcodes são parseados pelo WordPress e chamam funções callback registradas. Eles não executam código PHP arbitrário.

**Por que é importante:** Shortcodes são mais seguros que permitir execução direta de PHP, mas você ainda precisa escapar a saída adequadamente.

**Como lembrar:** Shortcode = função callback registrada, não execução direta de PHP.

### Equívoco 2: "Blocos Gutenberg substituem shortcodes"
**Realidade:** Blocos e shortcodes servem propósitos diferentes. Blocos são para edição de conteúdo, shortcodes são para inserção dinâmica de conteúdo. Ambos podem coexistir.

**Por que é importante:** Entender quando usar blocos vs shortcodes ajuda a escolher a ferramenta certa para o trabalho.

**Como lembrar:** Blocos = edição visual. Shortcodes = inserção programática de conteúdo.

### Equívoco 3: "Widgets API está depreciada"
**Realidade:** A Widgets API clássica ainda funciona e é mantida. Widgets baseados em blocos são uma adição, não uma substituição (ainda).

**Por que é importante:** Muitos temas e plugins ainda usam widgets clássicos. Ambos os sistemas são válidos.

**Como lembrar:** Widgets clássicos = ainda suportados. Widgets de bloco = opção mais nova.

### Equívoco 4: "Blocos dinâmicos sempre precisam de renderização server-side"
**Realidade:** Blocos dinâmicos podem usar JavaScript client-side para buscar e renderizar dados, reduzindo carga do servidor.

**Por que é importante:** Entender opções de renderização ajuda a otimizar performance.

**Como lembrar:** Server-side = renderização PHP. Client-side = busca JavaScript.

---

<a id="fundamentos-de-shortcodes"></a>
## Fundamentos de Shortcodes

### O que são Shortcodes?

**Shortcodes** são tags especiais que permitem inserir funcionalidades dinâmicas em posts e páginas sem escrever código PHP.

**Características:**
- Tags especiais entre colchetes `[nome_shortcode]`
- Processados quando o conteúdo é exibido
- Podem ter atributos e conteúdo interno
- Simples de usar para usuários finais
- Perfeitos para funcionalidades reutilizáveis

**Tipos de Shortcodes:**
```
[shortcode]                                    - Simples (self-closing)
[shortcode atributo="valor"]                   - Com atributos
[shortcode]conteúdo[/shortcode]                - Com conteúdo interno
[shortcode atributo="valor"]conteúdo[/shortcode] - Combinado
```

---

<a id="criar-shortcodes-basicos"></a>
## Criar Shortcodes Básicos

### 6.2.1 Registrar Shortcodes

```php
class Meu_Plugin_Shortcodes {
    
    public function __construct() {
        // Registrar shortcodes
        add_shortcode('meu_botao', [$this, 'shortcode_botao']);
        add_shortcode('meu_alert', [$this, 'shortcode_alert']);
        add_shortcode('portfolio_grid', [$this, 'shortcode_portfolio_grid']);
        add_shortcode('form_contato', [$this, 'shortcode_form_contato']);
        add_shortcode('preco', [$this, 'shortcode_preco']);
        add_shortcode('galeria_custom', [$this, 'shortcode_galeria']);
        add_shortcode('contador', [$this, 'shortcode_contador']);
        add_shortcode('tabs', [$this, 'shortcode_tabs']);
        add_shortcode('tab', [$this, 'shortcode_tab']);
        
        // Enfileirar assets quando shortcode for usado
        add_action('wp_enqueue_scripts', [$this, 'enqueue_shortcode_assets']);
    }
}

new Meu_Plugin_Shortcodes();
```

### 6.2.2 Shortcode Simples - Botão

**Uso:** `[meu_botao texto="Clique Aqui" url="https://exemplo.com"]`

```php
/**
 * Shortcode de botão simples
 */
public function shortcode_botao($atts) {
    // Atributos padrão
    $atts = shortcode_atts([
        'texto'   => 'Saiba Mais',
        'url'     => '#',
        'cor'     => 'primary',
        'tamanho' => 'medium',
        'target'  => '_self',
        'class'   => '',
    ], $atts, 'meu_botao');
    
    // Sanitizar atributos
    $texto = sanitize_text_field($atts['texto']);
    $url = esc_url($atts['url']);
    $cor = sanitize_html_class($atts['cor']);
    $tamanho = sanitize_html_class($atts['tamanho']);
    $target = in_array($atts['target'], ['_self', '_blank']) ? 
        $atts['target'] : '_self';
    $class = sanitize_html_class($atts['class']);
    
    // Validação
    if (empty($url)) {
        return '<!-- Shortcode botão: URL inválida -->';
    }
    
    // HTML
    $html = sprintf(
        '<a href="%s" class="btn btn-%s btn-%s %s" target="%s">%s</a>',
        $url,
        esc_attr($cor),
        esc_attr($tamanho),
        esc_attr($class),
        esc_attr($target),
        esc_html($texto)
    );
    
    return $html;
}
```

### 6.2.3 Shortcode com Atributos - Alert/Notificação

**Uso:** `[meu_alert tipo="info" titulo="Atenção" fechar="sim"]Conteúdo da mensagem[/meu_alert]`

```php
/**
 * Shortcode de alerta com atributos
 */
public function shortcode_alert($atts, $content = '') {
    // Defaults
    $atts = shortcode_atts([
        'tipo'   => 'info',      // info, success, warning, error
        'titulo' => '',
        'fechar' => 'sim',       // sim ou não
        'icon'   => 'sim',
    ], $atts, 'meu_alert');
    
    // Sanitizar
    $tipo = in_array($atts['tipo'], ['info', 'success', 'warning', 'error']) ?
        $atts['tipo'] : 'info';
    $titulo = sanitize_text_field($atts['titulo']);
    $fechar = sanitize_text_field($atts['fechar']) === 'sim';
    $content = wp_kses_post($content); // Permitir HTML básico
    
    // Cores por tipo
    $cores = [
        'info'    => ['bg' => '#d1ecf1', 'text' => '#0c5460', 'border' => '#bee5eb'],
        'success' => ['bg' => '#d4edda', 'text' => '#155724', 'border' => '#c3e6cb'],
        'warning' => ['bg' => '#fff3cd', 'text' => '#856404', 'border' => '#ffeeba'],
        'error'   => ['bg' => '#f8d7da', 'text' => '#721c24', 'border' => '#f5c6cb'],
    ];
    
    $cor = $cores[$tipo];
    $icon_class = [
        'info'    => 'ℹ️',
        'success' => '✓',
        'warning' => '⚠️',
        'error'   => '✕',
    ][$tipo];
    
    // HTML
    $html = sprintf(
        '<div class="alert alert-%s" style="background:%s; color:%s; border:1px solid %s; padding:15px; border-radius:4px; margin:15px 0;">',
        esc_attr($tipo),
        esc_attr($cor['bg']),
        esc_attr($cor['text']),
        esc_attr($cor['border'])
    );
    
    if ($titulo || sanitize_text_field($atts['icon']) === 'sim') {
        $html .= '<strong>';
        if (sanitize_text_field($atts['icon']) === 'sim') {
            $html .= $icon_class . ' ';
        }
        $html .= esc_html($titulo) . '</strong>';
    }
    
    $html .= $content;
    
    if ($fechar) {
        $html .= '<button onclick="this.parentElement.style.display=\'none\';" 
                 style="float:right; background:none; border:none; font-size:20px; cursor:pointer;">×</button>';
    }
    
    $html .= '</div>';
    
    return $html;
}
```

### 6.2.4 Shortcode com Query - Posts Recentes

**Uso:** `[posts_recentes numero="5" categoria="noticias" ordenar="desc"]`

```php
/**
 * Shortcode que exibe posts recentes
 */
public function shortcode_posts_recentes($atts) {
    $atts = shortcode_atts([
        'numero'   => 5,
        'categoria' => '',
        'post_type' => 'post',
        'ordenar'  => 'desc',
    ], $atts, 'posts_recentes');
    
    $numero = absint($atts['numero']);
    $categoria = sanitize_text_field($atts['categoria']);
    $post_type = sanitize_text_field($atts['post_type']);
    $ordenar = $atts['ordenar'] === 'asc' ? 'ASC' : 'DESC';
    
    // Preparar args da query
    $args = [
        'posts_per_page' => $numero,
        'post_type'      => $post_type,
        'orderby'        => 'date',
        'order'          => $ordenar,
    ];
    
    if (!empty($categoria)) {
        $args['tax_query'] = [
            [
                'taxonomy' => 'category',
                'field'    => 'slug',
                'terms'    => $categoria,
            ]
        ];
    }
    
    // Query
    $query = new WP_Query($args);
    
    if (!$query->have_posts()) {
        return '<p>Nenhum post encontrado.</p>';
    }
    
    $html = '<ul class="posts-recentes">';
    
    while ($query->have_posts()) {
        $query->the_post();
        
        $html .= sprintf(
            '<li><a href="%s">%s</a> - <span class="data">%s</span></li>',
            esc_url(get_permalink()),
            esc_html(get_the_title()),
            esc_html(get_the_date('d/m/Y'))
        );
    }
    
    $html .= '</ul>';
    
    wp_reset_postdata();
    
    return $html;
}
```

---

<a id="shortcodes-avancados"></a>
## Shortcodes Avançados

### 6.3.1 Shortcodes com Processamento de Formulários

```php
/**
 * Shortcode para formulário de contato customizado
 */
public function shortcode_form_contato($atts, $content = '') {
    $atts = shortcode_atts([
        'email' => get_option('admin_email'),
        'assunto' => 'Novo contato do site',
    ], $atts, 'form_contato');
    
    // Lidar com submissão
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && 
        isset($_POST['form_contato_nonce']) &&
        wp_verify_nonce($_POST['form_contato_nonce'], 'form_contato_action')) {
        
        $nome = sanitize_text_field($_POST['nome'] ?? '');
        $email = sanitize_email($_POST['email'] ?? '');
        $mensagem = wp_kses_post($_POST['mensagem'] ?? '');
        
        if (!empty($nome) && is_email($email) && !empty($mensagem)) {
            // Enviar email
            wp_mail(
                sanitize_email($atts['email']),
                sanitize_text_field($atts['assunto']),
                wp_kses_post($mensagem),
                ['Reply-To: ' . $email]
            );
            
            echo '<div class="alert alert-success">Mensagem enviada com sucesso!</div>';
        } else {
            echo '<div class="alert alert-error">Por favor, preencha todos os campos.</div>';
        }
    }
    
    // Form HTML
    ob_start();
    ?>
    <form method="POST" class="form-contato">
        <input type="hidden" name="form_contato_nonce" 
               value="<?php echo wp_create_nonce('form_contato_action'); ?>" />
        
        <p>
            <label for="nome">Nome:</label>
            <input type="text" name="nome" id="nome" required />
        </p>
        
        <p>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required />
        </p>
        
        <p>
            <label for="mensagem">Mensagem:</label>
            <textarea name="mensagem" id="mensagem" rows="5" required></textarea>
        </p>
        
        <p>
            <button type="submit" class="btn btn-primary">Enviar</button>
        </p>
    </form>
    <?php
    
    return ob_get_clean();
}
```

### 6.3.2 Shortcodes Aninhados (Nested)

**Uso:** 
```
[tabs]
  [tab titulo="Tab 1"]Conteúdo 1[/tab]
  [tab titulo="Tab 2"]Conteúdo 2[/tab]
[/tabs]
```

```php
/**
 * Shortcode de container para tabs
 */
public function shortcode_tabs($atts, $content = '') {
    // Contar tabs
    $tab_count = substr_count($content, '[tab ');
    
    if ($tab_count === 0) {
        return '<!-- Nenhuma tab encontrada -->';
    }
    
    // Gerar IDs únicos
    $tabs_id = 'tabs-' . uniqid();
    
    // Processar conteúdo (shortcodes internos)
    $content = do_shortcode($content);
    
    // HTML
    $html = '<div class="tabs-container" id="' . $tabs_id . '">';
    $html .= '<div class="tabs-nav">';
    
    // Botões das abas (via JavaScript)
    $html .= '</div>';
    
    $html .= '<div class="tabs-content">';
    $html .= $content;
    $html .= '</div>';
    
    $html .= '</div>';
    
    return $html;
}

/**
 * Shortcode individual de tab
 */
public function shortcode_tab($atts, $content = '') {
    $atts = shortcode_atts([
        'titulo' => 'Tab',
    ], $atts, 'tab');
    
    $titulo = sanitize_text_field($atts['titulo']);
    $content = wp_kses_post($content);
    
    return sprintf(
        '<div class="tab" data-titulo="%s">%s</div>',
        esc_attr($titulo),
        $content
    );
}
```

---

<a id="widgets-api-classica"></a>
## Widgets API Clássica

### O que são Widgets?

**Widgets** são blocos de conteúdo que podem ser adicionados às áreas de widget (sidebars) do tema WordPress.

**Características:**
- Arrastáveis e configuráveis visualmente no admin
- Herdam da classe `WP_Widget`
- Suportam múltiplas instâncias
- Cada instância pode ter configurações diferentes
- Integrados no customizador do WordPress

---

<a id="criar-widgets-customizados"></a>
## Criar Widgets Customizados

### 6.5.1 Widget Básico - Posts Recentes

```php
class Meu_Plugin_Recent_Posts_Widget extends WP_Widget {
    
    /**
     * Construtor do Widget
     */
    public function __construct() {
        $widget_ops = [
            'classname'   => 'meu_plugin_recent_posts',
            'description' => 'Exibe posts recentes com thumbnail',
            'customize_selective_refresh' => true,
        ];
        
        parent::__construct(
            'meu_plugin_recent_posts',
            'Meu Plugin - Posts Recentes',
            $widget_ops
        );
    }
    
    /**
     * Front-end do widget (o que é exibido no site)
     */
    public function widget($args, $instance) {
        // Before widget (definido pelo tema)
        echo $args['before_widget'];
        
        // Título do widget
        if (!empty($instance['title'])) {
            echo $args['before_title'] . 
                 apply_filters('widget_title', $instance['title']) . 
                 $args['after_title'];
        }
        
        // Parâmetros
        $number = !empty($instance['number']) ? absint($instance['number']) : 5;
        $show_thumbnail = isset($instance['show_thumbnail']) ? 
            (bool) $instance['show_thumbnail'] : true;
        $show_date = isset($instance['show_date']) ? 
            (bool) $instance['show_date'] : true;
        $show_excerpt = isset($instance['show_excerpt']) ? 
            (bool) $instance['show_excerpt'] : false;
        $post_type = !empty($instance['post_type']) ? 
            sanitize_text_field($instance['post_type']) : 'post';
        
        // Query posts
        $args_query = [
            'posts_per_page' => $number,
            'post_type'      => $post_type,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'post_status'    => 'publish',
        ];
        
        $query = new WP_Query($args_query);
        
        if ($query->have_posts()) {
            echo '<ul class="recent-posts-list">';
            
            while ($query->have_posts()) {
                $query->the_post();
                
                echo '<li>';
                
                // Thumbnail
                if ($show_thumbnail && has_post_thumbnail()) {
                    echo '<a href="' . esc_url(get_permalink()) . '">';
                    the_post_thumbnail('thumbnail', 
                        ['class' => 'post-thumbnail']);
                    echo '</a>';
                }
                
                // Título e link
                echo '<a href="' . esc_url(get_permalink()) . '">' . 
                     esc_html(get_the_title()) . 
                     '</a>';
                
                // Data
                if ($show_date) {
                    echo '<span class="post-date">' . 
                         esc_html(get_the_date('d/m/Y')) . 
                         '</span>';
                }
                
                // Excerpt
                if ($show_excerpt) {
                    echo '<p class="post-excerpt">' . 
                         esc_html(wp_trim_words(get_the_excerpt(), 20)) . 
                         '</p>';
                }
                
                echo '</li>';
            }
            
            echo '</ul>';
        } else {
            echo '<p>Nenhum post encontrado.</p>';
        }
        
        wp_reset_postdata();
        
        // After widget
        echo $args['after_widget'];
    }
    
    /**
     * Backend do widget (form de edição)
     */
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $number = !empty($instance['number']) ? absint($instance['number']) : 5;
        $show_thumbnail = isset($instance['show_thumbnail']) ? 
            (bool) $instance['show_thumbnail'] : true;
        $show_date = isset($instance['show_date']) ? 
            (bool) $instance['show_date'] : true;
        $show_excerpt = isset($instance['show_excerpt']) ? 
            (bool) $instance['show_excerpt'] : false;
        $post_type = !empty($instance['post_type']) ? 
            $instance['post_type'] : 'post';
        
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">
                Título:
            </label>
            <input 
                class="widefat" 
                id="<?php echo $this->get_field_id('title'); ?>"
                name="<?php echo $this->get_field_name('title'); ?>"
                type="text" 
                value="<?php echo esc_attr($title); ?>" />
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('number'); ?>">
                Número de posts:
            </label>
            <input 
                class="tiny-text" 
                id="<?php echo $this->get_field_id('number'); ?>"
                name="<?php echo $this->get_field_name('number'); ?>"
                type="number" 
                min="1" 
                max="20"
                value="<?php echo absint($number); ?>" />
        </p>
        
        <p>
            <input 
                type="checkbox" 
                id="<?php echo $this->get_field_id('show_thumbnail'); ?>"
                name="<?php echo $this->get_field_name('show_thumbnail'); ?>"
                <?php checked($show_thumbnail); ?> />
            <label for="<?php echo $this->get_field_id('show_thumbnail'); ?>">
                Mostrar thumbnail
            </label>
        </p>
        
        <p>
            <input 
                type="checkbox" 
                id="<?php echo $this->get_field_id('show_date'); ?>"
                name="<?php echo $this->get_field_name('show_date'); ?>"
                <?php checked($show_date); ?> />
            <label for="<?php echo $this->get_field_id('show_date'); ?>">
                Mostrar data
            </label>
        </p>
        
        <p>
            <input 
                type="checkbox" 
                id="<?php echo $this->get_field_id('show_excerpt'); ?>"
                name="<?php echo $this->get_field_name('show_excerpt'); ?>"
                <?php checked($show_excerpt); ?> />
            <label for="<?php echo $this->get_field_id('show_excerpt'); ?>">
                Mostrar excerpt
            </label>
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('post_type'); ?>">
                Tipo de post:
            </label>
            <select 
                class="widefat" 
                id="<?php echo $this->get_field_id('post_type'); ?>"
                name="<?php echo $this->get_field_name('post_type'); ?>">
                <option value="post" <?php selected($post_type, 'post'); ?>>
                    Posts
                </option>
                <option value="page" <?php selected($post_type, 'page'); ?>>
                    Páginas
                </option>
            </select>
        </p>
        <?php
    }
    
    /**
     * Processar atualização do widget
     */
    public function update($new_instance, $old_instance) {
        $instance = [];
        $instance['title'] = sanitize_text_field($new_instance['title'] ?? '');
        $instance['number'] = absint($new_instance['number'] ?? 5);
        $instance['show_thumbnail'] = isset($new_instance['show_thumbnail']);
        $instance['show_date'] = isset($new_instance['show_date']);
        $instance['show_excerpt'] = isset($new_instance['show_excerpt']);
        $instance['post_type'] = sanitize_text_field($new_instance['post_type'] ?? 'post');
        
        return $instance;
    }
}

// Registrar widget
add_action('widgets_init', function() {
    register_widget('Meu_Plugin_Recent_Posts_Widget');
});
```

### 6.5.2 Widget com Media Picker

```php
class Meu_Plugin_Banner_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'meu_plugin_banner',
            'Meu Plugin - Banner',
            ['description' => 'Widget de banner com imagem e link']
        );
    }
    
    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        if (!empty($instance['title'])) {
            echo $args['before_title'] . $instance['title'] . $args['after_title'];
        }
        
        if (!empty($instance['image_id'])) {
            $image = wp_get_attachment_image($instance['image_id'], 'full');
            
            if (!empty($instance['link'])) {
                echo '<a href="' . esc_url($instance['link']) . '">';
                echo $image;
                echo '</a>';
            } else {
                echo $image;
            }
        }
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $image_id = !empty($instance['image_id']) ? $instance['image_id'] : '';
        $link = !empty($instance['link']) ? $instance['link'] : '';
        
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">
                Título:
            </label>
            <input 
                class="widefat" 
                id="<?php echo $this->get_field_id('title'); ?>"
                name="<?php echo $this->get_field_name('title'); ?>"
                type="text" 
                value="<?php echo esc_attr($title); ?>" />
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('image_id'); ?>">
                Imagem:
            </label>
            <div id="image-container-<?php echo $this->id; ?>">
                <?php if (!empty($image_id)) {
                    echo wp_get_attachment_image($image_id, 'thumbnail');
                } ?>
            </div>
            <button type="button" class="button media-picker"
                    data-widget-id="<?php echo $this->id; ?>"
                    data-field-name="<?php echo $this->get_field_name('image_id'); ?>">
                Escolher Imagem
            </button>
            <input type="hidden" 
                   id="<?php echo $this->get_field_id('image_id'); ?>"
                   name="<?php echo $this->get_field_name('image_id'); ?>"
                   value="<?php echo esc_attr($image_id); ?>" />
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('link'); ?>">
                Link:
            </label>
            <input 
                class="widefat" 
                id="<?php echo $this->get_field_id('link'); ?>"
                name="<?php echo $this->get_field_name('link'); ?>"
                type="url" 
                value="<?php echo esc_url($link); ?>" />
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        return [
            'title'    => sanitize_text_field($new_instance['title'] ?? ''),
            'image_id' => absint($new_instance['image_id'] ?? 0),
            'link'     => esc_url($new_instance['link'] ?? ''),
        ];
    }
}

add_action('widgets_init', function() {
    register_widget('Meu_Plugin_Banner_Widget');
});
```

---

<a id="fundamentos-de-gutenberg-blocks"></a>
## Fundamentos de Gutenberg Blocks

### O que são Gutenberg Blocks?

**Gutenberg Blocks** são a unidade básica de conteúdo no editor visual do WordPress (desde versão 5.0).

**Características:**
- Criados com React e JavaScript (mas podem ser dinâmicos em PHP)
- Preview em tempo real no editor
- Mais poderosos que shortcodes
- Suportam múltiplas configurações na sidebar
- Integrados no customizador

**Tipos de Blocos:**
- **Static Blocks:** Conteúdo fixo salvo no banco
- **Dynamic Blocks:** Renderizado via PHP no front-end
- **InnerBlocks:** Blocos que contêm outros blocos

---

<a id="criar-custom-blocks"></a>
## Criar Custom Blocks

### 6.7.1 Estrutura Básica de um Gutenberg Block

```php
class Meu_Plugin_Gutenberg_Blocks {
    
    public function __construct() {
        add_action('init', [$this, 'register_blocks']);
        add_action('enqueue_block_editor_assets', [$this, 'enqueue_block_editor_assets']);
        add_action('enqueue_block_assets', [$this, 'enqueue_block_assets']);
    }
    
    /**
     * Registrar blocos
     */
    public function register_blocks() {
        // Verificar se Gutenberg está ativo
        if (!function_exists('register_block_type')) {
            return;
        }
        
        // Registrar bloco estático
        register_block_type('meu-plugin/alert', [
            'editor_script' => 'meu-plugin-blocks-editor',
            'editor_style'  => 'meu-plugin-blocks-editor-style',
            'style'         => 'meu-plugin-blocks-style',
        ]);
        
        // Registrar bloco dinâmico
        register_block_type('meu-plugin/latest-posts', [
            'editor_script'   => 'meu-plugin-blocks-editor',
            'editor_style'    => 'meu-plugin-blocks-editor-style',
            'style'           => 'meu-plugin-blocks-style',
            'render_callback' => [$this, 'render_latest_posts_block'],
        ]);
    }
    
    /**
     * Enfileirar scripts do editor
     */
    public function enqueue_block_editor_assets() {
        // Editor script
        wp_enqueue_script(
            'meu-plugin-blocks-editor',
            plugin_dir_url(__FILE__) . 'js/blocks-editor.js',
            ['wp-blocks', 'wp-element', 'wp-editor', 'wp-components']
        );
        
        // Editor style
        wp_enqueue_style(
            'meu-plugin-blocks-editor-style',
            plugin_dir_url(__FILE__) . 'css/blocks-editor.css'
        );
    }
    
    /**
     * Enfileirar styles do front-end
     */
    public function enqueue_block_assets() {
        wp_enqueue_style(
            'meu-plugin-blocks-style',
            plugin_dir_url(__FILE__) . 'css/blocks.css'
        );
    }
    
    /**
     * Render callback para bloco dinâmico
     */
    public function render_latest_posts_block($attributes) {
        $posts_per_page = intval($attributes['postsPerPage'] ?? 5);
        $columns = intval($attributes['columns'] ?? 3);
        
        $args = [
            'posts_per_page' => $posts_per_page,
            'post_type'      => 'post',
            'orderby'        => 'date',
            'order'          => 'DESC',
        ];
        
        $query = new WP_Query($args);
        
        if (!$query->have_posts()) {
            return '<p>Nenhum post encontrado.</p>';
        }
        
        $html = '<div class="wp-block-meu-plugin-latest-posts" 
                     style="display: grid; grid-template-columns: repeat(' . 
                     absint($columns) . ', 1fr); gap: 20px;">';
        
        while ($query->have_posts()) {
            $query->the_post();
            
            $html .= '<article class="post-card">';
            $html .= '<h3><a href="' . esc_url(get_permalink()) . '">' . 
                     esc_html(get_the_title()) . '</a></h3>';
            $html .= '<p class="date">' . esc_html(get_the_date()) . '</p>';
            $html .= '</article>';
        }
        
        $html .= '</div>';
        
        wp_reset_postdata();
        
        return $html;
    }
}

new Meu_Plugin_Gutenberg_Blocks();
```

### 6.7.2 JavaScript para Blocos (blocks-editor.js)

```javascript
const { registerBlockType } = wp.blocks;
const { RichText, InspectorControls, ColorPalette } = wp.editor;
const { PanelBody, RangeControl, SelectControl } = wp.components;

// Bloco de Alerta
registerBlockType('meu-plugin/alert', {
    title: 'Alerta',
    icon: 'info',
    category: 'common',
    attributes: {
        message: {
            type: 'string',
            default: 'Conteúdo do alerta'
        },
        type: {
            type: 'string',
            default: 'info'
        }
    },
    edit: ({ attributes, setAttributes }) => {
        const { message, type } = attributes;
        
        return [
            <InspectorControls key="inspector">
                <PanelBody title="Configurações do Alerta">
                    <SelectControl
                        label="Tipo"
                        value={type}
                        options={[
                            { label: 'Info', value: 'info' },
                            { label: 'Sucesso', value: 'success' },
                            { label: 'Aviso', value: 'warning' },
                            { label: 'Erro', value: 'error' }
                        ]}
                        onChange={(value) => setAttributes({ type: value })}
                    />
                </PanelBody>
            </InspectorControls>,
            <div key="content" className={`alert alert-${type}`}>
                <RichText
                    value={message}
                    onChange={(value) => setAttributes({ message: value })}
                    placeholder="Digite o conteúdo do alerta"
                />
            </div>
        ];
    },
    save: ({ attributes }) => {
        const { message, type } = attributes;
        
        return (
            <div className={`alert alert-${type}`}>
                <RichText.Content value={message} />
            </div>
        );
    }
});

// Bloco de Posts Recentes
registerBlockType('meu-plugin/latest-posts', {
    title: 'Posts Recentes',
    icon: 'list-view',
    category: 'widgets',
    attributes: {
        postsPerPage: {
            type: 'number',
            default: 5
        },
        columns: {
            type: 'number',
            default: 3
        }
    },
    edit: ({ attributes, setAttributes }) => {
        const { postsPerPage, columns } = attributes;
        
        return (
            <InspectorControls>
                <PanelBody title="Configurações">
                    <RangeControl
                        label="Número de Posts"
                        value={postsPerPage}
                        onChange={(value) => setAttributes({ postsPerPage: value })}
                        min={1}
                        max={20}
                    />
                    <RangeControl
                        label="Colunas"
                        value={columns}
                        onChange={(value) => setAttributes({ columns: value })}
                        min={1}
                        max={4}
                    />
                </PanelBody>
            </InspectorControls>
        );
    },
    save: () => {
        return null; // Dynamic block - renderizado via PHP
    }
});
```

---

<a id="dynamic-blocks-e-block-patterns"></a>
## Dynamic Blocks e Block Patterns

### 6.8.1 Block Patterns

```php
class Meu_Plugin_Block_Patterns {
    
    public function __construct() {
        add_action('init', [$this, 'register_patterns']);
    }
    
    public function register_patterns() {
        if (!function_exists('register_block_pattern')) {
            return;
        }
        
        // Pattern 1: Hero Section
        register_block_pattern('meu-plugin/hero-section', [
            'title'       => __('Hero Section', 'meu-plugin'),
            'description' => __('Seção hero com título e botão', 'meu-plugin'),
            'categories'  => ['buttons', 'text'],
            'content'     => '<!-- wp:heading {"textAlign":"center","level":1} -->
<h1 class="has-text-align-center">Bem-vindo ao nosso site</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"textAlign":"center"} -->
<p class="has-text-align-center">Descrição curta do seu negócio</p>
<!-- /wp:paragraph -->

<!-- wp:button {"align":"center","backgroundColor":"primary","textColor":"white"} -->
<div class="wp-block-button aligncenter"><a class="wp-block-button__link has-primary-background-color has-white-color has-background">Saiba Mais</a></div>
<!-- /wp:button -->',
        ]);
        
        // Pattern 2: Portfolio Showcase
        register_block_pattern('meu-plugin/portfolio-showcase', [
            'title'       => __('Portfolio Showcase', 'meu-plugin'),
            'description' => __('Vitrine de portfólio com título', 'meu-plugin'),
            'categories'  => ['gallery', 'featured'],
            'content'     => '<!-- wp:heading {"textAlign":"center"} -->
<h2 class="has-text-align-center">Nossos Projetos</h2>
<!-- /wp:heading -->

<!-- wp:meu-plugin/portfolio-grid {"columns":3,"numberOfItems":6} /-->',
        ]);
        
        // Pattern 3: Testimonials
        register_block_pattern('meu-plugin/testimonials', [
            'title'       => __('Testimonials Section', 'meu-plugin'),
            'description' => __('Seção de depoimentos', 'meu-plugin'),
            'categories'  => ['text', 'media'],
            'content'     => '<!-- wp:heading {"textAlign":"center"} -->
<h2 class="has-text-align-center">O que dizem sobre nós</h2>
<!-- /wp:heading -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph -->
<p><em>"Melhor serviço que já contratei!"</em></p>
<!-- /wp:paragraph -->
<p><strong>- Cliente Satisfeito</strong></p>
</div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph -->
<p><em>"Qualidade excepcional!"</em></p>
<!-- /wp:paragraph -->
<p><strong>- Outro Cliente</strong></p>
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->',
        ]);
    }
}

new Meu_Plugin_Block_Patterns();
```

---

<a id="boas-praticas"></a>
## Boas Práticas

### ✅ Segurança

```php
// SEMPRE sanitizar entrada
$input = sanitize_text_field($_POST['input']);

// SEMPRE escapar saída
echo esc_html($data);
echo esc_attr($data);
echo esc_url($data);
echo esc_js($data);
echo wp_kses_post($data); // HTML permitido

// SEMPRE verificar nonce em formulários
wp_verify_nonce($_POST['nonce'], 'action_name');

// SEMPRE verificar capabilities
if (!current_user_can('manage_options')) {
    wp_die('Permissão negada');
}
```

### ✅ Performance

```php
// Usar transients para dados que mudam
if (false === ($data = get_transient('meu_plugin_cache_key'))) {
    $data = expensive_operation();
    set_transient('meu_plugin_cache_key', $data, HOUR_IN_SECONDS);
}

// Enfileirar assets corretamente
wp_enqueue_style('meu-plugin', plugin_dir_url(__FILE__) . 'css/style.css');
wp_enqueue_script('meu-plugin', plugin_dir_url(__FILE__) . 'js/script.js', 
                  ['jquery'], '1.0', true);

// Usar wp_localize_script para dados no JS
wp_localize_script('meu-plugin', 'meuPluginData', [
    'ajaxUrl' => admin_url('admin-ajax.php'),
    'nonce'   => wp_create_nonce('meu_plugin_nonce'),
]);
```

### ✅ Compatibilidade

```php
// Verificar existência de funções
if (function_exists('register_block_type')) {
    // Gutenberg está disponível
}

// Verificar versão do WordPress
if (version_compare(get_bloginfo('version'), '5.0', '>=')) {
    // WordPress 5.0 ou superior
}

// Verificar se plugin está ativo
if (is_plugin_active('woocommerce/woocommerce.php')) {
    // WooCommerce está ativo
}
```

---

<a id="resumo-da-fase-6"></a>
## 📚 Resumo da Fase 6

Você aprendeu:

✅ **Shortcodes**
- Criar shortcodes simples, com atributos, com conteúdo interno
- Processamento de formulários em shortcodes
- Shortcodes aninhados e reutilizáveis

✅ **Widgets**
- Estrutura completa `WP_Widget`
- Formulários de configuração
- Renderização no front-end
- Media picker em widgets

✅ **Gutenberg Blocks**
- Registrar blocos estáticos e dinâmicos
- React/JSX para criação de blocos
- Block Patterns para reutilização
- Integração com PHP para renderização dinâmica

✅ **Boas Práticas**
- Segurança (sanitização e escapagem)
- Performance (transients e cache)
- Compatibilidade (verificações)

---

**Versão:** 1.0  
**Última atualização:** Janeiro 2026  
**Autor:** André | Especialista em PHP e WordPress
