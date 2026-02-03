<?php
/**
 * Exemplo 04: Bloco Gutenberg dinâmico (apenas PHP, render_callback)
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   register_block_type( 'namespace/nome', [ 'render_callback' => callable, 'attributes' => [...] ] ).
 *   render_callback( $attributes, $content ) recebe atributos e retorna HTML; escapar sempre.
 *   No editor pode aparecer placeholder se não houver editor_script; para UI no editor precisa de JS (wp.blocks).
 *
 * @package EstudosWP
 * @subpackage 06-Shortcodes-Widgets-Gutenberg
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'estudos_wp_register_bloco_dinamico' );

function estudos_wp_register_bloco_dinamico() {
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	register_block_type(
		'estudos-wp/posts-recentes',
		array(
			'api_version'     => 2,
			'title'           => 'Estudos WP – Posts recentes',
			'description'     => 'Lista de posts recentes (bloco dinâmico).',
			'category'        => 'widgets',
			'icon'            => 'list-view',
			'keywords'        => array( 'posts', 'recentes' ),
			'attributes'      => array(
				'numero'  => array(
					'type'    => 'number',
					'default' => 5,
				),
				'titulo'  => array(
					'type'    => 'string',
					'default' => 'Posts recentes',
				),
			),
			'render_callback' => 'estudos_wp_render_bloco_posts_recentes',
		)
	);
}

/**
 * Render do bloco no front-end.
 *
 * @param array  $attributes Atributos do bloco.
 * @param string $content    Conteúdo interno (para blocos com InnerBlocks).
 * @return string HTML.
 */
function estudos_wp_render_bloco_posts_recentes( $attributes, $content = '' ) {
	$numero = isset( $attributes['numero'] ) ? absint( $attributes['numero'] ) : 5;
	$numero = max( 1, min( 20, $numero ) );
	$titulo = isset( $attributes['titulo'] ) ? $attributes['titulo'] : 'Posts recentes';
	$titulo = sanitize_text_field( $titulo );

	$query = new WP_Query(
		array(
			'posts_per_page' => $numero,
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);

	$html = '<div class="wp-block-estudos-wp-posts-recentes">';
	$html .= '<h3 class="estudos-wp-block-title">' . esc_html( $titulo ) . '</h3>';

	if ( $query->have_posts() ) {
		$html .= '<ul class="estudos-wp-block-posts-list">';
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
	} else {
		$html .= '<p>Nenhum post encontrado.</p>';
	}

	$html .= '</div>';

	wp_reset_postdata();
	return $html;
}
