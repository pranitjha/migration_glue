<?php
namespace Drupal\mysql_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for the comments.
 *
 * @MigrateSource(
 *   id = "comments"
 * )
 */
class Comments extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('comments', 'ct')
      ->fields('ct');
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'id' => [
        'type' => 'integer',
        'alias' => 'ct',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'id' => $this->t('ID'),
      'content_id' => $this->t('Content Id'),
      'title' => $this->t('Title'),
      'content' => $this->t('Content'),
      'comment_by' => $this->t('Comment By'),
      'reply_to' => $this->t('Reply To'),
      'created' => $this->t('Created'),
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
