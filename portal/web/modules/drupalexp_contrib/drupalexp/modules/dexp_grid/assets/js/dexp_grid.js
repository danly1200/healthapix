(function ($, Drupal, settings) {
  'use strict';
  var breakpoints = {
    lg_cols: 'all and (min-width: 1200px)',
    md_cols: 'all and (min-width: 992px) and (max-width: 1199px)',
    sm_cols: 'all and (min-width: 768px) and (max-width: 991px)',
    xs_cols: 'all and (max-width: 767px)'
  };
  settings.dexp_grid.resize = true;
  settings.dexp_grid.window_w = 0;
  settings.dexp_grid.resizeHandle = null;

  var resizeEvent = function () {
    $('.views-view-dexp-grid').each(function () {
      var grid = $(this).find('.dexp-grid-item').parent();
      var uuid = $(this).data('uuid');
      var cols = 1;
      $.each(breakpoints, function (screen, query) {
        if (window.matchMedia(query).matches) {
          cols = parseInt(settings.dexp_grid[uuid][screen]);
        }
      });
      var colWidth = ($(grid).width() - ((cols - 1) * settings.dexp_grid[uuid].margin)) / cols;
      var colHeight = Math.ceil(colWidth / settings.dexp_grid[uuid].ratio);
      $(this).find('.dexp-grid-sizer').css({
        width: colWidth,
        margin: settings.dexp_grid[uuid].margin
      });
      settings.dexp_grid[uuid].itemWidth = colWidth;
      settings.dexp_grid[uuid].itemHeight = colHeight;
      $(grid).find('.dexp-grid-item').each(function () {
        var multiplier_w = $(this).data('grid-width') || false;
        var multiplier_h = $(this).data('grid-height') || false;
        if (multiplier_w !== false && multiplier_h !== false) {
          multiplier_w = multiplier_w > cols ? cols : multiplier_w;
          multiplier_h = cols == 1 ? 1 : multiplier_h;
          $(this).css({
            width: Math.ceil(colWidth * multiplier_w + (multiplier_w - 1) * settings.dexp_grid[uuid].margin),
            height: colHeight * multiplier_h + (multiplier_h - 1) * settings.dexp_grid[uuid].margin
          });
        } else {
          $(this).width(Math.ceil(colWidth));
        }
        $(this).css({marginBottom: settings.dexp_grid[uuid].margin});
      });
      $(grid).width($(grid).width() + 12);
      $(grid).shuffle('update').on('update', function () {
        $(this).shuffle('update');
      });
      $(grid).width('auto');
    });
  };

  Drupal.behaviors.dexp_grid_size = {
    attach: function (context, settings) {
      $(window).on('load resize', function (e) {
        if ($(window).width() == settings.dexp_grid.window_w)
          return false;
        settings.dexp_grid.window_w = $(window).width();
        /* Disable update when resize */
        clearTimeout(settings.dexp_grid.resizeHandle);
        settings.dexp_grid.resizeHandle = setTimeout(function () {
          resizeEvent();
        }, 500);
      });
    }
  };

  Drupal.behaviors.dexp_grid = {
    attach: function () {
      $('.views-view-dexp-grid').once('shuffle').each(function () {
        var grid = $(this).find('.dexp-grid-item').parent().addClass('dexp-grid-inner');
        var sizer = $(this).find('.dexp-grid-sizer');
        $(grid).shuffle({
          itemSelector: '.dexp-grid-item',
          sizer: sizer,
          speed: 500
        });
        $(document).ajaxSuccess(function() {
          grid.data('shuffle').appended(grid.find('.dexp-grid-item').not('.filtered'));
          grid.shuffle('update');
        });
      });
      resizeEvent();
    }
  };
})(jQuery, Drupal, drupalSettings);