<?php
/**
 * REFERÊNCIA RÁPIDA – ACF: get_field, the_field, has_block
 *
 * get_field('nome_campo', $post_id); the_field('nome_campo', $post_id); have_rows('repeater'); the_row(); get_sub_field('sub').
 * Verificar function_exists('get_field') antes de usar; ACF pode não estar ativo.
 * Fonte: 015-WordPress-Fase-15-Topicos-Complementares-Avancados.md
 *
 * @package EstudosWP
 * @subpackage 15-Topicos-Complementares-Avancados
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('get_field')) {
    return;
}

// Obter valor de campo (post atual ou por ID)
function estudos_wp_acf_exemplo($post_id = null) {
    $post_id = $post_id ?: get_the_ID();
    $titulo = get_field('titulo_extra', $post_id);
    $imagem = get_field('imagem_destaque', $post_id); // array ou ID conforme tipo
    $lista  = [];
    if (have_rows('itens', $post_id)) {
        while (have_rows('itens', $post_id)) {
            the_row();
            $lista[] = get_sub_field('nome');
        }
    }
    return ['titulo' => $titulo, 'imagem' => $imagem, 'lista' => $lista];
}

// No template: the_field('titulo_extra'); (echo com escape manual se necessário)
// esc_html(get_field('titulo_extra'))
