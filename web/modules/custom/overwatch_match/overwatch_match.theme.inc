<?php

/**
 * @file
 * Preprocesses for custom theme hooks.
 */

/**
 * Implements template_preprocess_HOOK().
 */
function template_preprocess_overwatch_match_add_competitive_form(&$variables) {
  $variables['#attached']['drupalSettings']['overwatchMatchAddCompetitiveForm']['token'] = \Drupal::csrfToken()->get();
}