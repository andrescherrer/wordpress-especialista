<?php
/**
 * Exemplo 05: Migrations – migrate, rollback, list
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   Listar: glob( migrations/*.php ); ordenar; executadas em get_option( 'meu_plugin_migrations', array() ).
 *   Executar: require arquivo; classe Migration_Nome com up(); adicionar nome à opção.
 *   Rollback: última na opção; método down(); remover da opção.
 *   --step=N: array_slice( pendentes, 0, N ).
 *
 * Uso: wp meu-plugin migrate [--step=1] | wp meu-plugin rollback | wp meu-plugin migrations list
 *
 * @package EstudosWP
 * @subpackage 09-WP-CLI-Ferramentas
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

/**
 * Sistema de migrations: migrate, rollback, list.
 */
class Estudos_WP_CLI_Migration_Command {

	private function get_migrations_dir() {
		return defined( 'MEU_PLUGIN_PATH' ) ? MEU_PLUGIN_PATH . 'migrations' : dirname( dirname( __FILE__ ) ) . '/migrations';
	}

	private function get_all_migrations() {
		$dir   = $this->get_migrations_dir();
		$files = is_dir( $dir ) ? glob( $dir . '/*.php' ) : array();
		$list  = array();
		foreach ( $files as $f ) {
			$list[] = basename( $f, '.php' );
		}
		sort( $list );
		return $list;
	}

	private function get_executed_migrations() {
		$opt = get_option( 'meu_plugin_migrations', array() );
		return is_array( $opt ) ? $opt : array();
	}

	private function get_pending_migrations() {
		return array_diff( $this->get_all_migrations(), $this->get_executed_migrations() );
	}

	/**
	 * Executar migrações pendentes.
	 *
	 * ## OPTIONS
	 *
	 * [--step=<n>]
	 * : Quantas migrações executar
	 * ---
	 * default: 999
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     wp meu-plugin migrate
	 *     wp meu-plugin migrate --step=1
	 *
	 * @when after_wp_load
	 */
	public function migrate( $args, $assoc_args ) {
		$step = isset( $assoc_args['step'] ) ? absint( $assoc_args['step'] ) : 999;
		$pending = array_values( $this->get_pending_migrations() );
		$pending = array_slice( $pending, 0, $step );

		if ( empty( $pending ) ) {
			WP_CLI::log( 'Nenhuma migração pendente.' );
			return;
		}

		$progress = \WP_CLI\Utils\make_progress_bar( 'Migrando', count( $pending ) );
		foreach ( $pending as $migration ) {
			$this->run_migration( $migration, 'up' );
			$executed = $this->get_executed_migrations();
			$executed[] = $migration;
			update_option( 'meu_plugin_migrations', $executed );
			$progress->tick();
		}
		$progress->finish();
		WP_CLI::success( 'Migrações executadas!' );
	}

	/**
	 * Fazer rollback da última migração.
	 *
	 * ## EXAMPLES
	 *
	 *     wp meu-plugin rollback
	 *
	 * @when after_wp_load
	 */
	public function rollback( $args, $assoc_args ) {
		$executed = $this->get_executed_migrations();
		if ( empty( $executed ) ) {
			WP_CLI::log( 'Nenhuma migração para rollback.' );
			return;
		}
		$last = end( $executed );
		WP_CLI::confirm( "Fazer rollback de {$last}?" );
		$this->run_migration( $last, 'down' );
		$key = array_search( $last, $executed, true );
		unset( $executed[ $key ] );
		update_option( 'meu_plugin_migrations', array_values( $executed ) );
		WP_CLI::success( 'Rollback executado!' );
	}

	/**
	 * Listar migrações (status e nome).
	 *
	 * ## EXAMPLES
	 *
	 *     wp meu-plugin migrations list
	 *
	 * @when after_wp_load
	 */
	public function list_migrations( $args, $assoc_args ) {
		$all     = $this->get_all_migrations();
		$executed = $this->get_executed_migrations();
		$items   = array();
		foreach ( $all as $m ) {
			$items[] = array(
				'status'    => in_array( $m, $executed, true ) ? '✓' : '✗',
				'migration' => $m,
			);
		}
		\WP_CLI\Utils\format_items( 'table', $items, array( 'status', 'migration' ) );
	}

	private function run_migration( $migration, $method ) {
		$dir  = $this->get_migrations_dir();
		$file = $dir . '/' . $migration . '.php';
		if ( ! file_exists( $file ) ) {
			throw new Exception( "Arquivo não encontrado: {$migration}" );
		}
		require_once $file;
		$class = 'Meu_Plugin_Migration_' . str_replace( array( '-', '/' ), '_', $migration );
		if ( ! class_exists( $class ) ) {
			throw new Exception( "Classe não encontrada: {$class}" );
		}
		$instance = new $class();
		if ( ! method_exists( $instance, $method ) ) {
			throw new Exception( "Método {$method} não existe em {$class}" );
		}
		$instance->$method();
	}
}

WP_CLI::add_command( 'meu-plugin migrate', array( 'Estudos_WP_CLI_Migration_Command', 'migrate' ) );
WP_CLI::add_command( 'meu-plugin rollback', array( 'Estudos_WP_CLI_Migration_Command', 'rollback' ) );
WP_CLI::add_command( 'meu-plugin migrations list', array( 'Estudos_WP_CLI_Migration_Command', 'list_migrations' ) );
