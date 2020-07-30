<?php

/**
 * Wordpress-based implementation of a CMRF Core
 *
 * Very heavily based on the Drupal implementation.
 *
 * @author Björn Endres, SYSTOPIA (endres@systopia.de) - original Drupal version
 * @author Rich Lott, Artful Robot (https://artfulrobot.uk)
 */

namespace CMRF\Wordpress;

require_once(__DIR__ .'/Call.php');
require_once(__DIR__ .'/Connection/Curl.php');
require_once(__DIR__ .'/Connection/Local.php');
//require_once(__DIR__ .'/SQLPersistingCallFactory.php');

use CMRF\Core\Connection;
use CMRF\PersistenceLayer\SQLPersistingCallFactory;


class Core extends \CMRF\Core\Core {

  /**
   * @var Array
   *   An array of connectors keyed by the connector system name and a user label
   *   and a callback function to instantiate the connector class.
   */
  public $connectors = [];

  /**
   * @var Array of profiles.
   */
  public $profiles = [];

  /**
   * @var string. Key to $profiles array for the default profile.
   */
  public $default_profile;

  protected $connections = array();

  /** Core */
  protected static $singleton;

  /**
   * Get the Core object
   */
  public static function singleton() {
    if (!isset(static::$singleton)) {
      static::$singleton = new static();
    }
    return static::$singleton;
  }

  /**
   * @todo document.
   *
   * I think that this is responsible for:
   * 1. obtaining a database connection,
   * 2. instantiating a SQLPersistingCallFactory (with the connection from step 1)
   * 3. calling parent (which simply stores the SQLPersistingCallFactory in callfactory).
   */
  public function __construct() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wpcmrf_core_call';
    $connection = new \mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $factory = new SQLPersistingCallFactory($connection, $table_name, array('\CMRF\Wordpress\Call','createNew'), array('\CMRF\Wordpress\Call','createWithRecord'));

    // Load profiles
    $result =$wpdb->get_results("SELECT * FROM {$wpdb->prefix}wpcivimrf_profile", 'ARRAY_A');
    foreach($result as $profile) {
      $this->profiles[$profile['id']] = $profile;
    }

    parent::__construct($factory);
  }

  public function getDefaultProfile() {
    if (!isset($this->default_profile)) {
      foreach ($this->profiles as $id => $profile) {
        if ($profile['default']) {
          $this->default_profile = $profile;
        }
      }
    }
    return $this->default_profile;
  }

  /**
   * Retrieve the connection from the connection profile
   * Get instance of the connector through a callback function.
   *
   * @param $connector_id
   * @return Connection
   */
  protected function getConnection($connector_id) {
    if (!isset($this->profile[$connector_id])) {
      $connectors = $this->getRegisteredConnectors();
      $profile = $this->getConnectionProfile($connector_id);
      if (!isset($connectors[$profile['connector']])) {
        error_log('ERROR: cmrf_core: No connector available for ' . $profile['connector']);
        return;
      }
      if (!isset($connectors[$profile['connector']]['callback']) || !function_exists($connectors[$profile['connector']]['callback'])) {
        error_log('ERROR: cmrf_core: No connector callback available for ' . $profile['connector']);
        return;
      }
      $this->connections[$connector_id] = call_user_func($connectors[$profile['connector']]['callback'], $this, $connector_id);
    }

    return $this->connections[$connector_id];
  }


  /*********************************************************
   *  Use Wordpress options to store config for the moment  *
   *********************************************************/

  public function getConnectionProfiles() {
    return $this->profiles;
  }

  public function getConnectionProfile($connector_id) {
    $connection_profiles = $this->getConnectionProfiles();
    if (isset($connection_profiles[$connector_id])) {
      return $connection_profiles[$connector_id];
    } else {
      throw new \Exception("Invalid profile '$connector_id'.", 1);
    }
  }

  public function getRegisteredConnectors() {
    $connectors['curl'] = array(
      'label' => __('Remote connection', 'wpcmrf'),
      'callback' => 'wpcmrf_core_curl_connector',
    );
    if (function_exists('civi_wp')) {
      $connectors['local'] = [
        'label' => __('Local connection', 'wpcmrf'),
        'callback' => 'wpcmrf_core_local_connector',
      ];
    }
    return $connectors;
  }

  protected function storeRegisteredConnectors($connectors) {
    $this->connectors = $connectors;
  }

  protected function getSettings() {
    // This function doesn't do anything
  }

  protected function storeSettings($settings) {
    // This function doesn't do anything.
  }


}

