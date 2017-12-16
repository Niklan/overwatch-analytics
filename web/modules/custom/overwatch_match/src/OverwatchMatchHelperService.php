<?php

namespace Drupal\overwatch_match;

/**
 * Class OverwatchMatchHelperService.
 */
class OverwatchMatchHelperService {

  protected $entityTypeManager;

  /**
   * Constructs a new OverwatchMatchHelperService object.
   */
  public function __construct() {
    $this->entityTypeManager = \Drupal::entityTypeManager()
      ->getStorage('overwatch_match');
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

}
