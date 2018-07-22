<?php

namespace Drupal\migration_glue\Plugin\DataSource;

use Drupal\Core\Form\FormStateInterface;
use Drupal\migration_mapper\DataSourceBase;

/**
 * Provides a 'xml' data source.
 *
 * @DataSource(
 *   id = "xml",
 *   name = @Translation("XML"),
 * )
 */
class Xml extends DataSourceBase {

  protected $sourcePaths = [];

  /**
   * {@inheritdoc}
   */
  public function process(string $data, array $config) {
    // Load XML object.
    $xml = simplexml_load_string($data);
    $xpath = $item_path = $config['source']['xml_item_selector'];;
    $simple_xml_elements = $xml->xpath($xpath);

    foreach ($simple_xml_elements as $simple_xml_element) {
      foreach ($simple_xml_element->children() as $children) {
        $name = $children->getName();
      }
    }

    // Here we hardcoding returning array. We need to return data in this
    // format.
    return [
      '/pages/page/id' => '/pages/page/id',
      '/pages/page/title' => '/pages/page/title',
      '/pages/page/names' => '/pages/page/names',
    ];

  }

  /**
   * {@inheritdoc}
   */
  public function getSourceConfigForm(FormStateInterface $form_state) {
    $form = [];
    $form['source']['xml_path'] = [
      '#type' => 'textfield',
      '#title' => 'Path to Xml file.',
      '#default_value' => '/path/to/sample/xml/file.xml',
    ];
    $form['source']['xml_item_selector'] = [
      '#type' => 'textfield',
      '#title' => 'Item selector',
      '#default_value' => '/this/test',
    ];
    $form['source']['xml_key'] = [
      '#type' => 'textfield',
      '#title' => 'Key',
      '#default_value' => 'id',
      '#size' => 20,
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getDestinationConfigForm(FormStateInterface $form_state) {
    $form = [];
    $form['source']['unique_row_id'] = [
      '#type' => 'select',
      '#title' => 'Unique row identifier.',
      '#options' => $form_state->get('columns'),
    ];
    $form['source']['unique_row_type'] = [
      '#type' => 'select',
      '#title' => 'Unique row type.',
      '#options' => ['string' => 'String', 'integer' => 'Integer'],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getSourceSection(FormStateInterface $form_state) {
    return [
      'source' => [
        'plugin' => 'url',
        'data_parser_plugin' => 'xml',
        'data_fetcher_plugin' => 'file',
        'urls' => $form_state->getValue('data_source_config_wrapper')['source']['xml_path'],
        'item_selector' => $form_state->getValue('data_source_config_wrapper')['source']['xml_item_selector'],
        'fields' => $this->sourceFieldFormatter($form_state->get('columns')),
        'ids' => [$form_state->getValue('unique_row_id') => ['type' => $form_state->getValue('unique_row_type')]],
      ],
    ];
  }

  /**
   * Source field formatter for yml.
   *
   * @param array $columns
   *   Column names.
   *
   * @return array
   *   Formatted column names.
   */
  private function sourceFieldFormatter(array $columns) {
    $output = [];
    foreach ($columns as $value) {
      $field = [];
      $field['name'] = $field['label'] = $field['selector'] = $value;
      array_push($output, $field);
    }
    return $output;

  }

}
