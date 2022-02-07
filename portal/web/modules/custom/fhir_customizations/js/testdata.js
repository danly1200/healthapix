(function ($, Drupal) {
  Drupal.behaviors.fhir_datasets = {
    attach: function (context, settings) {
      // Test data page code
      $(".inner-payload").hide();
      $(".inner-payload.default-active").show();
      $('.node-test-data .mdc-tab').click(function () {
        var id = $(this).attr('id');
        $(".inner-payload").hide();
        $(".inner-payload-" + id).show();
        $('.node-test-data .mdc-tab__text-label').removeClass('scope-tab-active');
        $('.mdc-tab').removeClass('mdc-tab--active');
        $(this).addClass('mdc-tab--active');
        // Clipboard functionality for the FHIR samples page
        // new ClipboardJS('.ghc-fhir-sample-copy-btn');
      });
      var currentlist = window.location.pathname;
      $("#block-views-block-test-data-list-block-1 a").each(function () {
        if ($(this).attr('href') == currentlist) {
          $(this).addClass('isactive');
        }
      });
    }
  };
})(jQuery, Drupal);
