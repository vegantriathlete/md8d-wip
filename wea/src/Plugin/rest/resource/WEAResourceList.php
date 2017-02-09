<?php

namespace Drupal\wea\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;

/**
 * Provides a resource to list water eco action items.
 *
 * @RestResource(
 *   id = "wea",
 *   label = @Translation("Water eco action item list"),
 *   uri_paths = {
 *     "canonical" = "/wea/actions"
 *   }
 * )
 */
class WEAResourceList extends ResourceBase {

  /**
   * Responds to GET requests.
   *
   * Returns a list of water eco action items.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The response containing the log entry.
   */
  public function get() {
      //$record = db_query("SELECT * FROM {watchdog} WHERE wid = :wid", array(':wid' => $id))
      //  ->fetchAssoc();
$record = [
  'item_1' => array(
    'id' => 1,
    'title' => 'first wea item'
  ),
  'item_2' => array(
    'id' => 2,
    'title' => 'second wea item'
  )
];
      if (!empty($record)) {
        return new ResourceResponse($record);
      }

  }

}
