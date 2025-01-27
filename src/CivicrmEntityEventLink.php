<?php

namespace Drupal\civicrm_entity_calendar_links;

use Drupal\civicrm_entity\CiviCrmApi;
use Spatie\CalendarLinks\Link as CalendarLink;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @todo Add class description.
 */
final class CivicrmEntityEventLink {

  /**
   * The CiviCRM API Service.
   *
   * @var \Drupal\civicrm_entity\CiviCrmApi
   */
  protected $civicrmApi;

  /**
   * CivicrmEntityEventLink constructor.
   *
   * @param \Drupal\civicrm_entity\CiviCrmApi $civicrm_api
   *   The CiviCRM API Service.
   */
  public function __construct(CiviCrmApi $civicrm_api) {
    $this->civicrmApi = $civicrm_api;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('civicrm_entity.api')
    );
  }

  /**
   * Get the event link.
   *
   * @param object $event
   *   The event object.
   * @param string $calendar_type
   *   The calendar type.
   *
   * @return string
   *   The event link.
   */

  public function getEventLink($event, $calendar_type) {

    $location_id = $event->get('loc_block_id')->getValue();
    $location = $this->civicrmApi->get('LocBlock', [
      'sequential' => 1,
      'id' => $location_id[0]['target_id'],
    ]);
    $address = $this->civicrmApi->get('Address', [
      'sequential' => 1,
      'id' => $location[0]['address_id'],
    ]);
    $state = $this->civicrmApi->get('StateProvince', [
      'sequential' => 1,
      'id' => $address[0]['state_province_id'],
    ]);
    $address[0]['state_province'] = $state[0]['abbreviation'];
    // create the google calendar link
    $link = CalendarLink::create(
      $event->get('title')->getValue()[0]['value'],
      \DateTime::createFromFormat('Y-m-d\\TH:i:s', $event->get('start_date')->getValue()[0]['value'], new \DateTimeZone('UTC')),
      \DateTime::createFromFormat('Y-m-d\\TH:i:s', $event->get('end_date')->getValue()[0]['value'], new \DateTimeZone('UTC'))
    )
      ->description($event->get('summary')->getValue()[0]['value'])
      ->address($address[0]['street_address'] . ', ' . $address[0]['city'] . ', ' . $address[0]['state_province'] . ' ' . $address[0]['postal_code']);

    switch ($calendar_type) {
      case 'google':
        return $link->google();
      case 'outlook':
        return $link->webOutlook();
      case 'office':
        return $link->webOffice();

    // raise an exception if the calendar type is not supported
    throw new \InvalidArgumentException('Unsupported calendar type: ' . $calendar_type);
    }
  }

}
