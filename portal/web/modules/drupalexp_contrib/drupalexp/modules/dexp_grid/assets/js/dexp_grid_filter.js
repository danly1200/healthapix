(function ($, Drupal, settings) {
  Drupal.behaviors.dexp_grid_filter = {
    attach: function (context, settings) {
      $('ul.dexp-grid-filter').once('filter').each(function () {
        var grid = $(this).parents('.view-header').next('.view-content').find('.dexp-grid-item').parent();// $($(this).data('grid')).find('.dexp-grid-inner');
        var $this = $(this);
        $(this).find('a[data-filter]').each(function () {
          $(this).click(function (e) {
            $this.find('a[data-filter]').removeClass('active');
            $(this).addClass('active');
            var filter_by = $(this).data('filter');
            grid.shuffle('shuffle', function ($el, shuffle) {
              return filter_by == '*' || $el.hasClass(filter_by);
            });
          });
        });
        var url = window.location.href, idx = url.indexOf("#"), hash = idx != -1 ? url.substring(idx + 1) : "";
        if (hash != "") {
          setTimeout(function(){
            $('ul.dexp-grid-filter a[data-filter=' + hash + ']').trigger('click');
          },1000)
        }
      });
    }
  };
})(jQuery, Drupal, drupalSettings);