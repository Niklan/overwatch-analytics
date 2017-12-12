<?php

/**
 * @file
 * Contains overwatch_season.page.inc.
 *
 * Page callback for Overwatch season entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Overwatch season templates.
 *
 * Default template: overwatch_season.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_overwatch_season(array &$variables) {
  // Fetch OverwatchSeason Entity Object.
  $overwatch_season = $variables['elements']['#overwatch_season'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
