<?php
/**
 * Exemplo 01: Shortcode básico (atributos, sanitize, escape)
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   add_shortcode( 'tag', function( $atts, $content = '' ) { return $html; } );
 *   shortcode_atts( $defaults, $atts, 'tag' ); sanitize atributos; escape na saída (esc_html, esc_attr, esc_url).
 *
 * Uso: [estudos_wp_botao texto="Clique" url="https://exemplo.com"]
 *
 * @package EstudosWP
 * @subpackage 06-Shortcodes-Widgets-Gutenberg
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_shortcode( 'estudos_wp_botao', 'estudos_wp_shortcode_botao' );

function estudos_wp_shortcode_botao( $atts ) {
	$atts = shortcode_atts(
		array(
			'texto'   => 'Saiba mais',
			'url'     => '#',
			'cor'     => 'primary',
			'tamanho' => 'medium',
			'target'  => '_self',
			'class'   => '',
		),
		$atts,
		'estudos_wp_botao'
	);

	$texto   = sanitize_text_field( $atts['texto'] );
	$url     = esc_url( $atts['url'] );
	$cor     = sanitize_html_class( $atts['cor'] );
	$tamanho = sanitize_html_class( $atts['tamanho'] );
	$target  = in_array( $atts['target'], array( '_self', '_blank' ), true ) ? $atts['target'] : '_self';
	$class   = sanitize_html_class( $atts['class'] );

	if ( empty( $atts['url'] ) || $url === '#' ) {
		return '<!-- Shortcode botão: informe url -->';
	}

	return sprintf(
		'<a href="%s" class="estudos-wp-btn estudos-wp-btn-%s estudos-wp-btn-%s %s" target="%s" rel="noopener">%s</a>',
		$url,
		$cor,
		$tamanho,
		$class,
		esc_attr( $target ),
		esc_html( $texto )
	);
}

// Shortcode: posts recentes
add_shortcode( 'estudos_wp_posts_recentes', 'estudos_wp_shortcode_posts_recentes' );

function estudos_wp_shortcode_posts_recentes( $atts ) {
	$atts = shortcode_atts(
		array(
			'numero' => 5,
			'tipo'   => 'post',
		),
		$atts,
		'estudos_wp_posts_recentes'
	);

	$numero = absint( $atts['numero'] );
	$numero = $numero >= 1 && $numero <= 20 ? $numero : 5;
	$tipo   = sanitize_key( $atts['tipo'] );

	$query = new WP_Query(
		array(
			'post_type'      => $tipo,
			'posts_per_page' => $numero,
			'post_status'    => 'publish',
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);

	if ( ! $query->have_posts() ) {
		wp_reset_postdata();
		return '<p>Nenhum post encontrado.</p>';
	}

	$html = '<ul class="estudos-wp-posts-recentes">';
	while ( $query->have_posts() ) {
		$query->the_post();
		$html .= sprintf(
			'<li><a href="%s">%s</a> <span class="date">%s</span></li>',
			esc_url( get_permalink() ),
			esc_html( get_the_title() ),
			esc_html( get_the_date( 'd/m/Y' ) )
		);
	}
	$html .= '</ul>';

	wp_reset_postdata();
	return $html;
}
