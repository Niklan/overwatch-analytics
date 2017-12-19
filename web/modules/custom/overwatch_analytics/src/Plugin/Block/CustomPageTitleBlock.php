<?php

namespace Drupal\overwatch_analytics\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'CustomPageTitleBlock' block.
 *
 * @Block(
 *  id = "custom_page_title_block",
 *  admin_label = @Translation("Custom page title block"),
 * )
 */
class CustomPageTitleBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['custom_page_title_block']['#markup'] = 'Implement CustomPageTitleBlock.';

    return $build;
  }

}
