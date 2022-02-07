(function($, Drupal, settings){
  Drupal.behaviors.dexp_grid_admin = {
    attach: function(context, settings){
      $('.dexp-grid-masonry-resize').once('resize').each(function(){
        var grid = $(this).find('.dexp-grid-inner');
        var uuid = $(this).data('uuid');
        var shuffle = grid.data('shuffle');
        var view = $(this).attr('id');
        $(grid).find('.dexp-grid-item').resizable({
          start: function(){
            settings.dexp_grid.resize = false;
          },
          resize: function () {
            grid.width(grid.width()+12);
            grid.shuffle('update');
            grid.width('auto');
            settings.dexp_grid.resize = true;
          },
          stop: function(event, ui){
            $('#dexp-grid-message').css({display:'block'});
            var w = Math.round(ui.size.width / settings.dexp_grid[uuid].itemWidth);
            var h = Math.round(ui.size.height / settings.dexp_grid[uuid].itemHeight);
            $(this).data({gridWidth:w, gridHeight: h});
            Drupal.ajax({
              url: Drupal.url('dexp-grid/update'),
              progress: {
                type: "throbber"
              },
              submit: {
                view: settings.dexp_grid[uuid].view,
                display_id: settings.dexp_grid[uuid].display_id,
                index: $(this).data('index'),
                width: w,
                height: h
              }
            }).execute();
          }
        });
        $(grid).on('layout.shuffle', function(){
          $(grid).find('.dexp-grid-item').resizable('option',{
            grid: [ settings.dexp_grid[uuid].itemWidth + settings.dexp_grid[uuid].margin, settings.dexp_grid[uuid].itemHeight + settings.dexp_grid[uuid].margin ],
            maxWidth: grid.width() + 10
          });
        });
      });
    }
  };
})(jQuery, Drupal, drupalSettings);