<?php

namespace Drupal\overwatch_hero;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\overwatch_hero\Entity\OverwatchHeroInterface;

/**
 * Defines the storage handler class for Overwatch hero entities.
 *
 * This extends the base storage class, adding required special handling for
 * Overwatch hero entities.
 *
 * @ingroup overwatch_hero
 */
interface OverwatchHeroStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Overwatch hero revision IDs for a specific Overwatch hero.
   *
   * @param \Drupal\overwatch_hero\Entity\OverwatchHeroInterface $entity
   *   The Overwatch hero entity.
   *
   * @return int[]
   *   Overwatch hero revision IDs (in ascending order).
   */
  public function revisionIds(OverwatchHeroInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Overwatch hero author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Overwatch hero revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\overwatch_hero\Entity\OverwatchHeroInterface $entity
   *   The Overwatch hero entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(OverwatchHeroInterface $entity);

  /**
   * Unsets the language for all Overwatch hero with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
