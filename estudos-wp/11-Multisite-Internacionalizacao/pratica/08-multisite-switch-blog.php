<?php
/**
 * REFERÊNCIA RÁPIDA – switch_to_blog / restore_current_blog
 *
 * switch_to_blog($site_id); ... get_option(), get_posts(); restore_current_blog();
 * Não esquecer restore_current_blog() (um por switch_to_blog); aninhamento suportado mas cuidado com stack.
 * Fonte: 011-WordPress-Fase-11-Multisite-Internacionalizacao.md
 *
 * @package EstudosWP
 * @subpackage 11-Multisite-Internacionalizacao
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!is_multisite()) {
    return;
}

/**
 * Obter opção de cada site da rede (exemplo).
 *
 * @return array [ site_id => blogname ]
 */
function estudos_wp_network_blognames() {
    $sites = get_sites(['number' => 100]);
    $out = [];
    foreach ($sites as $site) {
        $id = (int) $site->blog_id;
        switch_to_blog($id);
        $out[$id] = get_option('blogname');
        restore_current_blog();
    }
    return $out;
}

// Uso: sempre emparelhar switch_to_blog($id) com restore_current_blog() no mesmo fluxo (ou em finally).
