<?php

namespace Drupal\aquifer\Plugin\QueueWorker;

use Drupal\aquifer\AquiferManagerServiceInterface
use Drupal\Core\Queue\QueueWorkerBase;

/**
 * Updates the aquifer content types.
 *
 * @QueueWorker(
 *   id = "aquifer_updates",
 *   title = @Translation("Update Aquifers"),
 *   cron = {"time" = 60}
 * )
 */
class AquiferUpdate extends QueueWorkerBase {

  /**
   * The Aquifer Manager Service
   * @var Drupal\aquifer\AquiferManagerServiceInterface
   */
  protected $aquiferManagerService;

  // @todo: I've got to see if QueueWorkerBase gives me access to the service
  //        containter so that I can write a __construct method that will
  //        receive the AquiferManagerService
  /**
   * Constructs the Queue Worker
   */
  public function __construct() {
  }

  /**
   * {@inheritdoc}
   */
  public function processItem($data) {
  }

}
