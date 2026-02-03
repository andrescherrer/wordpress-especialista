<?php
/**
 * Exemplo 02: Shortcode com atributos e conteúdo interno
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   Callback( $atts, $content = '' ); shortcode_atts para defaults; wp_kses_post( $content ) se permitir HTML.
 *   Para aninhados: do_shortcode( $content ) processa shortcodes dentro do conteúdo.
 *
 * Uso: [estudos_wp_alert tipo="info" titulo="Atenção"]Conteúdo da mensagem.[/estudos_wp_alert]
 *
 * @package EstudosWP
 * @subpackage 06-Shortcodes-Widgets-Gutenberg
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_shortcode( 'estudos_wp_alert', 'estudos_wp_shortcode_alert' );

function estudos_wp_shortcode_alert( $atts, $content = '' ) {
	$atts = shortcode_atts(
		array(
			'tipo'   => 'info',
			'titulo' => '',
			'fechar' => 'sim',
		),
		$atts,
		'estudos_wp_alert'
	);

	$tipos_permitidos = array( 'info', 'success', 'warning', 'error' );
	$tipo   = in_array( $atts['tipo'], $tipos_permitidos, true ) ? $atts['tipo'] : 'info';
	$titulo = sanitize_text_field( $atts['titulo'] );
	$fechar = strtolower( $atts['fechar'] ) === 'sim';
	$content = wp_kses_post( $content );

	$classes = array( 'estudos-wp-alert', 'estudos-wp-alert-' . $tipo );
	if ( $fechar ) {
		$classes[] = 'estudos-wp-alert-dismissible';
	}

	$html = '<div class="' . esc_attr( implode( ' ', $classes ) ) . '" role="alert">';

	if ( $titulo !== '' ) {
		$html .= '<strong class="estudos-wp-alert-title">' . esc_html( $titulo ) . '</strong> ';
	}

	$html .= '<span class="estudos-wp-alert-content">' . $content . '</span>';

	if ( $fechar ) {
		$html .= ' <button type="button" class="estudos-wp-alert-close" aria-label="Fechar">&times;</button>';
	}

	$html .= '</div>';

	return $html;
}
