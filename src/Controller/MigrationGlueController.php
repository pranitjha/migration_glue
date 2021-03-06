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
      $this->messenger()->addError(t('Please create your migration first.'));
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
    $migration_list = $this->entityTypeManager()->getListBuilder('migration')->render();
    // Here we updating the 'execute' link so that user is taken to
    // 'migration_glue' section.
    if (!empty($migration_list['table']['#rows'])) {
      foreach ($migration_list['table']['#rows'] as $key => $row) {
        if (is_array($row['operations']) && !empty($row['operations']['data']['#links'])) {
          $url = $row['operations']['data']['#links']['simple_form']['url'];
          $url = Url::fromRoute('migration_glue.run_migration', $url->getRouteParameters());
          $migration_list['table']['#rows'][$key]['operations']['data']['#links']['simple_form']['url'] = $url;

          // Prepare url for delete.
          $delete_url = Url::fromRoute('migration_glue.delete_migration', [
            'id' => $key,
          ], [
            'query' => [
              'destination' => Url::fromRoute('migration_glue.list_migration', [
                'migration_group' => $migration_group,
              ])->toString(),
            ],
          ]);
          $migration_list['table']['#rows'][$key]['operations']['data']['#links']['delete'] = [
            'url' => $delete_url,
            'title' => $this->t('Delete'),
          ];

          // Prepare url for clone.
          $clone_url = Url::fromRoute('migration_glue.clone_migration', [
            'id' => $key,
          ], [
            'query' => [
              'destination' => Url::fromRoute('migration_glue.list_migration', [
                'migration_group' => $migration_group,
              ])->toString(),
            ],
          ]);
          $migration_list['table']['#rows'][$key]['operations']['data']['#links']['clone'] = [
            'url' => $clone_url,
            'title' => $this->t('Clone'),
          ];

          // Prepare url for edit.
          $edit_url = Url::fromRoute('migration_glue.edit_migration', [], [
            'query' => [
              'destination' => Url::fromRoute('migration_glue.list_migration', [
                'migration_group' => $migration_group,
              ])->toString(),
              'migration' => $key,
            ],
          ]);
          $migration_list['table']['#rows'][$key]['operations']['data']['#links']['edit'] = [
            'url' => $edit_url,
            'title' => $this->t('Edit'),
          ];
        }
      }
    }
    return $migration_list;
  }

  /**
   * Renders migration add form.
   *
   * @return array
   *   Migration add form.
   */
  public function addMigration() {
    return $this->formBuilder()->getForm('\Drupal\migration_glue\Form\AddMigrationForm');
  }

  /**
   * Renders migration edit form.
   *
   * @return array
   *   Migration edit form.
   */
  public function editMigrations() {
    return $this->formBuilder()->getForm('\Drupal\migration_glue\Form\EditMigrationForm');
  }

}
