<?php

namespace Drupal\sitedash_connector\Controller;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\system\SystemManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * The controller that will respond to requests on the connected website.
 */
class WebsiteInformationController extends ControllerBase {
  /**
   * System Manager Service.
   *
   * @var \Drupal\system\SystemManager
   */
  protected $systemManager;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * WebsiteInformationController constructor.
   *
   * @param Drupal\system\SystemManager $systemManager
   *   The system manager variable to access status report information.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The configuration factory.
   */
  public function __construct(SystemManager $systemManager, ConfigFactoryInterface $configFactory) {
    $this->systemManager = $systemManager;
    $this->configFactory = $configFactory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('system.manager'),
      $container->get('config.factory')
    );
  }

  /**
   * The endpoint callback function for handling requests.
   *
   * @param \Symfony\Component\HttpFoundation\Request $http_request
   *   The HTTP request that was received.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The response in JSON format.
   */
  public function data(Request $http_request) {
    $content = json_decode($http_request->getContent(), TRUE);
    if (!empty($content)) {
      if ($this->config('sitedash_connector.settings')->get('token') != $content['token']) {
        return new JsonResponse(['Invalid Token'], 403);
      }
      switch ($content['information']) {
        case 'status_report':
          $data = $this->statusReport();
          break;

        case 'logs':
          $data = $this->getLogs();
          break;

        case 'content_statistics':
          $data = $this->getContentStatistics();
          break;

      }

      return new JsonResponse($data, 200);
    }

    return new JsonResponse(['Invalid Request'], 200);
  }

  /**
   * Provides status report information.
   *
   * @return array
   *   Returns the data from the status report page.
   */
  public function statusReport() {
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
    return $data;
  }

  /**
   * Provides logs information.
   *
   * @return array
   *   Returns the website logs.
   */
  public function getLogs() {
    // @todo Add logic to fetch log messages.
    return [];
  }

  /**
   * Provides content statistics.
   *
   * @return array
   *   Returns the content statistics.
   */
  public function getContentStatistics() {
    // @todo Add logic to fetch content statistics.
    return [];
  }

}
