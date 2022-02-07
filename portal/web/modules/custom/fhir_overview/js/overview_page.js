(function ($, Drupal) {
  Drupal.behaviors.fhir_overview_page = {
    attach: function (context, settings) {
      var sidebar = $('.overview-page .sidebar-wrapper');
      $(window).scroll(function() {
        if ($(window).scrollTop() >= $('.mdc-container').offset().top) {
          sidebar.addClass('fixed');
        } else {
          sidebar.removeClass('fixed');
        }

        $('.right-section-title').each(function() {
          if($(window).scrollTop() >= $(this).offset().top - 170) {
            var id = $(this).attr('id');
            $(".section-headings .section-title").find("a.section-link").removeClass('active');
            $(".section-headings .section-title").find("a.section-link[href ='#" + id + "']").addClass('active');
          }
        });


      });
    }
  };
})(jQuery, Drupal);
