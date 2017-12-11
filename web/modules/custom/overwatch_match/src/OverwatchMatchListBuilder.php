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
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Overwatch match ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\overwatch_match\Entity\OverwatchMatch */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.overwatch_match.edit_form',
      ['overwatch_match' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
