/*
  This is the javascript file that will contain all the custom javascript code that you will be writing to customize the interactivity of the Apigee developer portal.
*/
(function ($, Drupal) {
  $(document).ready(function () {
    $('.apigee-footer-logo').click(function () {
      location.href = "http://apigee.com/about/solutions/healthcare";
    });
    $('.persistent-footer-logo').click(function () {
      location.href = "https://digitalapicraft.com";
    });
    $('body').on('click', '.modal-edit-app-cancel-extra', function () {
      $('.modal-dialog .close').trigger('click');
      return false;
    });
    $('body').on('click', '.modal-delete-app-cancel', function () {
      $('.modal-dialog .close').trigger('click');
      return false;
    });
  });
  /*
    Added active class to quick tab
  */
  var path = window.location.pathname;
  if (path.charAt(path.length - 1) == "/")
    path = path.substring(0, path.length - 1);
  if (path.charAt(0) != "/")
    path = "/" + path;
  $(".quick-tab-apis a[href='" + path + "']").addClass("active");


  var height = $(window).height();
  var headerHeight = $('header').outerHeight();
  var footerHeight = $('footer').outerHeight();
  var finalHeight = height - headerHeight - footerHeight - 75;
  $('.page-container').css('min-height', (finalHeight - 14));

  // Swagger oauth swparation
  $("body").on('click', ".b2c-authorize-btn", function () {
    setTimeout(function () {
      $(".modal-dialog-ux").addClass('b2c-container');
      // if there is no B2C content display message
      if (!$(".modal-ux-content div").is("#OAuthV2B2C")) {
        $(".modal-ux-content").append(" <b>These endpoints are not available in B2C use case</b>.");
      }
    }, 30);

  });
  $("body").on('click', ".b2b-authorize-btn", function () {
    setTimeout(function () {
      $(".modal-dialog-ux").addClass('b2b-container');
    }, 30);
  });
// Fetch all api's selected from the admin for showing custom attributes
  $.get('/fhir_get_api/api_resource', function (data, status) {
    var splitapi = data.toString().toLowerCase();
    splitapi = splitapi.replace(' ', '-');
    var apiarr = splitapi.split(',');
    var countarr = [];
    for (var i = 0; i < apiarr.length; ++i) {
      // only if checked display custom attribs
      countarr[apiarr[i]] = 1;
      // if json file is empty and display error show custom attrib
      if ($('.js-form-type-managed-file').hasClass('has-error')) {
        $(".custom_attrib input").prop('required', 'required');
        $(".custom_attrib label").addClass('form-required');
        $('.custom_attrib').attr('style', 'display: block');
        countarr[apiarr[i]] = 2;
      }
      $('#edit-api-products-' + apiarr[i]).click(function () {
        present = true;
        var id = $(this).attr('id');
        var idtrim = id.replace('edit-api-products-', '');
        countarr[idtrim] = countarr[idtrim] + 1;
        // once checkbox is selected
        if (countarr[idtrim] % 2 == 0) {
          $('.custom_attrib').addClass('custom_attrib_add' + idtrim);
          $('.custom_attrib_add' + idtrim).attr('style', 'display: block');
          //$(".custom_attrib input").prop('required', true);
          $(".custom_attrib input, .custom_attrib_file input").prop('required', 'required');
          $(".form-type-managed-file input").prop('required', 'required');
          $(".custom_attrib label").addClass('form-required');

        } else {
          $('.custom_attrib').removeClass('custom_attrib_add' + idtrim);
          var allclasses = $('.custom_attrib').attr('class');
          var pieces = allclasses.split(' ');
          var lastclass = pieces[pieces.length - 1];
          var remclass = idtrim;
          if (lastclass.indexOf('custom_attrib_add') != -1) {
            $('.custom_attrib_add' + idtrim).attr('style', 'display: block');
          } else {
            $('.custom_attrib').attr('style', 'display: none');
            $(".custom_attrib input").prop('required', false);
            $(".custom_attrib label").removeClass('form-required');
          }
        }

      });
    }
  });
  // Dont submit the form when error comes
  $('.modal-add-app-submit').click(function (e) {
    if ($('#edit-file-markup div').hasClass('alert-danger')) {
      e.preventDefault();
    }
  });

  /*
  * Code to fix issue DE16 GH-290
  * To handle the Apps Edit form: Toggle the view of custom attributes.
  * */
  $(document).ajaxComplete(function (event, xhr, settings) {

    if ((settings.url.indexOf("/apps/") !== -1) && (settings.url.indexOf("/edit?_wrapper_format=drupal_modal") !== -1)) {
      let admin_config_data = [];
      $.get("/fhir_get_api/api_resource", function (data) {
        admin_config_data = data;
      });
      $("input[id*=edit-credential-]").click(function () {
        let selected = [];
        $('div[id*=edit-credential-].form-checkboxes input:checked').each(function () {
          selected.push($(this).attr('value'));
        });

        $.each(selected, function (key, value) {
          let index = $.inArray(value, admin_config_data);
          if (index !== -1) {

            if ($("div[id*=edit-field-dataset-wrapper]").hasClass('visually-hidden')) {
              $("div[id*=edit-field-dataset-wrapper]").removeClass('visually-hidden');
            }
            if ($("div[id*=edit-field-fhirstore-wrapper]").hasClass('visually-hidden')) {
              $("div[id*=edit-field-fhirstore-wrapper]").removeClass('visually-hidden');
            }
            if ($("div[id*=edit-field-location-wrapper]").hasClass('visually-hidden')) {
              $("div[id*=edit-field-location-wrapper]").removeClass('visually-hidden');
            }
            if ($("div[id*=edit-field-project-wrapper]").hasClass('visually-hidden')) {
              $("div[id*=edit-field-project-wrapper]").removeClass('visually-hidden');
            }
            return false;

          } else {

            $("div[id*=edit-field-dataset-wrapper]").addClass('visually-hidden');
            $("div[id*=edit-field-fhirstore-wrapper]").addClass('visually-hidden');
            $("div[id*=edit-field-location-wrapper]").addClass('visually-hidden');
            $("div[id*=edit-field-project-wrapper]").addClass('visually-hidden');
          }
        });
      });
    }
  });

})(jQuery), Drupal;
