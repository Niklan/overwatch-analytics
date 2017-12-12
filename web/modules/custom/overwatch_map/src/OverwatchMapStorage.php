<?php

namespace Drupal\overwatch_map;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
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
class OverwatchMapStorage extends SqlContentEntityStorage implements OverwatchMapStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(OverwatchMapInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {overwatch_map_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(OverwatchMapInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {overwatch_map_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('overwatch_map_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
