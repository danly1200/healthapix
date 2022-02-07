(function ($, Drupal, drupalSettings) {
	Drupal.behaviors.dexp_block_settings_video_background = {
    attach: function (context, settings) {
			$('.dexp-video-background').once('YTPlayer').each(function(){
        var $this = $(this);
        var property = eval('(' + $this.data('property') + ')');
        var myPlayer = $(this).YTPlayer();
        myPlayer.on('YTPReady', function(){
          if(property.mute){
            $this.find('.buttonBar').addClass('YTPMuted');
          }else{
            $this.find('.buttonBar').addClass('YTPUnmuted');
          }
        });
        if(property.showControls){
          myPlayer.on('YTPPlay YTPPause', function(e){
            $this.find('.buttonBar').removeClass('YTPPlay YTPPause').addClass(e.type);
          });
          myPlayer.on('YTPMuted YTPUnmuted', function(e){
            $this.find('.buttonBar').removeClass('YTPMuted YTPUnmuted').addClass(e.type);
          });
        }
			});
		}
	};
})(jQuery, Drupal, drupalSettings);