/**
 * @file
 * Add competitive match behaviors.
 */

(function($, Drupal) {

  'use strict';

  Drupal.behaviors.overwatchMatchAddCompetitiveForm = {
    attach: function(context, settings) {
      let addCompetitiveForms = $(context).
        find('.overwatch-match-add-competitive-form').
        once('add-competitive-form');

      if (addCompetitiveForms.length) {
        $.each(addCompetitiveForms, (key, formElement) => {
          let vm = new Vue({
            el: formElement,
            delimiters: ['${', '}'],
            data: {
              formValues: settings.overwatchMatchAddCompetitiveForm.formValues,
              error: '',
            },
            methods: {
              submitForm() {
                $.ajax({
                  type: 'POST',
                  url: '/add/match/competitive/callback',
                  data: this.formValues,
                }).done(response => {
                  location.reload();
                }).fail(response => {
                  this.error = response.responseJSON.error.message;
                });
              },
            },
          });
        });
      }
    },
  };

})(jQuery, Drupal);

