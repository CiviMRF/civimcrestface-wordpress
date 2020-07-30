<?php
/**
 * @author Jaap Jansma <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

namespace CMRF\Wordpress\Connection;

use CMRF\Core\Call as Call;

class Local extends \CMRF\Connection\Local {
  /**
   * execute the given call synchroneously
   *
   * return call status
   */
  public function executeCall(Call $call) {
    /*
     * Copied from CiviCRM invoke function as there is a problem with timezones
     * when the local connection is used.
     *
     * CRM-12523
     * WordPress has it's own timezone calculations
     * CiviCRM relies on the php default timezone which WP
     * overrides with UTC in wp-settings.php
     */
    $wpBaseTimezone = date_default_timezone_get();
    $wpUserTimezone = get_option('timezone_string');
    if ($wpUserTimezone) {
      date_default_timezone_set($wpUserTimezone);
      \CRM_Core_Config::singleton()->userSystem->setMySQLTimeZone();
    }

    $return = parent::executeCall($call);

    /*
     * Reset the timezone back the original setting.
     */
    if ($wpBaseTimezone) {
      date_default_timezone_set($wpBaseTimezone);
    }

    return $return;
  }
}