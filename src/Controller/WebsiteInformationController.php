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
      return new JsonResponse($this->statusReport(), 200);
    }
  }

  /**
   * Responds to information GET requests.
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

}
