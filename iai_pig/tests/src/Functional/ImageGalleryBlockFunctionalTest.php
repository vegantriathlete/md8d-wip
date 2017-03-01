<?php

namespace Drupal\Tests\iai_pig\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests the image gallery block.
 *
 * @group iai_pig
 */
class ImageGalleryBlockFunctionalTest extends BrowserTestBase {

  /**
   * Modules to install.
   *
   * @var array
   */
  public static $modules = array('iai_pig', 'iai_pig_product', 'block');

  /**
   * Tests a product that does not have an image attached.
   */
  public function testProductWithoutImage() {

    // Create a product piece of content
    $node = $this->drupalCreateNode(array(
      'title' => t('Product without an image'),
      'type' => 'product',
    ));
    $node->save();

    // Place the block in the sidebar
    $this->drupalPlaceBlock('iai_product_image_gallery');

    $message = 'There were no product images to display.';
    $this->drupalGet('node/' . $node->id());
    //$this->assertContains($message, $this->getTextContent());
    $this->assertSession()->responseContains($message);
  }

  /**
   * Tests a product that has an image attached.
   */
  public function testProductWithImage() {

    // Create a product piece of content
    $node = $this->drupalCreateNode(array(
      'title' => t('Product with an image'),
      'type' => 'product',
    ));
    $node->field_image->generateSampleItems();
    $node->save();

    // Place the block in the sidebar
    $this->drupalPlaceBlock('iai_product_image_gallery');

    $this->drupalGet('node/' . $node->id());
    $this->assertSession()->responseContains('data-dialog-type="modal"');
    $this->assertLinkByHref('/xiai_pig/display_product_image/' . $node->id() . '/0');
  }

}
