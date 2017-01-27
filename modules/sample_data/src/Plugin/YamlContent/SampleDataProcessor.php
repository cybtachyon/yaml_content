<?php

namespace Drupal\sample_data\Plugin\YamlContent;

use Drupal\yaml_content\ImportProcessorBase;
use Drupal\sample_data\SampleDataLoader;

/**
 * Import processor to support entity queries and references.
 *
 * @ImportProcessor(
 *   id = "sample_data",
 *   label = @Translation("Sample Data Processor"),
 * )
 */
class SampleDataProcessor extends ImportProcessorBase {

  /**
   * @var SampleDataLoader
   */
  protected $data_loader;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->data_loader = \Drupal::service('sample_data.loader');
  }

  /**
   * {@inheritdoc}
   */
  public function preprocess(array &$import_data) {
    $context = $this->configuration;

    if (isset($context['dataset'])) {
      $data = $this->loadSampleDataSet($context['dataset']);

      $value = $data->get($context['lookup']);
    }
    elseif (isset($context['data_type'])) {
      $params = isset($context['params']) ? $context['params'] : [];
      $value = $this->data_loader->loadSample($context['data_type'], $params);
    }

    $import_data[] = $value;
  }

  /**
   * Load sample data set.
   *
   * The following keys are searched for within the $config array:
   * - module
   *   The module containing the data file to be loaded.
   * - path
   *   The path within the module to look for the data file. Defaults to
   *   `content/data`.
   * - file
   *   The name of the data file such that the file name is `<file>.data.yml`.
   *
   * @param array $config
   * @return \Drupal\sample_data\SampleDataSet
   */
  protected function loadSampleDataSet(array $config) {
    $path = drupal_get_path('module', $config['module']);
    $path .= '/' . (isset($config['path']) ? $config['path'] : 'content/data');
    $path .= '/' . $config['file'] . '.data.yml';

    $data = $this->data_loader->loadDataSet($path);

    return $data;
  }
}