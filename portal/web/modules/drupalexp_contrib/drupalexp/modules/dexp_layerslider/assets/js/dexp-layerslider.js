(function ($, Drupal, drupalSettings) {
  "use strict";
  var navigationTpl = {
    hesperiden: '',
    gyges: '',
    hades: '<div class="tp-arr-allwrapper"><div class="tp-arr-imgholder"></div></div>',
    ares: '<div class="tp-title-wrap"><span class="tp-arr-titleholder">{{title}}</span></div>',
    hebe: '<div class="tp-title-wrap">	<span class="tp-arr-titleholder">{{title}}</span><span class="tp-arr-imgholder"></span> </div>',
    hermes: '<div class="tp-arr-allwrapper"><div class="tp-arr-imgholder"></div><div class="tp-arr-titleholder">{{title}}</div></div>',
    persephone: '',
    erinyen: '<div class="tp-title-wrap"><div class="tp-arr-imgholder"></div><div class="tp-arr-img-over"></div><span class="tp-arr-titleholder">{{title}}</span></div>',
    zeus: '<div class="tp-title-wrap"><div class="tp-arr-imgholder"></div></div>',
    metis: '',
    dione: '<div class="tp-arr-imgwrapper"><div class="tp-arr-imgholder"></div></div>',
    uranus: '',
  };
  var bulletTpl = {
    hesperiden: '',
    gyges: '',
    hades: '<span class="tp-bullet-image"></span>',
    ares: '<span class="tp-bullet-title">{{title}}</span>',
    hebe: '<span class="tp-bullet-image"></span>',
    hermes: '',
    hephaistos: '',
    persephone: '',
    erinyen: '',
    zeus: '<span class="tp-bullet-image"></span><span class="tp-bullet-imageoverlay"></span><span class="tp-bullet-title">{{title}}</span>',
    metis: '<span class="tp-bullet-img-wrap"><span class="tp-bullet-image"></span></span><span class="tp-bullet-title">{{title}}</span>',
    dione: '<span class="tp-bullet-img-wrap"><span class="tp-bullet-image"></span></span><span class="tp-bullet-title">{{title}}</span>',
    uranus: '<span class="tp-bullet-inner"></span>',
  };
  Drupal.behaviors.dexp_layerslider = {
    attach: function (context, settings) {
      $('.layerslider-banner').once('revolution').each(function () {
        settings.dexp_layerslider.google_fonts = settings.dexp_layerslider.google_fonts || {};
        var slider_settings = settings.dexp_layerslider_settings[$(this).attr('id')];
        $.each(settings.dexp_layerslider.google_fonts, function () {
          if (this.family === '')
            return;
          this.variant = this.variant || 'regular';
          var apiUrl = [];
          apiUrl.push('//fonts.googleapis.com/css?family=');
          apiUrl.push(this.family.replace(/ /g, '+'));
          apiUrl.push(':');
          apiUrl.push(this.variant);
          var url = apiUrl.join('');
          $('head').append('<style media="all">@import url("' + url + '");</style>');
        });
        slider_settings.timer = slider_settings.timer || '';
        if (slider_settings.timer === '') {
          slider_settings.hideTimerBar = 'on';
        }
        slider_settings.navigation = {
          arrows: {
            enable: Boolean(slider_settings.arrows),
            style: slider_settings.arrows_style,
            tmp: navigationTpl[slider_settings.arrows_style],
            hide_onleave: !Boolean(slider_settings.arrows_always_show)
          },
          bullets: {
            enable: Boolean(slider_settings.bullets),
            style: slider_settings.bullets_style,
            tmp: bulletTpl[slider_settings.bullets_style],
            hide_onleave: !Boolean(slider_settings.bullets_always_show),
            h_align: 'center',
            v_align: 'bottom',
            h_offset: 0,
            v_offset: 20,
            space: 5
          }
        };
        if (slider_settings.touchenabled == 'on') {
          slider_settings.navigation.touch = {
            touchenabled: 'on',
            swipe_threshold: 75,
            swipe_min_touches: 1,
            swipe_direction: 'horizontal',
            drag_block_vertical: true
          }
        }
        slider_settings.fullScreen = $(window).height() > $(window).width() ? 'off' : slider_settings.fullScreen;
        slider_settings.extensions = settings.dexp_layerslider.extensionsPath;
        var api = $(this).revolution(slider_settings);
        var $this = $(this);
        setTimeout(function () {
          $this.css({
            opacity: 1
          })
        }, 200);
        $(this).data('revslider', api);
      });
    }
  };
})(jQuery, Drupal, drupalSettings);
