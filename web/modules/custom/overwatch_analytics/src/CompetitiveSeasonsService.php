<?php

namespace Drupal\overwatch_analytics;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\overwatch_match\Entity\OverwatchMatch;

/**
 * Class CompetitiveSeasonsService.
 */
class CompetitiveSeasonsService {

  /**
   * Season for which analyze will be done.
   *
   * @var \Drupal\overwatch_season\Entity\OverwatchSeason
   */
  protected $overwatchSeason;

  /**
   * User for which this analyze will be done.
   *
   * @var \Drupal\user\Entity\User
   */
  protected $account;

  /**
   * Result of analyze.
   *
   * @var array
   *   Structured data with analyze.
   */
  protected $result;

  /**
   * Helper property to work with entities.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * Matches for current account and season.
   *
   * @var array
   *  An array with OverwatchMaches entites.
   */
  protected $matches;

  /**
   * Constructs a new CompetitiveSeasonsService object.
   */
  public function __construct(EntityTypeManager $entityTypeManager) {
    $this->result = [];

    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * Set OverwatchSeason.
   *
   * @param int $sid
   *   OverwatchSeason entity id.
   *
   * @return \Drupal\overwatch_analytics\CompetitiveSeasonsService
   */
  public function setSeason($sid) {
    $this->overwatchSeason = $this->entityTypeManager->getStorage('overwatch_season')
      ->load($sid);
    return $this;
  }

  /**
   * Set account of user for which analyze will be done.
   *
   * @param int $uid
   *   User entity id.
   *
   * @return \Drupal\overwatch_analytics\CompetitiveSeasonsService
   */
  public function setAccount($uid) {
    $this->account = $this->entityTypeManager->getStorage('user')->load($uid);
    return $this;
  }

  /**
   * Return computed results.
   *
   * @return array|bool
   *   An array with all results, if something wrong with account or season
   *   returns FALSE.
   */
  public function getResult() {
    if ($this->account && $this->overwatchSeason) {
      if ($matches = $this->loadMatches()) {
        $this->calculateSummary();
        return $this->result;
      }
      return FALSE;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Find and load all matches for current user and season.
   *
   * @return array
   *   An array with matches. If no one found, returns empty array.
   */
  protected function loadMatches() {
    if (empty($this->matches)) {
      $this->matches = $this->entityTypeManager->getStorage('overwatch_match')
        ->loadByProperties([
          'user_id' => $this->account->id(),
          'field_season' => $this->overwatchSeason->id(),
        ]);
    }
    return $this->matches;
  }

  /**
   * Calculate summary statistic.
   */
  protected function calculateSummary() {
    $summary = [];
    $summary['games_played'] = count($this->matches);

    // If first match has no result status, this mean this match was initial
    // for SR.
    if (reset($this->matches)->field_match_result->isEmpty()) {
      $summary['games_played']--;
    }

    // Wins, losses and draws.
    $summary['wins'] = 0;
    $summary['draws'] = 0;
    $summary['losses'] = 0;
    /** @var \Drupal\overwatch_match\Entity\OverwatchMatch $match */
    foreach ($this->matches as $match) {
      if (!$match->field_match_result->isEmpty()) {
        switch ($match->field_match_result->value) {
          case OverwatchMatch::STATUS_VICTORY:
            $summary['wins']++;
            break;

          case OverwatchMatch::STATUS_DRAW:
            $summary['draws']++;
            break;

          case OverwatchMatch::STATUS_DEFEAT:
            $summary['losses']++;
        }
      }
    }

    // WLD in percentage.
    if ($summary['games_played']) {
      $summary['win_percentage'] = ($summary['wins'] * 100) / $summary['games_played'];
      $summary['draw_percentage'] = ($summary['draws'] * 100) / $summary['games_played'];
      $summary['losses_percentage'] = ($summary['losses'] * 100) / $summary['games_played'];
    }
    else {
      $summary['win_percentage'] = $summary['draw_percentage'] = $summary['losses_percentage'] = 0;
    }

    $this->result['summary'] = $summary;
  }

}
