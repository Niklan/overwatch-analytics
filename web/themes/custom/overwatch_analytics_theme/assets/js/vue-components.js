(function($) {

  Vue.component('select2', {

    /**
     * Template for component.
     */
    template: `<select>
  <slot></slot>
</select>`,

    props: {
      /**
       * An array with options list.
       */
      options: {},
      default: {},
    },

    data: function() {
      return {
        /**
         * Value of select.
         */
        value: [],
      }
    },

    /**
     * Fired up when Vue.js is loaded and ready.
     */
    mounted: function() {
      let vm = this;
      $(this.$el).
        select2({data: this.options}).
        val(vm.default).
        trigger('change').
        on('change', function() {
          vm.value = $(this).val();
          vm.$emit('input', vm.value);
          vm.$emit('change', vm.value);
        });
    },

    watch: {
      /**
       * Update value for select if value was changed in JS.
       * @param value
       */
      value: function(value) {
        $(this.$el).val(value);
      },

      /**
       * Update options.
       * @param options
       */
      options: function(options) {
        $(this.$el).empty().select2({data: options});
      },
    },

    /**
     * Destroy callback, called when Vue.js instance was destroyed.
     */
    destroyed: function() {
      // Disable plugin for this instance.
      $(this.$el).off().select2('destroy');
    },

  });

})(jQuery);