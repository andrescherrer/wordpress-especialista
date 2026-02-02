# 🚀 FASE 8: Performance, Cache e Otimização

**Versão:** 1.0  
**Data:** Janeiro 2026  
**Nível:** Especialista em PHP  
**Objetivo:** Dominar estratégias de cache, otimização e performance no WordPress

---

**Navegação:** [Índice](000-WordPress-Topicos-Index.md) | [← Fase 7](007-WordPress-Fase-7-WP-CLI-Fundamentals.md) | [Fase 9 →](009-WordPress-Fase-9-WP-CLI-Ferramentas.md)

---

## 📑 Índice

1. [Fundamentos de Performance](#fundamentos-de-performance)
2. [Object Cache](#object-cache)
3. [Cache Invalidation Patterns](#84-cache-invalidation-patterns)
4. [Cache Warming Strategies](#85-cache-warming-strategies)
5. [Cache Monitoring e Debugging](#86-cache-monitoring-e-debugging)
6. [Exemplos Reais: WooCommerce, Blog, etc.](#87-exemplos-reais-woocommerce-blog-etc)
7. [Transients API](#transients-api)
8. [Fragment Caching](#fragment-caching)
9. [Query Optimization](#query-optimization)
10. [Page Caching](#page-caching)
11. [CDN Integration](#cdn-integration)
12. [Asset Optimization](#asset-optimization)
13. [Database Optimization](#database-optimization)
14. [Performance Monitoring](#performance-monitoring)
15. [Boas Práticas](#boas-práticas)

---

## 🎯 Objetivos de Aprendizado

Ao final desta fase, você será capaz de:

1. ✅ Implementar Object Cache usando WordPress Cache API com Redis/Memcached
2. ✅ Usar Transients API para cache de banco de dados efetivamente
3. ✅ Aplicar padrões de invalidação de cache (versionamento de chaves, invalidação em cascata)
4. ✅ Implementar estratégias de cache warming para conteúdo crítico
5. ✅ Monitorar performance de cache (taxas de hit/miss, debugging)
6. ✅ Otimizar queries de banco de dados para reduzir carga
7. ✅ Implementar page caching e integração CDN
8. ✅ Aplicar boas práticas de monitoramento e otimização de performance

## 📝 Autoavaliação

Teste seu entendimento:

- [ ] Qual é a diferença entre Object Cache e Transients API?
- [ ] Como você previne cache stampede quando múltiplos processos tentam regenerar cache?
- [ ] O que é o padrão stale-while-revalidate e quando você deve usá-lo?
- [ ] Como você implementa invalidação em cascata quando dados relacionados mudam?
- [ ] Quais são os trade-offs entre taxa de cache hit e frescor do cache?
- [ ] Como você otimiza queries lentas usando `EXPLAIN` e indexação?
- [ ] Qual é a diferença entre page caching e object caching?
- [ ] Como você mede e monitora performance de cache?

## 🛠️ Projeto Prático

**Construir:** Plugin de Otimização de Performance

Crie um plugin que:
- Implemente Object Cache com Redis
- Use Transients API para queries caras
- Implemente padrões de invalidação de cache
- Inclua cache warming para conteúdo popular
- Monitore taxas de cache hit/miss
- Otimize queries de banco de dados
- Forneça dashboard admin para estatísticas de cache

**Tempo estimado:** 12-15 horas  
**Dificuldade:** Avançado

---

## ❌ Equívocos Comuns

### Equívoco 1: "Mais cache é sempre melhor"
**Realidade:** Excesso de cache pode causar dados desatualizados, problemas de memória e dificuldades de debugging. Cache estrategicamente, não em todos os lugares.

**Por que é importante:** Cache inadequado pode causar bugs, problemas de performance e pesadelos de manutenção.

**Como lembrar:** Cache = equilíbrio entre frescor e performance. Cache o que faz sentido.

### Equívoco 2: "Object Cache e Transients são a mesma coisa"
**Realidade:** Object Cache é em memória (Redis/Memcached), Transients usam banco de dados. Diferentes casos de uso e características de performance.

**Por que é importante:** Escolher o método de cache errado pode prejudicar performance ou causar perda de dados.

**Como lembrar:** Object Cache = rápido, volátil. Transients = persistente, mais lento.

### Equívoco 3: "Invalidação de cache é simples"
**Realidade:** Invalidação de cache é um dos problemas mais difíceis em ciência da computação. Você precisa de estratégias como versionamento, invalidação em cascata e TTL.

**Por que é importante:** Invalidação ruim leva a dados desatualizados e bugs difíceis de debugar.

**Como lembrar:** Invalidação de cache = problema complexo. Use padrões comprovados (versionamento, cascata).

### Equívoco 4: "Page caching elimina a necessidade de otimização de queries"
**Realidade:** Page caching ajuda, mas queries lentas ainda impactam geração de cache, páginas admin e usuários logados. Otimize queries também.

**Por que é importante:** Queries lentas afetam cache warming, performance admin e experiência de usuários logados.

**Como lembrar:** Page cache + otimização de queries = solução completa.

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

### 8.4 Cache Invalidation Patterns

**Problema:** Cache desatualizado pode servir dados incorretos ou obsoletos aos usuários.

**Solução:** Implementar estratégias de invalidação inteligentes que mantêm cache atualizado sem perder performance.

#### 8.4.1 Cache Key Versioning

**Conceito:** Usar versões nas chaves de cache para invalidar grupos inteiros facilmente.

```php
<?php
class Cache_Key_Versioning {
    
    private function get_cache_version($group) {
        $version_key = 'cache_version_' . $group;
        $version = wp_cache_get($version_key, 'cache_versions');
        
        if (false === $version) {
            $version = time(); // Usar timestamp como versão inicial
            wp_cache_set($version_key, $version, 'cache_versions', 0); // Sem expiração
        }
        
        return $version;
    }
    
    private function increment_cache_version($group) {
        $version_key = 'cache_version_' . $group;
        $current_version = $this->get_cache_version($group);
        wp_cache_set($version_key, $current_version + 1, 'cache_versions', 0);
    }
    
    public function get_cached_data($key, $group) {
        $version = $this->get_cache_version($group);
        $versioned_key = $key . '_v' . $version;
        
        return wp_cache_get($versioned_key, $group);
    }
    
    public function set_cached_data($key, $data, $group, $expiration = 3600) {
        $version = $this->get_cache_version($group);
        $versioned_key = $key . '_v' . $version;
        
        wp_cache_set($versioned_key, $data, $group, $expiration);
    }
    
    public function invalidate_group($group) {
        // Apenas incrementar versão - todas as chaves antigas ficam obsoletas
        $this->increment_cache_version($group);
    }
}

// Uso
$cache = new Cache_Key_Versioning();

// Salvar dados
$cache->set_cached_data('products_list', $products, 'products', 3600);

// Buscar dados
$products = $cache->get_cached_data('products_list', 'products');

// Invalidar todo o grupo (incrementa versão)
$cache->invalidate_group('products');
// Agora get_cached_data retornará false (chave antiga não existe mais)
```

**Vantagens:**
- Invalidação instantânea de grupos inteiros
- Não precisa deletar múltiplas chaves
- Performance melhor em grandes volumes

#### 8.4.2 Cascade Invalidation (Invalidação em Cascata)

**Conceito:** Quando um recurso é atualizado, invalidar todos os caches relacionados.

```php
<?php
class Cascade_Cache_Invalidation {
    
    /**
     * Mapear dependências de cache
     */
    private $cache_dependencies = [
        'post' => [
            'post_list',
            'author_posts',
            'category_posts',
            'related_posts',
        ],
        'author' => [
            'author_stats',
            'author_posts',
        ],
        'category' => [
            'category_posts',
            'category_stats',
        ],
    ];
    
    /**
     * Invalidar cache em cascata
     */
    public function invalidate_cascade($resource_type, $resource_id) {
        if (!isset($this->cache_dependencies[$resource_type])) {
            return;
        }
        
        $dependencies = $this->cache_dependencies[$resource_type];
        
        foreach ($dependencies as $cache_group) {
            $this->invalidate_cache_group($cache_group, $resource_id);
        }
    }
    
    /**
     * Invalidar grupo específico
     */
    private function invalidate_cache_group($group, $resource_id = null) {
        switch ($group) {
            case 'post_list':
                wp_cache_delete('all_posts', 'posts');
                wp_cache_delete('featured_posts', 'posts');
                break;
                
            case 'author_posts':
                if ($resource_id) {
                    $post = get_post($resource_id);
                    if ($post) {
                        wp_cache_delete('author_' . $post->post_author . '_posts', 'authors');
                    }
                }
                break;
                
            case 'category_posts':
                if ($resource_id) {
                    $categories = wp_get_post_categories($resource_id);
                    foreach ($categories as $cat_id) {
                        wp_cache_delete('category_' . $cat_id . '_posts', 'categories');
                    }
                }
                break;
                
            case 'related_posts':
                if ($resource_id) {
                    wp_cache_delete('related_' . $resource_id, 'posts');
                }
                break;
        }
    }
}

// Hook para invalidar automaticamente
add_action('save_post', function($post_id) {
    $invalidator = new Cascade_Cache_Invalidation();
    $invalidator->invalidate_cascade('post', $post_id);
});

add_action('set_object_terms', function($object_id, $terms, $tt_ids, $taxonomy) {
    if ($taxonomy === 'category') {
        $invalidator = new Cascade_Cache_Invalidation();
        $invalidator->invalidate_cascade('category', $object_id);
    }
}, 10, 4);
```

#### 8.4.3 Cache Stampede Prevention (Prevenção de Cache Stampede)

**Problema:** Quando cache expira, múltiplas requisições simultâneas podem causar sobrecarga no banco.

**Solução:** Usar locks para garantir que apenas uma requisição regenere o cache.

```php
<?php
class Cache_Stampede_Prevention {
    
    /**
     * Buscar com prevenção de stampede
     */
    public function get_with_lock($key, $group, $callback, $expiration = 3600) {
        $cache_key = $key;
        $lock_key = $key . '_lock';
        
        // Tentar obter do cache
        $data = wp_cache_get($cache_key, $group);
        
        if (false !== $data) {
            return $data; // Cache hit
        }
        
        // Verificar se alguém está regenerando
        $lock = wp_cache_get($lock_key, $group);
        
        if (false !== $lock) {
            // Alguém está regenerando, aguardar um pouco e tentar novamente
            usleep(100000); // 100ms
            $data = wp_cache_get($cache_key, $group);
            
            if (false !== $data) {
                return $data; // Cache foi regenerado
            }
        }
        
        // Criar lock
        wp_cache_set($lock_key, true, $group, 30); // Lock por 30 segundos
        
        try {
            // Regenerar cache
            $data = call_user_func($callback);
            
            // Salvar no cache
            wp_cache_set($cache_key, $data, $group, $expiration);
            
            return $data;
        } finally {
            // Remover lock
            wp_cache_delete($lock_key, $group);
        }
    }
}

// Uso
$cache = new Cache_Stampede_Prevention();

$products = $cache->get_with_lock(
    'expensive_products',
    'products',
    function() {
        // Query pesada que queremos cachear
        return get_posts([
            'post_type' => 'product',
            'posts_per_page' => 100,
            'meta_query' => [
                ['key' => '_price', 'value' => 100, 'compare' => '>=']
            ],
        ]);
    },
    3600 // 1 hora
);
```

#### 8.4.4 Stale-While-Revalidate Pattern

**Conceito:** Servir cache antigo imediatamente enquanto regenera em background.

```php
<?php
class Stale_While_Revalidate {
    
    public function get_with_stale($key, $group, $callback, $expiration = 3600) {
        $cache_key = $key;
        $stale_key = $key . '_stale';
        
        // Tentar cache atual
        $data = wp_cache_get($cache_key, $group);
        
        if (false !== $data) {
            return $data; // Cache atual disponível
        }
        
        // Tentar cache stale
        $stale_data = wp_cache_get($stale_key, $group);
        
        if (false !== $stale_data) {
            // Servir stale enquanto regenera em background
            $this->regenerate_async($key, $group, $callback, $expiration);
            return $stale_data;
        }
        
        // Nenhum cache disponível, regenerar sincronamente
        $data = call_user_func($callback);
        wp_cache_set($cache_key, $data, $group, $expiration);
        wp_cache_set($stale_key, $data, $group, $expiration * 2); // Stale dura o dobro
        
        return $data;
    }
    
    private function regenerate_async($key, $group, $callback, $expiration) {
        // Usar Action Scheduler ou WP Cron para regenerar em background
        if (!as_has_scheduled_action('regenerate_cache_' . $key)) {
            as_schedule_single_action(
                time(),
                'regenerate_cache_' . $key,
                [$key, $group, $expiration]
            );
        }
    }
}

// Registrar handler para regeneração
add_action('regenerate_cache_expensive_products', function($key, $group, $expiration) {
    $data = get_posts([/* query pesada */]);
    wp_cache_set($key, $data, $group, $expiration);
    wp_cache_set($key . '_stale', $data, $group, $expiration * 2);
}, 10, 3);
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

### 8.5 Cache Warming Strategies

**Problema:** Cache vazio após deploy ou restart causa lentidão inicial.

**Solução:** Pré-aquecer cache com dados críticos antes que usuários acessem.

#### 8.5.1 Estratégias de Warming

```php
<?php
class Cache_Warming {
    
    /**
     * Warming completo após deploy
     */
    public function warm_all_caches() {
        // 1. Posts mais populares
        $this->warm_popular_posts();
        
        // 2. Páginas principais
        $this->warm_main_pages();
        
        // 3. Categorias principais
        $this->warm_main_categories();
        
        // 4. Dados de API externa
        $this->warm_external_apis();
        
        // 5. Queries complexas frequentes
        $this->warm_complex_queries();
    }
    
    private function warm_popular_posts() {
        $popular_posts = get_posts([
            'post_type' => 'post',
            'posts_per_page' => 20,
            'orderby' => 'comment_count',
            'order' => 'DESC',
        ]);
        
        foreach ($popular_posts as $post) {
            // Cachear dados do post
            wp_cache_set('post_' . $post->ID, $post, 'posts', DAY_IN_SECONDS);
            
            // Cachear meta do post
            $meta = get_post_meta($post->ID);
            wp_cache_set('post_meta_' . $post->ID, $meta, 'post_meta', DAY_IN_SECONDS);
        }
    }
    
    private function warm_main_pages() {
        $pages = ['home', 'about', 'contact', 'blog'];
        
        foreach ($pages as $page_slug) {
            $page = get_page_by_path($page_slug);
            if ($page) {
                wp_cache_set('page_' . $page->ID, $page, 'pages', DAY_IN_SECONDS);
            }
        }
    }
    
    private function warm_main_categories() {
        $categories = get_categories(['number' => 10]);
        
        foreach ($categories as $category) {
            $posts = get_posts([
                'category' => $category->term_id,
                'posts_per_page' => 10,
            ]);
            
            wp_cache_set(
                'category_' . $category->term_id . '_posts',
                $posts,
                'categories',
                HOUR_IN_SECONDS
            );
        }
    }
    
    private function warm_external_apis() {
        // Cachear dados de API externa que são usados frequentemente
        $api_data = wp_remote_get('https://api.example.com/data');
        
        if (!is_wp_error($api_data)) {
            set_transient('external_api_data', $api_data['body'], HOUR_IN_SECONDS);
        }
    }
    
    private function warm_complex_queries() {
        // Cachear queries complexas que são executadas frequentemente
        $complex_query = new WP_Query([
            'post_type' => 'product',
            'meta_query' => [
                ['key' => '_featured', 'value' => '1'],
                ['key' => '_stock_status', 'value' => 'instock'],
            ],
            'posts_per_page' => 50,
        ]);
        
        wp_cache_set('featured_products', $complex_query->posts, 'products', HOUR_IN_SECONDS);
    }
}

// Executar warming após deploy
add_action('init', function() {
    if (defined('DOING_WARMING') && DOING_WARMING) {
        $warmer = new Cache_Warming();
        $warmer->warm_all_caches();
    }
});
```

#### 8.5.2 Warming em Deployment

```php
<?php
/**
 * Script para executar warming após deploy
 * wp cache-warm
 */
WP_CLI::add_command('cache-warm', function() {
    WP_CLI::line('Iniciando cache warming...');
    
    $warmer = new Cache_Warming();
    $warmer->warm_all_caches();
    
    WP_CLI::success('Cache warming concluído!');
});

// Ou via Action Scheduler após deploy
add_action('deployment_complete', function() {
    as_schedule_single_action(time(), 'warm_cache_after_deploy');
});

add_action('warm_cache_after_deploy', function() {
    $warmer = new Cache_Warming();
    $warmer->warm_all_caches();
});
```

#### 8.5.3 Warming Incremental

```php
<?php
class Incremental_Cache_Warming {
    
    /**
     * Aquecer cache quando post é publicado
     */
    public function warm_on_publish($post_id) {
        $post = get_post($post_id);
        
        // Cachear post
        wp_cache_set('post_' . $post_id, $post, 'posts', DAY_IN_SECONDS);
        
        // Cachear related posts
        $this->warm_related_posts($post_id);
        
        // Cachear author stats
        $this->warm_author_stats($post->post_author);
    }
    
    private function warm_related_posts($post_id) {
        $categories = wp_get_post_categories($post_id);
        
        foreach ($categories as $cat_id) {
            $related = get_posts([
                'category__in' => [$cat_id],
                'post__not_in' => [$post_id],
                'posts_per_page' => 5,
            ]);
            
            wp_cache_set(
                'related_' . $post_id,
                $related,
                'posts',
                DAY_IN_SECONDS
            );
        }
    }
    
    private function warm_author_stats($author_id) {
        $stats = [
            'post_count' => count_user_posts($author_id),
            'total_views' => $this->get_total_views($author_id),
        ];
        
        wp_cache_set('author_stats_' . $author_id, $stats, 'authors', HOUR_IN_SECONDS);
    }
}

add_action('publish_post', function($post_id) {
    $warmer = new Incremental_Cache_Warming();
    $warmer->warm_on_publish($post_id);
});
```

### 8.6 Cache Monitoring e Debugging

**Problema:** Difícil diagnosticar problemas de cache sem ferramentas adequadas.

**Solução:** Implementar logging e monitoramento de cache.

#### 8.6.1 Debugging de Cache

```php
<?php
class Cache_Debugger {
    
    private static $log = [];
    
    public static function log_cache_operation($operation, $key, $group, $result) {
        if (!WP_DEBUG) {
            return;
        }
        
        self::$log[] = [
            'time' => microtime(true),
            'operation' => $operation, // get, set, delete
            'key' => $key,
            'group' => $group,
            'result' => $result, // hit, miss, set, deleted
        ];
    }
    
    public static function get_cache_stats() {
        $stats = [
            'total_operations' => count(self::$log),
            'gets' => 0,
            'hits' => 0,
            'misses' => 0,
            'sets' => 0,
            'deletes' => 0,
        ];
        
        foreach (self::$log as $entry) {
            if ($entry['operation'] === 'get') {
                $stats['gets']++;
                if ($entry['result'] === 'hit') {
                    $stats['hits']++;
                } else {
                    $stats['misses']++;
                }
            } elseif ($entry['operation'] === 'set') {
                $stats['sets']++;
            } elseif ($entry['operation'] === 'delete') {
                $stats['deletes']++;
            }
        }
        
        $stats['hit_rate'] = $stats['gets'] > 0 
            ? ($stats['hits'] / $stats['gets']) * 100 
            : 0;
        
        return $stats;
    }
    
    public static function print_stats() {
        if (!WP_DEBUG) {
            return;
        }
        
        $stats = self::get_cache_stats();
        
        echo '<div style="background: #f0f0f0; padding: 20px; margin: 20px;">';
        echo '<h3>Cache Statistics</h3>';
        echo '<pre>';
        print_r($stats);
        echo '</pre>';
        echo '</div>';
    }
}

// Wrapper para logging
function debug_cache_get($key, $group) {
    $result = wp_cache_get($key, $group);
    Cache_Debugger::log_cache_operation(
        'get',
        $key,
        $group,
        $result !== false ? 'hit' : 'miss'
    );
    return $result;
}

function debug_cache_set($key, $data, $group, $expiration = 0) {
    $result = wp_cache_set($key, $data, $group, $expiration);
    Cache_Debugger::log_cache_operation('set', $key, $group, $result ? 'set' : 'failed');
    return $result;
}

// Adicionar stats ao footer em debug mode
if (WP_DEBUG) {
    add_action('wp_footer', ['Cache_Debugger', 'print_stats']);
}
```

#### 8.6.2 Monitoring de Hit/Miss Rates

```php
<?php
class Cache_Monitor {
    
    public function track_cache_performance() {
        $stats = get_option('cache_performance_stats', []);
        
        $current_stats = [
            'timestamp' => time(),
            'hit_rate' => $this->calculate_hit_rate(),
            'cache_size' => $this->estimate_cache_size(),
            'backend' => $this->get_cache_backend(),
        ];
        
        $stats[] = $current_stats;
        
        // Manter apenas últimas 100 medições
        if (count($stats) > 100) {
            $stats = array_slice($stats, -100);
        }
        
        update_option('cache_performance_stats', $stats);
    }
    
    private function calculate_hit_rate() {
        // Implementar cálculo de hit rate
        // Pode usar Cache_Debugger ou métricas do backend
        return 0.85; // 85% hit rate
    }
    
    private function estimate_cache_size() {
        // Estimar tamanho do cache
        // Depende do backend (Redis, Memcached, etc)
        return 0;
    }
    
    private function get_cache_backend() {
        if (class_exists('Redis')) {
            return 'Redis';
        } elseif (class_exists('Memcached')) {
            return 'Memcached';
        } else {
            return 'Default';
        }
    }
    
    public function get_performance_report() {
        $stats = get_option('cache_performance_stats', []);
        
        if (empty($stats)) {
            return 'Nenhum dado coletado ainda.';
        }
        
        $avg_hit_rate = array_sum(array_column($stats, 'hit_rate')) / count($stats);
        
        return [
            'average_hit_rate' => round($avg_hit_rate * 100, 2) . '%',
            'measurements' => count($stats),
            'latest' => end($stats),
        ];
    }
}

// Executar monitoramento periodicamente
add_action('wp_scheduled_cache_monitor', function() {
    $monitor = new Cache_Monitor();
    $monitor->track_cache_performance();
});

// Agendar monitoramento (executar a cada hora)
if (!wp_next_scheduled('wp_scheduled_cache_monitor')) {
    wp_schedule_event(time(), 'hourly', 'wp_scheduled_cache_monitor');
}
```

#### 8.6.3 Tools e Plugins para Debugging

```php
<?php
/**
 * Plugin de debugging de cache
 */
class Cache_Debug_Tool {
    
    public function __construct() {
        if (current_user_can('manage_options')) {
            add_action('admin_bar_menu', [$this, 'add_admin_bar_menu'], 100);
            add_action('admin_footer', [$this, 'show_cache_info']);
        }
    }
    
    public function add_admin_bar_menu($wp_admin_bar) {
        $wp_admin_bar->add_node([
            'id' => 'cache-debug',
            'title' => 'Cache Debug',
            'href' => '#',
        ]);
    }
    
    public function show_cache_info() {
        if (!WP_DEBUG) {
            return;
        }
        
        ?>
        <div id="cache-debug-info" style="display:none; position:fixed; bottom:0; right:0; background:#fff; padding:20px; border:1px solid #ccc; z-index:9999; max-width:400px;">
            <h3>Cache Info</h3>
            <pre><?php
                $stats = Cache_Debugger::get_cache_stats();
                print_r($stats);
            ?></pre>
            <button onclick="document.getElementById('cache-debug-info').style.display='none'">Fechar</button>
        </div>
        <script>
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey && e.shiftKey && e.key === 'C') {
                    document.getElementById('cache-debug-info').style.display = 
                        document.getElementById('cache-debug-info').style.display === 'none' ? 'block' : 'none';
                }
            });
        </script>
        <?php
    }
}

new Cache_Debug_Tool();
```

### 8.7 Exemplos Reais: WooCommerce, Blog, etc.

#### 8.7.1 WooCommerce: Cache de Produtos

```php
<?php
/**
 * Sistema de cache para WooCommerce
 */
class WooCommerce_Cache_Manager {
    
    /**
     * Cache de lista de produtos com invalidação inteligente
     */
    public function get_products($args = []) {
        // Criar chave baseada nos argumentos
        $cache_key = 'wc_products_' . md5(serialize($args));
        $cache_group = 'woocommerce';
        
        $products = wp_cache_get($cache_key, $cache_group);
        
        if (false === $products) {
            $query = new WC_Product_Query($args);
            $products = $query->get_products();
            
            // Cachear por 1 hora
            wp_cache_set($cache_key, $products, $cache_group, HOUR_IN_SECONDS);
        }
        
        return $products;
    }
    
    /**
     * Cache de preço do produto (muda frequentemente)
     */
    public function get_product_price($product_id) {
        $cache_key = 'wc_price_' . $product_id;
        $cache_group = 'woocommerce_prices';
        
        $price = wp_cache_get($cache_key, $cache_group);
        
        if (false === $price) {
            $product = wc_get_product($product_id);
            $price = $product ? $product->get_price() : 0;
            
            // Cachear por 5 minutos (preços podem mudar)
            wp_cache_set($cache_key, $price, $cache_group, 5 * MINUTE_IN_SECONDS);
        }
        
        return $price;
    }
    
    /**
     * Cache de estoque (crítico para performance)
     */
    public function get_stock_status($product_id) {
        $cache_key = 'wc_stock_' . $product_id;
        $cache_group = 'woocommerce_stock';
        
        $status = wp_cache_get($cache_key, $cache_group);
        
        if (false === $status) {
            $product = wc_get_product($product_id);
            $status = $product ? $product->get_stock_status() : 'outofstock';
            
            // Cachear por 1 minuto (estoque muda frequentemente)
            wp_cache_set($cache_key, $status, $cache_group, MINUTE_IN_SECONDS);
        }
        
        return $status;
    }
    
    /**
     * Invalidar cache quando produto é atualizado
     */
    public function __construct() {
        add_action('woocommerce_update_product', [$this, 'invalidate_product_cache'], 10, 1);
        add_action('woocommerce_reduce_order_stock', [$this, 'invalidate_stock_cache'], 10, 1);
        add_action('woocommerce_restore_order_stock', [$this, 'invalidate_stock_cache'], 10, 1);
    }
    
    public function invalidate_product_cache($product_id) {
        // Invalidar cache do produto
        wp_cache_delete('wc_product_' . $product_id, 'woocommerce');
        wp_cache_delete('wc_price_' . $product_id, 'woocommerce_prices');
        
        // Invalidar listas que podem conter este produto
        wp_cache_delete('wc_products_featured', 'woocommerce');
        wp_cache_delete('wc_products_onsale', 'woocommerce');
        
        // Usar versioning para invalidar todas as queries
        $this->increment_cache_version('woocommerce');
    }
    
    public function invalidate_stock_cache($order) {
        foreach ($order->get_items() as $item) {
            $product_id = $item->get_product_id();
            wp_cache_delete('wc_stock_' . $product_id, 'woocommerce_stock');
        }
    }
    
    private function increment_cache_version($group) {
        $version_key = 'cache_version_' . $group;
        $version = wp_cache_get($version_key, 'cache_versions') ?: 1;
        wp_cache_set($version_key, $version + 1, 'cache_versions', 0);
    }
}

new WooCommerce_Cache_Manager();
```

#### 8.7.2 Blog: Cache de Posts e Relacionados

```php
<?php
/**
 * Sistema de cache para blog
 */
class Blog_Cache_Manager {
    
    /**
     * Cache de posts recentes com invalidação em cascata
     */
    public function get_recent_posts($limit = 10) {
        $cache_key = 'blog_recent_' . $limit;
        $cache_group = 'blog';
        
        $posts = wp_cache_get($cache_key, $cache_group);
        
        if (false === $posts) {
            $posts = get_posts([
                'post_type' => 'post',
                'posts_per_page' => $limit,
                'post_status' => 'publish',
                'orderby' => 'date',
                'order' => 'DESC',
            ]);
            
            wp_cache_set($cache_key, $posts, $cache_group, HOUR_IN_SECONDS);
        }
        
        return $posts;
    }
    
    /**
     * Cache de posts relacionados (query pesada)
     */
    public function get_related_posts($post_id, $limit = 5) {
        $cache_key = 'related_' . $post_id;
        $cache_group = 'blog_related';
        
        $related = wp_cache_get($cache_key, $cache_group);
        
        if (false === $related) {
            $categories = wp_get_post_categories($post_id);
            
            if (!empty($categories)) {
                $related = get_posts([
                    'category__in' => $categories,
                    'post__not_in' => [$post_id],
                    'posts_per_page' => $limit,
                    'orderby' => 'rand', // Query pesada!
                ]);
            } else {
                $related = [];
            }
            
            // Cachear por 6 horas (posts relacionados não mudam muito)
            wp_cache_set($cache_key, $related, $cache_group, 6 * HOUR_IN_SECONDS);
        }
        
        return $related;
    }
    
    /**
     * Cache de contagem de comentários (muda frequentemente)
     */
    public function get_comment_count($post_id) {
        $cache_key = 'comment_count_' . $post_id;
        $cache_group = 'blog_comments';
        
        $count = wp_cache_get($cache_key, $cache_group);
        
        if (false === $count) {
            $count = get_comments_number($post_id);
            
            // Cachear por 5 minutos
            wp_cache_set($cache_key, $count, $cache_group, 5 * MINUTE_IN_SECONDS);
        }
        
        return $count;
    }
    
    /**
     * Cache de arquivo mensal (posts por mês)
     */
    public function get_monthly_archive($year, $month) {
        $cache_key = 'archive_' . $year . '_' . $month;
        $cache_group = 'blog_archives';
        
        $posts = wp_cache_get($cache_key, $cache_group);
        
        if (false === $posts) {
            $posts = get_posts([
                'post_type' => 'post',
                'year' => $year,
                'monthnum' => $month,
                'posts_per_page' => -1,
            ]);
            
            // Cachear por 1 dia (arquivos não mudam muito)
            wp_cache_set($cache_key, $posts, $cache_group, DAY_IN_SECONDS);
        }
        
        return $posts;
    }
    
    /**
     * Invalidar cache em cascata
     */
    public function __construct() {
        add_action('save_post', [$this, 'invalidate_on_save'], 10, 1);
        add_action('wp_insert_comment', [$this, 'invalidate_comment_cache'], 10, 1);
        add_action('delete_post', [$this, 'invalidate_on_delete'], 10, 1);
    }
    
    public function invalidate_on_save($post_id) {
        $post = get_post($post_id);
        
        if ($post->post_type !== 'post' || $post->post_status !== 'publish') {
            return;
        }
        
        // Invalidar posts recentes
        wp_cache_delete('blog_recent_10', 'blog');
        wp_cache_delete('blog_recent_20', 'blog');
        
        // Invalidar arquivo mensal
        $year = date('Y', strtotime($post->post_date));
        $month = date('m', strtotime($post->post_date));
        wp_cache_delete('archive_' . $year . '_' . $month, 'blog_archives');
        
        // Invalidar posts relacionados deste post
        wp_cache_delete('related_' . $post_id, 'blog_related');
        
        // Invalidar posts relacionados de posts na mesma categoria
        $categories = wp_get_post_categories($post_id);
        foreach ($categories as $cat_id) {
            // Buscar outros posts nesta categoria e invalidar seus relacionados
            $other_posts = get_posts([
                'category__in' => [$cat_id],
                'post__not_in' => [$post_id],
                'posts_per_page' => 20,
            ]);
            
            foreach ($other_posts as $other_post) {
                wp_cache_delete('related_' . $other_post->ID, 'blog_related');
            }
        }
    }
    
    public function invalidate_comment_cache($comment_id) {
        $comment = get_comment($comment_id);
        if ($comment) {
            wp_cache_delete('comment_count_' . $comment->comment_post_ID, 'blog_comments');
        }
    }
    
    public function invalidate_on_delete($post_id) {
        $this->invalidate_on_save($post_id);
    }
}

new Blog_Cache_Manager();
```

#### 8.7.3 E-commerce: Cache de Carrinho e Checkout

```php
<?php
/**
 * Cache para dados de e-commerce (carrinho, checkout)
 */
class Ecommerce_Cache_Manager {
    
    /**
     * Cache de dados de shipping (calculado, pesado)
     */
    public function get_shipping_rates($destination, $items) {
        $cache_key = 'shipping_' . md5(serialize([$destination, $items]));
        $cache_group = 'ecommerce';
        
        $rates = wp_cache_get($cache_key, $cache_group);
        
        if (false === $rates) {
            // Calcular shipping (pode ser pesado)
            $rates = $this->calculate_shipping($destination, $items);
            
            // Cachear por 10 minutos
            wp_cache_set($cache_key, $rates, $cache_group, 10 * MINUTE_IN_SECONDS);
        }
        
        return $rates;
    }
    
    /**
     * Cache de taxas (dependem de localização)
     */
    public function get_tax_rates($location) {
        $cache_key = 'tax_' . md5(serialize($location));
        $cache_group = 'ecommerce';
        
        $rates = wp_cache_get($cache_key, $cache_group);
        
        if (false === $rates) {
            $rates = WC_Tax::find_rates([
                'country' => $location['country'],
                'state' => $location['state'],
                'postcode' => $location['postcode'],
            ]);
            
            // Cachear por 1 hora (taxas não mudam frequentemente)
            wp_cache_set($cache_key, $rates, $cache_group, HOUR_IN_SECONDS);
        }
        
        return $rates;
    }
    
    /**
     * Cache de produtos em destaque (usado em várias páginas)
     */
    public function get_featured_products() {
        $cache_key = 'featured_products';
        $cache_group = 'ecommerce';
        
        $products = wp_cache_get($cache_key, $cache_group);
        
        if (false === $products) {
            $products = wc_get_featured_product_ids();
            
            // Cachear por 30 minutos
            wp_cache_set($cache_key, $products, $cache_group, 30 * MINUTE_IN_SECONDS);
        }
        
        return $products;
    }
    
    private function calculate_shipping($destination, $items) {
        // Simulação de cálculo de shipping
        return [
            'standard' => 10.00,
            'express' => 25.00,
        ];
    }
    
    /**
     * Invalidar cache quando necessário
     */
    public function __construct() {
        // Invalidar quando produto é atualizado
        add_action('woocommerce_update_product', function($product_id) {
            wp_cache_delete('featured_products', 'ecommerce');
        });
        
        // Invalidar taxas quando configuração muda
        add_action('woocommerce_settings_saved', function() {
            wp_cache_flush_group('ecommerce'); // Flush completo do grupo
        });
    }
}

new Ecommerce_Cache_Manager();
```

#### 8.7.4 API Externa: Cache de Dados de Terceiros

```php
<?php
/**
 * Cache para dados de API externa
 */
class External_API_Cache {
    
    /**
     * Cache de dados de API com stale-while-revalidate
     */
    public function get_api_data($endpoint, $params = []) {
        $cache_key = 'api_' . md5($endpoint . serialize($params));
        $cache_group = 'external_api';
        $stale_key = $cache_key . '_stale';
        
        // Tentar cache atual
        $data = wp_cache_get($cache_key, $cache_group);
        if (false !== $data) {
            return $data;
        }
        
        // Tentar cache stale
        $stale_data = wp_cache_get($stale_key, $cache_group);
        if (false !== $stale_data) {
            // Regenerar em background
            $this->regenerate_async($endpoint, $params, $cache_key, $cache_group);
            return $stale_data;
        }
        
        // Buscar da API
        $response = wp_remote_get($endpoint, [
            'timeout' => 10,
            'body' => $params,
        ]);
        
        if (is_wp_error($response)) {
            return false;
        }
        
        $data = json_decode(wp_remote_retrieve_body($response), true);
        
        // Cachear atual e stale
        wp_cache_set($cache_key, $data, $cache_group, 15 * MINUTE_IN_SECONDS);
        wp_cache_set($stale_key, $data, $cache_group, 1 * HOUR_IN_SECONDS);
        
        return $data;
    }
    
    private function regenerate_async($endpoint, $params, $cache_key, $cache_group) {
        // Usar Action Scheduler para regenerar em background
        if (!as_has_scheduled_action('regenerate_api_cache_' . $cache_key)) {
            as_schedule_single_action(
                time(),
                'regenerate_api_cache_' . $cache_key,
                [$endpoint, $params, $cache_key, $cache_group]
            );
        }
    }
}

// Handler para regeneração
add_action('regenerate_api_cache_api_', function($endpoint, $params, $cache_key, $cache_group) {
    $response = wp_remote_get($endpoint, ['body' => $params]);
    
    if (!is_wp_error($response)) {
        $data = json_decode(wp_remote_retrieve_body($response), true);
        wp_cache_set($cache_key, $data, $cache_group, 15 * MINUTE_IN_SECONDS);
        wp_cache_set($cache_key . '_stale', $data, $cache_group, 1 * HOUR_IN_SECONDS);
    }
}, 10, 4);

new External_API_Cache();
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
