(function ($, Drupal) {
  Drupal.behaviors.myModuleBehavior = {
    attach: function (context, settings) {

      //for text field
      const {MDCTextField} = mdc.textField;
      const textField = [].map.call(document.querySelectorAll('.mdc-text-field'), function(el) {
        return new MDCTextField(el);
      });

      //submit


      //checkbox

      //select
      const {MDCSelect} = mdc.select;
      const textSelect = [].map.call(document.querySelectorAll('.mdc-select'), function(el) {
        return new MDCSelect(el);
      });


    // Select depending on ul and li in mdc-theme
      $(context).find('.mdc-container').once("main-content-wrapper").each(function () {
        $("#mdc-select-ul li").on("click",function() {
          $(this).closest(".select-parent-wrapper").find(".select-class-wrapper select").val($(this).data("value"));
        });
      });


    }






  };
})(jQuery, Drupal);
