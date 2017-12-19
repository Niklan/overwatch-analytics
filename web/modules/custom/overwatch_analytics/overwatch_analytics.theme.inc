<?php

/**
 * @file
 * Main file for custom theme hook processes.
 */

use Drupal\overwatch_season\Entity\OverwatchSeason;
use Drupal\overwatch_season\Entity\OverwatchSeasonInterface;
use Drupal\user\Entity\User;
use Drupal\user\UserInterface;

/**
 * Implements template_preprocess_HOOK().
 */
function template_preprocess_overwatch_analytics_navigation_auth_block(&$variables) {
  $variables['is_authenticated'] = \Drupal::currentUser()->isAuthenticated();

  if ($variables['is_authenticated']) {
    /** @var User $user */
    $user = User::load(\Drupal::currentUser()->id());
    if (!$user->user_picture->isEmpty()) {
      $variables['picture'] = $user->user_picture->entity->uri->value;
    }
    else {
      $variables['picture'] = FALSE;
    }
    $variables['username'] = $user->label();
    if ($user->hasField('bnet_oauth_battletag') && !$user->bnet_oauth_battletag->isEmpty()) {
      $battletag = explode('#', $user->bnet_oauth_battletag->value);
      $variables['username'] = $battletag[0];
    }
    $variables['profile_url'] = $user->toUrl()->toString();
  }
}

/**
 * Implements template_preprocess_HOOK().
 */
function template_preprocess_overwatch_analytics_season_statistic_for_user(&$variables) {
  $variables['is_empty'] = TRUE;
  $variables['path_to_theme'] = drupal_get_path('theme', 'overwatch_analytics_theme');
  if (!empty($variables['uid']) && !empty($variables['sid'])) {
    $account = User::load($variables['uid']);
    $overwatch_season = OverwatchSeason::load($variables['sid']);
    if ($account instanceof UserInterface && $overwatch_season instanceof OverwatchSeasonInterface) {
      $matches = \Drupal::entityTypeManager()
        ->getStorage('overwatch_match')
        ->loadByProperties([
          'field_season.target_id' => $overwatch_season->id(),
          'user_id' => $account->id(),
        ]);

      if (!empty($matches)) {
        $variables['is_empty'] = FALSE;
        $summary = &$variables['summary'];

        $summary['match_played'] = count($matches);
      }
    }
  }
}
