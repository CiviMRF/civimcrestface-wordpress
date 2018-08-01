<?php
/**
 * exact copy of Drupal's.
 */

namespace CMRF\Wordpress\Connection;

use \CMRF\Connection\Curl as AbstractCurl;
use \CMRF\Core\Call;

class Curl extends AbstractCurl {

  public function queueCall(Call $call) {
    // We don't have to do anything here.
    // Except for saving the call.
    $this->core->getFactory()->update($call);
  }

  /**
   * execute the given call synchroneously
   * 
   * return call status
   */
  public function executeCall(Call $call) {
    $profile               = $this->getProfile();

    $request               = $this->getAPI3Params($call);
    // $request['api_key']    = $profile['api_key'];
    // $request['key']        = $profile['site_key'];
    // $request['version']    = 3;
    // $request['entity']     = $call->getEntity();
    // $request['action']     = $call->getAction();
    $post_data = "entity=" . $call->getEntity();
    $post_data .= "&action=" . $call->getAction();
    $post_data .= "&api_key={$profile['api_key']}&key={$profile['site_key']}&version=3&XDEBUG_SESSION_START=sdf";
    $post_data .= "&json=" . urlencode(json_encode($request));

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_POST,           1);
    curl_setopt($curl, CURLOPT_POSTFIELDS,     $post_data);
    curl_setopt($curl, CURLOPT_URL,            $profile['url']);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSLVERSION,     1);

    $response = curl_exec($curl);
    if (curl_error($curl)){
      $call->setStatus(Call::STATUS_FAILED, curl_error($curl));
      return NULL;
    } else {
      $reply = json_decode($response, true);
      if ($reply===NULL) {
        $call->setStatus(Call::STATUS_FAILED, curl_error($curl));
        return NULL;
      } else {
        $call->setReply($reply);
        return $reply;
      }
    }
  }

}
