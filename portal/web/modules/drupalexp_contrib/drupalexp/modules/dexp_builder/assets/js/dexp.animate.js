(function($, Drupal, settings) {
  "use strict";

  Drupal.behaviors.dexp_builder_animate = {
    attach: function(context, settings) {
      AOS.init();
      $(window).on('load', function() {
        AOS.refresh();
      });
    }
  };
})(jQuery, Drupal, drupalSettings);
