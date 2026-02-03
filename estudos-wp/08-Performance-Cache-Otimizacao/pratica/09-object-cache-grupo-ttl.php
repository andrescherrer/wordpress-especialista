<?php
/**
 * REFERÊNCIA RÁPIDA – Object cache com grupo e TTL
 *
 * wp_cache_set($key, $value, $group, $expire); wp_cache_get($key, $group).
 * wp_cache_add (não sobrescreve); wp_cache_delete($key, $group); wp_cache_flush_group($group).
 * $expire em segundos; 0 = sem expiração. Grupo: namespace para evitar colisão.
 *
 * Fonte: 008-WordPress-Fase-8-Performance-Cache-Otimizacao.md
 *
 * @package EstudosWP
 * @subpackage 08-Performance-Cache-Otimizacao
 */

if (!defined('ABSPATH')) {
    exit;
}

$grupo = 'estudos_wp_produtos';
$ttl  = 300; // 5 minutos

function estudos_wp_get_produtos_cached() {
    global $grupo, $ttl;
    $key = 'lista_10';
    $cached = wp_cache_get($key, $grupo);
    if (false !== $cached) {
        return $cached;
    }
    $query = new WP_Query([
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => 10,
        'fields'         => 'ids',
    ]);
    $ids = $query->posts;
    wp_cache_set($key, $ids, $grupo, $ttl);
    return $ids;
}

// Invalidar ao salvar post
add_action('save_post', function ($post_id) {
    if (get_post_type($post_id) === 'post') {
        wp_cache_delete('lista_10', 'estudos_wp_produtos');
    }
});
