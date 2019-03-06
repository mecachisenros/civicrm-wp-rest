<?php
/**
 * Base controller class.
 *
 * @since 0.1
 */

namespace CiviCRM_WP_REST\Controller;

abstract class Base extends \WP_REST_Controller {

	/**
	 * Route namespace.
	 *
	 * @since 0.1
	 * @var string
	 */
	protected $namespace = 'civicrm/v3';

}
