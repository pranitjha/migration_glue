<?php

namespace Drupal\migration_glue\Form;

use Drupal\Core\Config\StorageInterface;
use Drupal\migration_glue\MigrationGlueManager;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Serialization\Yaml;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RequestStack;
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
   * Request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * EditMigrationForm constructor.
   *
   * @param \Drupal\Core\Config\StorageInterface $config_storage
   *   Config storage.
   * @param \Drupal\migration_glue\MigrationGlueManager $glue_manager
   *   Migration glue manager.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   Request stack.
   */
  public function __construct(StorageInterface $config_storage, MigrationGlueManager $glue_manager, RequestStack $request_stack) {
    $this->configStorage = $config_storage;
    $this->migrationGlueManager = $glue_manager;
    $this->requestStack = $request_stack;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.storage'),
      $container->get('migration_glue.manager'),
      $container->get('request_stack')
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

    // If migration id available in query string, then use that.
    if (!empty($migration = $this->requestStack->getCurrentRequest()->query->get('migration'))) {
      $form['migration']['#default_value'] = $migration;
      $form['export']['#default_value'] = Yaml::encode($this->configStorage->read('migrate_plus.migration.' . $migration));;
    }

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Update'),
    ];

    $form['actions']['back_link'] = [
      '#title' => $this->t('Go Back'),
      '#type' => 'link',
      '#url' => Url::fromRoute('migration_glue.list_migration', ['migration_group' => 'default']),
      '#attributes' => [
        'class' => ['button']
      ]
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
    $this->messenger()->addMessage($this->t('Migration is updated successfully.'));
  }

}
