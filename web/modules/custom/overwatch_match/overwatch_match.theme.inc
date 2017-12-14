<?php

/**
 * @file
 * Preprocesses for custom theme hooks.
 */

/**
 * Implements template_preprocess_HOOK().
 */
function template_preprocess_overwatch_match_add_competitive_form(&$variables) {
  $settings = &$variables['#attached']['drupalSettings']['overwatchMatchAddCompetitiveForm'];
  $settings['formValues'] = [
    'token' => \Drupal::csrfToken()->get(),
    'hasSr' => FALSE,
    // 1 - placement, 2 - base.
    'matchType' => NULL,
    'matchResult' => NULL,
    'seasons' => [],
    'seasonSelected' => NULL,
    'maps' => [],
    'mapSelected' => NULL,
    'groupSize' => 1,
    'startingSide' => NULL,
    'score' => NULL,
    'sr' => NULL,
    'heroes' => [],
    'heroesSelected' => [],
  ];

  /** @var \Drupal\overwatch_season\OverwatchSeasonHelperService $overwatch_season_helper */
  $overwatch_season_helper = \Drupal::service('overwatch_season.helper');
  $settings['formValues']['seasons'] = $overwatch_season_helper->getAllSeasons();
  $settings['formValues']['seasonSelected'] = $overwatch_season_helper->getCurrentSeason()['id'];
}