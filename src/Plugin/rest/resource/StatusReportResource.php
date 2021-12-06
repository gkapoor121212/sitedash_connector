<?php

namespace Drupal\sitedash_connector\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ModifiedResourceResponse;
use Drupal\system\SystemManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a Status Report Resource.
 *
 * @RestResource(
 *   id = "status_report_resource",
 *   label = @Translation("Status Report Resource"),
 *   uri_paths = {
 *     "canonical" = "/website-information/status-report"
 *   }
 * )
 */
class StatusReportResource extends ResourceBase {

  /**
   * System Manager Service.
   *
   * @var \Drupal\system\SystemManager
   */
  protected $systemManager;

  /**
   * Constructs a new Status Report Resource object.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, array $serializer_formats, LoggerInterface $logger, SystemManager $systemManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
    $this->systemManager = $systemManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('sitedash_connector'),
      $container->get('system.manager')
    );
  }

  /**
   * Responds to information GET requests.
   *
   * @return \Drupal\rest\ResourceResponse
   *   Returns the JSON response of data from the status report page.
   */
  public function get() {
    $data = [];
    $requirements = $this->systemManager->listRequirements();
    foreach ($requirements as $property => $requirement) {
      if (!is_null($requirement['title'])) {
        $title = $requirement['title']->__tostring();
      }
      if (!is_null($requirement['value'])) {
        if (is_string($requirement['value'])) {
          $value = $requirement['value'];
        }
        else {
          $value = $requirement['value']->__tostring();
        }
      }
      $data[$property] = [
        'title' => $title,
        'value' => $value,
        'severity' => $requirement['severity'],
        'weight' => $requirement['weight'],
      ];
      $title = '';
      $value = '';
    }
    return new ModifiedResourceResponse($data);
  }

}
