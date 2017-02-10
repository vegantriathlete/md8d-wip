<?php

namespace Drupal\wea\Plugin\rest\resource;

use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
   */
  protected $currentLanguage;

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
    $this->languageManager = $language_manager;
    $this->language = $this->language_manager->getCurrentLanguage();
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
   *   The response containing the log entry.
   */
  public function get() {
      //$record = db_query("SELECT * FROM {watchdog} WHERE wid = :wid", array(':wid' => $id))
      //  ->fetchAssoc();
$record = [
  'item_1' => array(
    'id' => 1,
    'title' => 'first wea item'
  ),
  'item_2' => array(
    'id' => 2,
    'title' => 'second wea item'
  )
];
      if (!empty($record)) {
        return new ResourceResponse($record);
      }

  }

}
