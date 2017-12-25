<?php

namespace Drupal\overwatch_season;

/**
 * Class OverwatchSeasonHelperService.
 */
class OverwatchSeasonHelperService {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * Constructs a new OverwatchSeasonHelperService object.
   */
  public function __construct() {
    $this->entityTypeManager = \Drupal::entityTypeManager()
      ->getStorage('overwatch_season');
  }

  /**
   * Create an array with all seasons and marks active.
   */
  public function getAllSeasons() {
    $result = &drupal_static(__METHOD__);
    if (!isset($result)) {
      $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
      $query = \Drupal::entityQuery('overwatch_season')
        ->condition('status', 1)
        ->condition('langcode', $language)
        ->sort('created', 'DESC');
      $query_result = $query->execute();
      $seasons = $this->entityTypeManager->loadMultiple($query_result);

      $result = [];
      foreach ($seasons as $season) {
        $result[] = [
          'id' => $season->id(),
          'text' => $season->getTranslation($language)->label(),
        ];
      }
    }
    return $result;
  }

  /**
   * Find out current season.
   *
   * For now it just last added season.
   *
   * @todo dates and check for them, and support preseason.
   */
  public function getCurrentSeason() {
    $seasons = $this->getAllSeasons();
    return reset($seasons);
  }

}
