(function ($, Drupal) {
  Drupal.behaviors.fhir_datasets = {
    attach: function (context, settings) {

      // FHIR Datasets expand/collapse

      $('.dataset__title', context).bind('click', function(j) {

        var dropDown = $(this).closest('.acc__item').find('.dataset__server__panel');
        $(this).closest('.acc-datasets').find('.dataset__server__panel').not(dropDown).slideUp();
        // $('span.arrow-icon').html('keyboard_arrow_down');

        if ($(this).hasClass('active')) {
          $(this).removeClass('active');
          // $(this).find('span.arrow-icon').html('keyboard_arrow_down');
        } else {
          $(this).closest('.acc-datasets').find('.dataset__title.active').removeClass('active');
          $(this).addClass('active');
        }

        dropDown.stop(false, true).slideToggle();
        j.preventDefault();
      });

      $('.cancel-fhir-server', context).bind('click', function(j) {
        $('.ui-dialog-titlebar-close').click();
        j.preventDefault();
      });

    }
  };
})(jQuery, Drupal);
