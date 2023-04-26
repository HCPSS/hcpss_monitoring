<?php

namespace Drupal\hcpss_monitoring\Plugin\monitoring\SensorPlugin;

use Drupal\monitoring\Annotation\SensorPlugin;
use Drupal\monitoring\Result\SensorResultInterface;
use Drupal\monitoring\SensorPlugin\SensorPluginBase;

/**
 * @SensorPlugin(
 *   id = "php_version",
 *   label = @Translation("PHP Version"),
 *   description = @Translation("Current PHP version."),
 *   addable = TRUE,
 * )
 */
class PhpVersionSensorPlugin extends SensorPluginBase {

  /**
   * {@inheritdoc}
   */
  public function runSensor(SensorResultInterface $result) {
    $result->setStatus(SensorResultInterface::STATUS_OK);
    $result->setValue(phpversion());
    $result->setMessage('Current PHP version is ' . phpversion() . '.');
  }
}
