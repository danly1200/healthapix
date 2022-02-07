(function($, Drupal) {
  Drupal.behaviors.dexp_block_settings_animate_admin = {
    attach: function() {
      $('#edit-dexp-block-settings-animate-settings-animate').once(
        'update').each(function() {
        $(this).on('change', function() {
          $('#animate-sample div').attr('data-aos', '');
          $('#animate-sample div').attr('data-aos', $(this).val())
            .attr('class', '');
          setTimeout(function() {
            $('#animate-sample div').addClass('aos-animate');
          }, 800);
        })
      });
    }
  }
})(jQuery, Drupal);
