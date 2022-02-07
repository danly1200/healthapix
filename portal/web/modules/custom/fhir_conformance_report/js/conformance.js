(function ($, Drupal) {
  Drupal.behaviors.fhir_datasets = {
    attach: function (context, settings) {
      // Conformance page code

      $( "a#params-link", context).once().bind('click',function(event) {
          event.preventDefault();
         var params_open = $(this).find('span.params-open').attr('class');
         var class_show = $(this).attr('class');
         if(params_open.indexOf('hide') > -1) {
           $('.conformance-page').find('tr#'+class_show).removeClass('hide');
           $(this).find('span.params-open').removeClass('hide');
        } else {
           $('.conformance-page').find('tr#'+class_show).addClass('hide');
           $(this).find('span.params-open').addClass('hide');
         }
      });

    }
  };
})(jQuery, Drupal);
