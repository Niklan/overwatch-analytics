<?php

namespace Drupal\overwatch_analytics\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'NavigationAuthBlock' block.
 *
 * @Block(
 *  id = "navigation_auth_block",
 *  admin_label = @Translation("Navigation auth block"),
 * )
 */
class NavigationAuthBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#theme'] = 'overwatch_analytics_navigation_auth_block';

    return $build;
  }

  /**
   * @return array|string[]
   */
  public function getCacheContexts() {
    return [
      'user.roles',
    ];
  }

}
