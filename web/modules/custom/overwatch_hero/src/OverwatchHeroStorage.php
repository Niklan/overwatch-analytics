<?php

namespace Drupal\overwatch_hero;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
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
class OverwatchHeroStorage extends SqlContentEntityStorage implements OverwatchHeroStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(OverwatchHeroInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {overwatch_hero_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {overwatch_hero_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(OverwatchHeroInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {overwatch_hero_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('overwatch_hero_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
