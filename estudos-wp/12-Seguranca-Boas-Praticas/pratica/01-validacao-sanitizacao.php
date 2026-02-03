<?php
/**
 * Exemplo 01: Validação e sanitização de entrada
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   sanitize_email() + is_email(); sanitize_text_field() / sanitize_textarea_field();
 *   esc_url_raw() para URL a salvar; filter_var( $url, FILTER_VALIDATE_URL );
 *   absint() para inteiros; sanitize_key() para chaves; whitelist com in_array( $v, $allowed, true ).
 *
 * @package EstudosWP
 * @subpackage 12-Seguranca-Boas-Praticas
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Validar e sanitizar dados de formulário.
 */
class Estudos_WP_Security_Validator {

	/**
	 * Valida e sanitiza dados; retorna array com success, data e errors.
	 *
	 * @param array $data Dados brutos (ex.: $_POST).
	 * @return array{ success: bool, data: array, errors: array }
	 */
	public function validate_form_data( $data ) {
		$validated = array();
		$errors    = array();

		if ( isset( $data['email'] ) ) {
			$email = sanitize_email( $data['email'] );
			if ( ! is_email( $email ) ) {
				$errors['email'] = __( 'E-mail inválido', 'meu-plugin' );
			} else {
				$validated['email'] = $email;
			}
		}

		if ( isset( $data['url'] ) ) {
			$url = esc_url_raw( $data['url'] );
			if ( empty( $url ) || ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
				$errors['url'] = __( 'URL inválida', 'meu-plugin' );
			} else {
				$validated['url'] = $url;
			}
		}

		if ( isset( $data['name'] ) ) {
			$name = sanitize_text_field( $data['name'] );
			if ( '' === $name ) {
				$errors['name'] = __( 'Nome é obrigatório', 'meu-plugin' );
			} elseif ( strlen( $name ) < 3 ) {
				$errors['name'] = __( 'Nome deve ter pelo menos 3 caracteres', 'meu-plugin' );
			} else {
				$validated['name'] = $name;
			}
		}

		if ( isset( $data['description'] ) ) {
			$description = sanitize_textarea_field( $data['description'] );
			if ( '' === $description ) {
				$errors['description'] = __( 'Descrição é obrigatória', 'meu-plugin' );
			} else {
				$validated['description'] = $description;
			}
		}

		if ( isset( $data['quantity'] ) ) {
			$quantity = absint( $data['quantity'] );
			if ( $quantity <= 0 ) {
				$errors['quantity'] = __( 'Quantidade deve ser maior que zero', 'meu-plugin' );
			} else {
				$validated['quantity'] = $quantity;
			}
		}

		// Whitelist para status
		if ( isset( $data['status'] ) ) {
			$allowed = array( 'draft', 'published', 'archived' );
			$status  = sanitize_key( $data['status'] );
			if ( ! in_array( $status, $allowed, true ) ) {
				$errors['status'] = __( 'Status inválido', 'meu-plugin' );
			} else {
				$validated['status'] = $status;
			}
		}

		if ( isset( $data['tags'] ) && is_array( $data['tags'] ) ) {
			$validated['tags'] = array_map( 'sanitize_text_field', $data['tags'] );
		}

		return array(
			'success' => empty( $errors ),
			'data'    => $validated,
			'errors'  => $errors,
		);
	}
}
