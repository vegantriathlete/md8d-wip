<?php

namespace Drupal\aquifer\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an Aquifer block with the names of the aquifers.
 *
 * @Block(
 *   id = "aquifer_block",
 *   admin_label = @Translation("Aquifer listing"),
 *   category = @Translation("Lists (Views)")
 * )
 */
class AquiferBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The entity storage for aquifers.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $nodeStorage;

/******************************************************************************
 **                                                                          **
 ** This is an example of Dependency Injection. The necessary objects are    **
 ** being injected through the class's constructor.                          **
 **                                                                          **
 ******************************************************************************/
  /**
   * Constructs an AquiferBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
/******************************************************************************
 **                                                                          **
 ** We could have just as easily passed the EntityStorageInterface directly  **
 ** by injecting the node storage object like:                               **
 **   <code>$container->get('entity_type.manager')->getStorage('node')</code>**
 ** Had we gone this route, then we would need to use (above)                **
 **   Drupal\Core\Entity\EntityStorageInterface                              **
 ** instead of                                                               **
 **   Drupal\Core\Entity\EntityTypeManagerInterface                          **
 ** and we would also need to change our typehinting to use                  **
 **   EntityStorageInterface                                                 **
 ** instead of                                                               **
 **   EntityTypeManagerInterface                                             **
 **                                                                          **
 ******************************************************************************/
    $this->nodeStorage = $entity_type_manager->getStorage('node');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {

/******************************************************************************
 **                                                                          **
 ** The ContainerFactoryPluginInterface is what gave us access to Symfony's  **
 ** service container. Plugins don't get access to the service container if  **
 ** they don't implement the ContainerFactoryPluginInterface.                **
 **                                                                          **
 ******************************************************************************/
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    // By default, the block will contain 5 items.
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
      '#title' => $this->t('Number of aquifer items in block'),
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
 
/******************************************************************************
 **                                                                          **
 ** We are just retrieving all of the aquifers. In a real situation we might **
 ** do something like choosing the aquifers that were at a critical level or **
 ** filter and sort them by some other criteria.                             **
 **                                                                          **
 ******************************************************************************/
    $result = $this->nodeStorage->getQuery()
      ->condition('type', 'aquifer')
      ->condition('status', 1)
      ->range(0, $this->configuration['block_count'])
      ->sort('title', 'ASC')
      ->execute();

    if ($result) {
      // Only display the block if there are items to show.
      $items = $this->nodeStorage->loadMultiple($result);

      $build['list'] = [
        '#theme' => 'item_list',
        '#items' => [],
      ];
      foreach ($items as $item) {
        $build['list']['#items'][$item->id()] = [
          '#type' => 'markup',
          '#markup' => $item->label(),
        ];
      }

/******************************************************************************
 **                                                                          **
 ** We don't really need to worry about expiring the cache because we've got **
 ** only three aquifers. Nonethless, we are going to set the meta data so    **
 ** that when pieces of content are updated we make sure to rebuild this     **
 ** block. You can learn more about the Cache API in the Appendix.           **
 **                                                                          **
 ******************************************************************************/
      $build['#cache']['tags'][] = 'node_list';
      return $build;
    }
  }
}
