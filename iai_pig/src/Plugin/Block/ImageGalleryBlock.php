<?php

namespace Drupal\iai_pig\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an Eco action block with actions that are nearby.
 *
 * @Block(
 *   id = "iai_product_image_gallery",
 *   admin_label = @Translation("Product Image Gallery"),
 *   category = @Translation("Image Display")
 * )
 */
class ImageGalleryBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The entity storage for products.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $nodeStorage;

  // @todo: I think I'll be adding a service for some of the image processing

  /**
   * Constructs a product image gallery block object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $node_storage
   *   Entity storage for node entities.
   * @param \Drupal\personalization\PersonalizationIpServiceInterface $personalization_ip_service
   *   The personalization Ip Service.
   */
  //public function __construct(array $configuration, $plugin_id, $plugin_definition, $node_storage, PersonalizationIpServiceInterface $personalization_ip_service) {
  public function __construct(array $configuration, $plugin_id, $plugin_definition, $node_storage) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->nodeStorage = $node_storage;
    //$this->personalizationIpService = $personalization_ip_service;
  }


  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')->getStorage('node'),
      // $container->get('personalization.personalization_ip_service')
    );
  }


  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    // By default, the block will display 5 thumbnails.
    return array(
      'block_count' => 5,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $range = range(2, 20);
    $form['block_count'] = array(
      '#type' => 'select',
      '#title' => $this->t('Number of product images in block'),
      '#default_value' => $this->configuration['block_count'],
      '#options' => array_combine($range, $range),
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['block_count'] = $form_state->getValue('block_count');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = array();

    // Determine if we are on a page that points to a product.
    $product = $this->getProduct();

    if ($product) {
      // Retrieve the product images
      $image_data = $this->getImageData($product);

      $block_count = $this->configuration['block_count'];
      $item_count = 0;
      $build['list'] = [
        '#theme' => 'item_list',
        '#items' => [],
      ];

      // Temporary logic while I'm still building the functionality
      $build['list']['#items'][0] = [
        '#type' => 'markup',
        '#markup' => t('There were no product images to display.')
      ];

      while ($item_count < $block_count && isset($image_data[$item_count])) {
        $build['list']['#items'][$item_count] = [
          '#type' => 'markup',
          '#markup' => $image_data[$item_count]['link'],
        ];
        $item_count++;
      }
    }
    else {
      // Temporary logic while I'm still building the functionality
      $build['no_data'] = [
        '#type' => 'markup',
        '#markup' => t('This page does not reference a product.'),
      ];
    }

    // Do I need to set the cache tags so that the block gets rebuilt for each
    // path? Can I have it cached per path? That would be good.

    // We have not done anything with cache tags; the results of this block get
    // cached. If you add or delete product images, you won't see
    // those changes reflected in this block unless you get the cache to clear.
    // One way to do this (which is faster than clearing the cache for the entire
    // site) is to go into the block layout and configure and save this block.
    return $build;
  }

  private function getProduct() {
    $nid = $this->getProductNid();
    if ($nid) {
      $result = $this->nodeStorage->getQuery()
        ->condition('type', 'product')
        ->condition('nid', $nid)
        ->range(0, 1)
        ->execute();
      return Node::load(reset($result));
    }
    else {
      return NULL;
    }
  }

  private function getProductNid() {
    $nid = NULL;
    return $nid;
  }

  private function getImageData($product) {
    $image_data = array();
    return $image_data;
  }
}
