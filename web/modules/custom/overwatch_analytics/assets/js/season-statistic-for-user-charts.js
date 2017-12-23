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
        let config = {
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
                  '#7DFF00',
                  '#CD1535',
                  '#FFD600',
                ],
                label: 'Winrate',
              }],
            labels: [
              Drupal.t('Wins'),
              Drupal.t('Losses'),
              Drupal.t('Draws'),
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


