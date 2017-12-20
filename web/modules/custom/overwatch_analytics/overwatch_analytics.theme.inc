<?php

/**
 * @file
 * Main file for custom theme hook processes.
 */

use Drupal\user\Entity\User;

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
    /** @var \Drupal\overwatch_analytics\CompetitiveSeasonsService $competitive_season_analyze */
    $competitive_season_analyze = \Drupal::service('overwatch_analytics.competitive_season');
    $result = $competitive_season_analyze->setAccount($variables['uid'])
      ->setSeason($variables['sid'])
      ->getResult();
    if ($result) {
      $variables['is_empty'] = FALSE;
      $variables['results'] = $result;
    }
  }
}
