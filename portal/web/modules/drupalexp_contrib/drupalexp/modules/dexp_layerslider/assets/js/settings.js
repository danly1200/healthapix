(function ($, Drupal) {
  "use strict";
  Drupal.behaviors.dexp_layerslider_settings = {
    attach: function () {
      $('form#dexp-layerslider-settings').once('dexp-submit').each(function () {
        $(this).submit(function () {
          var settings = {};
          $('.setting-option').each(function () {
            if ($(this).is('[type=checkbox]')) {
              if ($(this).prop('checked')) {
                settings[$(this).attr('name')] = 1;
              } else {
                settings[$(this).attr('name')] = 0;
              }
            } else {
              settings[$(this).attr('name')] = $(this).val();
            }
          });
          $('input[name=settings]').val(JSON.stringify(settings));
        });
      });
    }
  };
})(jQuery, Drupal);
