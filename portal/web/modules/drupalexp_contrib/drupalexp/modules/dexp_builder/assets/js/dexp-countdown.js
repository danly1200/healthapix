(function($, Drupal){
    Drupal.behaviors.dexp_builder_countdown = {
        attach: function(){
            $('.dexp-builder-countdown').once('dexpcountdown').each(function(){
                var $this = $(this),
                    final_date = $this.data('final-date');
                $this.countdown(final_date)
                .on('update.countdown', function(event) {
                    var total = $this.data('total');
                    switch(total){
                        case 'hours':
                            $this.find('.clock-hour .value').html(event.offset.totalDays * 24 + event.offset.hours);
                            break;
                        case 'days':
                            $this.find('.clock-day .value').html(event.offset.totalDays);
                            $this.find('.clock-hour .value').html(event.strftime('%H'));
                            break;
                        case 'weeks':
                            $this.find('.clock-week .value').html(event.strftime('%-w'));
                            $this.find('.clock-day .value').html(event.strftime('%-d'));
                            $this.find('.clock-hour .value').html(event.strftime('%H'));
                            break;
                        default: //month
                            $this.find('.clock-month .value').html(event.strftime('%-m'));
                            $this.find('.clock-day .value').html(event.strftime('%-n'));
                            $this.find('.clock-hour .value').html(event.strftime('%H'));
                            break;
                    }
                    $this.find('.clock-minute .value').html(event.strftime('%M'));
                    $this.find('.clock-second .value').html(event.strftime('%S'));
                })
                .on('finish.countdown', function(event) {
                    $this.find('.dexp-countdown-clock').html($this.data('message')).addClass('clock-expired');
                });
            });
        }
    };
})(jQuery, Drupal);