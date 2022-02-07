(function ($, Drupal) {

  window.onload = function() {
    // var default_server_value = $('#edit-fhir-server .mdc-list-item.mdc-list-item--selected').text().trim();
    // var version = default_server_value.split("_");
    // // var version_name = version[0].toUpperCase();
    // var version_name = $('#fhir-smart-app .mdc-list li.mdc-list-item.mdc-list-item--selected').attr('data-version-name');
    // if(version_name.length == 0) {
    //   version_name = $('#fhir-app-edit .mdc-list li.mdc-list-item.mdc-list-item--selected').attr('data-version-name');
    // }
    //
    // $("#edit-user-scopes .fieldset-wrapper, #edit-patient-scopes .fieldset-wrapper").find("div.mdc-data-table__row.user-field-op").removeClass('hide');
    //
    // $( "#edit-user-scopes .fieldset-wrapper div.mdc-data-table__row.user-field-op, #edit-patient-scopes .fieldset-wrapper div.mdc-data-table__row.user-field-op" ).each(function( index ) {
    //   var version_attr = $( this ).attr('data-vname').split("$#");
    //   var row_hide = true;
    //   for ( var i = 0, l = version_attr.length; i < l; i++ ) {
    //     var version_name_attr = version_attr[i];
    //     if(version_name_attr === version_name) {
    //       row_hide = false;
    //     }
    //   }
    //   if(row_hide) {
    //     $( this ).addClass('hide');
    //   }
    //
    // });

  };

  $(document).on('click','input[name="radio_for_multi_select"]',function() {
    var selected_scope_type = $(this).attr('value');
    $('ul.improvedselect_all li').show();
    if(selected_scope_type == 'user_scopes') {
      $('ul.improvedselect_all li').each(function(value, key){
        var scope = $(this).attr('so');
        if(!scope.startsWith('user/')) {
          $(this).hide();
        }
      })
    } else if(selected_scope_type == 'patient_scopes') {
      $('ul.improvedselect_all li').each(function(value, key){
        var scope = $(this).attr('so');
        if(!scope.startsWith('patient/')) {
          $(this).hide();
        }
      })
    } else if(selected_scope_type == 'system_scopes') {
      $('ul.improvedselect_all li').each(function(value, key){
        var scope = $(this).attr('so');
        if(!scope.startsWith('system/')) {
          $(this).hide();
        }
      })
    } else {
      $('ul.improvedselect_all li').each(function(value, key){
        var scope = $(this).attr('so');
        if(scope.startsWith('user/') || scope.startsWith('patient/') || scope.startsWith('system/')) {
          $(this).hide();
        }
      })
    }

  //   var input_value = $(this).attr('value');
  //   var selected_fhir_server = $('#fhir-smart-app .mdc-list li.mdc-list-item.mdc-list-item--selected').attr('data-fhir-server-name').trim();
  //
  //   $.ajax({
  //     url: "/fhir_app/getScopes/" + input_value + '/' + selected_fhir_server,
  //     type: "post",
  //     dataType: "json",
  //     data: {input_value} ,
  //     success: function (response) {
  //       $('ul.improvedselect_all').html('');
  //       $('ul.improvedselect_all li').bind('click');
  //       $.each(response,function(item, index){
  //         $('ul.improvedselect_all').append('<li so="'+ item +'" class>'+ item +'</li>');
  //       })
  //
  //     },
  //     error: function(jqXHR, textStatus, errorThrown) {
  //
  //     }
  //   });
  })


  Drupal.behaviors.fhir_apps = {
    attach: function (context, settings) {

      // $('.form-item-fhir-server #mdc-select-ul li', context).bind('click', function(j) {

        // var selected_fhir_server = $(this).attr('data-fhir-server-name');

        // $.ajax({
        //       url: "/fhir_app/getScopes/" + selected_fhir_server,
        //       type: "post",
        //       dataType: "html",
        //       //data: {input_value} ,
        //       success: function (response) {
        //         // $('#scopes-multiselect-container').html(response);
        //         // $('ul.improvedselect_all').html('');
        //         // $('ul.improvedselect_all li').bind('click');
        //         // $.each(response,function(item, index){
        //           $('ul.improvedselect_sel').empty();
        //         $('select#scopes-multiselect-container').empty();
        //           $('select#scopes-multiselect-container').append('<option value="fhirUser">fhirUser</option>');
        //         // $('ul.improvedselect_all').empty();
        //         // $('ul.improvedselect_all').append('<li so="fhirUser">fhirUser</li>');
        //         const options = settings.improved_multi_select;
        //         options.selectors.forEach(function (selector) {
        //           improvedselectAttach('select#scopes-multiselect-container', options, context);
        //         });
        //         // $('ul.improvedselect_all').bind('click');
        //         // })
        //
        //       },
        //       error: function(jqXHR, textStatus, errorThrown) {
        //         alert('error');
        //       }
        //     });
        // var res = selected_server.split("_");
        //var version_name = res[0].toUpperCase();
        // var version_name = $(this).attr('data-version-name');

        // $("#edit-user-scopes .fieldset-wrapper, #edit-patient-scopes .fieldset-wrapper").find("div.mdc-data-table__row.user-field-op").removeClass('hide');

        // $( "#edit-user-scopes .fieldset-wrapper div.mdc-data-table__row.user-field-op, #edit-patient-scopes .fieldset-wrapper div.mdc-data-table__row.user-field-op" ).each(function( index ) {
        //   var version_attr = $( this ).attr('data-vname').split("$#");
        //   var row_hide = true;
        //   for ( var i = 0, l = version_attr.length; i < l; i++ ) {
        //     var version_name_attr = version_attr[i];
        //     if(version_name_attr === version_name) {
        //       row_hide = false;
        //     }
        //   }
        //   if(row_hide) {
        //     $( this ).addClass('hide');
        //   }
        //
        // });

      // });
    }
  };
})(jQuery, Drupal);

(function ($, Drupal) {
  'use strict';

  Drupal.behaviors.fhir_apps = {
    attach: function (context, settings) {
      $('.form-item-fhir-server #mdc-select-ul li', context).click( function(j) {
        var selected_fhir_server = $(this).attr('data-fhir-server-name');
        $.ajax({
          url: "/fhir_app/getScopes/" + selected_fhir_server,
          type: "post",
          dataType: "json",
          success: function (response) {
            $('ul.improvedselect_sel').empty();
            $('ul.improvedselect_all').empty();
            $('div.improvedselect').remove();
            $('select#scopes-multiselect-container').empty();
            $.each( response, function(index, scope_value){
              $('select#scopes-multiselect-container').append('<option value="'+ scope_value +'">'+ scope_value +'</option>');
            })

            Drupal.attachBehaviors(context, settings);

          },
          error: function (jqXHR, textStatus, errorThrown) {
            alert('error in rendering the scopes');
          }
        });
      })
    }
  }
})(jQuery, Drupal);
