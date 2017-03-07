<?php

/**
 * @file
 * Contains \Drupal\iai_pig\DisplayProductImage
 */

namespace Drupal\iai_pig\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\file\Entity\File;
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
   *   The fully loaded (translated) node entity
   * @param integer $delta
   *   The image instance to load
   *
   * @return array $render_array
   *   The render array
   */
  public function displayProductImage(Node $node, $delta) {
    // Note: We are making an assumption about a particular field name that
    //       Products use for the images. We did not define the Product content
    //       type with a custom module (which would allow us to have the module
    //       as a dependency, and thus ensure that the field name exists)
    //       because the Product was defined in the section of the course in
    //       which we were using only Core functionality. We had not yet started
    //       writing any custom code.

    // @todo: Make sure to use a particular image preset.
    //        I wonder if I should have the iai_pig module define a preset
    //        For the moment I am using the "large" image preset, which is
    //        defined by the Standard installation profile.

    if (isset($node->field_image[$delta])) {
      $image_data = $node->field_image[$delta]->getValue();
      $file = File::load($image_data['target_id']);
      $render_array['image_data'] = array(
        '#theme' => 'image_style',
        '#uri' => $file->getFileUri(),
        '#style_name' => 'large',
        '#alt' => $image_data['alt'],
      );
    }
    else {
      $render_array['image_data'] = array(
        '#type' => 'markup',
        '#markup' => $this->t('You are viewing @title. Unfortunately, there is no image defined for delta: @delta.', array('@title' => $node->title->value, '@delta' => $delta)),
      );
    }
    return $render_array;
  }

  /**
   * Page title callback
   *
   * @param \Drupal\node\Entity\Node $node
   *   The fully loaded (translated) node entity
   * @param integer $delta
   *   The image instance to load
   *
   * @return array $render_array
   *   The render array
   */
  public function pageTitleCallback(Node $node, $delta) {
    // Note: The Modal API did not handle this render array, which results in
    //       "Array" showing in the Modal title bar. I have changed the routing
    //        YAML file to use a static title.
    return [
      '#markup' => $this->t('Image @delta for @title', array('@delta' => $delta, '@title' => $node->title->value))
    ];
  }

}
