<?php

namespace Drupal\overwatch_hero;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Overwatch hero entities.
 *
 * @ingroup overwatch_hero
 */
class OverwatchHeroListBuilder extends EntityListBuilder {

  /**
   * Loads entity IDs using a pager sorted by the hero name.
   *
   * @return array
   *   An array of entity IDs.
   */
  protected function getEntityIds() {
    $query = $this->getStorage()->getQuery()
      ->sort($this->entityType->getKey('label'));

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
    $header['id'] = $this->t('Overwatch hero ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\overwatch_hero\Entity\OverwatchHero */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.overwatch_hero.edit_form',
      ['overwatch_hero' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
