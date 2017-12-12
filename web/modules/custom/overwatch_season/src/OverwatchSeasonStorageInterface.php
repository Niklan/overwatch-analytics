<?php

namespace Drupal\overwatch_season;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\overwatch_season\Entity\OverwatchSeasonInterface;

/**
 * Defines the storage handler class for Overwatch season entities.
 *
 * This extends the base storage class, adding required special handling for
 * Overwatch season entities.
 *
 * @ingroup overwatch_season
 */
interface OverwatchSeasonStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Overwatch season revision IDs for a specific Overwatch season.
   *
   * @param \Drupal\overwatch_season\Entity\OverwatchSeasonInterface $entity
   *   The Overwatch season entity.
   *
   * @return int[]
   *   Overwatch season revision IDs (in ascending order).
   */
  public function revisionIds(OverwatchSeasonInterface $entity);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\overwatch_season\Entity\OverwatchSeasonInterface $entity
   *   The Overwatch season entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(OverwatchSeasonInterface $entity);

  /**
   * Unsets the language for all Overwatch season with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
