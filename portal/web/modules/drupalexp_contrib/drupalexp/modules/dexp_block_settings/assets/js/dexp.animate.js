(function ($, Drupal, settings) {
  "use strict";
  Drupal.behaviors.dexp_block_settings_animate = {
    attach: function (context, settings) {
      AOS.init({
        disable: settings.dexp_block_settings.disbale_animation ? true : (settings.dexp_block_settings.disbale_animation_mobile ? 'mobile' : false),
        once: settings.dexp_block_settings.animation_once
      });
      $(window).on('load', function () {
        AOS.refresh();
      });
    }
  };
})(jQuery, Drupal, drupalSettings);