/**
 * @file
 * Season statistic for user charts behaviors.
 */

(function($, Drupal) {

  'use strict';

  Drupal.behaviors.ssfuWinratePie = {
    attach: function(context, settings) {
      let winratePieCanvas = $(context).
        find('#winrate-circle').
        once('winrate-pie');

      if (winratePieCanvas.length) {
        var config = {
          type: 'pie',
          data: {
            datasets: [
              {
                data: [
                  $(winratePieCanvas).data('wins'),
                  $(winratePieCanvas).data('losses'),
                  $(winratePieCanvas).data('draws'),
                ],
                backgroundColor: [
                  '#dc3545',
                  '#28a745',
                  '#fd7e14',
                ],
                label: 'Winrate',
              }],
            labels: [
              'Wins',
              'Losses',
              'Draws',
            ],
          },
          options: {
            responsive: true,
          },
        };

        let winratePieChart = new Chart(winratePieCanvas, config);
      }
    },
  };

})(jQuery, Drupal);


