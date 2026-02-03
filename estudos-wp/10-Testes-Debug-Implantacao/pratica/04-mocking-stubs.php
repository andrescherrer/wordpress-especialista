<?php
/**
 * Exemplo 04: Mocking e Stubs – createMock, expects/with, createStub, willReturn
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   Mock: createMock( Classe::class ); expects( $this->once() )->method( 'nome' )->with( $arg );
 *   Stub: createStub( Classe::class ); method( 'nome' )->willReturn( $valor );
 *   willThrowException( new \Exception() ), willReturnOnConsecutiveCalls( $a, $b ).
 *
 * @package EstudosWP
 * @subpackage 10-Testes-Debug-Implantacao
 */

namespace EstudosWP\Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Classe de exemplo (dependency).
 */
class EmailService {
	public function enviar( $to, $subject, $body ) {
		return wp_mail( $to, $subject, $body );
	}
}

/**
 * Classe sob teste (usa EmailService).
 */
class UserRegistrationService {
	/** @var EmailService */
	private $email;

	public function __construct( EmailService $email ) {
		$this->email = $email;
	}

	public function registrar( array $dados ) {
		$this->email->enviar(
			$dados['email'],
			'Bem-vindo',
			array( 'name' => $dados['name'] )
		);
		return true;
	}
}

/**
 * Testes com mock de EmailService.
 */
class EmailNotificationTest extends TestCase {

	/**
	 * @test
	 */
	public function envia_email_ao_registrar_usuario() {
		$email_mock = $this->createMock( EmailService::class );
		$email_mock->expects( $this->once() )
			->method( 'enviar' )
			->with(
				$this->stringContains( '@example.com' ),
				$this->stringContains( 'Bem-vindo' ),
				$this->isType( 'array' )
			);

		$service = new UserRegistrationService( $email_mock );
		$service->registrar( array(
			'name'  => 'André',
			'email' => 'andre@example.com',
		) );
	}

	/**
	 * @test
	 */
	public function stub_retorna_valor_configurado() {
		$gateway = $this->createStub( PaymentGatewayStub::class );
		$gateway->method( 'processar' )->willReturn( true );

		$this->assertTrue( $gateway->processar( 100.0, 'cart-123' ) );
	}

	/**
	 * @test
	 */
	public function stub_retorna_valores_diferentes_por_chamada() {
		$gateway = $this->createStub( PaymentGatewayStub::class );
		$gateway->method( 'obter_saldo' )->willReturnOnConsecutiveCalls( 100, 50, 0 );

		$this->assertEquals( 100, $gateway->obter_saldo() );
		$this->assertEquals( 50, $gateway->obter_saldo() );
		$this->assertEquals( 0, $gateway->obter_saldo() );
	}
}

/**
 * Classe stub de exemplo (gateway de pagamento).
 */
class PaymentGatewayStub {
	public function processar( $valor, $cart_id ) {
		return false;
	}

	public function obter_saldo() {
		return 0;
	}
}
