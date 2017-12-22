/**
 * @file
 * Select2 integration for theme behaviors.
 */

(function ($, Drupal) {

  'use strict';

  Drupal.behaviors.select2Integration = {
    attach: function (context, settings) {
      console.log('select2');
      let select = $(context).find('select').once('select2');

      if (select.length) {
        $.each(select, (key, element) => {
          //$(element).select2();
        });
      }
    }
  };

})(jQuery, Drupal);

