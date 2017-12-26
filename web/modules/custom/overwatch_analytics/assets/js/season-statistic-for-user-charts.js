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
              },
            ],
            labels: [
              Drupal.t('Wins'),
              Drupal.t('Losses'),
              Drupal.t('Draws'),
            ],
          },
          options: {
            responsive: true,
            title: {
              display: true,
              text: 'Win/Loss %',
            },
            maintainAspectRatio: false,
          },
        };

        let winratePieChart = new Chart(winratePieCanvas, config);
      }
    },
  };

  Drupal.behaviors.ssfuSrHistory = {
    attach: function(context, settings) {
      let srHistoryCanvas = $(context).
        find('#sr-history').
        once('sr-history');

      if (srHistoryCanvas.length) {
        let srHistoryData = $(srHistoryCanvas).data('sr-history');
        let labels = [];
        let data = [];
        $.each(srHistoryData, (key, item) => {
          let date = new Date(item.date * 1000);
          labels.push(date.getUTCDay() + '/' + date.getMonth() + '/' +
            date.getFullYear());
          data.push(item.sr);
        });

        let config = {
          type: 'line',
          data: {
            labels: labels,
            datasets: [
              {
                data: data,
                label: 'SR',
                borderColor: '#00A5E2',
                fill: false,
                borderJoinStyle: 'miter',
                cubicInterpolationMode: 'monotone',
                lineTension: 0,
              }],
          },
          options: {
            responsive: true,
            title: {
              display: true,
              text: 'Skill Rating over time',
            },
            maintainAspectRatio: false,
          },
        };

        let srHistory = new Chart(srHistoryCanvas, config);
      }
    },
  };

  Drupal.behaviors.ssfuWinLossStreaks = {
    attach: function(context, settings) {
      let chartCanvas = $(context).
        find('#win-loss-streaks-history').
        once('ssfu-chart');

      if (chartCanvas.length) {
        let historyData = $(chartCanvas).data('history');
        let labels = [];
        let data = [];
        $.each(historyData, (key, item) => {
          let date = new Date(item.date * 1000);
          labels.push(date.getUTCDay() + '/' + date.getMonth() + '/' +
            date.getFullYear());
          data.push(item.result);
        });

        let config = {
          type: 'line',
          data: {
            labels: labels,
            datasets: [
              {
                data: data,
                label: 'Win Loss Streak',
                borderColor: '#00A5E2',
                fill: false,
                borderJoinStyle: 'miter',
                cubicInterpolationMode: 'monotone',
                lineTension: 0,
              }],
          },
          options: {
            responsive: true,
            title: {
              display: true,
              text: 'Streaks',
            },
            maintainAspectRatio: false,
          },
        };

        let chart = new Chart(chartCanvas, config);
      }
    },
  };

})(jQuery, Drupal);


