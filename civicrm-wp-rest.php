<?php
/**
 * Plugin Name: CiviCRM WP REST API
 * Description: WordPress REST endpoints for CiviCRM's 'extern' endpoints.
 * Version: 0.1
 * Author: Andrei Mondoc
 * Author URI: https://github.com/mecachisenros
 * Plugin URI: https://github.com/mecachisenros/civicrm-wp-rest
 * GitHub Plugin URI: mecachisenros/civicrm-wp-rest
 */

if ( ! defined( 'WPINC' ) ) die( 'Silence...' );

// version
define( 'CIVICRM_WP_REST_VERSION', '0.1' );
// plugin basename
define( 'CIVICRM_WP_REST_BASENAME', plugin_basename( __FILE__ ) );
// plugin path
define( 'CIVICRM_WP_REST_PATH', plugin_dir_path( __FILE__ ) );
// source path
define( 'CIVICRM_WP_REST_SRC', trailingslashit( CIVICRM_WP_REST_PATH . 'src' ) );
// plugin url
define( 'CIVICRM_WP_REST_URL', plugin_dir_url( __FILE__ ) );

/**
 * Setting this to 'true' will replace all mailing URLs calls to 'extern/url.php' 
 * and 'extern/open.php' with their REST counterpart 'civicrm/v3/url' and 'civicrm/v3/open'.
 *
 * Use for test purposes, may affect mailing 
 * performance, see Plugin->replace_tracking_urls() method.
 */
define( 'CIVICRM_WP_REST_REPLACE_MAILING_TRACKING', false );

// init
add_action( 'init', function() {

	if ( ! function_exists( 'civi_wp' ) ) return;
	
	// autoloader
	require_once( CIVICRM_WP_REST_SRC . 'Autoloader.php' );
	CiviCRM_WP_REST\Autoloader::add_source( $source_path = CIVICRM_WP_REST_SRC );

	// init
	new CiviCRM_WP_REST\Plugin;

} );