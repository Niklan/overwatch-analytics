<?php

namespace Drupal\overwatch_match\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\overwatch_match\Entity\OverwatchMatch;
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
    $status = 200;
    if (\Drupal::csrfToken()->validate($form_values['token'])) {
      $has_permission = \Drupal::currentUser()->hasPermission('add overwatch match entities');
      if ($has_permission) {
        try {
          $match = OverwatchMatch::create([
            'type' => 'competitive',
          ]);

          if (!empty($form_values['sr'])) {
            $match->field_skill_rating->value = $form_values['sr'];
          }

          if (!empty($form_values['heroesSelected'])) {
            foreach ($form_values['heroesSelected'] as $hid) {
              $match->field_heroes[] = $hid;
            }
          }

          // @todo duration

          if (!empty($form_values['notes'])) {
            $match->field_notes->value = $form_values['notes'];
          }

          if (!empty($form_values['mapSelected'])) {
            $match->field_map = $form_values['mapSelected'];
          }

          if (!empty($form_values['groupSize'])) {
            $match->field_group_size->value = $form_values['groupSize'];
          }

          if (!empty($form_values['matchResult'])) {
            $match->field_match_result->value = $form_values['matchResult'];
          }

          if (!empty($form_values['seasonSelected'])) {
            $match->field_season = $form_values['seasonSelected'];
          }

          if (!empty($form_values['startingSide'])) {
            $match->field_starting_side->value = $form_values['startingSide'];
          }

          // @todo score

          if (!empty($form_values['matchType'])) {
            $match->field_special_match_type->value = $form_values['matchType'];
          }

          $match->save();
          $response['success'] = 'Match was added successfully.';
          drupal_set_message('Match was added successfully.');
        }
        catch (EntityStorageException $e) {
          \Drupal::logger('overwatch_match')->error($e);
          $response['error'] = 'Something went wrong when we try to create match.';
          $status = 400;
        }
      }
      else {
        $response['error'] = "You don't has permission to add match.";
        $status = 400;
      }
    }
    else {
      $response['error'] = 'Token is not the same.';
      $status = 400;
    }

    return JsonResponse::create($response, $status);
  }

}
