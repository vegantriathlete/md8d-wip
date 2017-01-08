<?php

namespace Drupal\aquifer\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an 'Aggregator feed' block with the latest items from the feed.
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

  /**
   * Constructs an AquiferBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $node_storage
   *   Entity storage for node entities.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, $node_storage) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->nodeStorage = $node_storage;
  }


  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity.manager')->getStorage('node'),
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
  protected function blockAccess(AccountInterface $account) {
    // Only grant access to users with the 'access content' permission.
    return AccessResult::allowedIfHasPermission($account, 'access content');
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
    $result = $this->nodeStorage->getQuery()
      ->condition('type', 'aquifer')
      ->range(0, $this->configuration['block_count'])
      ->sort('timestamp', 'DESC')
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
          '#type' => 'link',
          '#url' => $item->urlInfo(),
          '#title' => $item->label(),
        ];
      }
      $build['more_link'] = [
        '#type' => 'more_link',
        '#url' => $feed->urlInfo(),
        '#attributes' => ['title' => $this->t("View this feed's recent news.")],
      ];
      return $build;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    $cache_tags = parent::getCacheTags();
    $feed = $this->nodeStorage->load($this->configuration['feed']);
    return Cache::mergeTags($cache_tags, $feed->getCacheTags());
  }

}
