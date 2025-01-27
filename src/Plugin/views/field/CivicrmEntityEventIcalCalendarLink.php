<?php

namespace Drupal\civicrm_entity_calendar_links\Plugin\views\field;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Drupal\core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Render\Markup;

/**
 * @file
 * Defines Drupal\civicrm_entity_calendar_links\Plugin\views\field\EventViewsField.
 */

/**
 * Field handler to flag the node type.
 *
 * @ingroup views_field_handlers
 * @ViewsField("civicrm_entity_event_ical_calendar_link")
 */
class CivicrmEntityEventIcalCalendarLink extends FieldPluginBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

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
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
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
    $params = [
      'civicrm_event' => $event->id(),
    ];
    $ical_url = Url::fromRoute('civicrm_entity_calendar_links.event.ical', $params);
    // append font awesome icon to the link
    $render = [
      '#type' => 'link',
      '#title' => Markup::create('iCal/Outlook'),
      '#url' => $ical_url,
    ];
    return $render;
  }
}
