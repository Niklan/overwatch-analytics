(function($) {

  Vue.component('select2', {
    template: `<select>
  <slot></slot>
</select>`,

    props: [
      'options',
      'value',
    ],

    mounted: function() {
      let vm = this;
      $(this.$el)
      // init select2
        .select2({data: this.options}).val(this.value).trigger('change')
      // emit event on change.
        .on('change', function() {
          vm.$emit('input', this.value);
          vm.$emit('change', this.value);
        });
    },

    watch: {
      value: function(value) {
        // update value
        // @todo add support for multiple select.
        $(this.$el).val(value);
      },

      options: function(options) {
        // update options
        $(this.$el).empty().select2({data: options});
      },
    },

    destroyed: function() {
      $(this.$el).off().select2('destroy');
    },
  });

})(jQuery);