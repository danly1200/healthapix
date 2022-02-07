(function ($) {
    "use strict";
    Drupal.behaviors.dexp_shortcodes_piegraph = {
        attach: function () {
            $('.dexp-pie-chart').once('shortcode').each(function () {
                var $this = $(this);
                if (typeof $.fn.appear === 'function') {
                    $this.appear(function () {
                        $this.dexpPieChart();
                        $this.unbind('appear');
                    }, {
                        accX: 0,
                        accY: 0,
                        one: true
                    });
                } else {
                    $('.dexp-pie-chart').dexpPieChart();
                }
            });
        }
    };

    $.fn.dexpPieChart = function () {
        return this.each(function () {
            var $this = $(this),
                percent = $this.data('percent'),
                icon = $this.data('pieicon'),
                icon_style = $this.data('iconstyle'),                
                start = 0;
            $this.append('<div class="ppc-progress"><div class="ppc-progress-fill"></div></div><div class="ppc-percents"><div class="pcc-percents-wrapper"><i></i><span>%</span></div></div>');
            if (icon !== null && icon !== "") {
                $this.find('.ppc-percents i').addClass(icon).attr('style', icon_style);
            }
            var i = setInterval(function () {
                if (start <= percent) {
                    var deg = parseInt(start) * 3.6;
                    if (start > 50) {
                        $this.addClass('gt-50');
                    }
                    $this.find('.ppc-progress-fill').css('transform', 'rotate(' + deg + 'deg)');
                    $this.find('.ppc-percents span').html(start + '%');
                    start++;
                } else {
                    clearInterval(i);
                }
            }, 20);
        });
    };
})(jQuery, Drupal);