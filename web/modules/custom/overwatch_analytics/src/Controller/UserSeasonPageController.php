<?php

namespace Drupal\overwatch_analytics\Controller;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Controller\ControllerBase;
use Drupal\overwatch_season\Entity\OverwatchSeason;
use Drupal\user\UserInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class UserSeasonPageController.
 */
class UserSeasonPageController extends ControllerBase {

  protected $account;

  protected $overwatchSeason;

  /**
   * UserSeasonPageController constructor.
   */
  public function __construct() {
    $raw_values = \Drupal::request()->get('_raw_variables')->all();
    $users = $this->entityTypeManager()->getStorage('user')
      ->loadByProperties(['name' => $raw_values['username']]);
    $first_user = reset($users);
    if ($first_user instanceof UserInterface) {
      $account = $first_user;
      $overwatch_season = OverwatchSeason::load($raw_values['sid']);
      if ($overwatch_season) {
        $this->account = $account;
        $this->overwatchSeason = $overwatch_season;
        return;
      }
    }
    throw new NotFoundHttpException();
  }

  /**
   * Dynamic page callback.
   */
  public function getPageTitle() {
    return new FormattableMarkup('Statistic for @username in season @season', [
      '@username' => $this->account->label(),
      '@season' => $this->overwatchSeason->id(),
    ]);
  }

  /**
   * Content for page.
   */
  public function content() {

    return ['#markup' => 'Well Done!'];
  }

}
