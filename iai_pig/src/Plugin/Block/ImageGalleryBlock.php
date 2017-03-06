<?php

namespace Drupal\iai_pig\Plugin\Block;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;

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
 *
 * We are controlling the block caching by setting the context key above.
 * Whenever a node is saved it invalidates its cache context and the block
 * will be rebuilt. We make use of this context in our build method with the
 * code: <code>$node = $this->getContextValue('node');</code>
 *
 * Also, the context ensures that the block is present only on node pages.
 *
 * @see: http://drupal.stackexchange.com/questions/199527/how-do-i-correctly-setup-caching-for-my-custom-block-showing-content-depending-o
 * @see: http://drupal.stackexchange.com/questions/180907/how-do-i-make-a-block-that-pulls-the-current-node-content
 * @see: https://api.drupal.org/api/drupal/core!lib!Drupal!Component!Plugin!ContextAwarePluginBase.php/function/ContextAwarePluginBase%3A%3AgetContextValue/8.2.x
 *
 * Note: This block is not intended to be an "all powerful" block to be reused
 *       elsewhere. We are making certain assumptions to keep the example
 *       relatively simple.
 */
class ImageGalleryBlock extends BlockBase {

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
    $node = $this->getContextValue('node');

    // Determine if we are on a page that points to a product.
    $product = $this->getProduct($node);

    if ($product) {

      // Retrieve the product images
      $image_data = $this->getImageData($product);
      $block_count = $this->configuration['block_count'];
      $item_count = 0;
      $build['list'] = [
        '#theme' => 'item_list',
        '#items' => [],
      ];

      // This logic is just to give some positive feedback that the block is
      // being rendered. In reality, we'd likely just not have the block render
      // anything in this situation.
      $build['list']['#items'][0] = [
        '#type' => 'markup',
        '#markup' => $this->t('There were no product images to display.')
      ];

      // Note: We are using the "thumbnail" image preset, which is defined by
      //       the Standard installation profile.
      while ($item_count < $block_count && isset($image_data[$item_count])) {
        $file = File::load($image_data[$item_count]['target_id']);
        $link_text = [
          '#theme' => 'image_style',
          '#uri' => $file->getFileUri(),
          '#style_name' => 'thumbnail',
          '#alt' => $image_data[$item_count]['alt'],
        ];
        $url = Url::fromUserInput('/iai_pig/display_product_image/' . $product->nid->value . '/' . $item_count);
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
      // This logic is just to give some positive feedback that the block is
      // being rendered. In reality, we'd likely just not have the block render
      // anything in this situation.
      $build['no_data'] = [
        '#type' => 'markup',
        '#markup' => $this->t('This page does not reference a product.'),
      ];
    }

    return $build;
  }

  private function getProduct(Node $node) {
    // Note: For this example block we are concerned only with nodes.
    //       Specifically, we are operating under the assumption that this block
    //       should render only when it's being viewed on a Book page that
    //       references a Product or when it's being viewed directly on a
    //       Product page.

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

  private function getReferencedProduct($node) {
    // Note: We are making an assumption about a particular field name that
    //       Book pages use for the entity reference to products. We did not
    //       define the Product content type with a custom module (which would
    //       allow us to have the module as a dependency, and thus ensure that
    //       the field name exists) because the Product was defined in the
    //       section of the course in which we were using only Core
    //       functionality. We had not yet started writing any custom code.
    if (isset($node->field_product)) {
      $referenced_entities = $node->field_product->referencedEntities();
      $node = $referenced_entities[0];
      return $node;
    }
    else {
      return NULL;
    }
  }

  private function getImageData($product) {
    $image_data = array();
    foreach ($product->field_image as $image) {
      $image_data[] = $image->getValue();
    }
    return $image_data;
  }
}
