<?php

/**
 * @file
 * Contains overwatch_map.page.inc.
 *
 * Page callback for Overwatch map entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Overwatch map templates.
 *
 * Default template: overwatch_map.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_overwatch_map(array &$variables) {
  // Fetch OverwatchMap Entity Object.
  $overwatch_map = $variables['elements']['#overwatch_map'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
