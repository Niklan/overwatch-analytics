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
              },

              /**
               * Error message.
               */
              error: {
                type: String,
                default: '',
              },

              /**
               * Required for POST.
               */
              restToken: {
                type: String,
                default: '',
              },
            },

            data: {
              formValues: settings.overwatchMatchAddCompetitiveForm.formValues,
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
               * Get REST token for POST requests.
               */
              getRestToken() {
                return new Promise((resolve, reject) => {
                  if (this.restToken.length === 0) {
                    $.ajax({
                      url: '/rest/session/token',
                      dataType: 'text',
                      type: 'GET',
                      success: response => {
                        this.restToken = response;
                        resolve(response);
                      },
                      error: response => {
                        this.error = response.statusText;
                        reject(response);
                      },
                    });
                  }
                  else {
                    resolve(this.restToken);
                  }
                });
              },

              /**
               * Submit button handler for form.
               */
              submitForm() {
                this.loading = true;
                this.getRestToken().then(
                  result => {
                    $.ajax({
                      url: '/api/v1/add-competitive-match',
                      type: 'POST',
                      headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': result,
                      },
                      dataType: 'json',
                      data: JSON.stringify({
                        'form_values': this.formValues,
                      }),
                      success: () => {
                        this.loading = false;
                        location.reload();
                      },
                      error: response => {
                        this.loading = false;
                        this.error = response.statusText;
                      },
                    });
                  },
                  error => {
                    this.error = 'Form submissions failed';
                    this.loading = false;
                  }
                );
              },
            },
          });
        });
      }
    },
  };

})(jQuery, Drupal);

