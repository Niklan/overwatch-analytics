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
            scales: {
              xAxes: [
                {
                  display: false,
                },
              ],
            },
          },
        };

        let chart = new Chart(chartCanvas, config);
      }
    },
  };

  Drupal.behaviors.ssfuGamesPlayedByMap = {
    attach: function(context, settings) {
      let chartCanvas = $(context).
        find('#games-played-by-map').
        once('games-played-by-map');

      if (chartCanvas.length) {
        let data = $(chartCanvas).data('data');
        let counts = [];
        let labels = [];
        $.each(data, (key, item) => {
          counts.push(item.played_count);
          labels.push(item.label);
        });

        let config = {
          type: 'doughnut',
          data: {
            datasets: [
              {
                data: counts,
                backgroundColor: [
                  '#CC0000',
                  '#E67300',
                  '#FFFF00',
                  '#CFE2F3',
                  '#38761D',
                  '#FF9900',
                  '#B45F06',
                  '#76A5AF',
                  '#B7B7B7',
                  '#FFD966',
                  '#666666',
                  '#93C47D',
                  '#3C78D8',
                  '#C27BA0',
                  '#F6B26B',
                  '#00FFFF',
                ],
              },
            ],
            labels: labels,
          },
          options: {
            legend: {
              display: false,
            },
            responsive: true,
            title: {
              display: true,
              text: 'Games Played by Map',
            },
            maintainAspectRatio: false,
          },
        };

        let chart = new Chart(chartCanvas, config);
      }
    },
  };

  Drupal.behaviors.ssfuWinPercentageByMap = {
    attach: function(context, settings) {
      let chartCanvas = $(context).
        find('#win-percentage-by-map').
        once('win-percentage-by-map');

      if (chartCanvas.length) {
        let data = $(chartCanvas).data('data');
        console.log(data);
        let win_percentage = [];
        let labels = [];
        $.each(data, (key, item) => {
          win_percentage.push(item.win_percentage);
          labels.push(item.label);
        });

        let config = {
          type: 'bar',
          data: {
            datasets: [
              {
                data: win_percentage,
                backgroundColor: [
                  '#CC0000',
                  '#E67300',
                  '#FFFF00',
                  '#CFE2F3',
                  '#38761D',
                  '#FF9900',
                  '#B45F06',
                  '#76A5AF',
                  '#B7B7B7',
                  '#FFD966',
                  '#666666',
                  '#93C47D',
                  '#3C78D8',
                  '#C27BA0',
                  '#F6B26B',
                  '#00FFFF',
                ],
              },
            ],
            labels: labels,
          },
          options: {
            legend: {
              display: false,
            },
            responsive: true,
            title: {
              display: true,
              text: 'Win % by map',
            },
            maintainAspectRatio: false,
            scales: {
              xAxes: [{
                ticks: {
                  callback: function(value) {
                    return value.substr(0, 6) + '…';
                  }
                }
              }],
              yAxes: [
                {
                  ticks: {
                    min: 0,
                    max: 100,
                    callback: function(value) {
                      return value + '%';
                    },
                  },
                  scaleLabel: {
                    display: true,
                    labelString: 'Percentage',
                  },
                }],
            },
            tooltips: {
              enabled: true,
              mode: 'label',
              callbacks: {
                title: function(tooltipItems, data) {
                  let id = tooltipItems[0].index;
                  console.log(tooltipItems, data);
                  return data.labels[id];
                },
                label: function(tooltipItems, data) {
                  return tooltipItems.yLabel + '%';
                }
              }
            },
          },
        };

        let chart = new Chart(chartCanvas, config);
      }
    },
  };

})(jQuery, Drupal);


