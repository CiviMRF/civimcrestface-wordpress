<?php
/*
Plugin Name: CiviCRM-Wordpress CiviMcRestFace Connector
Plugin URI:  https://artfulrobot.uk
Description: Provides connector to a local or remote CiviCRM. This connector could be used by other plugins.
Version:     20200814
Author:      Rich Lott
Author URI:  https://artfulrobot.uk
License:     GPL3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: wpcmrf
*/

// All functions are Wordpress-specific.
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

$wpcmrf_version = '1.0';
define( 'WPCMRF_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once(WPCMRF_PLUGIN_DIR . '/vendor/autoload.php');
// @todo autoloader for this.
require_once(WPCMRF_PLUGIN_DIR . '/CMRF/Wordpress/Core.php');
if ( is_admin() ) {
  require_once(WPCMRF_PLUGIN_DIR . '/CMRF/Wordpress/Admin/AdminPage.php');
  add_action( 'init', array( '\CMRF\Wordpress\Admin\AdminPage', 'init' ) );
}

/**
 * @param $entity
 * @param $action
 * @param $parameters
 * @param $options
 * @param $profile_id
 * @param array $callbacks
 *
 * @return CMRF\Wordpress\Call
 */
function wpcmrf_api($entity, $action, $parameters, $options, $profile_id, $callbacks=array()) {
  $core = wpcmrf_get_core();
  $call = $core->createCall($profile_id, $entity, $action, $parameters, $options, $callbacks);
  $core->executeCall($call);
  return $call;
}

/**
 * @return \CMRF\Wordpress\Core
 */
function wpcmrf_get_core() {
  return \CMRF\Wordpress\Core::singleton();
}

function wpcmrf_core_curl_connector(\CMRF\Core\Core $core, $connector_id) {
  return new \CMRF\Wordpress\Connection\Curl($core, $connector_id);
}

function wpcmrf_core_local_connector(\CMRF\Core\Core $core, $connector_id) {
  civi_wp()->initialize();
  return new \CMRF\Wordpress\Connection\Local($core, $connector_id);
}

function wpcmrf_install() {
  global $wpdb;
  global $wpcmrf_version;
  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

  $table_name = $wpdb->prefix . "wpcivimrf_profile";
  $charset_collate = $wpdb->get_charset_collate();
  $sql = "CREATE TABLE $table_name (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      label varchar(255) DEFAULT '' NOT NULL,
      connector varchar(255) DEFAULT '' NOT NULL,
      url varchar(255) DEFAULT '' NOT NULL,
      site_key varchar(255) DEFAULT '' NOT NULL,
      api_key varchar(255) DEFAULT '' NOT NULL,      
  PRIMARY KEY  (id)
  ) $charset_collate;";

  dbDelta( $sql );

  $table_name = $wpdb->prefix . "wpcmrf_core_call";
  $sql = "
    CREATE TABLE `$table_name` (
        `cid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Call ID',
        `status` varchar(8) NOT NULL DEFAULT 'INIT' COMMENT 'Status',
        `connector_id` varchar(255) NOT NULL DEFAULT '' COMMENT 'Connector ID',
        `request` longtext COMMENT 'The request data sent',
        `reply` longtext COMMENT 'The reply data received',
        `metadata` text COMMENT 'Custom metadata on the request',
        `request_hash` varchar(255) NOT NULL DEFAULT '' COMMENT 'SHA1 hash of the request, enables quick lookups for caches',
        `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Creation timestamp of this call',
        `scheduled_date` timestamp NULL DEFAULT NULL COMMENT 'Scheduted timestamp of this call',
        `reply_date` timestamp NULL DEFAULT NULL COMMENT 'Reply timestamp of this call',
        `cached_until` timestamp NULL DEFAULT NULL COMMENT 'Cache timeout of this call',
        `retry_count` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Retry counter for multiple submissions',  
        PRIMARY KEY (`cid`),
        KEY `cmrf_by_connector` (`connector_id`,`status`),
        KEY `cmrf_cache_index` (`connector_id`,`request_hash`,`cached_until`)
    ) $charset_collate";

  dbDelta( $sql );

  add_option( "wpcmrf_version", $wpcmrf_version);
}

register_activation_hook( __FILE__, 'wpcmrf_install' );
