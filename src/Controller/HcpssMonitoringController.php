<?php

namespace Drupal\hcpss_monitoring\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\monitoring\Sensor\SensorManager;
use Drupal\monitoring\SensorRunner;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Returns responses for HCPSS Monitoring routes.
 */
class HcpssMonitoringController extends ControllerBase  {

  /**
   * The sensor runner.
   *
   * @var \Drupal\monitoring\SensorRunner
   */
  protected $sensorRunner;

  /**
   * The sensor manager.
   *
   * @var \Drupal\monitoring\Sensor\SensorManager
   */
  protected $sensorManager;

  /**
   * Constructs a \Drupal\monitoring\Form\SensorDetailForm object.
   *
   * @param \Drupal\monitoring\SensorRunner $sensor_runner
   *   The factory for configuration objects.
   * @param \Drupal\monitoring\Sensor\SensorManager $sensor_manager
   *   The sensor manager service.
   */
  public function __construct(SensorRunner $sensor_runner, SensorManager $sensor_manager) {
    $this->sensorRunner = $sensor_runner;
    $this->sensorManager = $sensor_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('monitoring.sensor_runner'),
      $container->get('monitoring.sensor_manager')
    );
  }

  private function getSensorResults() {
    $results = $this->sensorRunner->runSensors();

    $output = [];
    /** @var \Drupal\monitoring\SensorConfigInterface $sensor_config */
    foreach ($this->sensorManager->getEnabledSensorConfig() as $sensor_config) {
      /** @var \Drupal\monitoring\Result\SensorResultInterface $sensor_result */
      $sensor_result = $results[$sensor_config->id()];

      $result_array = $sensor_result->toArray();
      $result_array['label'] = $sensor_config->label();
      $result_array['description'] = $sensor_config->getDescription();
      $result_array['category'] = $sensor_config->getCategory();

      $output[] = $result_array;
    }

    return $output;
  }

  /**
   * Builds the response.
   */
  public function build() {
    return new JsonResponse($this->getSensorResults());
  }

  public function table() {
    $results = $this->getSensorResults();
    $header = array_keys($results[0]);
    $rows = [];
    foreach ($results as $result) {
      $rows[] = $result;
    }

    return [
      'build' => [
        '#type' => 'table',
        '#header' => $header,
        '#rows' => $rows,
      ]
    ];
  }
}
