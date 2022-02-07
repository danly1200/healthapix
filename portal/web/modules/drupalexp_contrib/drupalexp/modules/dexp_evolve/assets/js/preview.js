(function($, Drupal){
   "use strict";
    Drupal.behaviors.preview = {
        attach: function (context, settings) {
            var $predefine = settings.preview;
            $("select[name='box_style']").change(function(){
                if ($(this).val() == "") {
                    $(".preview-box-style").attr('src',"");
                } else {
                   $(".preview-box-style").attr('src',$predefine[$(this).val()]);
                }
            });
        }
    };
})(jQuery, Drupal);

