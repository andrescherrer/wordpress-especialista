<?php
/**
 * REFERÊNCIA RÁPIDA – Transients com expiração
 *
 * set_transient($key, $value, $expire); get_transient($key); delete_transient($key).
 * $expire em segundos; 0 = sem expiração (até delete). Salvos em wp_options (autoload = no para transients).
 *
 * Fonte: 008-WordPress-Fase-8-Performance-Cache-Otimizacao.md
 *
 * @package EstudosWP
 * @subpackage 08-Performance-Cache-Otimizacao
 */

if (!defined('ABSPATH')) {
    exit;
}

$transient_key = 'estudos_wp_feed_home';
$expire        = 12 * HOUR_IN_SECONDS; // 12 horas

function estudos_wp_get_feed_cached() {
    global $transient_key, $expire;
    $cached = get_transient($transient_key);
    if (false !== $cached) {
        return $cached;
    }
    $res = wp_remote_get('https://example.com/feed.json', ['timeout' => 10]);
    $data = [];
    if (!is_wp_error($res) && wp_remote_retrieve_response_code($res) === 200) {
        $body = wp_remote_retrieve_body($res);
        $data = json_decode($body, true) ?: [];
    }
    set_transient($transient_key, $data, $expire);
    return $data;
}

// Limpar ao publicar novo post (exemplo)
add_action('publish_post', function () {
    delete_transient('estudos_wp_feed_home');
});
