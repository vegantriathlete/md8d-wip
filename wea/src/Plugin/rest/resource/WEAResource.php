<?php

namespace Drupal\wea\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Drupal\rest\ModifiedResourceResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Provides a resource for a water eco action item.
 *
 * @RestResource(
 *   id = "wea_resource",
 *   label = @Translation("Water eco action item"),
 *   uri_paths = {
 *     "canonical" = "/wea/actions/{id}",
 *     "https://www.drupal.org/link-relations/create" = "/wea/actions"
 *   }
 * )
 */
class WEAResource extends ResourceBase {

  /**
   * Responds to GET requests.
   *
   * Returns a water eco action item for the specified ID.
   *
   * @param int $id
   *   The ID of the water eco action item.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The response containing the log entry.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
   *   Thrown when the log entry was not found.
   * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
   *   Thrown when no log entry was provided.
   */
  public function get($id = NULL) {
    if ($id) {
      //$record = db_query("SELECT * FROM {watchdog} WHERE wid = :wid", array(':wid' => $id))
      //  ->fetchAssoc();
$record = [
  'id' => $id
];
      if (!empty($record)) {
        return new ResourceResponse($record);
      }

      //throw new NotFoundHttpException(t('Log entry with ID @id was not found', array('@id' => $id)));
    }

    // As near as I can tell, it's not possible to get to this method without
    // providing and ID. When I access the path without an ID I get a "No route
    // found" message. I don't know why DBLogResource throws this error.
    throw new BadRequestHttpException(t('No Water Eco Action ID was provided'));
  }

  public function post() {
$entity = [
  'id' => '1',
  'title' => 'Yay I just created a new WEA!',
  'description' => 'This is a hard-coded wea to test the post method.'
];
    $response = new ModifiedResourceResponse($entity, 201, ['Location' => 'http://testmd8ddev/action/yay-created-new-wea']);
    return $response;
  }

}
