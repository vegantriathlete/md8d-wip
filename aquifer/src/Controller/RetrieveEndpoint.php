<?php

/**
 * @file
 * Contains \Drupal\aquifer\RetrieveEndpoint
 */

namespace Drupal\aquifer\Controller;

use Drupal\aquifer\AquiferRetrievalServiceInterface;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller methods for the RetrieveEndpoint controller
 *
 * This illustrates injecting a service with the service container and calling
 * a method of that service to retrieve a value. It builds a simple page that
 * displays the value.
 */
class RetrieveEndpoint extends ControllerBase {

  /**
   * Aquifer retrieval service.
   *
   * @var \Drupal\aquifer\AquiferRetrievalServiceInterface
   */
  protected $aquiferRetrievalService;

  /**
   * {@inheritdoc}
   */
  public function __construct(AquiferRetrievalServiceInterface $aquiferRetrievalService) {
    $this->aquiferRetrievalService = $aquiferRetrievalService;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('aquifer.aquifer_retrieval_service')
    );
  }

  /**
   * Return the name of the endpoint
   */
  public function getRESTEndpoint() {
    return [
      '#markup' => $this->t('The endpoint is: @endpoint', array('@endpoint' => $this->aquiferRetrievalService->getRESTEndpoint()))
    ];
  }

}
