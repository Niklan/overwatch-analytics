<?php

namespace Drupal\overwatch_analytics;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\overwatch_map\Entity\OverwatchMap;
use Drupal\overwatch_map\Entity\OverwatchMapInterface;
use Drupal\overwatch_match\Entity\OverwatchMatch;
use Drupal\overwatch_match\Entity\OverwatchMatchInterface;

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
        $this->srHistory();
        $this->winLossStreaks();
        $this->mapBreakdown();
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
            break;
        }
      }
    }

    // WLD in percentage.
    $summary['win_percentage'] = $this->calculatePercetage($summary['wins'], $summary['games_played']);
    $summary['draw_percentage'] = $this->calculatePercetage($summary['draws'], $summary['games_played']);
    $summary['losses_percentage'] = $this->calculatePercetage($summary['losses'], $summary['games_played']);

    $this->result['summary'] = $summary;
  }

  /**
   * Create an array with all matches dates and SR to it.
   */
  protected function srHistory() {
    $sr_history = [];
    foreach ($this->matches as $match) {
      if (!$match->field_skill_rating->isEmpty() && !$match->field_date->isEmpty()) {
        $sr_history[] = [
          'date' => $match->field_date->value,
          'sr' => $match->field_skill_rating->value,
        ];
      }
    }
    $this->result['sr_history'] = $sr_history;
  }

  /**
   * Calculate win and loss streak, with max and min as well.
   *
   * The loss streaks is negative values, and win streak are positive. Draws
   * keep current streak not touched. F.e. -2 means - two losses in a row, and 3
   * means three wins in a row.
   */
  protected function winLossStreaks() {
    $result = [];
    $current_streak = 0;
    foreach ($this->matches as $match) {
      if (!$match->field_match_result->isEmpty()) {
        switch ($match->field_match_result->value) {
          case OverwatchMatch::STATUS_VICTORY:
            if ($current_streak < 0) {
              $current_streak = 0;
            }
            $result['history'][] = [
              'date' => $match->field_date->value,
              'result' => ++$current_streak,
            ];
            break;

          case OverwatchMatch::STATUS_DRAW:
            $result['history'][] = [
              'date' => $match->field_date->value,
              'result' => $current_streak,
            ];
            break;

          case OverwatchMatch::STATUS_DEFEAT:
            if ($current_streak > 0) {
              $current_streak = 0;
            }
            $result['history'][] = [
              'date' => $match->field_date->value,
              'result' => --$current_streak,
            ];
            break;
        }
      }
    }

    $numbers = array_column($result['history'], 'result');
    $result['longest_win'] = max($numbers);
    // We don't need to show for people negative numbers, this is for charts
    // only.
    $result['longest_loss'] = abs(min($numbers));

    $this->result['win_loss_streaks'] = $result;
  }

  /**
   * Gather map statistic from matches.
   *
   * @throws \Consolidation\OutputFormatters\Exception\UnknownFieldException
   */
  protected function mapBreakdown() {
    $result = [];
    // This method require summary to be calculated already.
    if (empty($this->result['summary'])) {
      $this->calculateSummary();
    }
    /** @var \Drupal\overwatch_map\OverwatchMapHelperService $map_helper */
    $map_helper = \Drupal::service('overwatch_map.helper');
    $maps = $map_helper->loadAllMaps();
    $games_played = $this->result['summary']['games_played'];
    $overwatch_map_fields = \Drupal::service('entity_field.manager')
      ->getFieldDefinitions('overwatch_map', 'overwatch_map');
    $map_types = $overwatch_map_fields['field_map_types']->getSetting('allowed_values');
    $allowed_map_types = [
      OverwatchMapInterface::ASSAULT,
      OverwatchMapInterface::ASSAULT_ESCORT,
      OverwatchMapInterface::ESCORT,
      OverwatchMapInterface::CONTROL,
    ];

    // Add each allowed map type to result array as group.
    foreach ($allowed_map_types as $map_type) {
      $result['types'][$map_type] = [
        'label' => $map_types[$map_type],
        'maps' => [],
      ];
    }

    // Add information about every map in array.
    foreach ($maps as $map) {
      if ($map->isCompetitive()) {
        $result['types'][$map->field_map_types->value]['maps'][$map->id()] = $map->label();
        $result['maps'][$map->id()] = [
          'label' => $map->label(),
          'stats' => [
            'games_played' => 0,
            'wins' => 0,
            'losses' => 0,
            'draws' => 0,
          ],
        ];
      }
    }

    // Now we add basic stats for maps from matches.
    foreach ($this->matches as $match) {
      // There is possible that match has no map entered. For initial SR.
      if (!$match->field_map->isEmpty()) {
        /** @var OverwatchMap $match_map */
        $match_map = $match->field_map->entity;
        if ($match_map->isCompetitive()) {
          $map_stats = &$result['maps'][$match_map->id()]['stats'];
          $map_stats['games_played'] += 1;
          switch ($match->field_match_result->value) {
            case OverwatchMatchInterface::STATUS_VICTORY:
              $map_stats['wins'] += 1;
              break;

            case OverwatchMatchInterface::STATUS_DEFEAT:
              $map_stats['losses'] += 1;
              break;

            case OverwatchMatchInterface::STATUS_DRAW:
              $map_stats['draws'] += 1;
              break;
          }
        }
      }
    }

    // Count win, lose, draw percentage for stats.
    foreach ($result['maps'] as &$map_result) {
      $map_stats = &$map_result['stats'];
      $map_stats['wins_percent'] = $this->calculatePercetage($map_stats['wins'], $map_stats['games_played']) . '%';
      $map_stats['losses_percent'] = $this->calculatePercetage($map_stats['losses'], $map_stats['games_played']) . '%';
      $map_stats['draws_percent'] = $this->calculatePercetage($map_stats['draws'], $map_stats['games_played']) . '%';
      $map_stats['played_percent'] = $this->calculatePercetage($map_stats['games_played'], $this->result['summary']['games_played']) . '%';
    }

    $this->result['map_breakdown'] = $result;
  }

  /**
   * Calculate percentage from two numbers and format result.
   *
   * @param int $fist_number
   *   First number for which need to find percentage.
   * @param int $second_number
   *   Second number from which need to find percentage of first number.
   *
   * @return string
   *   Formatted percentage result.
   */
  protected function calculatePercetage($fist_number, $second_number) {
    if ($fist_number && $second_number) {
      $result = ($fist_number * 100) / $second_number;
      return number_format($result, 2);
    }
    return 0;
  }

}
