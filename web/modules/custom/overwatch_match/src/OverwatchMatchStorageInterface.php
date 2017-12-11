<?php

namespace Drupal\overwatch_match;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\overwatch_match\Entity\OverwatchMatchInterface;

/**
 * Defines the storage handler class for Overwatch match entities.
 *
 * This extends the base storage class, adding required special handling for
 * Overwatch match entities.
 *
 * @ingroup overwatch_match
 */
interface OverwatchMatchStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Overwatch match revision IDs for a specific Overwatch match.
   *
   * @param \Drupal\overwatch_match\Entity\OverwatchMatchInterface $entity
   *   The Overwatch match entity.
   *
   * @return int[]
   *   Overwatch match revision IDs (in ascending order).
   */
  public function revisionIds(OverwatchMatchInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Overwatch match author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Overwatch match revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\overwatch_match\Entity\OverwatchMatchInterface $entity
   *   The Overwatch match entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(OverwatchMatchInterface $entity);

  /**
   * Unsets the language for all Overwatch match with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
