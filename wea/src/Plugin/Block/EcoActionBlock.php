<?php

namespace Drupal\wea\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Drupal\personalization\PersonalizationIpServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an Eco action block with actions that are nearby.
 *
 * @Block(
 *   id = "eco_action_block",
 *   admin_label = @Translation("Nearby Eco Actions"),
 *   category = @Translation("Lists (Views)")
 * )
 */
class EcoActionBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The entity storage for aquifers.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $nodeStorage;

  /**
   * The personalization Ip Service.
   *
   * @var \Drupal\personalization\PersonalizationIpServiceInterface
   */
  protected $personalizationIpService;

  /**
   * Constructs an EcoActionBlock object.
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
  public function __construct(array $configuration, $plugin_id, $plugin_definition, $node_storage, PersonalizationIpServiceInterface $personalization_ip_service) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->nodeStorage = $node_storage;
    $this->personalizationIpService = $personalization_ip_service;
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
      $container->get('personalization.personalization_ip_service')
    );
  }


  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    // By default, the block will contain 5 items and set the radius to 10.
    return array(
      'block_count' => 5,
      'radius' => 10,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $range = range(2, 20);
    $form['block_count'] = array(
      '#type' => 'select',
      '#title' => $this->t('Number of action items in block'),
      '#default_value' => $this->configuration['block_count'],
      '#options' => array_combine($range, $range),
    );
    $range = range(1, 20);
    $form['radius'] = array(
      '#type' => 'select',
      '#title' => $this->t('Radius (km) within which to search for action items.'),
      '#default_value' => $this->configuration['radius'],
      '#options' => array_combine($range, $range),
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['block_count'] = $form_state->getValue('block_count');
    $this->configuration['radius'] = $form_state->getValue('radius');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    // We are just retrieving all of the eco actions. In a real situation we
    // would do something like filtering the actions so that they were within a
    // certain radius of the user.
    $radius = $this->configuration['radius'];
    $ip = $this->personalizationIpService->getIpAddress();
    $coordinates = $this->personalizationIpService->mapIpAddress($ip);
    $result = $this->nodeStorage->getQuery()
      ->condition('type', 'water_eco_action')
      ->range(0, $this->configuration['block_count'])
      ->sort('title', 'ASC')
      ->execute();

    if ($result) {
      $items = $this->nodeStorage->loadMultiple($result);

      $build['ip_address_and_radius'] = [
        '#type' => 'markup',
        '#markup' => t('Eco actions within @radius kilometers of @coordinates [@ipaddress]', array('@radius' => $radius, '@ipaddress' => $ip, '@coordinates' => $coordinates)),
      ];
      $build['list'] = [
        '#theme' => 'item_list',
        '#items' => [],
      ];
      foreach ($items as $item) {
        $url = Url::fromUserInput('/node/' . $item->nid->value);
        $build['list']['#items'][$item->id()] = [
          '#type' => 'markup',
          '#markup' => Link::fromTextAndUrl($item->label(), $url)->toString(),
        ];
      }
    }
    else {
      $build['no_items'] = [
        '#type' => 'markup',
        '#markup' => t('There are no actions in your area.'),
      ];
    }
    return $build;
  }

  // We have not done anything with cache tags; the results of this block get
  // cached. If you add or delete wea pieces of content, you won't see
  // those changes reflected in this block unless you get the cache to clear.
  // One way to do this (which is faster than clearing the cache for the entire
  // site) is to go into the block layout and configure and save this block.

}