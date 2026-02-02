<?php
/**
 * Exemplo 03: Validator e sanitização de saída
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   Validator: métodos estáticos que retornam true ou new WP_Error( 'code', 'msg', [ 'status' => 400 ] ).
 *   validate_email, validate_url, validate_integer( $value, $min, $max ), validate_string( $value, $min_len, $max_len ), validate_enum( $value, $allowed ).
 *   Sanitização de saída: esc_html, esc_url, wp_kses_post ao montar resposta da API.
 *
 * @package EstudosWP
 * @subpackage 03-REST-API-Avancado
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Validação reutilizável para REST API.
 */
class Estudos_WP_REST_Validator {

	public static function validate_email( $email ) {
		if ( ! is_email( $email ) ) {
			return new WP_Error(
				'invalid_email',
				'Email inválido',
				array( 'status' => 400 )
			);
		}
		return true;
	}

	public static function validate_url( $url ) {
		if ( ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
			return new WP_Error(
				'invalid_url',
				'URL inválida',
				array( 'status' => 400 )
			);
		}
		return true;
	}

	public static function validate_integer( $value, $min = null, $max = null ) {
		if ( ! is_numeric( $value ) || (int) $value != $value ) {
			return new WP_Error(
				'invalid_integer',
				'Deve ser um número inteiro',
				array( 'status' => 400 )
			);
		}
		$int = (int) $value;
		if ( $min !== null && $int < $min ) {
			return new WP_Error( 'value_too_small', "Valor mínimo: $min", array( 'status' => 400 ) );
		}
		if ( $max !== null && $int > $max ) {
			return new WP_Error( 'value_too_large', "Valor máximo: $max", array( 'status' => 400 ) );
		}
		return true;
	}

	public static function validate_string( $value, $min_length = 0, $max_length = null ) {
		if ( ! is_string( $value ) ) {
			return new WP_Error( 'invalid_string', 'Deve ser texto', array( 'status' => 400 ) );
		}
		$len = strlen( $value );
		if ( $len < $min_length ) {
			return new WP_Error( 'string_too_short', "Mínimo $min_length caracteres", array( 'status' => 400 ) );
		}
		if ( $max_length !== null && $len > $max_length ) {
			return new WP_Error( 'string_too_long', "Máximo $max_length caracteres", array( 'status' => 400 ) );
		}
		return true;
	}

	public static function validate_enum( $value, array $allowed ) {
		if ( ! in_array( $value, $allowed, true ) ) {
			return new WP_Error(
				'invalid_enum',
				'Valor inválido. Permitidos: ' . implode( ', ', $allowed ),
				array( 'status' => 400 )
			);
		}
		return true;
	}
}

/**
 * Sanitização de saída para respostas da API (evitar XSS).
 */
class Estudos_WP_REST_Sanitizer {

	/**
	 * Escapar texto para exibição em HTML.
	 *
	 * @param string $text Texto.
	 * @return string
	 */
	public static function escape_html( $text ) {
		return esc_html( $text );
	}

	/**
	 * Escapar URL para uso seguro.
	 *
	 * @param string $url URL.
	 * @return string
	 */
	public static function escape_url( $url ) {
		return esc_url( $url );
	}

	/**
	 * Conteúdo HTML permitido (post content).
	 *
	 * @param string $html HTML.
	 * @return string
	 */
	public static function kses_post( $html ) {
		return wp_kses_post( $html );
	}

	/**
	 * Preparar item para resposta: aplicar escape em strings.
	 *
	 * @param array $item Associativo com valores escalares.
	 * @return array
	 */
	public static function prepare_item_for_response( array $item ) {
		$out = array();
		foreach ( $item as $key => $value ) {
			if ( is_string( $value ) ) {
				$out[ $key ] = esc_html( $value );
			} elseif ( is_array( $value ) ) {
				$out[ $key ] = self::prepare_item_for_response( $value );
			} else {
				$out[ $key ] = $value;
			}
		}
		return $out;
	}
}

// Uso no controller (validação antes de salvar):
/*
$v = Estudos_WP_REST_Validator::validate_email( $request->get_param( 'email' ) );
if ( is_wp_error( $v ) ) return $v;

$v = Estudos_WP_REST_Validator::validate_string( $request->get_param( 'nome' ), 2, 100 );
if ( is_wp_error( $v ) ) return $v;

// Na resposta, escapar campos que vêm do usuário:
return [ 'title' => Estudos_WP_REST_Sanitizer::escape_html( $post->post_title ) ];
*/
