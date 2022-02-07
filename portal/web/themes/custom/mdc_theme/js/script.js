(function ($, Drupal) {

  $(window).on('load', function() {
    $( "ul.oauth-api-list li" ).each(function( index ) {
      var link_path = $(this).find('a').attr('href');
      $(this).removeClass('swagger-api-list-link-active swagger-api-list-link');
      if(location.pathname === link_path) {
        $(this).removeClass('swagger-api-tab');
        $(this).addClass('swagger-api-list-link-active swagger-api-list-link');
      }
    });
  });

  // Add min height to the content section
  var height = $(window).height();
  var headerHeight = $('header').outerHeight();
  var footerHeight = $('footer').outerHeight();
  var finalHeight = height - headerHeight - footerHeight;
  $('.content-wrapper .mdc-container').css('min-height', (finalHeight - 14));

  Drupal.behaviors.mdcThemeScript = {
    attach: function (context, settings) {

      /* Copied from old theme.*/
      $('body').on('click', '.modal-edit-app-cancel', function () {
        $('.ui-dialog .ui-dialog-titlebar-close').trigger('click');
        return false;
      });
      $('body').on('click', '.modal-delete-app-cancel', function () {
        $('.ui-dialog .ui-dialog-titlebar-close').trigger('click');
        return false;
      });

      $(".oauth-api-list li.swagger-api-tab").click(function() {
        // var x = location.pathname;
        console.log('x');
        // $('.oauth-api-list').find('li').removeClass('swagger-api-list-link-active');
        // $(this).find("a").attr('href');
        console.log($(this).find("a").attr('href'));
      });

      $(function() {
        var x = location.href;
        $('li.swagger-api-tab a[href^="/' + location.pathname.split("/node")[1] + '"]').addClass('active');
      });


      /* Copied from old theme. need to change in the later phase. */

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

      //handling the default/custom radio button on stu3 clicked.
      //currently used the field name of the stu3 option.
      // Need to change this complete flow in pahse 2.
      $(".form-checkbox[value='GHC DSTU3']").bind('click', function () {
        if ($(this).is(":checked")) {
          $('.stu3-fields-panel').addClass('stu3-fields-panel-active');
        } else {
          $('.stu3-fields-panel').removeClass('stu3-fields-panel-active');
        }
      });

      $('.form-item-custom-attr-data-options').bind('change', function () {

        var currValue = $('.form-item-custom-attr-data-options .form-radio:checked').val();
        if (currValue == 'default') {
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


        //for text field
        const {MDCTextField} = mdc.textField;
        const textField = [].map.call(document.querySelectorAll('.mdc-text-field'), function (el) {
          return new MDCTextField(el);
        });

      });

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


      $('.scope-tab').bind('click', function() {
        var currPanel = $(this).attr('data-panel');

        $('.scope-panel').hide();
        $('.'+currPanel).show();

        $('.scope-tab').removeClass('scope-tab-active');
        $(this).addClass('scope-tab-active');
      });

      var headerHeight;
      var reposition = function() {
        headerHeight || (headerHeight = $('header').outerHeight() + 20);
        setTimeout(function() {
          window.scrollBy(0, -headerHeight);
        }, 50);
      };

      $(document).on('click', 'form :submit', function(e) {
        var form;
        if ((form = $(e.target).closest('form')).length !== 1) {
          return;
        }
        if (!form[0].checkValidity || form[0].checkValidity()) {
          return;
        }
        reposition();
      });
    }
  };

  $(document).ready(function(e) {
    setTimeout(function() {
      if($('div#accordian').length > 0) {
        var active_li = $('div#accordian').find('li.active');
        active_li.closest('ul').parent('li').addClass('active');
        active_li.closest('ul.one-inner-api-accordion').parent('li').addClass('active');
      }
    }, 1000)

    $(document).on('click', 'div#accordian span.plus', function(e) {
        var link = $(this);
        var closest_ul = link.closest("ul");
        var parallel_active_links = closest_ul.find(".active")
        var closest_li = link.closest("li");
        var link_status = closest_li.hasClass("active");

        var count = 0;

        closest_ul.find("ul").slideUp(function() {
          if (++count == closest_ul.find("ul").length)
            parallel_active_links.removeClass("active");
        });

        if (!link_status) {
          closest_li.children("ul").slideDown({
            start: function () {
              $(this).css({
                display: "block"
              })
            }
          });
          closest_li.addClass("active");
        }
    });
    // slide functionality for the interoperability apis
    $('.swagger-api-list-links').slideUp();
    $(".swagger-api-list-header").click(function(){
      $(this).parent('div').find('.swagger-api-list-links').slideToggle();
    });
    // Clipboard functionality for the My apps page
    new ClipboardJS('.ghc-my-apps-copy-btn');
    // Clipboard functionality for the FHIR samples page

    $('.ghc-fhir-sample-copy-btn').click(function(){
      copyToClipboard($(this).parent('.payload-header-section').parent('.inner-payload').find('.payload-body-section').find('pre'));
    });

    $('span.show-consumer-key').click(function(){
      $(this).addClass('hide');
      var parentRow = $(this).parent('span').parent('td');
      parentRow.find('span#ghc-consumer-key').removeClass('hide');
      parentRow.find('span#ghc-consumer-secret').removeClass('hide');
      parentRow.find('span#my-apps-dots').addClass('hide');
      parentRow.find('span.hide-consumer-key').removeClass('hide');

    });

    $('span.hide-consumer-key').click(function(){
      $(this).addClass('hide');
      var parentRow = $(this).parent('span').parent('td');
      parentRow.find('span#ghc-consumer-key').addClass('hide');
      parentRow.find('span#ghc-consumer-secret').addClass('hide');
      parentRow.find('span#my-apps-dots').removeClass('hide');
      parentRow.find('span.show-consumer-key').removeClass('hide');

    });
  });

  // function to copy to clipboard
  function copyToClipboard(element) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(element).text()).select();
    document.execCommand("copy");
    $temp.remove();
  }

})(jQuery, Drupal);
