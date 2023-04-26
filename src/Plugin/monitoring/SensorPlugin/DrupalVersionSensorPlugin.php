<?php

namespace Drupal\hcpss_monitoring\Plugin\monitoring\SensorPlugin;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\monitoring\Annotation\SensorPlugin;
use Drupal\monitoring\Entity\SensorConfig;
use Drupal\monitoring\Result\SensorResultInterface;
use Drupal\monitoring\SensorPlugin\SensorPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @SensorPlugin(
 *   id = "drupal_version",
 *   label = @Translation("Drupal Version"),
 *   description = @Translation("Current Drupal version."),
 *   addable = TRUE,
 * )
 */
class DrupalVersionSensorPlugin extends SensorPluginBase {

  /**
   * @var ModuleHandlerInterface
   */
  protected $moduleHandler;

  public function __construct(SensorConfig $sensor_config, $plugin_id, $plugin_definition, ModuleHandlerInterface $moduleHandler) {
    parent::__construct($sensor_config, $plugin_id, $plugin_definition);
    $this->moduleHandler = $moduleHandler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, SensorConfig $sensor_config, $plugin_id, $plugin_definition) {
    return new static(
      $sensor_config,
      $plugin_id,
      $plugin_definition,
      $container->get('module_handler')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function runSensor(SensorResultInterface $result) {
    $available = update_get_available(TRUE);
    $this->moduleHandler->loadInclude('update', 'compare.inc');
    $updates = update_calculate_project_data($available);
    $current_version = \Drupal::VERSION;
    $recommended_version = $updates['drupal']['recommended'];

    $status = SensorResultInterface::STATUS_OK;
    $message = "Current Drupal version is $current_version.";

    if ($current_version != $recommended_version) {
      $status = SensorResultInterface::STATUS_WARNING;
      $message .= " $recommended_version is recommended.";
    }

    $result->setStatus($status);
    $result->setValue($current_version);
    $result->setExpectedValue($recommended_version);
    $result->setMessage($message);
  }
}
