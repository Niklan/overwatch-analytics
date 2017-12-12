<?php

namespace Drupal\overwatch_map;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\overwatch_map\Entity\OverwatchMapInterface;

/**
 * Defines the storage handler class for Overwatch map entities.
 *
 * This extends the base storage class, adding required special handling for
 * Overwatch map entities.
 *
 * @ingroup overwatch_map
 */
interface OverwatchMapStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Overwatch map revision IDs for a specific Overwatch map.
   *
   * @param \Drupal\overwatch_map\Entity\OverwatchMapInterface $entity
   *   The Overwatch map entity.
   *
   * @return int[]
   *   Overwatch map revision IDs (in ascending order).
   */
  public function revisionIds(OverwatchMapInterface $entity);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\overwatch_map\Entity\OverwatchMapInterface $entity
   *   The Overwatch map entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(OverwatchMapInterface $entity);

  /**
   * Unsets the language for all Overwatch map with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
