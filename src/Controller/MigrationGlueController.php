<?php

namespace Drupal\migration_glue\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class MigrationGlueController
 * @package Drupal\migration_glue\Controller
 */
class MigrationGlueController extends ControllerBase {

  /**
   * Renders migration creation form.
   *
   * @return array
   *   Migration creation form.
   */
  public function createMigration() {
    return $this->formBuilder()->getForm('\Drupal\migration_mapper\Form\MigrationAdminForm');
  }

  /**
   * Execute migration form.
   *
   * @param string $migration_group
   *   Migration group name.
   * @param string $migration
   *   Migration name.
   *
   * @return array|RedirectResponse
   *   Migration execution form or redirects to migration creation form.
   */
  public function runMigration($migration_group = 'no_migration', $migration = 'no_migration') {
    if ($migration == 'no_migration' || $migration_group == 'no_migration') {
      $this->messenger->addError(t('Please create your migration first.'));
      $url = Url::fromRoute('migration_glue.create_migration')->toString();
      return new RedirectResponse($url);
    }

    return $this->formBuilder()->getForm('\Drupal\migrate_tools\Form\MigrationExecuteForm');
  }

  /**
   * Lists migration in given group.
   *
   * @param string $migration_group
   *   Migration group.
   *
   * @return array
   *   Lists migration in given group.
   */
  public function listMigrations($migration_group = 'default') {
    // @todo: Refactor/Adjust operation link/button on list page so that it
    // redirects or takes user to migration_glue.run_migration route or remove
    // those operation links if possible.
    return $this->entityTypeManager()->getListBuilder('migration')->render();
  }

}
