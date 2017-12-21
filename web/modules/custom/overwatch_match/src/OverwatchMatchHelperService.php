<?php

namespace Drupal\overwatch_match;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\overwatch_match\Entity\OverwatchMatch;

/**
 * Class OverwatchMatchHelperService.
 */
class OverwatchMatchHelperService {

  protected $entityTypeManager;

  /**
   * Constructs a new OverwatchMatchHelperService object.
   */
  public function __construct(EntityTypeManager $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * @param null $account
   */
  public function getUserLastMatch($uid = NULL) {

  }

  /**
   * Check is user has in provided season matches with SR entered.
   *
   * This helps to detect, if player complete placement or not.
   *
   * @param int $season_id
   *   Entity id of Overwatch Season.
   * @param int $uid
   *   User ID, if empty, current user will be used.
   *
   * @return bool
   *   TRUE is user has SR in season, FALSE otherwise.
   */
  public function isUserHasSrInSeason($season_id, $uid = NULL) {
    if (empty($uid)) {
      $uid = \Drupal::currentUser()->id();
    }
    $query = \Drupal::entityQuery('overwatch_match')
      ->condition('status', 1)
      ->exists('field_skill_rating')
      ->condition('user_id', $uid)
      ->range(0, 1);
    return (bool) $query->count()->execute();
  }

  /**
   * Find previous match for user and season.
   */
  public function getPreviousMatch($match_id) {
    $overwatch_match = $this->entityTypeManager->getStorage('overwatch_match')
      ->load($match_id);
    if ($overwatch_match) {
      if (!$overwatch_match->field_season->isEmpty()) {
        $season_id = $overwatch_match->field_season->target_id;
        $query = \Drupal::entityQuery('overwatch_match')
          ->condition('field_season.target_id', $season_id)
          ->condition('created', $overwatch_match->created->value, '<=')
          ->condition('user_id', $overwatch_match->user_id->target_id)
          ->condition('id', $overwatch_match->id(), '<>')
          ->range(0, 1)
          ->sort('created', 'DESC');

        $entities = $query->execute();
        return $entities ? reset($entities) : FALSE;
      }
    }
    return FALSE;
  }

  /**
   * Finds last match for season.
   */
  public function getLastMatch($season_id) {
    $query = \Drupal::entityQuery('overwatch_match')
      ->condition('status', 1)
      ->condition('field_season', $season_id)
      ->range(0, 1)
      ->sort('created', 'DESC');

    $entities = $query->execute();
    return $entities ? reset($entities) : FALSE;
  }

  /**
   * Trying to set status for match based on previous matches.
   *
   * This method only update value for filed, but not save an entity.
   */
  public function updateMatchStatus(OverwatchMatch $match) {
    $previous_match_id = $this->getPreviousMatch($match->id());
    if ($previous_match_id) {
      $previous_match = $this->entityTypeManager->getStorage('overwatch_match')
        ->load($previous_match_id);
      $previous_sr = $previous_match->field_skill_rating->value;
      $current_sr = $match->field_skill_rating->value;
      if ($previous_sr == $current_sr) {
        $match->field_match_result = OverwatchMatch::STATUS_DRAW;
      }
      elseif ($previous_sr < $current_sr) {
        $match->field_match_result = OverwatchMatch::STATUS_VICTORY;
      }
      else {
        $match->field_match_result = OverwatchMatch::STATUS_DEFEAT;
      }
    }
  }

}
