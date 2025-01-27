<?php

namespace Drupal\civicrm_entity_calendar_links\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\civicrm_entity\Entity\CivicrmEntity;
use Drupal\civicrm_entity\CiviCrmApi;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Template\TwigEnvironment;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for gptimers routes.
 */
final class CivicrmEntityEventICalController extends ControllerBase {

  /**
   * The controller constructor.
   */
  public function __construct(
    private readonly TwigEnvironment $twig,
    private readonly CiviCrmApi $civicrmApi,
  ) {
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('twig'),
      $container->get('civicrm_entity.api')
    );
  }

  /**
   * Builds the response.
   */
  public function __invoke(CivicrmEntity $civicrm_event): Response {

    return $this->generateICalFile($civicrm_event);
  }

  protected function generateICalFile(CivicrmEntity $civicrm_event) {

    $location_id = $civicrm_event->get('loc_block_id')->getValue();

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


    // Load the template twig file and inject the event data.
    $module_path = \Drupal::service('extension.list.module')->getPath('civicrm_entity_calendar_links');
    $template = $this->twig->load($module_path . '/templates/ical.twig');
    $content = $template->render([
      'event' => $civicrm_event,
      'address' => $address[0],
    ]);

    // Create the response object.
    $response = new Response();
    $response->setContent($content);
    $response->headers->set('Content-Type', 'text/calendar; charset=utf-8');
    $response->headers->set('Content-Disposition', 'attachment; filename="event.ics"');

    return $response;
  }
}
