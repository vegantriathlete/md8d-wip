<?php

namespace Drupal\wea\Plugin\rest\resource;

use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\node\Entity\Node;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Provides a resource for a water eco action item.
 *
 * @RestResource(
 *   id = "wea_resource",
 *   label = @Translation("Water eco action item"),
 *   uri_paths = {
 *     "canonical" = "/wea/actions/{node}",
 *   }
 * )
 */
class WEAResource extends ResourceBase {

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * The currently selected language.
   */
  protected $currentLanguage;

  /**
   * Constructs a Drupal\wea\Plugin\rest\resource\WEAResource object.
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
   *   The config factory.
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
   * Responds to entity GET requests.
   *
   * Returns a water eco action item for the specified ID.
   *
   * @param \Drupal\node\Entity\Node $node
   *   The node object.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The response containing the entity with its accessible fields.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   */
  public function get(Node $node) {
    $node_access = $node->access('view', NULL, TRUE);
    if (!$node_access->isAllowed()) {
      throw new AccessDeniedHttpException();
    }
$record = [
  'id' => $id
  'type' => $node->getType();
  'title' => $node->getTitle();
];
      if (!empty($record)) {
        return new ResourceResponse($record);
      }

    }

  }

}
