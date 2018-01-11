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

            props: {
              loading: {
                type: Boolean,
                default: false,
              }
            },

            data: {
              formValues: settings.overwatchMatchAddCompetitiveForm.formValues,
              error: '',
            },

            methods: {

              /**
               * Checks is current user has Skill Rating for selected season.
               *
               * @param seasonId
               */
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

              /**
               * Submit button handler for form.
               */
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

