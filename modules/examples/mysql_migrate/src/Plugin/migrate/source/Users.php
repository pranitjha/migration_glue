<?php
namespace Drupal\mysql_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for the users.
 *
 * @MigrateSource(
 *   id = "mysql_users",
 * )
 */
class Users extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('users', 'u')
      ->fields('u');
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'username' => [
        'type' => 'string',
        'alias' => 'u',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'id' => $this->t('ID'),
      'email' => $this->t('Email'),
      'username' => $this->t('User Name'),
      'password' => $this->t('Password'),
      'roles' => $this->t('Roles'),
      'created' => $this->t('Created'),
      'updated' => $this->t('Updated'),
      'status' => $this->t('Status'),
      'picture' => $this->t('Picture'),
      'language' => $this->t('Language'),
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
