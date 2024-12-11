<?php
/**
 * Plugin Name: Connector to CiviCRM with CiviMcRestFace
 * Description: Provides an API connector to a local or remote CiviCRM installation. This connector could be used by other plugins. Funded by Artfulrobot, CiviCoop, civiservice.de, Bundesverband Soziokultur e.V., Article 19
 * Version: 1.0.9
 * Author: Rich Lott (Artfulrobot), Jaap Jansma (CiviCooP)
 * Plugin URI: https://github.com/CiviMRF/civimcrestface-wordpress
 * Text Domain: wpcmrf
 * Domain Path: /languages
 * License: AGPL-3.0
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

// All functions are Wordpress-specific.
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

$wpcmrf_version = '1.0.8';
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

function wpcmrf_core_curlauthx_connector(\CMRF\Core\Core $core, $connector_id) {
  return new \CMRF\Wordpress\Connection\CurlAuthX($core, $connector_id);
}

function wpcmrf_core_local_connector(\CMRF\Core\Core $core, $connector_id) {
  civi_wp()->initialize();
  return new \CMRF\Wordpress\Connection\Local($core, $connector_id);
}

function wpcmrf_install($network_wide) {
  if (is_multisite() && $network_wide) {
    foreach (get_sites(['fields' => 'ids']) as $blog_id) {
      switch_to_blog($blog_id);
      wpcmrf_install_into_current_blog();
      restore_current_blog();
    }
  }
  else {
    wpcmrf_install_into_current_blog();
  }
}

function wpcmrf_install_into_current_blog() {
  global $wpdb;
  global $wpcmrf_version;
  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

  $table_name = $wpdb->get_blog_prefix() . "wpcivimrf_profile";
  $charset_collate = $wpdb->get_charset_collate();
  $sql = "CREATE TABLE $table_name (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      label varchar(255) DEFAULT '' NOT NULL,
      connector varchar(255) DEFAULT '' NOT NULL,
      url varchar(255) DEFAULT '' NOT NULL,
      site_key varchar(255) DEFAULT '' NOT NULL,
      api_key varchar(255) DEFAULT '' NOT NULL,      
  PRIMARY KEY  (id)
  ) ENGINE = InnoDB $charset_collate;";

  dbDelta( $sql );

  $table_name = $wpdb->get_blog_prefix() . "wpcmrf_core_call";
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
    ) ENGINE = InnoDB $charset_collate";

  dbDelta( $sql );

  add_option( "wpcmrf_version", $wpcmrf_version);
}

register_activation_hook( __FILE__, 'wpcmrf_install' );

function wpcmrf_new_blog($blog_id) {

  //replace with your base plugin path E.g. dirname/filename.php
  if ( is_plugin_active_for_network( 'connector-civicrm-mcrestface/wpcmrf.php' ) ) {
    switch_to_blog($blog_id);
    wpcmrf_install_into_current_blog();
    restore_current_blog();
  }

}

add_action('wpmu_new_blog', 'wpcmrf_new_blog');
