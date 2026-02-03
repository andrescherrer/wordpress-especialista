<?php
/**
 * REFERÊNCIA RÁPIDA – Archive com filtro por termo (tax_query na URL)
 *
 * URL: /produtos/?categoria=livros ou ?term_slug=livros; get_query_var ou $_GET.
 * WP_Query com tax_query (taxonomy, field, terms); single: wp_get_post_terms($post_id, 'categoria').
 *
 * Fonte: 005-WordPress-Fase-5-CPT-Taxonomias.md
 *
 * @package EstudosWP
 * @subpackage 05-CPT-Taxonomias
 */

if (!defined('ABSPATH')) {
    exit;
}

// Em archive-produto.php (ou template do tema):
// $term_slug = get_query_var('categoria') ?: (isset($_GET['categoria']) ? sanitize_text_field($_GET['categoria']) : '');
// $args = ['post_type' => 'produto', 'post_status' => 'publish', 'posts_per_page' => 10];
// if ($term_slug) {
//     $args['tax_query'] = [['taxonomy' => 'categoria', 'field' => 'slug', 'terms' => $term_slug]];
// }
// $query = new WP_Query($args);

// Registrar query var para uso em links
add_filter('query_vars', function ($vars) {
    $vars[] = 'categoria';
    return $vars;
});

// Exemplo de listagem com filtro (para usar em shortcode ou template)
function estudos_wp_produtos_por_categoria($term_slug = '') {
    $args = [
        'post_type'      => 'produto',
        'post_status'    => 'publish',
        'posts_per_page' => 10,
    ];
    if (!empty($term_slug)) {
        $args['tax_query'] = [
            [
                'taxonomy' => 'categoria',
                'field'    => 'slug',
                'terms'    => sanitize_title($term_slug),
            ],
        ];
    }
    $query = new WP_Query($args);
    return $query;
}

// No single: termos do post
// $termos = wp_get_post_terms(get_the_ID(), 'categoria');
// foreach ($termos as $term) { echo esc_html($term->name); }
