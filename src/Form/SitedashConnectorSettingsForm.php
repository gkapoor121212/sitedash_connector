<?php

namespace Drupal\sitedash_connector\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class SitedashConnectorSettingsForm.
 *
 * @package Drupal\antibot\Form
 */
class SitedashConnectorSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'sitedash_connector.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'sitedash_connector_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('sitedash_connector.settings');

    $form['form_ids'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Token'),
      '#default_value' => $config->get('token'),
      '#disabled' => TRUE,
      '#description' => $this->t('Save this token in the website dashboard portal. Do not share this token with anybody.'),
    ];

    $form['actions']['submit']['#attributes']['disabled'] = 'disabled';
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

  }

}
