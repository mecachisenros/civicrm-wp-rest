<?php
/**
 * Rest controller class.
 *
 * @since 0.1
 */

namespace CiviCRM_WP_REST\Controller;

class Rest extends Base {

	/**
	 * The base route.
	 *
	 * @since 0.1
	 * @var string
	 */
	protected $rest_base = 'rest';

	/**
	 * Registers routes.
	 *
	 * @since 0.1
	 */
	public function register_routes() {

		register_rest_route( $this->get_namespace(), $this->get_rest_base(), [
			[
				'methods' => \WP_REST_Server::ALLMETHODS,
				'callback' => [ $this, 'get_items' ],
				'permission_callback' => [ $this, 'permissions_check' ],
				'args' => $this->get_item_args()
			],
			'schema' => [ $this, 'get_item_schema' ]
		] );

	}

	/**
	 * Check get permission.
	 *
	 * @since 0.1
	 * @param WP_REST_Request $request
	 * @return bool
	 */
	public function permissions_check( $request ) {

		if ( ! $this->is_valid_api_key( $request ) )
			return new \WP_Error( 'rest_forbidden', __( 'Param api_key is not valid.' ), [ 'status' => $this->authorization_status_code() ] );

		if ( ! $this->is_valid_site_key() )
			return new \WP_Error( 'rest_forbidden', __( 'Param key is not valid.' ), [ 'status' => $this->authorization_status_code() ] );

		return true;

	}

	/**
	 * Get items.
	 *
	 * @since 0.1
	 * @param WP_REST_Request $request
	 */
	public function get_items( $request ) {

		/**
		 * Filter formatted api params.
		 *
		 * @since 0.1
		 * @param array $params
		 * @param WP_REST_Request $request
		 */
		$params = apply_filters( 'civi_wp_rest/controller/rest/api_params', $this->get_formatted_api_params( $request ), $request );

		try {

			$items = civicrm_api3( ...$params );

		} catch ( \CiviCRM_API3_Exception $e ) {

			return new \WP_Error( 'civicrm_rest_api_error', $e->getMessage(), [ 'status' => $this->authorization_status_code() ] );

		}

		if ( ! isset( $items ) || empty( $items ) )
			return rest_ensure_response( [] );

		/**
		 * Filter civi api result.
		 *
		 * @since 0.1
		 * @param array $items
		 * @param WP_REST_Request $request
		 */
		$data = apply_filters( 'civi_wp_rest/controller/rest/api_result', $items, $params, $request );

		$data['values'] = array_reduce( $items['values'], function( $items, $item ) use ( $request ) {

			$response = $this->prepare_item_for_response( $item, $request );

			$items[] = $this->prepare_response_for_collection( $response );

			return $items;

		}, [] );

		return rest_ensure_response( $data );

	}

	/**
	 * Get formatted api params.
	 *
	 * @since 0.1
	 * @param WP_REST_Resquest $request
	 * @return array $params
	 */
	public function get_formatted_api_params( $request ) {

		$args = $request->get_params();

		// destructure entity and action
		[ 'entity' => $entity, 'action' => $action ] = $args;

		// unset unnecessary args
		unset( $args['entity'], $args['action'], $args['key'], $args['api_key'] );

		if ( ! isset( $args['json'] ) ) {

			$params = $args;

		} else {

			$params = is_string( $args['json'] ) ? json_decode( $args['json'], true ) : [];

		}

		// ensure check permissions is enabled
		$params['check_permissions'] = true;

		return [ $entity, $action, $params ];

	}

	/**
	 * Matches the item data to the schema.
	 *
	 * @since 0.1
	 * @param object $item
	 * @param WP_REST_Request $request
	 */
	public function prepare_item_for_response( $item, $request ) {

		return rest_ensure_response( $item );

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
			'title' => 'civicrm/v3/rest',
			'description' => 'CiviCRM API3 WP rest endpoint wrapper',
			'type' => 'object',
			'required' => [ 'entity', 'action', 'params' ],
			'properties' => [
				'is_error' => [
					'type' => 'integer'
				],
				'version' => [
					'type' => 'integer'
				],
				'count' => [
					'type' => 'integer'
				],
				'values' => [
					'type' => 'array'
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
			'key' => [
				'type' => 'string',
				'required' => true,
				'validate_callback' => function( $value, $request, $key ) {

					return $this->is_valid_site_key();

				}
			],
			'api_key' => [
				'type' => 'string',
				'required' => true,
				'validate_callback' => function( $value, $request, $key ) {

					return $this->is_valid_api_key( $request );

				}
			],
			'entity' => [
				'type' => 'string',
				'required' => true,
				'validate_callback' => function( $value, $request, $key ) {

					return is_string( $value );

				}
			],
			'action' => [
				'type' => 'string',
				'required' => true,
				'validate_callback' => function( $value, $request, $key ) {

					return is_string( $value );

				}
			],
			'json' => [
				'type' => ['string', 'array'],
				'required' => false,
				'validate_callback' => function( $value, $request, $key ) {

					return is_array( $value ) || $this->is_valid_json( $value );

				}
			]
		];

	}

	/**
	 * Checks if string is a valid json.
	 *
	 * @since 0.1
	 * @param string $param
	 * @return bool
	 */
	protected function is_valid_json( $param ) {

		$param = json_decode( $param, true );

		if ( ! is_array( $param ) ) return false;

 		return ( json_last_error() == JSON_ERROR_NONE );

	}

	/**
	 * Validates the site key.
	 *
	 * @since 0.1
	 * @return bool $is_valid_site_key
	 */
	private function is_valid_site_key() {

		return \CRM_Utils_System::authenticateKey( false );

	}

	/**
	 * Validates the api key.
	 *
	 * @since 0.1
	 * @param WP_REST_Resquest $request
	 * @return bool $is_valid_api_key
	 */
	private function is_valid_api_key( $request ) {

		$api_key = $request->get_param( 'api_key' );

		if ( ! $api_key ) return false;

		$contact_id = \CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact', $api_key, 'id', 'api_key' );

		// validate contact and login
		if ( $contact_id ) {

			$wp_user = $this->get_wp_user( $contact_id );

			$this->do_user_login( $wp_user );

			return true;

		}

		return false;

	}

	/**
	 * Get WordPress user data.
	 *
	 * @since 0.1
	 * @param int $contact_id The contact id
	 * @return bool|WP_User $user The WordPress user data
	 */
	protected function get_wp_user( int $contact_id ) {

		try {

			$uf_match = civicrm_api3( 'UFMatch', 'getsingle', [ 'contact_id' => $contact_id ] );

		} catch ( \CiviCRM_API3_Exception $e ) {

			return new \WP_Error( 'civicrm_rest_api_error', $e->getMessage(), [ 'status' => $this->authorization_status_code() ] );

		}

		$wp_user = get_userdata( $uf_match['uf_id'] );

		return $wp_user;

	}

	/**
	 * Logs in the WordPress user, needed to respect CiviCRM ACL and permissions.
	 *
	 * @since 0.1
	 * @param  WP_User $user
	 */
	protected function do_user_login( \WP_User $user ) {

		if ( is_user_logged_in() ) return;

		wp_set_current_user( $user->ID, $user->user_login );

		wp_set_auth_cookie( $user->ID );

		do_action( 'wp_login', $user->user_login );

	}

}
