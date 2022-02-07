(function($, Drupal){
  Drupal.behaviors.dexp_onepage_menu = {
    attach: function(){
      $('ul.dexp-onepage-menu').once('one-page').each(function(){
        var $this = $(this);
        $this.onePageNav({
          changeHash: true,
          currentClass: 'menu-item--active-trail',
          offsetTop: function(){ 
            return $this.data('offset');
          }
        });
      });
    }
  };
})(jQuery, Drupal);