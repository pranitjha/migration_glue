<?php
namespace Drupal\mysql_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for the content.
 *
 * @MigrateSource(
 *   id = "content"
 * )
 */
class Content extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('content', 'c')
      ->fields('c');
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'id' => [
        'type' => 'integer',
        'alias' => 'c',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'id' => $this->t('ID'),
      'title' => $this->t('Title'),
      'body' => $this->t('Body'),
      'image' => $this->t('Image'),
      'keywords' => $this->t('Keywords'),
      'author' => $this->t('Author'),
      'path' => $this->t('Path'),
      'date_created' => $this->t('Date Created'),
      'status' => $this->t('Status'),
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
