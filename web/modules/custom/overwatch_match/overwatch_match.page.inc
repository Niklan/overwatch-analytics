<?php

/**
 * @file
 * Contains overwatch_match.page.inc.
 *
 * Page callback for Overwatch match entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Overwatch match templates.
 *
 * Default template: overwatch_match.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_overwatch_match(array &$variables) {
  // Fetch OverwatchMatch Entity Object.
  $overwatch_match = $variables['elements']['#overwatch_match'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
