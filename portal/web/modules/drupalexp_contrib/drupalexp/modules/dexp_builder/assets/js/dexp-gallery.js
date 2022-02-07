(function($, Drupal){
    Drupal.behaviors.dexp_builder_gallery = {
        attach: function(){
            $('.dexp-builder-gallery').once('gallery').each(function(){
                var $this = $(this),
                    id = $this.attr('id'),
                    transition = $this.data('transition'),
                    slideshow = $this.data('slideshow'),
                    width = $this.data('width'),
                    height = $this.data('height');
                $this.find('a').once('item').each(function(){
                    $(this).attr('rel',id);
                });
                $this.find('a').colorbox({
                    rel: id,
                    width: width,
                    height: height,
                    slideshow: slideshow,
                    transition: transition,
                });
            });
        }
    };
})(jQuery, Drupal);