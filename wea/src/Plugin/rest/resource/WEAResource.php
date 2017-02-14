<?php

namespace Drupal\wea\Plugin\rest\resource;

use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\Entity\Node;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ModifiedResourceResponse;
use Drupal\rest\ResourceResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Provides a resource for a water eco action item.
 *
 * @RestResource(
 *   id = "wea_resource",
 *   label = @Translation("Water eco action item"),
 *   uri_paths = {
 *     "canonical" = "/wea/actions/{id}",
 *     "https://www.drupal.org/link-relations/create" = "/wea/actions"
 *   }
 * )
 */
class WEAResource extends ResourceBase {

  /**
   * The currently selected language.
   *
   * @var \Drupal\Core\Language\Language
   */
  protected $currentLanguage;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

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
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The currently logged in user.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, $serializer_formats, LoggerInterface $logger, LanguageManagerInterface $language_manager, AccountInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
    $this->currentLanguage = $language_manager->getCurrentLanguage();
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
      $container->get('entity_type.manager'),
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('rest'),
      $container->get('language_manager'),
      $container->get('current_user')
    );
  }

  /**
   * Responds to entity GET requests.
   *
   * Returns a water eco action item for the specified ID.
   *
   * @param string $id
   *   The ID of the object.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The response containing the entity with its accessible fields.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   */
  public function get($id) {
    if ($node = Node::load($id)) {
      $translated_node = $node->getTranslation($this->currentLanguage->getId());
      $node_access = $translated_node->access('view', NULL, TRUE);
      if (!$node_access->isAllowed()) {
        throw new AccessDeniedHttpException();
      }
      if ($node->getType() == 'water_eco_action' && $translated_node->status->value == 1) {
        $record = [
          'title' => $translated_node->getTitle(),
          'description' => $translated_node->field_wea_description->value,
          'coordinates' => $translated_node->field_wea_coordinates->value
        ];
      }
    }

    if (!empty($record)) {
      $response = new ResourceResponse($record, 200);
      $response->addCacheableDependency($record);
      return $response;
    }

    throw new NotFoundHttpException(t('Water eco action item with ID @id was not found', array('@id' => $id)));
  }

  /**
   * Responds to POST requests and saves a new water eco action item.
   *
   * @param array $data
   *   The POST data.
   *
   * @return \Drupal\rest\ModifiedResourceResponse
   *   The HTTP response object.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   */
  public function post($data = NULL) {
    if ($data == NULL) {
      throw new BadRequestHttpException('No data received.');
    }

    if (!$this->currentUser->hasPermission('create water_eco_action content')) {
      throw new AccessDeniedHttpException();
    }

    $node = Node::create(
      array(
        'type' => 'water_eco_action',
        'title' => $data['title'],
        'status' => 0,
        'langcode' => $data['language_code'],
        'field_wea_description' => $data['description'],
        'field_wea_status' => 'pending'
      )
    );
    try {
      $node->save();
      $this->logger->notice('Created Water Eco Action with ID %id.', array('%id' => $node->id()));

      // 201 Created responses return the newly created node in the response
      // body. These responses are not cacheable, so we add no cacheability
      // metadata here.
      $url = $node->urlInfo('canonical', ['absolute' => TRUE])->toString(TRUE);
      $response = new ModifiedResourceResponse($node, 201, ['Location' => $url->getGeneratedUrl()]);
      return $response;
    }
    catch (EntityStorageException $e) {
      throw new HttpException(500, 'Internal Server Error', $e);
    }
  }

  /**
   * Responds to PATCH requests and updates a water eco action item.
   *
   * @param string $id
   *   The ID of the object.
   * @param array $data
   *   The PATCH data.
   *
   * @return \Drupal\rest\ModifiedResourceResponse
   *   The HTTP response object.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   */
  public function patch($id, $data = NULL) {
    if ($data == NULL) {
      throw new BadRequestHttpException('No data received.');
    }
    if ($node = Node::load($id)) {
      if ($node->getType() != 'water_eco_action') {
        throw new BadRequestHttpException('You have not requested a Water Eco Action item.');
      }
      if (!$node->access('update')) {
        throw new AccessDeniedHttpException();
      }
      if (isset($data['language_code'])) {
        if ($node->hasTranslation($data['language_code'])) {
          $translated_node = $node->getTranslation($data['language_code']);
        }
        else {
          throw new BadRequestHttpException('This translation does not yet exist.');
        }
      }
      else {
        $translated_node = $node;
      }

      if (isset($data['title'])) {
        $translated_node->set('title', $data['title']);
      }
      $wea_fields = array(
        'contact_email',
        'coordinates',
        'description',
        'status',
        'urgency'
      );
      foreach ($wea_fields as $field) {
        if (isset($data[$field])) {
          // Note: We'd want to do some type of data validation
          $translated_node->set('field_wea_' . $field, $data[$field]);
        }
      }

      try {
        $translated_node->save();
        $this->logger->notice('Updated water eco action item with ID %id.', array('%id' => $id));

        // Return the updated node in the response body.
        return new ModifiedResourceResponse($translated_node, 200);
      }
      catch (EntityStorageException $e) {
        throw new HttpException(500, 'Internal Server Error', $e);
      }
    }
    throw new NotFoundHttpException(t('Water eco action item with ID @id was not found', array('@id' => $id)));
  }

  /**
   * Responds to water eco action DELETE requests.
   *
   * @param string $id
   *   The ID of the object.
   *
   * @return \Drupal\rest\ModifiedResourceResponse
   *   The HTTP response object.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   */
  public function delete($id) {
    if ($node = Node::load($id)) {
      if ($node->getType() != 'water_eco_action') {
        throw new BadRequestHttpException('You have not requested a Water Eco Action item.');
      }
      if (!$node->access('delete')) {
        throw new AccessDeniedHttpException();
      }
      if ($node->hasTranslation($this->currentLanguage->getId())) {
        $translated_node = $node->getTranslation($this->currentLanguage->getId());
      }
      else {
        throw new BadRequestHttpException('This translation does not yet exist.');
      }
      try {
        $translated_node->delete();
        $this->logger->notice('Deleted water eco action with ID %id and language %language.', array('%id' => $id, '%language' => $this->currentLanguage->getName()));

        // DELETE responses have an empty body.
        return new ModifiedResourceResponse(NULL, 204);
      }
      catch (EntityStorageException $e) {
        throw new HttpException(500, 'Internal Server Error', $e);
      }
    }
    throw new NotFoundHttpException(t('Water eco action item with ID @id was not found', array('@id' => $id)));
  }

}
