<?php

namespace Drupal\iai_pig\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Display a given image for a given product
 */
class CreateProductNode extends ControllerBase {
  public function createProductNode() {
    $node = Node::load(58);
    $render_array['debug'] = array(
      '#type' => 'markup',
      '#markup' => '<pre>' . print_r($node, 1) . '</pre>',
    );
    return $render_array;
    $node = Node::create(array(
      'title' => t('My programatically created node'),
      'type' => 'product',
    ));
    $node->field_image->generateSampleItems();
    $node->save();

    return new RedirectResponse(\Drupal::url('iai_pig.display_product_image', ['node' => $node->id(), 'delta' => 0]));
  }
}
