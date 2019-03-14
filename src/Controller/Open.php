<?php
/**
 * Open controller class.
 *
 * @since 0.1
 */

namespace CiviCRM_WP_REST\Controller;

class Open extends Base {

	/**
	 * The base route.
	 *
	 * @since 0.1
	 * @var string
	 */
	protected $rest_base = 'open';

	/**
	 * Registers routes.
	 *
	 * @since 0.1
	 */
	public function register_routes() {

		register_rest_route( $this->get_namespace(), $this->get_rest_base(), [
			[
				'methods' => \WP_REST_Server::READABLE,
				'callback' => [ $this, 'get_item' ],
				'args' => $this->get_item_args()
			],
			'schema' => [ $this, 'get_item_schema' ]
		] );

	}

	/**
	 * Get item.
	 *
	 * @since 0.1
	 * @param WP_REST_Request $request
	 */
	public function get_item( $request ) {

		$queue_id = $request->get_param( 'q' );

		// track open
		\CRM_Mailing_Event_BAO_Opened::open( $queue_id );


	}

	/**
	 * Item schema.
	 *
	 * @since 0.1
	 * @return array $schema
	 */
	public function get_item_schema() {

		return [
			'$schema' => 'http://json-schema.org/draft-04/schema#',
			'title' => 'civicrm/v3/open',
			'description' => 'CiviCRM Open endpoint',
			'type' => 'object',
			'required' => [ 'q' ],
			'properties' => [
				'q' => [
					'type' => 'integer'
				]
			]
		];

	}

	/**
	 * Item arguments.
	 *
	 * @since 0.1
	 * @return array $arguments
	 */
	public function get_item_args() {

		return [
			'q' => [
				'type' => 'integer',
				'required' => true,
				'validate_callback' => function( $value, $request, $key ) {

					return is_numeric( $value );

				}
			]
		];

	}

}
