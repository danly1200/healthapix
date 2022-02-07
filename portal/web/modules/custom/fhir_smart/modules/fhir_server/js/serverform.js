(function ($, Drupal , drupalSettings) {
  Drupal.behaviors.fhir_datasets = {
    attach: function (context, settings) {

      // Fetch the file from the FHIR Server
      $('#edit-field-server-file-0-upload-button').val('Browse files');
      $('.fhir-server-create #edit-field-fhir-version-autofill-0-target-id').attr("readonly","readonly");
      $("#edit-field-fhir-api-products li").click(function(event){

        // Ajax call to fetch the details from the Apigee edge
        var apiProduct = $.trim($(this).text());
        $.ajax({
          url: Drupal.url('get-product-details-from-apigee'),
          type: "POST",
          contentType:"application/json; charset=utf-8",
          data: JSON.stringify(apiProduct),
          dataType: "json",
          success: function(result) {
            prepopulate(result.name, result.fhir_server_name, false, result.fhir_server_url, result.fhir_version);
          }
        });
      });

      var getval =$("input[name='field_server_file[0][fids]']").val();
      if(getval){
        var href = $('.form-item-field-server-file-0  a').attr('href');

        $.get(href, function(result)
        {
            //var fhir_server_base_url = drupalSettings.fhir_server.base_url;
          if(typeof result.fhirserver !== "undefined") {
            var product = result.fhirserver.defaultproduct;
            prepopulate(product, result.fhirserver.name,true, result.fhirserver.proxyurl , result.fhirserver.fhirversion);
          }
        });
      }

      // Prepopulate value based on file or user inputs
      function prepopulate(productname, productvalue, file, fhir_server_url, versionname){

        // Fetch the fhir server base url dynamically
        var baseurl = fhir_server_url;

        if(file){
          var productexist = false;
          $("#edit-field-fhir-api-products li").each(function () {
            if ($(this).data('value') == productname ){
              $(this).siblings().removeClass("mdc-list-item--selected");
              $(this).addClass('mdc-list-item--selected');
              $(this).attr("selected","selected");
              $('#edit-field-fhir-api-products #demo-selected-text').text(productname);
              $(this).click();
              productexist = true;
            }
          });
          if(productexist == false){
            if( !($("form div").hasClass('file-validation-error'))){
              var error_html = '<div><div class="drupal-message-wrap error-message" role="contentinfo" aria-label="Error message">\n' +
                '                  <span class="drupal-message-status-heading">Error!</span>\n' +
                '                <span class="drupal-message-status-content"><em class="placeholder"></em>Please Upload a json file with valid product.</span>\n' +
                '        <span class="drupal-message-wrap-close-btn" onclick="this.parentNode.style.display=\'none\';"><span class="material-icons">clear</span></span>\n' +
                '      </div></div>';

              $("#drupal-message-wrap-id").prepend(error_html);
            }
          }else {
            $('.fhir-server-create #edit-field-fhir-version-autofill-0-target-id').val(versionname).focus();
            $('.fhir-server-create #edit-field-fhir-server-base-url-0-value').val(baseurl).focus();
            $('.fhir-server-create #edit-title-0-value').val(productvalue).focus();
          }
        } else {
          $('.fhir-server-create #edit-field-fhir-version-autofill-0-target-id').val(versionname).focus();
          $('.fhir-server-create #edit-field-fhir-server-base-url-0-value').val(baseurl).focus();
          $('.fhir-server-create #edit-title-0-value').val(productvalue).focus();
        }
      }
    }
  };
})(jQuery, Drupal, drupalSettings);
