<?php
/**
 * Exemplo 03: Testes de classe (service) – setUp, instância, vários métodos
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   setUp(): void { parent::setUp(); $this->service = new MinhaClasse(); } – roda antes de cada teste.
 *   assertInstanceOf( Classe::class, $obj ), assertEquals, assertTrue.
 *   expectException antes de chamada que deve lançar.
 *
 * A classe UserService e o modelo User são exemplos; substitua pelo seu service/model ou
 * defina stubs no próprio arquivo de teste.
 *
 * @package EstudosWP
 * @subpackage 10-Testes-Debug-Implantacao
 */

namespace EstudosWP\Tests\Unit\Services;

use PHPUnit\Framework\TestCase;

/**
 * Classe de exemplo (simula um service).
 */
class UserService {
	public function criar( array $dados ) {
		if ( empty( $dados['name'] ) || empty( $dados['email'] ) ) {
			throw new \Exception( 'Dados inválidos' );
		}
		if ( ! filter_var( $dados['email'], FILTER_VALIDATE_EMAIL ) ) {
			throw new \Exception( 'Email inválido' );
		}
		return (object) array(
			'id'    => 1,
			'name'  => $dados['name'],
			'email' => $dados['email'],
		);
	}

	public function encontra_por_id( $id ) {
		return (object) array( 'id' => $id, 'name' => 'Test', 'email' => 'test@example.com' );
	}
}

/**
 * Testes do UserService.
 */
class UserServiceTest extends TestCase {

	/** @var UserService */
	private $user_service;

	protected function setUp(): void {
		parent::setUp();
		$this->user_service = new UserService();
	}

	/**
	 * @test
	 */
	public function criar_retorna_objeto_usuario_com_dados_corretos() {
		$dados = array(
			'name'  => 'André Silva',
			'email' => 'andre@example.com',
		);

		$usuario = $this->user_service->criar( $dados );

		$this->assertIsObject( $usuario );
		$this->assertEquals( 'André Silva', $usuario->name );
		$this->assertEquals( 'andre@example.com', $usuario->email );
		$this->assertObjectHasProperty( 'id', $usuario );
	}

	/**
	 * @test
	 */
	public function criar_lanca_excecao_com_dados_invalidos() {
		$this->expectException( \Exception::class );

		$this->user_service->criar( array(
			'name'  => '',
			'email' => 'email-invalido',
		) );
	}

	/**
	 * @test
	 */
	public function encontra_por_id_retorna_objeto_usuario() {
		$usuario = $this->user_service->encontra_por_id( 1 );

		$this->assertIsObject( $usuario );
		$this->assertEquals( 1, $usuario->id );
	}
}
