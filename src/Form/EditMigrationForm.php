<?php

namespace Drupal\migration_glue\Form;

use Drupal\Core\Config\StorageInterface;
use Drupal\migration_glue\MigrationGlueManager;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Serialization\Yaml;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class EditMigrationForm.
 */
class EditMigrationForm extends FormBase {

  /**
   * The config storage.
   *
   * @var \Drupal\Core\Config\StorageInterface
   */
  protected $configStorage;

  /**
   * Migration glue manager.
   *
   * @var \Drupal\migration_glue\MigrationGlueManager
   */
  protected $migrationGlueManager;

  /**
   * EditMigrationForm constructor.
   *
   * @param \Drupal\Core\Config\StorageInterface $config_storage
   *   Config storage.
   * @param \Drupal\migration_glue\MigrationGlueManager $glue_manager
   *   Migration glue manager.
   */
  public function __construct(StorageInterface $config_storage, MigrationGlueManager $glue_manager) {
    $this->configStorage = $config_storage;
    $this->migrationGlueManager = $glue_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.storage'),
      $container->get('migration_glue.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'edit_migration_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['migration'] = [
      '#type' => 'select',
      '#title' => $this->t('Select migration'),
      '#required' => TRUE,
      '#options' => ['' => $this->t('- Select -')] + $this->migrationGlueManager->getMigrationList(),
      '#ajax' => [
        'callback' => '::updateExport',
        'wrapper' => 'migration-wrapper',
      ],
    ];

    $form['export'] = [
      '#title' => $this->t('Here is your migration configuration:'),
      '#type' => 'textarea',
      '#rows' => 24,
      '#required' => TRUE,
      '#prefix' => '<div id="migration-wrapper">',
      '#suffix' => '</div>',
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Update'),
    ];

    return $form;
  }

  /**
   * Handles switching the export textarea.
   */
  public function updateExport($form, FormStateInterface $form_state) {
    $name = 'migrate_plus.migration.' . $form_state->getValue('migration');
    $form['export']['#value'] = Yaml::encode($this->configStorage->read($name));
    return $form['export'];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $value = $form_state->getValue('export');
    $yml_data = Yaml::decode($value);
    $this->migrationGlueManager->registerMigration($yml_data);
    drupal_set_message($this->t('Migration is updated successfully'));
  }

}
