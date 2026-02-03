<?php
/**
 * Exemplo 04: Scaffolding – gerar módulos (class, service, controller)
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   Template com placeholders {CLASS_NAME}; str_replace; file_put_contents( $path, $content ).
 *   Criar dir: if ( ! is_dir( $dir ) ) mkdir( $dir, 0755, true );
 *   Nome da classe: str_replace( ['-','_'], '', ucwords( $name, '-_' ) ); arquivo: kebab-case.
 *
 * Uso: wp meu-plugin scaffold module NomeDoModulo [--type=service|controller]
 *
 * @package EstudosWP
 * @subpackage 09-WP-CLI-Ferramentas
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

/**
 * Scaffold: gerar classe, service ou controller.
 */
class Estudos_WP_CLI_Scaffold_Command {

	/**
	 * Gerar módulo.
	 *
	 * ## OPTIONS
	 *
	 * <name>
	 * : Nome do módulo (ex: Payment)
	 *
	 * [--type=<type>]
	 * : class, service ou controller
	 * ---
	 * default: class
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     wp meu-plugin scaffold module Payment
	 *     wp meu-plugin scaffold module Payment --type=service
	 *
	 * @when after_wp_load
	 */
	public function module( $args, $assoc_args ) {
		$name = isset( $args[0] ) ? $args[0] : '';
		if ( empty( $name ) ) {
			WP_CLI::error( 'Informe o nome do módulo.' );
		}

		$type = isset( $assoc_args['type'] ) ? $assoc_args['type'] : 'class';
		$class_name = $this->sanitize_class_name( $name );

		$base = defined( 'MEU_PLUGIN_PATH' ) ? MEU_PLUGIN_PATH : dirname( dirname( __FILE__ ) );
		$base = rtrim( $base, '/' );

		switch ( $type ) {
			case 'service':
				$this->generate_service( $class_name, $base );
				break;
			case 'controller':
				$this->generate_controller( $class_name, $base );
				break;
			default:
				$this->generate_class( $class_name, $base );
		}

		WP_CLI::success( "Módulo {$class_name} criado!" );
	}

	private function generate_class( $name, $base ) {
		$template = <<<'PHP'
<?php
/**
 * {CLASS_NAME} Class
 */

namespace MeuPlugin;

class {CLASS_NAME} {

	public function __construct() {
		$this->init();
	}

	public function init() {
		// Implementar
	}

	public function example() {
		// Implementar
	}
}

PHP;
		$template = str_replace( '{CLASS_NAME}', $name, $template );
		$dir      = $base . '/includes';
		$file     = $dir . '/class-' . $this->class_to_file( $name ) . '.php';
		$this->write_file( $file, $template );
	}

	private function generate_service( $name, $base ) {
		$template = <<<'PHP'
<?php
/**
 * {CLASS_NAME} Service
 */

namespace MeuPlugin\Services;

class {CLASS_NAME} {

	private static $instance;

	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->init();
	}

	private function init() {
		// Implementar
	}
}

PHP;
		$template = str_replace( '{CLASS_NAME}', $name, $template );
		$dir      = $base . '/includes/Services';
		$file     = $dir . '/class-' . $this->class_to_file( $name ) . '.php';
		$this->write_file( $file, $template );
	}

	private function generate_controller( $name, $base ) {
		$template = <<<'PHP'
<?php
/**
 * {CLASS_NAME} Controller
 */

namespace MeuPlugin\Controllers;

class {CLASS_NAME} {

	public function handle( $request ) {
		return array(
			'status' => 'success',
			'data'   => array(),
		);
	}
}

PHP;
		$template = str_replace( '{CLASS_NAME}', $name, $template );
		$dir      = $base . '/includes/Controllers';
		$file     = $dir . '/class-' . $this->class_to_file( $name ) . '.php';
		$this->write_file( $file, $template );
	}

	private function write_file( $path, $content ) {
		$dir = dirname( $path );
		if ( ! is_dir( $dir ) ) {
			mkdir( $dir, 0755, true );
		}
		if ( file_exists( $path ) ) {
			WP_CLI::error( "Arquivo já existe: {$path}" );
		}
		file_put_contents( $path, $content );
	}

	private function sanitize_class_name( $name ) {
		return str_replace( array( '-', '_', ' ' ), '', ucwords( $name, '-_ ' ) );
	}

	private function class_to_file( $name ) {
		return strtolower( preg_replace( '/([a-z])([A-Z])/', '$1-$2', $name ) );
	}
}

WP_CLI::add_command( 'meu-plugin scaffold module', array( 'Estudos_WP_CLI_Scaffold_Command', 'module' ) );
