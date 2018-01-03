<?php

namespace Drupal\overwatch_map;

/**
 * Class OverwatchMatachHelperService.
 */
class OverwatchMapHelperService {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * Constructs a new OverwatchMapHelperService object.
   */
  public function __construct() {
    $this->entityTypeManager = \Drupal::entityTypeManager()
      ->getStorage('overwatch_map');
  }

  /**
   * Return array with all maps.
   */
  public function getAllMaps() {
    $result = &drupal_static(__METHOD__);
    if (!isset($result)) {
      $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
      $query = \Drupal::entityQuery('overwatch_map')
        ->condition('status', 1)
        ->condition('langcode', $language)
        ->sort('name', 'ASC');
      $query_result = $query->execute();
      $maps = $this->entityTypeManager->loadMultiple($query_result);

      $result = [];
      foreach ($maps as $map) {
        $result[] = [
          'id' => $map->id(),
          'text' => $map->getTranslation($language)->label(),
        ];
      }
    }
    return $result;
  }

  /**
   * Return array with all entities of OverwatchMap.
   */
  public function loadAllMaps($langcode = NULL) {
    $result = &drupal_static(__METHOD__ . $langcode);
    if (!isset($result)) {
      $query = \Drupal::entityQuery('overwatch_map')
        ->condition('status', 1)
        ->sort('name', 'ASC');
      $query_result = $query->execute();
      $result = $this->entityTypeManager->loadMultiple($query_result);

      if (isset($langcode)) {
        foreach ($result as &$result_item) {
          $result_item = $result_item->getTranslation($langcode);
        }
      }
    }
    return $result;
  }

}
