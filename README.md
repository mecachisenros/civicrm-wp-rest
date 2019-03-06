CiviCRM WP REST API Wrapper
===========================

This is an experimental WordPress plugin that aims to expose CiviCRM's [extern](https://github.com/civicrm/civicrm-core/tree/master/extern) scripts as WordPress REST endpoints.

The are currently three endpoints:

1. `civicrm/v3/rest` - a wrapper around `civicrm_api3()`

	**Parameters**:
	- `key` - the site key, required
	- `api_key` - required, the contact api key
	- `entity` - required, the API entity
	- `action` - required, the API action
	- `json` - optional, json formatted string with the API parameters/argumets
	
	**Examples**:

	`https://example.com/wp-json/civicrm/v3/rest?entity=Contact&action=get&key=<site_key>&api_key=<api_key>&group=Administrators`

	`https://example.com/wp-json/civicrm/v3/rest?entity=Contact&action=get&key=<site_key>&api_key=<api_key>&json={"group": "Administrators"}`

2. `civicrm/v3/url` - a substition for `civicrm/extern/url.php` mailing tracking

3. `civicrm/v3/open` - a substition for `civicrm/extern/open.php` mailing tracking

### Settings
Set the `CIVICRM_WP_REST_REPLACE_MAILING_TRACKING` constant to `true` to replace mailing url and open tracking calls with their counterpart REST endpoints, `civicrm/v3/url` and `civicrm/v3/open`.

_Note: use this setting with caution, it may affect performance on large mailing, see `Plugin->replace_tracking_urls()` method._