<?php

/**
 * @file
 * Contains overwatch_hero.page.inc.
 *
 * Page callback for Overwatch hero entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Overwatch hero templates.
 *
 * Default template: overwatch_hero.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_overwatch_hero(array &$variables) {
  // Fetch OverwatchHero Entity Object.
  $overwatch_hero = $variables['elements']['#overwatch_hero'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
