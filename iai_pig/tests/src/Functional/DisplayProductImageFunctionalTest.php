<?php

namespace Drupal\Tests\iai_pig\Functional;

use Drupal\file\Entity\File;
use Drupal\Tests\BrowserTestBase;

/**
 * Tests the product image display.
 *
 * @group iai_pig
 */
class DisplayProductImageFunctionalTest extends BrowserTestBase {

  /**
   * Modules to install.
   *
   * @var array
   */
  public static $modules = array('iai_pig', 'iai_pig_product');

  /**
   * Tests a product that does not have an image attached.
   */
/*
  public function testProductWithoutImage() {

    // Create a product piece of content
    $node = $this->drupalCreateNode(array(
      'title' => t('Product without an image'),
      'type' => 'test_product',
    ));
    $message = 'You are viewing ' . $node->getTitle() . '. Unfortunately, there is no image defined for delta: 0.';

    $this->drupalGet('iai_pig/display_product_image/' . $node->id() . '/0');
    $this->assertContains($message, $this->getTextContent());
  }
*/

  /**
   * Tests a product that has an image attached.
   */
/**/
  public function testProductWithImage() {

    // Create a product piece of content
    $node = $this->drupalCreateNode(array(
      'title' => t('Product with an image'),
      'type' => 'test_product',
    ));
    $node->field_test_product_image->generateSampleItems();
//$node->field_test_product_image->generateSampleItems(3);
    $node->save();
//$image_data = $node->field_test_product_image->getValue();
    $message = 'You are viewing ' . $node->getTitle() . '. Unfortunately, there is no image defined for delta: 0.';

    $this->drupalGet('iai_pig/display_product_image/' . $node->id() . '/0');
    $pageText = $this->getTextContent();
    $this->assertContains('lmnopqrstuv', $pageText);
//$this->assertSame('debug', $image_data);
  }
/**/

}
