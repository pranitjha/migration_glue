<?php

namespace Drupal\migration_glue\Plugin\DestinationType;

use Drupal\migration_mapper\DestinationTypeBase;

/**
 * Provides a 'user' destination entity type.
 *
 * @DestinationType(
 *   id = "user",
 *   name = @Translation("User"),
 *   excludedFields = { },
 * )
 */
class Users extends DestinationTypeBase {

  /**
   * {@inheritdoc}
   */
  public function getDestinationYml() {
    return [
      'plugin' => 'entity:user',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getDestinationTypes() {
    $options = ['user' => 'User'];
    return $options;
  }

}
