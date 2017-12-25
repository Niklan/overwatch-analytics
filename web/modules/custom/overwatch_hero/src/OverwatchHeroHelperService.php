<?php

namespace Drupal\overwatch_hero;

/**
 * Class OverwatchHeroHelperService.
 */
class OverwatchHeroHelperService {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * Constructs a new OverwatchHeroHelperService object.
   */
  public function __construct() {
    $this->entityTypeManager = \Drupal::entityTypeManager()
      ->getStorage('overwatch_hero');
  }

  /**
   * Return array with all heroes.
   */
  public function getAllHeroes() {
    $result = &drupal_static(__METHOD__);
    if (!isset($result)) {
      $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
      $query = \Drupal::entityQuery('overwatch_hero')
        ->condition('status', 1)
        ->condition('langcode', $language)
        ->sort('name', 'ASC');
      $query_result = $query->execute();
      $heroes = $this->entityTypeManager->loadMultiple($query_result);

      $result = [];
      foreach ($heroes as $hero) {
        $result[] = [
          'id' => $hero->id(),
          'text' => $hero->getTranslation($language)->label(),
        ];
      }
    }
    return $result;
  }

}
