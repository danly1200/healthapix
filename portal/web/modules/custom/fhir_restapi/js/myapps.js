(function ($, Drupal) {
  window.onload = function() {

    if(window.location.hash) {
      var app_type_tab = window.location.hash.substring(1);
      if(app_type_tab === 'normal'){
        document.getElementById('my-app-btn-normal-app').click();
      }
    }
    noresultsfound();
  };

  function noresultsfound() {
    var all_apps = $(".myapps-wrapper div.acc-content div.acc div.acc__card").length;
    var hidden_apps = $(".myapps-wrapper div.acc-content div.acc .acc__card.card-hidden").length;
    $(".myapps-wrapper div.no-results").empty();
    if (all_apps > 0) {
      if (all_apps === hidden_apps) {
        $(".myapps-wrapper div.no-results").empty().append("<h2> No Apps Found</h2>");
      }
    }
  }
  function myAppsVersionFilter(button_app_type, selectedDropDown) {
    var button_app_type;
    var selectedDropDown;

    if (selectedDropDown === 'all') {
      $(".acc__card").each(function () {
        var currentValueAttr = $(this).attr("data-server-version");
        var app_type = $(this).attr("data-app-type");

        if (button_app_type !== app_type) {
          $(this).addClass("card-hidden");
        }

      });

    }
    else {
      $(".acc__card").each(function () {
        var currentValueVersion = $(this).attr("data-server-version");
        var app_type = $(this).attr("data-app-type");
        var app_title = $(this).attr("data-app-title");

        if (currentValueVersion.length > 0) {
          if (currentValueVersion !== selectedDropDown) {
            if (button_app_type === 'smart' && app_type !== 'smart') {
              $(this).addClass("card-hidden");
            }

            if (button_app_type === 'normal' && app_type !== 'normal') {
              $(this).addClass("card-hidden");
            }

            if (button_app_type === app_type) {
              $(this).addClass("card-hidden");
            }

          }
          else if (currentValueVersion === selectedDropDown) {
            if (button_app_type === 'smart' && app_type !== 'smart') {
              $(this).addClass("card-hidden");
            }

            if (button_app_type === 'normal' && app_type !== 'normal') {
              $(this).addClass("card-hidden");
            }
          }
        }
        else {
          if (button_app_type === 'smart' && app_type !== 'smart') {
            $(this).addClass("card-hidden");
          }
          if (button_app_type === 'normal' && app_type !== 'normal') {
            $(this).addClass("card-hidden");
          }

          if (button_app_type === app_type) {
            if (selectedDropDown) {
              $(this).addClass("card-hidden");
            }
          }
        }

      });
    }

    noresultsfound();

  }

  $('#my-app-btn-normal-app').on('click', function () {
    document.getElementById("register-app-a").href="/app/normal/add";

    $(this).removeClass('active').addClass('active');

    $('#my-app-btn-smart-app').removeClass('active');

    $('.title-rightside').find('button.launch-app').addClass('hide');

    $('.version-select').find('div.mdc-select').addClass('hide');
    $('.title-rightside').css('margin-top','20px');

    $(".acc").find(`[data-app-type='smart']`).addClass('card-hidden');
    $(".acc").find(`[data-app-type='normal']`).removeClass('card-hidden');

    // var selectedDropDown = $('ul.version-list').find('li.mdc-list-item.mdc-list-item--selected').attr('data-value') ;
    // var button_app_type = 'normal';

    // myAppsVersionFilter(button_app_type,selectedDropDown);


    noresultsfound();

  });

  $('#my-app-btn-smart-app').on('click', function () {
    document.getElementById("register-app-a").href="/app/smart/add";

    $(this).removeClass('active').addClass('active');
    $('#my-app-btn-normal-app').removeClass('active');

    $('.title-rightside').find('button.launch-app').removeClass('hide');

    $('.version-select').find('div.mdc-select').removeClass('hide');
    $('.title-rightside').css('margin-top','10px');


    $(".acc").find(`[data-app-type='normal']`).addClass('card-hidden');
    $(".acc").find(`[data-app-type='smart']`).removeClass('card-hidden');

    var selectedDropDown = $('ul.version-list').find('li.mdc-list-item.mdc-list-item--selected').attr('data-value');
    var button_app_type = 'smart';

    myAppsVersionFilter(button_app_type,selectedDropDown);

  });

  $('ul.version-list li.mdc-list-item').on('click', function() {

    var selectedDropDown = $(this).attr('data-value');
    $(".acc__card").each( function () {
      $(this).removeClass("card-hidden");
    });

    var button_active = $('.app-btn-group').find('button.active').attr('id');

    if(button_active === 'my-app-btn-smart-app'){
      var button_app_type = 'smart';
    } else if(button_active === 'my-app-btn-normal-app') {
      var button_app_type = 'normal';
    }
    myAppsVersionFilter(button_app_type,selectedDropDown);

  });

  Drupal.behaviors.fhir_restapi = {
    attach: function (context, settings) {

    //Tab bar
      const {MDCTabBar}  = mdc.tabBar;

      const tabBar = [].map.call(document.querySelectorAll('.mdc-tab-bar'),function (el) {
        // el.setActiveTab(1);
        return new MDCTabBar(el);
      });

     //Data table
      const {MDCDataTable}  = mdc.dataTable;
      const dataTable = [].map.call(document.querySelector('.mdc-data-table'),function (el) {
        return new MDCDataTable(el);
      });



      // My apps expand/collapse

      $('.acc__title', context).bind('click', function(j) {

        var dropDown = $(this).closest('.acc__card').find('.acc__panel');
        $(this).closest('.acc').find('.acc__panel').not(dropDown).slideUp();
        $('span.arrow-icon').html('keyboard_arrow_down');

        if ($(this).hasClass('active')) {
          $(this).removeClass('active');
          $(this).find('span.arrow-icon').html('keyboard_arrow_down');
        } else {
          $(this).closest('.acc').find('.acc__title.active').removeClass('active');
          $(this).addClass('active');
          $(this).find('span.arrow-icon').html('keyboard_arrow_up');
        }

        dropDown.stop(false, true).slideToggle();
        j.preventDefault();
      });

    }
  };
})(jQuery, Drupal);
