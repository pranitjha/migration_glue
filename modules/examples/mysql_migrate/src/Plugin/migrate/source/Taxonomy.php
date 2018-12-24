<?php
namespace Drupal\mysql_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for the content.
 *
 * @MigrateSource(
 *   id = "taxonomy"
 * )
 */
class Taxonomy extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('taxonomy', 't')
      ->fields('t');
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'term_name' => [
        'type' => 'string',
        'alias' => 't',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'term_id' => $this->t('Term ID'),
      'term_name' => $this->t('Term Name'),
      'term_desc' => $this->t('Term Desc'),
    ];

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    // Perform extra pre-processing for keywords terms, if needed.
    return parent::prepareRow($row);
  }
}
