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
      $has_permission = \Drupal::currentUser()
        ->hasPermission('add overwatch match entities');
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

          if (!empty($form_values['duration'])) {
            if (preg_match("/([0-9]+):?([0-9]+)?/", $form_values['duration'])) {
              $match->field_duration->value = $form_values['duration'];
            }
          }

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

          if (!empty($form_values['scoreTeam']) && !empty($form_values['scoreEnemy'])) {
            $match->field_score->value = $form_values['scoreTeam'] . ':' . $form_values['scoreEnemy'];
          }

          if (!empty($form_values['isPlacement'])) {
            $match->field_is_placement->value = $form_values['isPlacement'];
          }

          // Date.
          /** @var \Drupal\Core\Datetime\DateFormatter $date_formatter */
          $date_formatter = \Drupal::service('date.formatter');
          $request_time = \Drupal::time()->getRequestTime();
          if (!empty($form_values['dateDate'])) {
            $date = $form_values['dateDate'];
          }
          else {
            $date = $date_formatter->format($request_time, 'custom', 'd.m.Y');
          }

          if (!empty($form_values['dateTime'])) {
            $time = $form_values['dateTime'];
          }
          else {
            $time = $date_formatter->format($request_time, 'custom', 'h:m');
          }

          $date_time = strtotime("$date $time");
          $match->field_date = $date_time;

          // @todo after rework of errors.
          $match->save();
          $response['success'] = 'Match was added successfully.';
          drupal_set_message('Match was added successfully.');
        } catch (EntityStorageException $e) {
          \Drupal::logger('overwatch_match')->error($e);
          $response['error']['message'] = 'Something went wrong when we try to create match.';
          $response['error']['code'] = 400;
          $status = 400;
        }
      }
      else {
        $response['error']['message'] = "You don't has permission to add match.";
        $response['error']['code'] = 400;
        $status = 400;
      }
    }
    else {
      $response['error']['message'] = 'Token is not the same.';
      $response['error']['code'] = 400;
      $status = 400;
    }

    return JsonResponse::create($response, $status);
  }

}
