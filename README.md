CiviCRM WP REST API Wrapper
===========================

This is an experimental WordPress plugin that aims to expose CiviCRM's [extern](https://github.com/civicrm/civicrm-core/tree/master/extern) scripts as WordPress REST endpoints.

The are currently two endpoints:

1. `civicrm/v3/rest` - a wrapper around `civicrm_api3()`

	**Parameters**:
	- `key` - the site key, required
	- `api_key` - required, the contact api key
	- `entity` - required, the API entity
	- `action` - required, the API action
	- `json` - optional, json formatted string with the API parameters/argumets
	
	**Examples**:

	`https://example.com/wp-json/civicrm/v3/rest?entity=Contact&action=get&key=<site_key>&api_key=<api_key>&group=Administrators`

	`https://example.com/wp-json/civicrm/v3/rest?entity=Contact&action=get&key=<site_key>&api_key=<api_key>&json={"goup": "Administrators"}`

2. `civicrm/v3/url` - a substition for `civicrm/extern/url.php`