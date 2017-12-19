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
    $variables['profile_url'] = $user->toUrl()->toString();
  }
}
