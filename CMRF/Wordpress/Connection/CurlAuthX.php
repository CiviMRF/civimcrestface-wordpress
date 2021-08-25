<?php
/**
 * exact copy of Drupal's.
 */

namespace CMRF\Wordpress\Connection;

use \CMRF\Connection\CurlAuthX as AbstractCurl;
use \CMRF\Core\Call;

class CurlAuthX extends AbstractCurl {

  public function queueCall(Call $call) {
    // We don't have to do anything here.
    // Except for saving the call.
    $this->core->getFactory()->update($call);
  }

}
