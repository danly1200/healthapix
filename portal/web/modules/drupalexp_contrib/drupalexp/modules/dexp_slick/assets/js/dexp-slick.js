(function ($, Drupal, settings) {
  "use strict";
  Drupal.behaviors.dexp_slick = {
    attach: function (context, settings) {
      $('.dexp-slick-carousel').each(function () {
        if ($(this).find('>.dexp-slick-carousel-inner.slick-initialized').length === 0) {
          var uuid = $(this).data('uuid');
          var options = settings.dexp_slick[uuid];
          var booleanOptions = ['arrows', 'infinite', 'dots', 'centerMode', 'autoplay', 'pauseOnHover', 'swipe', 'variableWidth'];
          var integerOptions = ['initialSlide', 'lg', 'md', 'sm', 'xs', 'autoplaySpeed', 'rows', 'slideMargin', 'speed'];
          $(booleanOptions).each(function () {
            if (typeof options[this] !== 'undefined') {
              options[this] = Boolean(options[this]);
            }
          });
          if(options.arrows === true){
            if($(this).find('.slick-next').length === 1){
              options.nextArrow = $(this).find('.slick-next');
              options.prevArrow = $(this).find('.slick-prev');
            }
          }
          $(integerOptions).each(function () {
            if (typeof options[this] !== 'undefined') {
              options[this] = parseInt(options[this]);
            }
          });
          if (options.mode === 'vertical') {
            options.vertical = true;
            /* Disable centermode */
            options.centerMode = false;
          } else if (options.mode === 'fade') {
            options.fade = true;
            options.lg = options.md = options.sm = options.xs = 1;
          }
          if (options.swipe === false) {
            options.touchMove = false;
          }
          options.appendDots = $(this);
          if (options.centerMode) {
            if (options.lg % 2 === 0) {
              options.lg = options.lg + 1;
            }
            if (options.md % 2 === 0) {
              options.md = options.md + 1;
            }
            if (options.sm % 2 === 0) {
              options.sm = options.sm + 1;
            }
            if (options.xs % 2 === 0) {
              options.xs = options.xs + 1;
            }
          }
          options.rows = 1;
          options.slidesToShow = options.lg;
          options.responsive = [
            {
              breakpoint: 1200,
              settings: {
                slidesToShow: options.md
              }
            },
            {
              breakpoint: 992,
              settings: {
                slidesToShow: options.sm
              }
            },
            {
              breakpoint: 768,
              settings: {
                slidesToShow: options.xs
              }
            }
          ];
          if(options.vertical){
            $(this).find('>.dexp-slick-carousel-inner').on('init setPosition', function () {
              var $this = $(this);
              var maxHeight = 0;
              $this.find('.slick-slide').each(function(){
                maxHeight = $(this).height() > maxHeight ? $(this).height() : maxHeight;
              });
              $this.find('.slick-slide').height(maxHeight);
            });
          }
          $(this).find('>.dexp-slick-carousel-inner').on('init setPosition', function () {
            var $this = $(this);
            $(this).parents('.shuffle').trigger('update');
            if (options.centerMode) {
              $(this).find('.slick-slide').on('click', function (e) {
                if ($(this).hasClass('slick-center') === false) {
                  e.preventDefault();
                  $this.slick('slickGoTo', $(this).data('slick-index'));
                }
              });
            }
          }).on('setPosition', function () {
            $(this).parents('.shuffle').trigger('update');
          }).slick(options);
        }
      });
    }
  };
})(jQuery, Drupal, drupalSettings);