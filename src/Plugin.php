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

		add_filter( 'civicrm_alterMailParams', [ $this, 'replace_tracking_urls' ], 10, 2 );

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

	/**
	 * Filters the mailing html and replaces calls to 'extern/url.php' and
	 * 'extern/open.php' with their REST counterparts 'civicrm/v3/url' and 'civicrm/v3/open'.
	 *
	 * @uses 'civicrm_alterMailParams'
	 *
	 * @since 0.1
	 * @param array &$params Mail params
	 * @param string $context The Context
	 * @return array $params The filtered Mail params
	 */
	public function replace_tracking_urls( &$params, $context ) {

		if ( $context == 'civimail' && CIVICRM_WP_REST_REPLACE_MAILING_TRACKING ) {

			// track url endpoint
			$url_endpoint = rest_url( 'civicrm/v3/url' );
			// track opens endpoint
			$open_endpoint = rest_url( 'civicrm/v3/open' );

			// replace html extern url with endpoint
			$params['html'] = preg_replace( '/http.*civicrm\/extern\/url\.php/i', $url_endpoint, $params['html'] );
			// replace html extern open with endpoint
			$params['html'] = preg_replace( '/http.*civicrm\/extern\/open\.php/i', $open_endpoint, $params['html'] );

			// replace text extern url with endpoint
			$params['text'] = preg_replace( '/http.*civicrm\/extern\/url\.php/i', $url_endpoint, $params['text'] );
			// replace text extern open with endpoint
			$params['text'] = preg_replace( '/http.*civicrm\/extern\/open\.php/i', $open_endpoint, $params['text'] );

		}

		return $params;

	}

}
