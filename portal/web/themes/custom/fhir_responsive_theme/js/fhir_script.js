(function ($, Drupal) {
  Drupal.behaviors.myModuleBehavior = {
    attach: function (context, settings) {

      //handling the default/custom radio button on stu3 clicked.
      //currently used the field name of the stu3 option.
      // Need to change this complete flow in pahse 2.
      $(".form-checkbox[value='GHC DSTU3']").bind('click', function() {
        if ($(this).is(":checked")) {
          $('.stu3-fields-panel').addClass('stu3-fields-panel-active');
        } else {
          $('.stu3-fields-panel').removeClass('stu3-fields-panel-active');
        }
      });

      $('.form-item-custom-attr-data-options').bind('change', function() {

        var currValue = $('.form-item-custom-attr-data-options .form-radio:checked').val();
        if(currValue == 'default') {
          $('.field--name-field-fhirstore .form-text').val($('.field--name-field-fhirstore').attr('data-fhirstore')).prop('readonly', true);
          $('.field--name-field-dataset .form-text').val($('.field--name-field-dataset').attr('data-dataset')).prop('readonly', true);
          $('.field--name-field-project .form-text').val($('.field--name-field-project').attr('data-project')).prop('readonly', true);
          $('.field--name-field-location .form-text').val($('.field--name-field-location').attr('data-location')).prop('readonly', true);

          $('.field--name-field-fhir-storage-type .form-select').val('default');

        } else {
          $('.field--name-field-fhirstore .form-text').val($('.field--name-field-fhirstore').attr('data-fhirstore-original-value')).prop('readonly', false);
          $('.field--name-field-dataset .form-text').val($('.field--name-field-dataset').attr('data-dataset-original-value')).prop('readonly', false);
          $('.field--name-field-project .form-text').val($('.field--name-field-project').attr('data-project-original-value')).prop('readonly', false);
          $('.field--name-field-location .form-text').val($('.field--name-field-location').attr('data-location-original-value')).prop('readonly', false);

          $('.field--name-field-fhir-storage-type .form-select').val('custom');
        }

      });





    }






  };
})(jQuery, Drupal);