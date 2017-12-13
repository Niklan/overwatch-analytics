<?php

namespace Drupal\bnet_oauth\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class BnetAuthSettings.
 */
class BnetAuthSettings extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'bnet_oauth.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'bnet_auth_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['key'] = [
      '#type' => 'textfield',
      '#title' => 'Key (Client ID)',
      '#required' => TRUE,
      '#default_value' => \Drupal::state()->get('bnet_oauth_settings_key'),
    ];

    $form['secret'] = [
      '#type' => 'textfield',
      '#title' => 'Secret',
      '#required' => TRUE,
      '#default_value' => \Drupal::state()->get('bnet_oauth_settings_secret'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // State is used instead of configurations, because they are not exported
    // with config. This is security decision.
    \Drupal::state()->set('bnet_oauth_settings_key', $form_state->getValue('key'));
    \Drupal::state()->set('bnet_oauth_settings_secret', $form_state->getValue('secret'));

    parent::submitForm($form, $form_state);
  }

}
