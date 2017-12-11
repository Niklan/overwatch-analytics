<?php

namespace Drupal\overwatch_match\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Overwatch match entities.
 */
class OverwatchMatchViewsData extends EntityViewsData {

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
