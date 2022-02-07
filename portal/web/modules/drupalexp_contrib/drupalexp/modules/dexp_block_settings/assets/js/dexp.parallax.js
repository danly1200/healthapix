(function ($, Drupal) {
  "use strict";
  var parallaxRatio = 0.15;
  var adjustParallaxBg = function () {
    var windowWidth = $(window).width();
    var windowHeight = $(window).height();
    $('.dexp-parallax').each(function () {
      var parallax_length = 0;
      if ($(this).offset().top < windowHeight) {
        parallax_length = $(this).offset().top + $(this).outerHeight();
      } else {
        parallax_length = windowHeight + $(this).outerHeight();
      }
      var backgroundWidth = 'auto';
      var backgroundHeight = parallax_length * parallaxRatio + $(this).outerHeight();
      var backgroundRatio = $(this).data('bgWidth') / $(this).data('bgHeight');
      if (backgroundHeight * backgroundRatio < $(this).outerWidth()) {
        backgroundWidth = $(this).outerWidth();
        backgroundHeight = backgroundWidth / backgroundRatio;
      }
      backgroundWidth = backgroundWidth == 'auto' ? 'auto' : backgroundWidth + 'px';
      $(this).css({
        backgroundSize: backgroundWidth + ' ' + backgroundHeight + 'px'
      });
    });
  };
  $(window).on('load, resize', function () {
    adjustParallaxBg();
  });
  Drupal.behaviors.dexp_block_settings_parallax = {
    attach: function (context, settings) {
      adjustParallaxBg();
      $('body').once('dexp-parallax').each(function () {
        $(window).on('scroll', function () {
          var windowScrollTop = $(window).scrollTop();
          var windowHeight = $(window).height();
          var windowScrollBottom = windowScrollTop + windowHeight;
          $('.dexp-parallax').each(function () {
            var offsetTop = $(this).offset().top;
            var offsetBottom = offsetTop + $(this).outerHeight();
            if (offsetTop < windowHeight && offsetBottom >= windowScrollTop) {
              var bgPsY = (windowScrollTop) * parallaxRatio * -1;
              $(this).css({
                backgroundPositionY: bgPsY
              });
            } else if (offsetTop <= windowScrollBottom && offsetBottom >= windowScrollTop) {
              var bgPsY = (windowScrollBottom - offsetTop) * parallaxRatio * -1;
              $(this).css({
                backgroundPositionY: bgPsY
              });
            }
          });
        });
      });
    }
  };
})(jQuery, Drupal);