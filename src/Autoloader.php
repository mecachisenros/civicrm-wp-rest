<?php
/**
 * Autoloader class.
 *
 * @since 0.1
 */

namespace CiviCRM_WP_REST;

class Autoloader {

	/**
	 * Namespace.
	 *
	 * @since 0.1
	 * @var string
	 */
	private $namespace = 'CiviCRM_WP_REST';

	/**
	 * Plugin source path.
	 *
	 * @since 0.1
	 * @var string
	 */
	private $source_path;

	/**
	 * Constructor.
	 *
	 * @since 0.1
	 * @param string $namespace The plugin namespace
	 * @param string $source_path The plugin source path
	 */
	public function __construct( $source_path ) {

		$this->source_path = $source_path;
		$this->register_autoloader();

	}

	/**
	 * Registers the autoloader.
	 *
	 * @since 0.1
	 * @return bool Wehather the autoloader has been registered or not
	 */
	private function register_autoloader() {

		return spl_autoload_register( [ $this, 'autoload' ] );

	}

	/**
	 * Loads the classes.
	 *
	 * @since 0.1
	 * @param string $class_name The class name to load
	 */
	private function autoload( $class_name ) {

		if ( false === strpos( $class_name, $this->namespace ) ) return;

		$parts = explode( '\\', $class_name );

		// remove namespace and join class path
		$class_path = str_replace( '_', '-', implode( DIRECTORY_SEPARATOR, array_slice( $parts, 1 ) ) );

		$path = $this->source_path . DIRECTORY_SEPARATOR . $class_path . '.php';

		// require file
		if ( file_exists( $path ) ) require $path;

	}

}
