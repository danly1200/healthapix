(function ($, Drupal, settings) {
  "use strict";
  Drupal.behaviors.dexp_video_popup_shortcode = {
    attach: function (context, settings) {
      var colorboxSettings = $.extend({iframe: true, innerWidth:640, innerHeight:390}, settings.colorbox);
      $('.dexp-video-popup', context).once('colorbox').each(function () {
        if($(this).is('a')){
          $(this).colorbox(colorboxSettings);
        }else if($(this).parent().is('a')){
          $(this).parent().colorbox(colorboxSettings);
        }
      });
    }
  };
})(jQuery, Drupal, drupalSettings);