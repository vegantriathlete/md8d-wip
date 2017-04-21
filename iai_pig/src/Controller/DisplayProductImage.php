<?php

/**
 * @file
 * Contains \Drupal\iai_pig\DisplayProductImage
 */

namespace Drupal\iai_pig\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\file\Entity\File;
use Drupal\iai_product\ProductManagerServiceInterface;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
   * Presentation Manager Service.
   *
   * @var \Drupal\iai_product\ProductManagerServiceInterface
   */
  protected $productManagerService;

  /**
   * {@inheritdoc}
   */
  public function __construct(ProductManagerServiceInterface $product_manager_service) {
    $this->productManagerService = $product_manager_service;
  }
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('iai_product.product_manager_service')
    );
  }

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
    $productImages = $this->productManagerService->retrieveProductImages($node);
    if (isset($productImages[$delta])) {
      $file = File::load($productImages[$delta]['target_id']);
      $render_array['image_data'] = array(
        '#theme' => 'image_style',
        '#uri' => $file->getFileUri(),
        '#style_name' => 'product_large',
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
