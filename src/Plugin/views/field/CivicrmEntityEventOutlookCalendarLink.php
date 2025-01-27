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
 * @ViewsField("civilrm_entity_event_outlook_calendar_link")
 */
class CivicrmEntityEventOutlookCalendarLink extends FieldPluginBase {

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

  // protected function defineOptions() {
  //   $options = parent::defineOptions();
  //   $options['disable_when_event_full'] = ['default' => '0'];
  //   return $options;
  // }

  /**
   * {@inheritdoc}
   */

  // public function buildOptionsForm(&$form, FormStateInterface $form_state) {
  //   $form['disable_when_event_full'] = [
  //     '#type' => 'checkbox',
  //     '#title' => $this->t('Disable when event is full'),
  //     '#default_value' => $this->options['disable_when_event_full'],
  //   ];
  //   parent::buildOptionsForm($form, $form_state);
  // }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {

    /** @var \Drupal\civicrm_entity\Event $event */
    $event = $values->_entity;

    $url = Url::fromUri($this->eventLink->getEventLink($event, 'outlook'));
    // append font awesome icon to the link
    $render = [
      '#type' => 'link',
      '#title' => Markup::create('Outlook.com'),
      '#url' => $url,
    ];
    return $render;
  }
}
