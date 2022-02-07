(function ($, Drupal) {
    "use strict";
    Drupal.behaviors.dexp_shortcodes_progress_circle = {
        attach: function () {
            $('.dexp-progress-circle').once('shortcode').each(function () {                
                var $this = $(this);
                if (typeof $.fn.appear === 'function') {
                    $this.appear(function () {
                        $this.dexpProgressCirlce();
                        $this.unbind('appear');
                    }, {
                        accX: 0,
                        accY: 0,
                        one: true
                    });
                } else {
                    $this.dexpProgressCirlce();
                }
            });
        }
    };

    $.fn.dexpProgressCirlce = function () {
        return this.each(function () {
            var $this = $(this),
                percent = $this.data('percent'),
                radius = $this.data('radius'),
                bar = $this.data('bar'),
                color = $this.data('color'),
                duration = $this.data('duration'),
                start = 0,
                size = radius * 2,
                is_50 = 1;
            $($this).find('.dexp-progress-color').css('display','block');
            var i = setInterval(function () {
                if (start <= percent) {
                    var deg = parseInt(start) * 3.6;
                    if (start > 50 && is_50) {
                        $this.css('border-color', color);
                        $this.find('.dexp-progress-bar').css('clip', 'rect(0,' + radius + 'px,' + size +'px,0)');
                        $this.find('.dexp-progress-color').css('clip', 'rect(0,' + size + 'px,' + size +'px,' + radius +'px)');
                        $this.find('.dexp-progress-color').css('border-color', bar);
                        is_50 = 0;
                    }
                    $this.find('.dexp-progress-color').css('transform', 'rotate(' + deg + 'deg)');
                    $this.find('.dexp-progress-content span').html(start + '%');
                    start++;
                } else {
                    clearInterval(i);
                }
            }, duration);
        });
    };
})(jQuery, Drupal);
