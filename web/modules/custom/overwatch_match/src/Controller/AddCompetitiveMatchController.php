<?php

namespace Drupal\overwatch_match\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class AddCompetitiveMatchController.
 */
class AddCompetitiveMatchController extends ControllerBase {

  /**
   * Main content with form.
   *
   * @return array
   *   Return form.
   */
  public function content() {
    return [
      '#theme' => 'overwatch_match_add_competitive_form',
      '#cache' => [
        'max-age' => 0,
      ],
    ];
  }

}
