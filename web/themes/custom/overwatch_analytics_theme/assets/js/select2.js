/**
 * @file
 * Select2 integration for theme behaviors.
 */

(function ($, Drupal, Vue) {

  'use strict';

  Drupal.behaviors.select2Integration = {
    attach: function (context, settings) {
      let select = $(context).find('select').once('select2');

      if (select.length) {
        $.each(select, (key, element) => {
          $(element).select2();
        });
      }
    }
  };

})(jQuery, Drupal, Vue);

Vue.component('select2', {
  props: ['options', 'value'],
  template: '#select2-template',
  mounted: function () {
    let vm = this
    $(this.$el)
    // init select2
      .select2({ data: this.options })
      .val(this.value)
    .trigger('change')
    // emit event on change.
      .on('change', function () {
        vm.$emit('input', this.value)
      })
  },
  watch: {
    value: function (value) {
      // update value
      $(this.$el).val(value)
    },
    options: function (options) {
      // update options
      $(this.$el).empty().select2({ data: options })
    }
  },
  destroyed: function () {
    $(this.$el).off().select2('destroy')
  }
})
