<?php
/*
Plugin Name: CiviCRM-Wordpress CRMF Connector
Plugin URI:  https://artfulrobot.uk
Description: Provides connector
Version:     20180716
Author:      Rich Lott
Author URI:  https://artfulrobot.uk
License:     GPL3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: wpcmrf
*/

// All functions are Wordpress-specific.
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once(__DIR__ . '/vendor/autoload.php');
// @todo autoloader for this.
require_once(__DIR__ . '/CMRF/Wordpress/Core.php');

function cmrf_core_curl_connector(\CMRF\Core\Core $core, $connector_id) {
  return new \CMRF\Wordpress\Connection\Curl($core, $connector_id);
}
