<?php

namespace Drupal\image_copyright\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure image copyright settings for this site.
 */
class ImageCopyrightUninstallForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['image_copyright.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'image_copyright_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('image_copyright.settings');

    $form['delete_table'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Delete the "image_copyright" table upon uninstall'),
      '#default_value' => $config->get('delete_table'),
      '#description' => $this->t('If checked, the table will be deleted when the module is uninstalled.'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('image_copyright.settings')
      ->set('delete_table', $form_state->getValue('delete_table'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}
