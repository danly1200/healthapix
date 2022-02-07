(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.dexp_builder = {
    attach: function (context, settings) {
      $('.dexp-builder-lightbox').once('colorbox').each(function () {
        var colorboxSettings = $.extend({}, settings.colorbox);
        if ($(this).hasClass('iframe')) {
          colorboxSettings.iframe = true;
          colorboxSettings.innerWidth = colorboxSettings.initialWidth;
          colorboxSettings.innerHeight = colorboxSettings.initialHeight;
        }
        if ($(this).hasClass('twitter-embed')) {
          var $this = $(this);
          var post_id = $(this).attr('href').substring($(this).attr('href').lastIndexOf('/') + 1);
          jQuery.ajax({
            url: 'https://publish.twitter.com/oembed?url=https://twitter.com/Interior/status/' + post_id,
            dataType: 'jsonp',
            success: function (response) {
              console.log(response);
              colorboxSettings.iframe = false;
              colorboxSettings.innerWidth = 520;
              colorboxSettings.innerHeight = '80%';
              colorboxSettings.html = response.html;
              $this.colorbox(colorboxSettings);
            }
          });
        } else if ($(this).hasClass('instagram-embed')) {
          var $this = $(this);
          var post_id = $(this).attr('href').substring($(this).attr('href').lastIndexOf('/') + 1);
          jQuery.ajax({
            url: 'https://api.instagram.com/oembed/?url=' + $this.attr('href'),
            dataType: 'jsonp',
            success: function (response) {
              if(typeof response.thumbnail_url !== 'undefined'){
                $this.css({
                  backgroundImage: 'url('+response.thumbnail_url+')',
                  backgroundSize: 'cover'
                });
                $this.find('img').css({opacity:0});
              }
              colorboxSettings.iframe = false;
              colorboxSettings.innerHeight = '80%';
              var html_id = uniqueHtmlID();
              var el = $('<div>').attr('id', html_id);
              el.html(response.html);
              $('body').append(el);
              el.wrap('<div style="display:none">');
              colorboxSettings.inline = true;
              $this.attr('href', '#' + html_id);
              $this.colorbox(colorboxSettings);
            }
          });
        } else {
          var match = $(this).attr('href').match(/^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/);
          var youtube_id = (match&&match[7].length==11)? match[7] : null;
          if(youtube_id != null){
            var thumnbnail = 'https://img.youtube.com/vi/' + youtube_id + '/maxresdefault.jpg';
            $(this).css({
              backgroundImage: 'url(' + thumnbnail + ')',
              backgroundSize: 'cover'
            });
            $(this).find('img').css({opacity:0});
          }
          //if($(this).)
          $(this).colorbox(colorboxSettings);
        }
      });
    }
  };
  function uniqueHtmlID() {
    return 'ins-' + Math.floor(Math.random() * 26) + Date.now();
  };
})(jQuery, Drupal, drupalSettings);