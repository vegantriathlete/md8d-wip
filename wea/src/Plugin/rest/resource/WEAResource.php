<?php

namespace Drupal\wea\Plugin\rest\resource;

use Drupal\node\Entity\Node;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Provides a resource for a water eco action item.
 *
 * @RestResource(
 *   id = "wea_resource",
 *   label = @Translation("Water eco action item"),
 *   uri_paths = {
 *     "canonical" = "/wea/actions/{node}",
 *   }
 * )
 */
class WEAResource extends ResourceBase {

  /**
   * Responds to entity GET requests.
   *
   * Returns a water eco action item for the specified ID.
   *
   * @param \Drupal\node\Entity\Node $node
   *   The node object.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The response containing the entity with its accessible fields.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   */
  public function get(Node $node) {
    $node_access = $node->access('view', NULL, TRUE);
    if (!$node_access->isAllowed()) {
      throw new AccessDeniedHttpException();
    }
$record = [
  'id' => $id
  'type' => $node->getType();
  'title' => $node->getTitle();
];
      if (!empty($record)) {
        return new ResourceResponse($record);
      }

    }

  }

}
