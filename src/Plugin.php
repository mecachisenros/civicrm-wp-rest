<?php
/**
 * Main plugin class.
 *
 * @since 0.1
 */

namespace CiviCRM_WP_REST;

class Plugin {

	/**
	 * Constructor.
	 *
	 * @since 0.1
	 */
	public function __construct() {

		$this->register_hooks();

	}

	/**
	 * Register hooks.
	 *
	 * @since 1.0
	 */
	protected function register_hooks() {

		add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );

	}

	/**
	 * Registers Rest API routes.
	 *
	 * @since 0.1
	 */
	public function register_rest_routes() {

		// rest endpoint
		$rest_controller = new Controller\Rest;
		$rest_controller->register_routes();
		
		// url controller
		$url_controller = new Controller\Url;
		$url_controller->register_routes();

		// open controller
		$open_controller = new Controller\Open;
		$open_controller->register_routes();

		/**
		 * Opportunity to add more rest routes.
		 *
		 * @since 0.1
		 */
		do_action( 'civi_wp_rest/plugin/rest_routes_registered' );

	}

}
