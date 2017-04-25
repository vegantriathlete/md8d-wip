<?php

namespace Drupal\iai_product_guide\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Link;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a block with a link to start the tour.
 *
 * @Block(
 *   id = "iai_tour_link",
 *   admin_label = @Translation("Link for the IAI Product Guide tour"),
 *   category = @Translation("Links"),
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
 ******************************************************************************/
class TourLinkBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Constructs an TourLinkBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, AccountInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentUser = $current_user;
  }


  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

/******************************************************************************
 **                                                                          **
 ** @see:                                                                    **
 ** https://api.drupal.org/api/drupal/core!lib!Drupal!Component!Plugin!ContextAwarePluginBase.php/function/ContextAwarePluginBase%3A%3AgetContextValue/8.2.x
 **                                                                          **
 ******************************************************************************/
    $node = $this->getContextValue('node');
    if ($this->currentUser->hasPermission('access tour') && $node->getType() == 'book') {
      $url = Url::fromRoute('<current>', array(), array('query' => array('tour' => 1)));
      $build['tour_link']= [
        '#type' => 'markup',
        '#markup' => Link::fromTextAndUrl(t('Take the tour!'), $url)->toString(),
      ];
      $build['#attached']['library'][] = 'tour/tour';
      return $build;
    }
  }

}
