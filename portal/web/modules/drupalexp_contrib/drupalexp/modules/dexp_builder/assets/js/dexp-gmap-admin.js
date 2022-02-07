(function ($, Drupal, DrupalSettings) {
  "use strict";
  var map = null;
  var marker = null;
  Drupal.behaviors.dexp_builder_gmap_admin = {
    attach: function (context, settings) {
      $('input#dexp-builder-gmap-marker-find-ln-lg').once('click').each(function(){
        $(this).on('click', function(e){
          e.preventDefault();
          $('#dexp-builder-gmap-preview').css({height:300, marginTop: '10px'});
          map = new google.maps.Map(document.getElementById('dexp-builder-gmap-preview'), {
            scrollwheel: false,
            zoom: 14
          });
          google.maps.event.addListener(map, 'click', function(event) {
            if(marker != null){
              marker.setMap(null);
            }
            marker = new google.maps.Marker({
              position: event.latLng, 
              map: map
            });
            $('input[name=lat]').val(event.latLng.lat());
            $('input[name=lng]').val(event.latLng.lng());
          });
          var geocoder = new google.maps.Geocoder();
          var address = $('input[name=address]').val();
          geocoder.geocode({
            address: address
          }, function(response, status){
            if (status === google.maps.GeocoderStatus.OK) {
              marker = new google.maps.Marker({
                position: new google.maps.LatLng(response[0].geometry.location.lat(), response[0].geometry.location.lng()),
                map: map,
                animation: google.maps.Animation.DROP
              });
              $('input[name=lat]').val(response[0].geometry.location.lat());
              $('input[name=lng]').val(response[0].geometry.location.lng());
              map.setCenter(response[0].geometry.location);
            }else{
              alert('Address not found');
            }
          });
        });
      });
    }
  };
})(jQuery, Drupal, drupalSettings);