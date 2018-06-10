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
    $form_builder = \Drupal::formBuilder();
    $form = $form_builder->getForm('\Drupal\migration_mapper\Form\MigrationAdminForm');
    return $form;
  }

  /**
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
      // @todo: Use messageer service.
      drupal_set_message(t('Please create your migration first.'), 'error');
      $url = Url::fromRoute('migration_glue.create_migration')->toString();
      return new RedirectResponse($url);
    }

    $form_builder = \Drupal::formBuilder();
    $form = $form_builder->getForm('\Drupal\migrate_tools\Form\MigrationExecuteForm');
    return $form;
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
    $render = \Drupal::entityTypeManager()->getListBuilder('migration')->render();
    return $render;
  }

}
