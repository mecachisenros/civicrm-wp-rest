<?php
/**
 * Base controller class.
 *
 * @since 0.1
 */

namespace CiviCRM_WP_REST\Controller;

use CiviCRM_WP_REST\Endpoint\Endpoint_Interface;

abstract class Base extends \WP_REST_Controller implements Endpoint_Interface {

	/**
	 * Gets the endpoint namespace.
	 *
	 * @since 0.1
	 * @return string $namespace
	 */
	public function get_namespace() {

		return self::NAMESPACE;

	}

	/**
	 * Gets the rest base route.
	 *
	 * @since 0.1
	 * @return string $rest_base
	 */
	public function get_rest_base() {

		return '/' . $this->rest_base;

	}

	/**
	 * Authorization status code.
	 *
	 * @since 0.1
	 * @return int $status
	 */
	protected function authorization_status_code() {

		$status = 401;

		if ( is_user_logged_in() ) $status = 403;

		return $status;

	}

}
