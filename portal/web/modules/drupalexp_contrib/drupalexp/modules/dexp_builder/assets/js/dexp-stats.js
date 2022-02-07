(function ($, Drupal) {
  'use strict';
  Drupal.behaviors.dexp_builder_stats = {
    attach: function () {
      $('.dexp-stats').once('shortcode').each(function () {
        var $this = $(this);
        $(this).appear(function () {
          $this.find('.stats-count').animate({'number': $this.data('number')}, {
            step: function (n) {
              var decimal = $this.data('decimal');
              var text = parseInt(n);
              if(decimal != 'none'){
                text = text.toLocaleString();
                text = text.replace( ',', decimal );
              }
              $(this).text(text);
            },
            duration: $this.data('duration'),
            easing: 'linear'
          });
        }, {
          accX: 0,
          accY: 0,
          one: true
        });
      });
    }
  };
})(jQuery, Drupal);
