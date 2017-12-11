<?php

namespace Drupal\overwatch_match\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class OverwatchMatchTypeForm.
 */
class OverwatchMatchTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $overwatch_match_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $overwatch_match_type->label(),
      '#description' => $this->t("Label for the Overwatch match type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $overwatch_match_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\overwatch_match\Entity\OverwatchMatchType::load',
      ],
      '#disabled' => !$overwatch_match_type->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $overwatch_match_type = $this->entity;
    $status = $overwatch_match_type->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Overwatch match type.', [
          '%label' => $overwatch_match_type->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Overwatch match type.', [
          '%label' => $overwatch_match_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($overwatch_match_type->toUrl('collection'));
  }

}
