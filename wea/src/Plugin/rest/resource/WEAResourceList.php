<?php

namespace Drupal\wea\Plugin\rest\resource;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Provides a resource to list water eco action items.
 *
 * @RestResource(
 *   id = "wea",
 *   label = @Translation("Water eco action item list"),
 *   uri_paths = {
 *     "canonical" = "/wea/actions"
 *   }
 * )
 */
class WEAResourceList extends ResourceBase {

  /**
   * The currently selected language.
   *
   * @var \Drupal\Core\Language\Language
   */
  protected $currentLanguage;

  /**
   * The entity storage for aquifers.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $nodeStorage;

  /**
   * Constructs a Drupal\wea\Plugin\rest\resource\WEAResourceList object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager
   * @param array $serializer_formats
   *   The available serialization formats.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, $serializer_formats, LoggerInterface $logger, LanguageManagerInterface $language_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
    $this->currentLanguage = $language_manager->getCurrentLanguage();
    $this->nodeStorage = $entity_type_manager->getStorage('node');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('rest'),
      $container->get('language_manager')
    );
  }

  /**
   * Responds to GET requests.
   *
   * Returns a list of water eco action items.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The response containing the list of items.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   */
  public function get() {
    // We are just retrieving all of the water eco action items. In a real
    // situation we might do something like inspecting query arguments to filter
    // and sort them by some other criteria.
    $result = $this->nodeStorage->getQuery()
      ->condition('type', 'water_eco_action')
      ->condition('langcode', $this->currentLanguage->getId())
      ->condition('status', 1)
      ->sort('title', 'ASC')
      ->execute();

    if ($result) {
      $items = $this->nodeStorage->loadMultiple($result);
      foreach ($items as $item) {
        $translated_item = $item->getTranslation($this->currentLanguage->getId());
        $record[] = [
          'id' => $item->nid->value,
          'title' => $translated_item->getTitle()
        ];
      }
    }
    if (!empty($record)) {
      $response = new ResourceResponse($record);
      $response->addCacheableDependency($record);
      return $response;
    }
    throw new NotFoundHttpException(t('No water eco action items were found.'));

  }

}
