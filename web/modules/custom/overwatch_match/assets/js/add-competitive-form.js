/**
 * @file
 * Add competitive match behaviors.
 */

(function ($, Drupal) {

  'use strict';

  Drupal.behaviors.overwatchMatchAddCompetitiveForm = {
    attach: function (context, settings) {
      let addCompetitiveForms = $(context).find('.overwatch-match-add-competitive-form').once('add-competitive-form');

      if (addCompetitiveForms.length) {
        $.each(addCompetitiveForms, (key, formElement) => {
          let vm = new Vue({
            el: formElement,
            delimiters: ['${', '}'],
            data: {
              token: settings.overwatchMatchAddCompetitiveForm.token,
              is_placement_match: 0,
              match_result: null,
            }
          });
        });
      }
    }
  };

})(jQuery, Drupal);

