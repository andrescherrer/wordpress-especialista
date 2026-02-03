<?php
/**
 * Exemplo 03: Output formatado em comandos WP-CLI
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   WP_CLI::log() | success() | warning() | error( $msg, $exit = true )
 *   WP_CLI::table( $headers, $rows )  — $rows = array of arrays
 *   WP_CLI::line( '%GVerde%n %Rvermelho%n %YAmarelo%n %BAzul%n' )
 *   WP_CLI::confirm( 'Continuar?' )
 *   $bar = \WP_CLI\Utils\make_progress_bar( 'Label', $total ); $bar->tick(); $bar->finish();
 *
 * Uso: wp exemplo-output demo
 *
 * @package EstudosWP
 * @subpackage 07-WP-CLI-Fundamentos
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

/**
 * Comando de demonstração de output.
 */
class Estudos_WP_CLI_Output_Command {

	/**
	 * Mostrar todos os tipos de output.
	 *
	 * ## EXAMPLES
	 *
	 *     wp exemplo-output demo
	 *
	 * @when after_wp_load
	 */
	public function demo( $args, $assoc_args ) {
		WP_CLI::log( 'Mensagem informativa (log)' );
		WP_CLI::success( 'Operação completada com sucesso!' );
		WP_CLI::warning( 'Cuidado: isso pode levar tempo' );
		// WP_CLI::error( 'Erro grave!', false ); // false = não encerra

		// Tabela
		$items = array(
			array( 'id' => 1, 'nome' => 'João', 'email' => 'joao@exemplo.com' ),
			array( 'id' => 2, 'nome' => 'Maria', 'email' => 'maria@exemplo.com' ),
		);
		WP_CLI::table(
			array( 'id', 'nome', 'email' ),
			array_map(
				function ( $item ) {
					return array( $item['id'], $item['nome'], $item['email'] );
				},
				$items
			)
		);

		// Cores
		WP_CLI::line( '%GVerde%n %R Vermelho%n %Y Amarelo%n %B Azul%n' );

		// Progress bar (exemplo curto)
		$progress = \WP_CLI\Utils\make_progress_bar( 'Processando', 10 );
		for ( $i = 0; $i < 10; $i++ ) {
			usleep( 100000 );
			$progress->tick();
		}
		$progress->finish();
		WP_CLI::log( '' );
	}
}

WP_CLI::add_command( 'exemplo-output', 'Estudos_WP_CLI_Output_Command' );
