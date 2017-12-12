<?php

namespace Drupal\overwatch_map\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Overwatch map entities.
 */
class OverwatchMapViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.

    return $data;
  }

}
