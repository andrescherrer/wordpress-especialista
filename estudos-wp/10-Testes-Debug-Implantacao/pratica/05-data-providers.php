<?php
/**
 * Exemplo 05: Data Providers – @dataProvider para múltiplos casos
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   No docblock do teste: @dataProvider nomeDoMetodo
 *   Método estático que retorna array: [ 'cenário' => [ $arg1, $arg2 ], ... ]
 *   O teste recebe os argumentos: function testAlgo( $arg1, $arg2 ) { ... }
 *
 * @package EstudosWP
 * @subpackage 10-Testes-Debug-Implantacao
 */

namespace EstudosWP\Tests\Unit\Validation;

use PHPUnit\Framework\TestCase;

/**
 * Testes de validação de email com data provider.
 */
class EmailValidationTest extends TestCase {

	/**
	 * @test
	 * @dataProvider emails_validos
	 */
	public function aceita_emails_validos( $email ) {
		$this->assertTrue( valida_email( $email ) );
	}

	/**
	 * @test
	 * @dataProvider emails_invalidos
	 */
	public function rejeita_emails_invalidos( $email ) {
		$this->assertFalse( valida_email( $email ) );
	}

	/**
	 * Data provider: emails válidos.
	 *
	 * @return array<string, array<string>>
	 */
	public static function emails_validos(): array {
		return array(
			'email simples'     => array( 'andre@example.com' ),
			'email com ponto'   => array( 'andre.silva@example.com' ),
			'email com número'  => array( 'andre123@example.com' ),
			'email subdomínio'  => array( 'andre@mail.example.com' ),
			'email com hífen'   => array( 'andre-silva@example.com' ),
		);
	}

	/**
	 * Data provider: emails inválidos.
	 *
	 * @return array<string, array<string>>
	 */
	public static function emails_invalidos(): array {
		return array(
			'sem @'           => array( 'andreexample.com' ),
			'sem domínio'     => array( 'andre@' ),
			'sem usuário'     => array( '@example.com' ),
			'espaços'         => array( 'andre @example.com' ),
			'vazio'           => array( '' ),
			'espaço no domínio' => array( 'andre@exa mple.com' ),
		);
	}
}

if ( ! function_exists( 'valida_email' ) ) {
	function valida_email( $email ) {
		return is_string( $email ) && '' !== $email && false !== filter_var( $email, FILTER_VALIDATE_EMAIL );
	}
}
