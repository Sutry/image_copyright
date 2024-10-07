<?php

namespace Drupal\image_copyright\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a form for editing the copyright text for an image.
 */
class ImageCopyrightForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'image_copyright_form';
  }


  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $fid = NULL) {
    $form_state->set('fid', $fid);

    // Перевіряємо, чи задано значення fid. Якщо так, отримуємо текст копірайтів з БД.
    if (!empty($fid)) {
      $record = \Drupal::database()->select('image_copyright')
        ->fields('image_copyright')
        ->condition('fid', $fid)
        ->execute()
        ->fetchAssoc();

      $default_value = isset($record['copyright']) ? $record['copyright'] : '';
    } else {
      $default_value = '';
    }

    $form['text_block'] = [
      '#type' => 'markup',
      '#markup' => '<small>(for our sites)↓<br>&lt;a href="https://mycredit.in.ua" target="_blank"&gt;MyCredit.in.ua&lt;/a&gt;<br>
        &lt;a href="https://news24.one" target="_blank"&gt;News24.One&lt;/a&gt;<br>
        (for not ours)↓<br>Owner &lt;a href="https://site.com" target="_blank" rel="nofollow noopener"&gt;Site.com&lt;/a&gt;</small>',
    ];

    $form['copyright'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Copyright Text'),
      '#default_value' => $default_value,
      '#required' => TRUE,
    ];


    // Додаємо елементи форми для редагування тексту копірайтів.
    $form['copyright'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Copyright Text'),
      '#default_value' => $default_value,
      '#required' => TRUE,
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];

    return $form;
  }


  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Add any form validation logic here.
  }


  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $fid = $form_state->get('fid');
    $copyright = trim($form_state->getValue('copyright'));

    // Перевіряємо, чи існує вже запис для даного fid.
    $existing_record = \Drupal::database()->select('image_copyright')
      ->fields('image_copyright')
      ->condition('fid', $fid)
      ->execute()
      ->fetchAssoc();

    if ($existing_record) {
      // Якщо запис існує, оновлюємо його.
      \Drupal::database()->update('image_copyright')
        ->fields(['copyright' => $copyright])
        ->condition('fid', $fid)
        ->execute();
    } else {
      // Якщо запис не існує, створюємо новий.
      \Drupal::database()->insert('image_copyright')
        ->fields([
          'fid' => $fid,
          'copyright' => $copyright,
        ])
        ->execute();
    }

    // Встановлюємо повідомлення про успішне збереження.
    $this->messenger()->addStatus($this->t('Copyright text has been saved.'));
  }

}
