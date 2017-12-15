<?php

namespace Drupal\overwatch_match\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class OverwatchMatchAddCompetitiveController.
 */
class OverwatchMatchAddCompetitiveController extends ControllerBase {

  /**
   * Main content with form.
   *
   * @return array
   *   Return form.
   */
  public function form() {
    return [
      '#theme' => 'overwatch_match_add_competitive_form',
      '#cache' => [
        'max-age' => 0,
      ],
    ];
  }

  /**
   * AJAX callback handler.
   */
  public function submitForm() {
    $form_values = \Drupal::request()->request->all();

    $response = [];
    if (\Drupal::csrfToken()->validate($form_values['token'])) {
      // @todo
      return JsonResponse::create($form_values);
    }
    else {
      $response['error'] = 'Token is not the same.';
      return JsonResponse::create($response, 400);
    }
  }

}
