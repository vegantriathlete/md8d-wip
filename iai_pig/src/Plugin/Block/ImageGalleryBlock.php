<?php

namespace Drupal\iai_pig\Plugin\Block;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\iai_product\ProductManagerServiceInterface;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an image gallery block.
 *
 * @Block(
 *   id = "iai_product_image_gallery",
 *   admin_label = @Translation("Product Image Gallery"),
 *   category = @Translation("Image Display"),
 *   context = {
 *     "node" = @ContextDefinition(
 *       "entity:node",
 *       label = @Translation("Current Node")
 *     )
 *   }
 * )
 */

/******************************************************************************
 **                                                                          **
 ** The context ensures that the block is present only on node pages.        **
 **                                                                          **
 ** Whenever a node is saved it invalidates its cache context and the block  **
 ** will be rebuilt. We make use of this context in our build method with the**
 **   <code>$node = $this->getContextValue('node');</code>                   **
 **                                                                          **
 ** This block is not intended to be an "all powerful" block to be reused    **
 ** elsewhere. We are making certain assumptions to keep the example         **
 ** relatively simple.                                                       **
 **                                                                          **
 ******************************************************************************/
class ImageGalleryBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The Product Manager Service.
   *
   * @var \Drupal\iai_product\ProductManagerServiceInterface
   */
  protected $productManagerService;

  /**
   * Constructs Product Image Gallery block object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\iai_product\ProductManagerServiceInterface
   *   $product_manager_service
   *   The Product Manager Service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ProductManagerServiceInterface $product_manager_service) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->productManagerService = $product_manager_service;
  }
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('iai_product.product_manager_service')
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

/******************************************************************************
 **                                                                          **
 ** @see:                                                                    **
 ** https://api.drupal.org/api/drupal/core!lib!Drupal!Component!Plugin!ContextAwarePluginBase.php/function/ContextAwarePluginBase%3A%3AgetContextValue/8.2.x
 **                                                                          **
 ******************************************************************************/
    // @todo: I think I need to change this to the translated node after I
    //        receive it. See related todo below. When I'm on the book page
    //        things get messed up with the spanish version. When I'm on the
    //        product page it works fine with the spanish version.
    $node = $this->getContextValue('node');

    // Determine if we are on a page that points to a product.
    $product = $this->getProduct($node);

    if ($product) {

      // Retrieve the product images
      $image_data = $this->productManagerService->retrieveProductImages($product);
      $block_count = $this->configuration['block_count'];
      $item_count = 0;
      $build['list'] = [
        '#theme' => 'item_list',
        '#items' => [],
      ];

/******************************************************************************
 **                                                                          **
 ** This logic is just to give some positive feedback that the block is being**
 ** rendered. In reality, we'd likely just not have the block render anything**
 ** in this situation.                                                       **
 **                                                                          **
 ******************************************************************************/
      $build['list']['#items'][0] = [
        '#type' => 'markup',
        '#markup' => $this->t('There were no product images to display.')
      ];

      while ($item_count < $block_count && isset($image_data[$item_count])) {
        $file = File::load($image_data[$item_count]['target_id']);
        $link_text = [
          '#theme' => 'image_style',
          '#uri' => $file->getFileUri(),
          '#style_name' => 'product_thumbnail',
          '#alt' => $image_data[$item_count]['alt'],
        ];

/******************************************************************************
 **                                                                          **
 ** This is the Modal API.                                                   **
 ** @see: https://www.drupal.org/node/2488192 for more information.          **
 **                                                                          **
 ******************************************************************************/
        $options = array(
          'attributes' => array(
            'class' => array(
              'use-ajax',
            ),
            'data-dialog-type' => 'modal',
            'data-dialog-options' => Json::encode([
              'width' => 700,
            ]),
          ),
        );
        $url = Url::fromRoute('iai_pig.display_product_image', array('node' => $product->nid->value, 'delta' => $item_count));
        $url->setOptions($options);
        $build['list']['#items'][$item_count] = [
          '#type' => 'markup',
          '#markup' => Link::fromTextAndUrl(drupal_render($link_text), $url)
                       ->toString(),
        ];
        $item_count++;
      }
      $build['#attached']['library'][] = 'core/drupal.dialog.ajax';
    }
    else {

/******************************************************************************
 **                                                                          **
 ** This logic is just to give some positive feedback that the block is being**
 ** rendered. In reality, we'd likely just not have the block render anything**
 ** in this situation.                                                       **
 **                                                                          **
 ******************************************************************************/
      $build['no_data'] = [
        '#type' => 'markup',
        '#markup' => $this->t('This page does not reference a product.'),
      ];
    }

    return $build;
  }

  /**
   * Get a product
   *
   * @param \Drupal\node\Entity\Node $node
   *   The fully loaded node object.
   * @return \Drupal\node\Entity\Node $product
   *   The fully loaded product
   */
  private function getProduct(Node $node) {

/******************************************************************************
 **                                                                          **
 ** For this example block we are concerned only with nodes. Specifically, we**
 ** are operating under the assumption that this block should render only    **
 ** when it's being viewed on a Book page that references a Product or when  **
 ** it's being viewed directly on a Product page.                            **
 **                                                                          **
 ******************************************************************************/
    if ($node) {
      // Check if this is a Product node already
      if ($node->getType() == 'product') {
        return $node;
      }

      // Check if this node references a Product
      $product = $this->getReferencedProduct($node);

      return $product;
    }
    else {
      return NULL;
    }
  }

  /**
   * Get a referenced product
   *
   * @param \Drupal\node\Entity\Node $node
   *   The fully loaded node object.
   * @return \Drupal\node\Entity\Node $product
   *   The fully loaded referenced product
   */
  private function getReferencedProduct($node) {

/******************************************************************************
 **                                                                          **
 ** We are making an assumption about a particular field name that Book pages**
 ** use for the entity reference to products. We added this field to the book**
 **  pages ourselves.                                                        **
 **                                                                          **
 ******************************************************************************/
    if (isset($node->field_product)) {
      // @todo: Determine if we've got a bug with not using the translated
      //        node, which results in the referenced entities being
      //        incorrect. I'm not sure if it's our bug or Drupal's bug.
      $referenced_entities = $node->field_product->referencedEntities();
      $product = $referenced_entities[0];
      return $product;
    }
    else {
      return NULL;
    }
  }
}
