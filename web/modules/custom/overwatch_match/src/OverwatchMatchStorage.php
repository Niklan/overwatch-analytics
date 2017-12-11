<?php

namespace Drupal\overwatch_match;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
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
class OverwatchMatchStorage extends SqlContentEntityStorage implements OverwatchMatchStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(OverwatchMatchInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {overwatch_match_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {overwatch_match_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(OverwatchMatchInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {overwatch_match_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('overwatch_match_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
