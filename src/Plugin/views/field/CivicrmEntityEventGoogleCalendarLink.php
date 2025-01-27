<?php

namespace Drupal\civicrm_entity_calendar_links\Plugin\views\field;

use Drupal\civicrm_entity\CiviCrmApi;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Drupal\core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Render\Markup;
use Drupal\civicrm_entity_calendar_links\CivicrmEntityEventLink;

/**
 * @file
 * Defines Drupal\civicrm_entity_calendar_links\Plugin\views\field\EventViewsField.
 */

/**
 * Field handler to flag the node type.
 *
 * @ingroup views_field_handlers
 * @ViewsField("civicrm_entity_event_google_calendar_link")
 */
class CivicrmEntityEventGoogleCalendarLink extends FieldPluginBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The CiviCRM API Service.
   *
   * @var \Drupal\civicrm_entity\CiviCrmApi
   */
  protected $civicrmApi;

  /**
   * The CivicrmEntityEventLink Service.
   *
   * @var \Drupal\civicrm_entity_calendar_links\CivicrmEntityEventLink
   */
  protected $eventLink;

  /**
   * DetachedViewsField constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\civicrm_entity\CiviCrmApi $civicrm_api
   *   The CiviCRM API Service.
   * @param \Drupal\civicrm_entity_calendar_links\Utils\CivicrmEntityEventLink $eventLink
   *  The CivicrmEntityEventLink Service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, CiviCrmApi $civicrm_api, CivicrmEntityEventLink $eventLink) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
    $this->civicrmApi = $civicrm_api;
    $this->eventLink = $eventLink;
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
      $container->get('civicrm_entity.api'),
      $container->get('civicrm_entity_calendar_links.eventlink')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    // Do nothing -- to override the parent query.
  }

  /**
   * {@inheritdoc}
   */
  public function usesGroupBy() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {

    /** @var \Drupal\civicrm_entity\Event $event */
    $event = $values->_entity;

    $url = Url::fromUri($this->eventLink->getEventLink($event, 'google'));
    // append font awesome icon to the link
    $render = [
      '#type' => 'link',
      '#title' => Markup::create('Google Calendar'),
      '#url' => $url,
    ];
    return $render;
  }
}
