<?php

namespace Drupal\migration_glue\Plugin\Menu;

use Drupal\Core\Menu\LocalTaskDefault;
use Drupal\Core\Routing\RouteMatchInterface;

class MigrationRunTab extends LocalTaskDefault {

  /**
  * {@inheritdoc}
  */
  public function getRouteParameters(RouteMatchInterface $route_match) {
    $migration_name = $route_match->getParameter('migration');
    $migration_group = $route_match->getParameter('migration_group');
    return [
      'migration' => empty($migration_name) ? 'no_migration' : $migration_name,
      'migration_group' => empty($migration_group) ? 'no_migration' : $migration_group,
    ];
  }

}
