# 🚀 FASE 8: Performance, Cache e Otimização

**Versão:** 1.0  
**Data:** Janeiro 2026  
**Nível:** Especialista em PHP  
**Objetivo:** Dominar estratégias de cache, otimização e performance no WordPress

---

**Navegação:** [📚 Índice](000-WordPress-Topicos-Index.md) | [← Fase 7](007-WordPress-Fase-7-WP-CLI-Fundamentals.md) | [Fase 9 →](009-WordPress-Fase-9-WP-CLI-Ferramentas.md)

---

## 📑 Índice

1. [Fundamentos de Performance](#fundamentos-de-performance)
2. [Object Cache](#object-cache)
3. [Transients API](#transients-api)
4. [Fragment Caching](#fragment-caching)
5. [Query Optimization](#query-optimization)
6. [Page Caching](#page-caching)
7. [CDN Integration](#cdn-integration)
8. [Asset Optimization](#asset-optimization)
9. [Database Optimization](#database-optimization)
10. [Performance Monitoring](#performance-monitoring)
11. [Boas Práticas](#boas-práticas)

---

## 🔍 Fundamentos de Performance no WordPress

### Principais Gargalos de Performance

1. **Queries ao banco de dados não otimizadas** - N+1 queries, sem cache
2. **Falta de cache** - Object cache, transients, page cache
3. **Muitas requisições HTTP** - Assets não concatenados/minificados
4. **Arquivos não otimizados** - CSS/JS grandes, imagens pesadas
5. **Plugins mal desenvolvidos** - Código ineficiente, muitos plugins
6. **Temas pesados** - Muita funcionalidade desnecessária
7. **Configuração ruim do servidor** - PHP opcache, GZIP desativado

### Métricas Importantes (Core Web Vitals)

- **TTFB (Time to First Byte)** - Tempo até primeira resposta do servidor
- **FCP (First Contentful Paint)** - Quando primeiro conteúdo aparece
- **LCP (Largest Contentful Paint)** - Quando conteúdo principal carrega
- **TTI (Time to Interactive)** - Quando página fica interativa
- **TBT (Total Blocking Time)** - Tempo que JavaScript bloqueia thread
- **CLS (Cumulative Layout Shift)** - Quanto a página se move durante load

---

## 💾 Object Cache - Cache de Objetos

### Exemplo 1: Cache Simples de Query

```php
/**
 * Sistema de Cache com WordPress Object Cache
 */
class Meu_Plugin_Object_Cache {
    
    /**
     * Exemplo 1: Cache simples de query
     */
    public function get_featured_posts() {
        // Criar chave única para o cache
        $cache_key = 'meu_plugin_featured_posts';
        $cache_group = 'meu_plugin';
        
        // Tentar obter do cache
        $posts = wp_cache_get($cache_key, $cache_group);
        
        // Se não estiver em cache, buscar do banco
        if (false === $posts) {
            $args = [
                'post_type'      => 'post',
                'posts_per_page' => 10,
                'meta_query'     => [
                    [
                        'key'   => '_featured',
                        'value' => '1',
                    ],
                ],
            ];
            
            $query = new WP_Query($args);
            $posts = $query->posts;
            
            // Salvar no cache por 1 hora (3600 segundos)
            wp_cache_set($cache_key, $posts, $cache_group, 3600);
        }
        
        return $posts;
    }
    
    /**
     * Exemplo 2: Cache com invalidação
     */
    public function get_post_views($post_id) {
        $cache_key = 'post_views_' . $post_id;
        $cache_group = 'meu_plugin_views';
        
        $views = wp_cache_get($cache_key, $cache_group);
        
        if (false === $views) {
            $views = get_post_meta($post_id, '_post_views', true);
            $views = $views ? absint($views) : 0;
            
            wp_cache_set($cache_key, $views, $cache_group, HOUR_IN_SECONDS);
        }
        
        return $views;
    }
    
    /**
     * Incrementar views
     */
    public function increment_post_views($post_id) {
        $cache_key = 'post_views_' . $post_id;
        $cache_group = 'meu_plugin_views';
        
        // Incrementar na meta
        $views = get_post_meta($post_id, '_post_views', true);
        $views = $views ? absint($views) + 1 : 1;
        update_post_meta($post_id, '_post_views', $views);
        
        // Atualizar cache
        wp_cache_set($cache_key, $views, $cache_group, HOUR_IN_SECONDS);
    }
    
    /**
     * Invalidar cache ao salvar post
     */
    public function __construct() {
        add_action('save_post', [$this, 'invalidate_cache']);
    }
    
    public function invalidate_cache($post_id) {
        // Invalidar cache de featured posts
        wp_cache_delete('meu_plugin_featured_posts', 'meu_plugin');
        
        // Invalidar cache de views desse post
        $cache_key = 'post_views_' . $post_id;
        wp_cache_delete($cache_key, 'meu_plugin_views');
    }
}

new Meu_Plugin_Object_Cache();
```

### Exemplo 3: Cache com Grupos (Meta Cache)

```php
/**
 * Cache com grupos - ideal para dados relacionados
 */
class Meu_Plugin_Meta_Cache {
    
    /**
     * Cache de dados do autor
     */
    public function get_author_stats($author_id) {
        $cache_group = 'author_stats';
        $cache_key = 'author_' . $author_id;
        
        $stats = wp_cache_get($cache_key, $cache_group);
        
        if (false === $stats) {
            $stats = [
                'post_count'     => count_user_posts($author_id),
                'total_views'    => $this->get_author_total_views($author_id),
                'recent_posts'   => $this->get_recent_author_posts($author_id),
                'avg_views'      => $this->calculate_avg_views($author_id),
            ];
            
            wp_cache_set($cache_key, $stats, $cache_group, DAY_IN_SECONDS);
        }
        
        return $stats;
    }
    
    /**
     * Invalidar todas as estatísticas do autor
     */
    public function invalidate_author_cache($author_id) {
        wp_cache_delete('author_' . $author_id, 'author_stats');
    }
    
    /**
     * Flush de todos os caches do plugin
     */
    public function flush_all_caches() {
        // Invalidar grupos específicos
        wp_cache_delete('meu_plugin_featured_posts', 'meu_plugin');
        
        // Ou flush completo (menos eficiente)
        // wp_cache_flush();
    }
    
    // Métodos auxiliares (placeholders)
    private function get_author_total_views($author_id) { return 0; }
    private function get_recent_author_posts($author_id) { return []; }
    private function calculate_avg_views($author_id) { return 0; }
}

new Meu_Plugin_Meta_Cache();
```

### Cache Backends Disponíveis

```php
/**
 * WordPress Object Cache Backends
 */

// 1. APCu (em processo, rápido mas não persistente entre requisições)
// Ideal para: Single server

// 2. Memcached (separado, distribuído, rápido)
// Ideal para: Multi-server, alta concorrência
// Plugins: https://wordpress.org/plugins/memcached/

// 3. Redis (separado, distribuído, mais features)
// Ideal para: Multi-server, operações complexas, sessões
// Plugins: https://wordpress.org/plugins/wp-redis/

// Detectar qual backend está sendo usado:
echo 'Cache backend: ' . wp_cache_get_last_changed('meu_plugin');

// Verificar se persistent cache está ativo:
if (wp_using_persistent_cache()) {
    echo 'Persistent cache ativo!';
} else {
    echo 'Usando in-memory cache apenas.';
}
```

---

## ⏱️ Transients API - Cache em Banco de Dados

### Exemplo 1: Transient Simples

```php
/**
 * Uso básico de Transients
 */
class Meu_Plugin_Transients {
    
    /**
     * Buscar dados com cache via Transient
     */
    public function get_widget_data() {
        $transient_key = 'meu_plugin_widget_data';
        
        // Tentar obter do transient
        $data = get_transient($transient_key);
        
        if (false === $data) {
            // Buscar dados do banco/API
            $data = [
                'title' => 'Widget Title',
                'items' => $this->fetch_widget_items(),
                'count' => 0,
            ];
            
            // Salvar transient por 12 horas
            set_transient($transient_key, $data, 12 * HOUR_IN_SECONDS);
        }
        
        return $data;
    }
}
```

### Exemplo 2: Transients com API Externa

```php
/**
 * Cache de dados de API externa
 */
class Meu_Plugin_API_Cache {
    
    /**
     * Buscar dados com fallback
     */
    public function get_feed_data($feed_url) {
        $transient_key = 'feed_' . md5($feed_url);
        
        // Tentar transient
        $data = get_transient($transient_key);
        
        if (false === $data) {
            // Buscar da API
            $response = wp_remote_get($feed_url, [
                'timeout' => 10,
            ]);
            
            if (is_wp_error($response)) {
                // Se falhar, usar cache vencido se disponível
                $data = get_option('_transient_' . $transient_key);
                if (false === $data) {
                    return new WP_Error('api_failed', 'Feed indisponível');
                }
            } else {
                $data = wp_remote_retrieve_body($response);
                
                // Cache por 6 horas
                set_transient($transient_key, $data, 6 * HOUR_IN_SECONDS);
            }
        }
        
        return $data;
    }
    
    /**
     * Limpar transient manualmente
     */
    public function clear_feed_cache($feed_url) {
        $transient_key = 'feed_' . md5($feed_url);
        delete_transient($transient_key);
    }
}
```

### Exemplo 3: Transients com Múltiplos Dados

```php
/**
 * Cache complexo de múltiplos dados
 */
class Meu_Plugin_Complex_Cache {
    
    /**
     * Cache de estatísticas completas
     */
    public function get_site_stats() {
        $transient_key = 'meu_plugin_site_stats';
        
        $stats = get_transient($transient_key);
        
        if (false === $stats) {
            // Coletar múltiplos dados
            $stats = [
                'total_posts'     => wp_count_posts('post')->publish,
                'total_users'     => count_users()['total_users'],
                'total_comments'  => wp_count_comments(),
                'featured_posts'  => $this->get_featured_posts(),
                'recent_comments' => $this->get_recent_comments(),
                'popular_posts'   => $this->get_popular_posts(),
                'generated_at'    => current_time('mysql'),
            ];
            
            // Cache por 24 horas
            set_transient($transient_key, $stats, DAY_IN_SECONDS);
        }
        
        return $stats;
    }
    
    /**
     * Limpar transients ao fazer alterações
     */
    public function __construct() {
        // Limpar ao salvar post
        add_action('publish_post', [$this, 'clear_stats_cache']);
        add_action('delete_post', [$this, 'clear_stats_cache']);
        
        // Limpar ao criar usuário
        add_action('user_register', [$this, 'clear_stats_cache']);
        
        // Limpar ao criar comentário
        add_action('comment_post', [$this, 'clear_stats_cache']);
    }
    
    public function clear_stats_cache() {
        delete_transient('meu_plugin_site_stats');
    }
    
    // Métodos auxiliares (placeholders)
    private function get_featured_posts() { return []; }
    private function get_recent_comments() { return []; }
    private function get_popular_posts() { return []; }
}

new Meu_Plugin_Complex_Cache();
```

### Limpar Transients Expirados

```php
/**
 * Função para limpar transients expirados
 */
function meu_plugin_cleanup_expired_transients() {
    global $wpdb;
    
    $time = time();
    
    // Deletar transients expirados
    $wpdb->query(
        $wpdb->prepare(
            "DELETE a, b FROM {$wpdb->options} a, {$wpdb->options} b
            WHERE a.option_name LIKE %s
            AND a.option_name NOT LIKE %s
            AND b.option_name = CONCAT('_transient_timeout_', SUBSTRING(a.option_name, 12))
            AND b.meta_value < %d",
            $wpdb->esc_like('_transient_') . '%',
            $wpdb->esc_like('_transient_timeout_') . '%',
            $time
        )
    );
    
    // Para multisite
    if (is_multisite()) {
        $wpdb->query(
            $wpdb->prepare(
                "DELETE a, b FROM {$wpdb->sitemeta} a, {$wpdb->sitemeta} b
                WHERE a.meta_key LIKE %s
                AND a.meta_key NOT LIKE %s
                AND b.meta_key = CONCAT('_site_transient_timeout_', SUBSTRING(a.meta_key, 17))
                AND b.meta_value < %d",
                $wpdb->esc_like('_site_transient_') . '%',
                $wpdb->esc_like('_site_transient_timeout_') . '%',
                $time
            )
        );
    }
}

// Agendar limpeza diária
if (!wp_next_scheduled('meu_plugin_cleanup_transients')) {
    wp_schedule_event(time(), 'daily', 'meu_plugin_cleanup_transients');
}

add_action('meu_plugin_cleanup_transients', 'meu_plugin_cleanup_expired_transients');
```

---

## 🎨 Fragment Caching - Cache de HTML

### Exemplo 1: Cache de Sidebar

```php
/**
 * Cache de fragmentos HTML
 */
class Meu_Plugin_Fragment_Cache {
    
    /**
     * Renderizar sidebar com cache
     */
    public function render_sidebar() {
        $cache_key = 'meu_plugin_sidebar_html';
        
        // Tentar obter do cache
        $html = wp_cache_get($cache_key);
        
        if (false === $html) {
            // Gerar HTML
            ob_start();
            ?>
            <aside class="sidebar">
                <div class="widget-area">
                    <?php
                    if (is_active_sidebar('primary')) {
                        dynamic_sidebar('primary');
                    }
                    ?>
                </div>
            </aside>
            <?php
            $html = ob_get_clean();
            
            // Cache por 6 horas
            wp_cache_set($cache_key, $html, '', 6 * HOUR_IN_SECONDS);
        }
        
        echo wp_kses_post($html);
    }
    
    /**
     * Invalidar cache ao atualizar widgets
     */
    public function __construct() {
        add_action('sidebar_admin_setup', function() {
            wp_cache_delete('meu_plugin_sidebar_html');
        });
    }
}
```

### Exemplo 2: Cache de Related Posts

```php
/**
 * Cache de posts relacionados
 */
public function get_related_posts_html($post_id) {
    $cache_key = 'related_posts_' . $post_id;
    
    $html = wp_cache_get($cache_key);
    
    if (false === $html) {
        $post = get_post($post_id);
        
        $args = [
            'post_type'      => 'post',
            'posts_per_page' => 3,
            'post__not_in'   => [$post_id],
            'orderby'        => 'rand',
        ];
        
        // Se tiver tags
        if (!empty(wp_get_post_tags($post_id))) {
            $tags = wp_get_post_tags($post_id, ['fields' => 'ids']);
            $args['tag__in'] = $tags;
        }
        
        $related = new WP_Query($args);
        
        ob_start();
        if ($related->have_posts()) {
            echo '<div class="related-posts">';
            while ($related->have_posts()) {
                $related->the_post();
                echo '<article>';
                echo '<h3>' . esc_html(get_the_title()) . '</h3>';
                echo '<p>' . wp_kses_post(get_the_excerpt()) . '</p>';
                echo '</article>';
            }
            echo '</div>';
        }
        wp_reset_postdata();
        
        $html = ob_get_clean();
        
        // Cache por 24 horas
        wp_cache_set($cache_key, $html, '', DAY_IN_SECONDS);
    }
    
    return $html;
}
```

---

## 🔍 Query Optimization - Otimização de Queries

### Evitar N+1 Queries

```php
/**
 * PROBLEMA: N+1 queries
 */
class Meu_Plugin_Query_Problems {
    
    /**
     * ❌ RUIM: N+1 queries (1 para posts + 1 para cada post meta)
     */
    public function get_featured_posts_bad() {
        $posts = get_posts([
            'meta_key' => '_featured',
            'meta_value' => '1',
        ]);
        
        foreach ($posts as $post) {
            // Cada iteração = 1 query
            $views = get_post_meta($post->ID, '_views', true);
            $rating = get_post_meta($post->ID, '_rating', true);
        }
    }
    
    /**
     * ✅ BOM: Usar update_postmeta_cache para eager loading
     */
    public function get_featured_posts_good() {
        $posts = get_posts([
            'meta_key' => '_featured',
            'meta_value' => '1',
        ]);
        
        // Carregar todas as metas de uma vez
        update_postmeta_cache(wp_list_pluck($posts, 'ID'));
        
        foreach ($posts as $post) {
            // Agora está no cache
            $views = get_post_meta($post->ID, '_views', true);
            $rating = get_post_meta($post->ID, '_rating', true);
        }
    }
}
```

### WP_Query com Caching

```php
/**
 * WP_Query otimizada
 */
class Meu_Plugin_Query_Optimization {
    
    public function get_featured_posts() {
        $cache_key = 'featured_posts_query';
        
        $posts = wp_cache_get($cache_key);
        
        if (false === $posts) {
            // Usar WP_Query
            $query = new WP_Query([
                'post_type'              => 'post',
                'posts_per_page'         => 10,
                'meta_key'               => '_featured',
                'meta_value'             => '1',
                'ignore_sticky_posts'    => true,
                'no_found_rows'          => true, // Não contar total
                'update_post_term_cache' => true, // Carregar termos
                'update_post_meta_cache' => true, // Carregar metas
            ]);
            
            $posts = $query->posts;
            
            wp_cache_set($cache_key, $posts, '', HOUR_IN_SECONDS);
        }
        
        return $posts;
    }
    
    /**
     * Não usar query_posts
     */
    public function bad_example() {
        // ❌ NÃO USE
        // query_posts() modifica global $wp_query
        // Causa problemas com pagination e outros componentes
        // query_posts(['meta_key' => '_featured']);
    }
}
```

---

## 📄 Page Caching - Cache de Página Completa

### Implementar Page Cache Manual

```php
/**
 * Sistema de Page Cache simples
 */
class Meu_Plugin_Page_Cache {
    
    private $cache_dir;
    
    public function __construct() {
        $this->cache_dir = WP_CONTENT_DIR . '/cache/pages';
        
        // Criar diretório se não existir
        if (!is_dir($this->cache_dir)) {
            wp_mkdir_p($this->cache_dir);
        }
        
        // Servir cache se disponível
        add_action('template_redirect', [$this, 'serve_cached_page']);
        
        // Armazenar página após rendering
        add_action('wp_footer', [$this, 'cache_page']);
        
        // Invalidar cache ao salvar
        add_action('save_post', [$this, 'invalidate_cache']);
    }
    
    /**
     * Tentar servir página em cache
     */
    public function serve_cached_page() {
        // Não cachear admin, logged in, ou com parâmetros
        if (is_admin() || is_user_logged_in() || !empty($_GET)) {
            return;
        }
        
        $cache_file = $this->get_cache_file();
        
        if (file_exists($cache_file)) {
            readfile($cache_file);
            die();
        }
    }
    
    /**
     * Armazenar página em cache
     */
    public function cache_page() {
        if (is_admin() || is_user_logged_in() || !empty($_GET)) {
            return;
        }
        
        $cache_file = $this->get_cache_file();
        
        // Obter conteúdo renderizado
        $html = ob_get_flush();
        
        // Salvar em arquivo
        file_put_contents($cache_file, $html);
    }
    
    /**
     * Gerar caminho do arquivo de cache
     */
    private function get_cache_file() {
        $hash = md5(home_url(add_query_arg([])));
        return $this->cache_dir . '/' . $hash . '.html';
    }
    
    /**
     * Invalidar cache
     */
    public function invalidate_cache($post_id) {
        $cache_file = $this->cache_dir . '/' . md5(home_url()) . '.html';
        
        if (file_exists($cache_file)) {
            unlink($cache_file);
        } else {
            // Limpar todo o cache
            $files = glob($this->cache_dir . '/*.html');
            foreach ($files as $file) {
                unlink($file);
            }
        }
    }
    
    /**
     * Limpar cache antigo
     */
    public function cleanup_old_cache() {
        $files = glob($this->cache_dir . '/*.html');
        $now = time();
        
        foreach ($files as $file) {
            // Deletar cache mais antigo que 1 dia
            if ($now - filemtime($file) > DAY_IN_SECONDS) {
                unlink($file);
            }
        }
    }
}

new Meu_Plugin_Page_Cache();
```

---

## 🌐 CDN Integration - Integração com CDN

### Reescrever URLs para CDN

```php
/**
 * Integração com CloudFlare ou CDN customizado
 */
class Meu_Plugin_CDN_Integration {
    
    private $cdn_url;
    private $local_url;
    
    public function __construct() {
        $this->cdn_url = get_option('meu_plugin_cdn_url', '');
        $this->local_url = home_url();
        
        if (empty($this->cdn_url)) {
            return;
        }
        
        // Reescrever URLs
        add_filter('wp_get_attachment_url', [$this, 'rewrite_url']);
        add_filter('wp_calculate_image_srcset', [$this, 'rewrite_srcset'], 10, 5);
        add_filter('style_loader_src', [$this, 'rewrite_url']);
        add_filter('script_loader_src', [$this, 'rewrite_url']);
        add_filter('the_content', [$this, 'rewrite_content_urls'], 999);
    }
    
    /**
     * Reescrever URL individual
     */
    public function rewrite_url($url) {
        // Não reescrever em admin
        if (is_admin()) {
            return $url;
        }
        
        // Não reescrever URLs externas
        if (strpos($url, $this->local_url) === false) {
            return $url;
        }
        
        // Reescrever para CDN
        return str_replace($this->local_url, $this->cdn_url, $url);
    }
    
    /**
     * Reescrever srcset (imagens responsivas)
     */
    public function rewrite_srcset($sources, $size_array, $image_src, $image_meta, $attachment_id) {
        if (empty($sources)) {
            return $sources;
        }
        
        foreach ($sources as $width => $source) {
            $sources[$width]['url'] = $this->rewrite_url($source['url']);
        }
        
        return $sources;
    }
    
    /**
     * Reescrever URLs no conteúdo
     */
    public function rewrite_content_urls($content) {
        if (is_admin() || is_feed()) {
            return $content;
        }
        
        // Reescrever URLs de imagens
        $content = str_replace(
            $this->local_url . '/wp-content/',
            $this->cdn_url . '/wp-content/',
            $content
        );
        
        return $content;
    }
}

new Meu_Plugin_CDN_Integration();
```

---

## ✂️ Asset Optimization - Otimização de Assets

### Minificação e Concatenação

```php
/**
 * Otimizar CSS e JavaScript
 */
class Meu_Plugin_Asset_Optimization {
    
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_optimized_assets']);
    }
    
    /**
     * Enqueue com defer/async
     */
    public function enqueue_optimized_assets() {
        // CSS crítico (inline)
        $critical_css = file_get_contents(plugin_dir_path(__FILE__) . 'css/critical.css');
        wp_register_style('meu-plugin-critical', false);
        wp_enqueue_style('meu-plugin-critical');
        wp_add_inline_style('meu-plugin-critical', $critical_css);
        
        // CSS não-crítico (async)
        wp_enqueue_style(
            'meu-plugin-main',
            plugin_dir_url(__FILE__) . 'css/style.min.css',
            [],
            '1.0'
        );
        wp_style_add_data('meu-plugin-main', 'media', 'print');
        
        // JavaScript com defer
        wp_enqueue_script(
            'meu-plugin-app',
            plugin_dir_url(__FILE__) . 'js/app.min.js',
            [],
            '1.0',
            true // Footer
        );
        wp_script_add_data('meu-plugin-app', 'defer', true);
    }
    
    /**
     * Remover assets desnecessários
     */
    public function cleanup_unnecessary_assets() {
        add_action('wp_enqueue_scripts', function() {
            // Remover emojis
            remove_action('wp_head', 'print_emoji_detection_script', 7);
            remove_action('wp_print_styles', 'print_emoji_styles');
            
            // Remover WP JSON link
            remove_action('wp_head', 'rest_output_link_wp_head', 10);
            
            // Remover Windows Live Writer
            remove_action('wp_head', 'wlwmanifest_link');
            
            // Remover RSD (Really Simple Discovery)
            remove_action('wp_head', 'rsd_link');
        }, 999);
    }
}

new Meu_Plugin_Asset_Optimization();
```

---

## 🗄️ Database Optimization - Otimização de Banco de Dados

### Análise e Otimização de Queries

```php
/**
 * Otimizar banco de dados
 */
class Meu_Plugin_Database_Optimization {
    
    /**
     * Limpar dados desnecessários
     */
    public function cleanup_database() {
        global $wpdb;
        
        // Deletar revisões antigas
        $wpdb->query(
            "DELETE FROM {$wpdb->posts} 
            WHERE post_type = 'revision' 
            AND post_modified < DATE_SUB(NOW(), INTERVAL 30 DAY)"
        );
        
        // Deletar spam de auto-save
        $wpdb->query(
            "DELETE FROM {$wpdb->posts} 
            WHERE post_type = 'revision' 
            AND post_name LIKE '%autosave%'"
        );
        
        // Deletar transients expirados
        $wpdb->query(
            "DELETE FROM {$wpdb->options} 
            WHERE option_name LIKE '%transient%' 
            AND option_value = ''"
        );
    }
    
    /**
     * Analisar query performance
     */
    public function analyze_query() {
        global $wpdb;
        
        // Ativar EXPLAIN
        $results = $wpdb->get_results(
            "EXPLAIN SELECT * FROM {$wpdb->posts} WHERE post_type = 'post' LIMIT 10"
        );
        
        foreach ($results as $row) {
            // Verificar key_len, rows, Extra
            // Se usar filesort ou temporary, query precisa otimização
        }
    }
    
    /**
     * Criar índices customizados
     */
    public function create_custom_indexes() {
        global $wpdb;
        
        // Índice para meta queries comuns
        $wpdb->query(
            "ALTER TABLE {$wpdb->postmeta} 
            ADD KEY featured (meta_key, meta_value(10))"
        );
        
        // Índice para autor + status
        $wpdb->query(
            "ALTER TABLE {$wpdb->posts} 
            ADD KEY author_status (post_author, post_status)"
        );
    }
}

new Meu_Plugin_Database_Optimization();
```

---

## 📊 Performance Monitoring - Monitoramento de Performance

### Sistema de Profiling

```php
/**
 * Profiling e monitoring de performance
 */
class Meu_Plugin_Profiler {
    
    private static $markers = [];
    
    /**
     * Iniciar medição
     */
    public static function start($name) {
        self::$markers[$name] = [
            'start' => microtime(true),
            'memory_start' => memory_get_usage(true),
        ];
    }
    
    /**
     * Terminar medição
     */
    public static function stop($name) {
        if (!isset(self::$markers[$name])) {
            return;
        }
        
        $marker = self::$markers[$name];
        $time = (microtime(true) - $marker['start']) * 1000; // ms
        $memory = (memory_get_usage(true) - $marker['memory_start']) / 1024 / 1024; // MB
        
        self::$markers[$name]['time'] = round($time, 2) . ' ms';
        self::$markers[$name]['memory'] = round($memory, 2) . ' MB';
    }
    
    /**
     * Exibir relatório
     */
    public static function display() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        echo '<div style="background:#f5f5f5;padding:15px;margin:10px;font-family:monospace;border:1px solid #ddd;">';
        echo '<h3>Performance Report</h3>';
        echo '<table style="width:100%;border-collapse:collapse;">';
        echo '<tr style="background:#333;color:white;">';
        echo '<th style="text-align:left;padding:8px;border-bottom:1px solid #ddd;">Marker</th>';
        echo '<th style="text-align:right;padding:8px;border-bottom:1px solid #ddd;">Time</th>';
        echo '<th style="text-align:right;padding:8px;border-bottom:1px solid #ddd;">Memory</th>';
        echo '</tr>';
        
        foreach (self::$markers as $name => $data) {
            echo '<tr>';
            echo '<td style="padding:8px;border-bottom:1px solid #ddd;">' . esc_html($name) . '</td>';
            echo '<td style="text-align:right;padding:8px;border-bottom:1px solid #ddd;">' . esc_html($data['time']) . '</td>';
            echo '<td style="text-align:right;padding:8px;border-bottom:1px solid #ddd;">' . esc_html($data['memory']) . '</td>';
            echo '</tr>';
        }
        
        echo '</table>';
        echo '</div>';
    }
}

// Uso:
// Meu_Plugin_Profiler::start('database_query');
// // ... código ...
// Meu_Plugin_Profiler::stop('database_query');
// add_action('wp_footer', ['Meu_Plugin_Profiler', 'display']);
```

---

## ✅ Boas Práticas de Performance

### Checklist de Performance

- ✅ **Usar Object Cache** para queries frequentes
- ✅ **Usar Transients** para dados externos
- ✅ **Lazy Load** imagens e conteúdo pesado
- ✅ **Minificar** CSS e JavaScript
- ✅ **Otimizar** imagens (WebP, compressão)
- ✅ **Evitar N+1 queries** com eager loading
- ✅ **Usar índices** no banco para queries lentas
- ✅ **Cache CDN** para assets estáticos
- ✅ **Limpar** dados desnecessários regularmente
- ✅ **Monitorar** performance com ferramentas

### Ferramentas Recomendadas

**Para Debugging:**
- Query Monitor - Visualiza queries, hooks, performance
- Xdebug - Debugger de PHP
- New Relic - APM (Application Performance Monitoring)

**Para Caching:**
- Redis - Cache persistente, high-performance
- Memcached - Cache distribuído
- WP Super Cache - Page cache simples
- W3 Total Cache - Cache completo

**Para Análise:**
- Google PageSpeed Insights
- WebPageTest
- GTmetrix
- Lighthouse

### Performance Profile

```
Objetivo      | Métrica       | Target
------------- | ------------- | ----------
TTFB          | Time to First | < 500 ms
FCP           | First Paint   | < 1.5 s
LCP           | Main Content  | < 2.5 s
TTI           | Interactive   | < 3.8 s
CLS           | Layout Shift  | < 0.1
Total Size    | Page Weight   | < 5 MB
```

---

**Versão:** 1.0  
**Atualizado:** Janeiro 2026  
**Próxima fase:** Fase 9 - WP-CLI e Ferramentas de Desenvolvimento
