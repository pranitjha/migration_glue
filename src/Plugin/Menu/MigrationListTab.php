<?php

namespace Drupal\migration_glue\Plugin\Menu;

use Drupal\Core\Menu\LocalTaskDefault;
use Drupal\Core\Routing\RouteMatchInterface;

class MigrationListTab extends LocalTaskDefault {

  /**
  * {@inheritdoc}
  */
  public function getRouteParameters(RouteMatchInterface $route_match) {
    $migration_group = $route_match->getParameter('migration_group');
    return [
      'migration_group' => empty($migration_group) ? 'default' : $migration_group,
    ];
  }

}
