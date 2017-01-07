<?php

namespace Drupal\aquifer;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * An implementation of AquiferManagerServiceInterface.
 */
class AquiferManagerService implements AquiferRetrievalServiceInterface {

  /**
   * Entity storage for node entities.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $nodeStorage;

  /**
   * Constructs the Aquifer Manager Service object.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $node_storage
   *   Entity storage for node entities.
   */
  public function __construct(EntityStorageInterface $node_storage) {
    $this->nodeStorage = $node_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')->getStorage('node')
    );
  }

  /**
   * {@inheritdoc}
   */
  private function createAquifer(array $aquifer_data) {
  }

  /**
   * {@inheritdoc}
   */
  public function readAquifer($aquifer_name) {
    $node = $this->getAquifer($aquifer_name);
  }

  /**
   * {@inheritdoc}
   */
  public function updateAquifer($aquifer_data) {
    // See if the aquifer already exists.
    $query = $this->nodeStorage->getQuery()
      ->condition('type', 'aquifer')
      ->condition('title', $aquifer_data['name'])
      ->count();

    $count_nodes = $query->execute();

    if ($count_nodes == 0) {
      $this->createAquifer($aquifer_data);
    }
    elseif ($count_nodes == 1) {
      // Retrieve the aquifer
      $node = $this->getAquifer($aquifer_data['name']);

      array_push($aquifer_data); // remove the name attribute
      // @todo: I'm not sure the entity storage (NodeInterface?) api lets me do this
      foreach ($aquifer_data as $property => $value) {
        $node->field_aquifer_{$property} = $value;
      }
      $node->save();
    }
    else {
      // Do something about the fact that there is more than one aquifer with
      // this name.
    }

  }

  /**
   * Retrieve the specific node
   *
   * @param string $aquifer_name
   *   The name of the aquifer to retrieve
   *
   * @return NodeInterface $node
   *   The fully loaded node interface
   */
  private function getAquifer($aquifer_name) {
    // @todo: This seems terribly inefficient. I need to see about a better way
    //        to do this.
    $query = $this->nodeStorage->getQuery()
      ->condition('type', 'aquifer')
      ->condition('title', $aquifer_name);

    $entity_ids = $query->execute();
    $nodes = $this->nodeStorage->loadMultiple($entity_ids);

    // @todo: I still need to return the NodeInterface
  }

}
