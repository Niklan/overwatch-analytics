<?php

namespace Drupal\overwatch_season\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Overwatch season entities.
 */
class OverwatchSeasonViewsData extends EntityViewsData {

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
