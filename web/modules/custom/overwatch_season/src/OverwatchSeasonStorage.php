<?php

namespace Drupal\overwatch_season;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
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
class OverwatchSeasonStorage extends SqlContentEntityStorage implements OverwatchSeasonStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(OverwatchSeasonInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {overwatch_season_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {overwatch_season_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(OverwatchSeasonInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {overwatch_season_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('overwatch_season_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
