<?php

/**
 * @file
 * Contains \Drupal\iai_pig\DisplayProductImage
 */

namespace Drupal\iai_pig\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;

/**
 * Display a given image for a given product
 */
class DisplayProductImage extends ControllerBase {

  /**
   * In order to display a Modal, there needs to be a URL that will be
   * displayed inside the modal. At least, I think this is the case.
   *
   * This controller is responsible for providing that URL that will be
   * displayed inside the modal.
   */

  /**
   * Display a (translated) product image
   *
   * @param \Drupal\node\Entity\Node $node
   *   The fully loaded node entity
   *   // @todo: I think it's possible for me to have Symfony pass the loaded
   *             entity by using typehinting in the arguments
   * @param integer $delta
   *   The image instance to load
   *   // @todo: Make sure that I'm loading the "translated" version of the image
   *
   * @return array $render_array
   *   The render array
   */
  public function displayProductImage(Node $node, $delta) {

    // @todo: Make sure to use a particular image preset.
    //        I wonder if I should have the iai_pig module define a preset

    $render_array['still_testing'] = array(
      '#type' => 'markup',
      '#markup' => t('You are successfully viewing the output for the DisplayProductImage Controller for node: @title.', array('@title' => $node->title->value)),
    );
    return $render_array;
  }

}
