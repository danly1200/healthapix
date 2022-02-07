(function ($, Drupal) {
  'use strict';
  Drupal.behaviors.dexp_builder_skillbar = {
        attach: function () {
            $('.dexp-builder-progress-bar').once('shortcode').each(function () {                
                var $this = $(this);
                if (typeof $.fn.appear === 'function') {
                    $this.appear(function () {
                        $this.dexpProgressBar();
                        $this.unbind('appear');
                    }, {
                        accX: 0,
                        accY: 0,
                        one: true
                    });
                } else {
                    $this.dexpProgressBar();
                }
            });
        }
    };
    $.fn.dexpProgressBar = function () {
        return this.each(function () {
            var $this = $(this),
                //duration = $this.data('duration'),
                percent = $this.data('percent'),
                start = 0;
                $this.find('.dexp-progress-bar').css('width', percent + '%');
            var i = setInterval(function () {
                if (start <= percent*2) {                    
                    if(start%2 == 0)
                        $this.find('.dexp-progress-percent').html(start/2 + '%');
                    start++;
                } else {
                    clearInterval(i);
                }
            }, 1);
        });
    };
})(jQuery, Drupal);