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
              // Check is user has SR in selected season.
              onSeasonChange(seasonId) {
                $.ajax({
                  url: '/api/v1/is-user-has-rating-in-season',
                  dataType: 'json',
                  data: {
                    '_format': 'json',
                    'sid': seasonId,
                  },
                  type: 'GET',
                  success: response => {
                    this.formValues.hasSr = response.has_sr;
                    this.formValues.isPlacement = !response.has_sr;
                  },
                  error: response => {
                    this.error = response.message;
                  },
                });
              },
              submitForm() {
                window.scrollTo(0, 0);
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

