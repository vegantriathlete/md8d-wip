<?php

namespace Drupal\aquifer\Plugin\QueueWorker;

use Drupal\aquifer\AquiferManagerServiceInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Updates the aquifer content types.
 *
 * @QueueWorker(
 *   id = "aquifer_updates",
 *   title = @Translation("Update Aquifers"),
 *   cron = {"time" = 60}
 * )
 */
class AquiferUpdate extends QueueWorkerBase implements ContainerFactoryPluginInterface {

  /**
   * The Aquifer Manager Service
   * @var Drupal\aquifer\AquiferManagerServiceInterface
   */
  protected $aquiferManagerService;

  /**
   * Constructs the Queue Worker
   */
  public function __construct(AquiferManagerServiceInterface $aquifer_manager_service) {
    $this->aquiferManagerService = $aquifer_manager_service;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $container->get('aquifer.aquifer_manager_service')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function processItem($data) {
    $this->aquiferManagerService->updateAquifer($data);
  }

}