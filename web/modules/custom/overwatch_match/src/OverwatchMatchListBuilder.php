<?php

namespace Drupal\overwatch_match;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Overwatch match entities.
 *
 * @ingroup overwatch_match
 */
class OverwatchMatchListBuilder extends EntityListBuilder {

  /**
   * Loads entity IDs using a pager sorted by the entity id.
   *
   * @return array
   *   An array of entity IDs.
   */
  protected function getEntityIds() {
    $query = $this->getStorage()->getQuery()
      ->sort($this->entityType->getKey('id'), 'DESC');

    // Only add the pager if a limit is specified.
    if ($this->limit) {
      $query->pager($this->limit);
    }
    return $query->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = 'MID';
    $header['player'] = $this->t('Player');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\overwatch_match\Entity\OverwatchMatch */
    $row['id'] = Link::createFromRoute(
      $entity->id(),
      'entity.overwatch_match.edit_form',
      ['overwatch_match' => $entity->id()]
    );
    $row['player'] = $entity->user_id->entity->label();
    return $row + parent::buildRow($entity);
  }

}
