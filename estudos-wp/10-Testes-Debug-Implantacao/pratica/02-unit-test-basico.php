<?php
/**
 * Exemplo 02: Teste unitário básico – asserts e expectException
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   Estender PHPUnit\Framework\TestCase; métodos test* ou @test.
 *   assertEquals( $esperado, $real ), assertTrue, assertFalse, assertSame, assertInstanceOf.
 *   expectException( \Exception::class ) antes do código que deve lançar.
 *   Arrange / Act / Assert para organizar o teste.
 *
 * Copie para tests/Unit/ e ajuste: as funções formata_data, calcula_com_imposto, valida_email,
 * processa_dados devem existir no plugin ou ser carregadas no bootstrap.
 *
 * @package EstudosWP
 * @subpackage 10-Testes-Debug-Implantacao
 */

namespace EstudosWP\Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Testes de funções utilitárias (helpers).
 */
class UtilTest extends TestCase {

	/**
	 * @test
	 */
	public function formata_data_retorna_dd_mm_yyyy() {
		$data     = '2026-01-29';
		$resultado = formata_data( $data );
		$this->assertEquals( '29/01/2026', $resultado );
	}

	/**
	 * @test
	 */
	public function calcula_com_imposto_soma_15_porcento() {
		$valor   = 100.0;
		$imposto = 0.15;
		$total   = calcula_com_imposto( $valor, $imposto );
		$this->assertEqualsWithDelta( 115.0, $total, 0.01 );
		$this->assertIsFloat( $total );
	}

	/**
	 * @test
	 */
	public function valida_email_aceita_email_valido_e_rejeita_invalido() {
		$this->assertTrue( valida_email( 'andre@example.com' ) );
		$this->assertFalse( valida_email( 'email-invalido' ) );
		$this->assertFalse( valida_email( '' ) );
	}

	/**
	 * @test
	 */
	public function processa_dados_lanca_excecao_com_dados_invalidos() {
		$this->expectException( \InvalidArgumentException::class );
		processa_dados( null );
	}
}

// --- Funções de exemplo (implemente no plugin ou remova e use as suas) ---

if ( ! function_exists( 'formata_data' ) ) {
	function formata_data( $data ) {
		$ts = strtotime( $data );
		return $ts ? gmdate( 'd/m/Y', $ts ) : '';
	}
}

if ( ! function_exists( 'calcula_com_imposto' ) ) {
	function calcula_com_imposto( $valor, $imposto ) {
		return round( $valor * ( 1 + $imposto ), 2 );
	}
}

if ( ! function_exists( 'valida_email' ) ) {
	function valida_email( $email ) {
		return is_string( $email ) && '' !== $email && false !== filter_var( $email, FILTER_VALIDATE_EMAIL );
	}
}

if ( ! function_exists( 'processa_dados' ) ) {
	function processa_dados( $dados ) {
		if ( null === $dados ) {
			throw new \InvalidArgumentException( 'Dados inválidos' );
		}
		return $dados;
	}
}
