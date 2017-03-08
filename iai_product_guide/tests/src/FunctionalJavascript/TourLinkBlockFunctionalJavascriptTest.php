<?php

namespace Drupal\Tests\iai_product_guide\FunctionalJavascript;

use Drupal\FunctionalJavascriptTests\JavascriptTestBase;

/**
 * Tests the image gallery block.
 *
 * @group iai_product_guide
 */
class TourLinkBlockFunctionalJavascriptTest extends JavascriptTestBase {

  /**
   * Modules to install.
   *
   * @var array
   */
  public static $modules = [
    'block',
    'book',
    'menu_ui',
    'node',
    'text',
    'tour',
    'user',
    'iai_product_guide',
    'iai_pig',
    'iai_pg_product',
    'iai_pg_book',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    // Create and log in an administrative user.
    $adminUser = $this->drupalCreateUser(array(
      'administer blocks',
      'access administration pages',
    ));
    $this->drupalLogin($adminUser);

    // Place the blocks in the content area
    $block_url = 'admin/structure/block/add/iai_product_image_gallery/classy';
    $edit = [
      'region' => 'content',
    ];
    $this->drupalPostForm($block_url, $edit, 'Save block');
    $block_url = 'admin/structure/block/add/iai_tour_link/classy';
    $this->drupalPostForm($block_url, $edit, 'Save block');
    $this->drupalLogout();
  }

  /**
   * Tests taking the tour.
   */
  public function testTourLink() {

    // Create a product piece of content
    $product = $this->drupalCreateNode(array(
      'title' => t('Product with an image'),
      'type' => 'product',
    ));
    $product->field_image->generateSampleItems();
    $product->save();

    // Create a book page
    $book = $this->drupalCreateNode(array(
      'title' => t('Product User Guide'),
      'type' => 'book',
      //'field_product' => array(
        //'target_id' => $product->id(),
      //),
    ));
// I am having zero success getting the field_product to be recognized. When I set the
// configuration in config/install instead of config/optional I get an error that there are
// unmet dependencies; that is why field_product is not being defined. I can't figure out
// what the unmet dependencies are, though.
    //$book->set('field_product', $product->id());
    $book->save();
//$this->assertSame('debug', $book);

    // Create and log in a user.
    $testUser = $this->drupalCreateUser(array(
      'access content',
      'access tour',
    ));
    $this->drupalLogin($testUser);

    $this->drupalGet('node/' . $book->id());
    $page = $this->getSession()->getPage();
    $targetLink = $page->findLink('Take the tour!');
    $this->assertNotEmpty($targetLink);
    $targetLink->click();
// The screen shot is not showing the tip on the page
$this->createScreenshot('/tmp/bookPage-' . time() . '.jpg');
    $targetTip = $page->find('css', '.tip-introduction');
// and the assertion ends up being false
    $this->assert($targetTip->isVisible());
  }

}
