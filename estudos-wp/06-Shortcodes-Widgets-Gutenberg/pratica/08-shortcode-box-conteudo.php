<?php
/**
 * REFERÊNCIA RÁPIDA – Shortcode com conteúdo envolto [tag]content[/tag]
 *
 * add_shortcode('tag', callback); callback($atts, $content = '').
 * shortcode_atts($defaults, $atts, 'tag'); wp_kses_post($content) ou do_shortcode($content) para aninhados.
 * Fonte: 006-WordPress-Fase-6-Shortcodes-Widgets-Gutenberg.md
 *
 * @package EstudosWP
 * @subpackage 06-Shortcodes-Widgets-Gutenberg
 */

if (!defined('ABSPATH')) {
    exit;
}

add_shortcode('estudos_wp_box', 'estudos_wp_shortcode_box');

function estudos_wp_shortcode_box($atts, $content = '') {
    $atts = shortcode_atts([
        'titulo' => '',
        'classe' => 'box-padrao',
    ], $atts, 'estudos_wp_box');
    $titulo = sanitize_text_field($atts['titulo']);
    $classe = esc_attr(sanitize_html_class($atts['classe']));
    $content = wp_kses_post($content);
    if (!empty($content)) {
        $content = do_shortcode($content);
    }
    $out = '<div class="' . $classe . '">';
    if ($titulo) {
        $out .= '<h3 class="box-titulo">' . esc_html($titulo) . '</h3>';
    }
    $out .= '<div class="box-conteudo">' . $content . '</div></div>';
    return $out;
}

// Uso: [estudos_wp_box titulo="Destaque"]Conteúdo aqui. Pode ter [outro_shortcode].[/estudos_wp_box]
