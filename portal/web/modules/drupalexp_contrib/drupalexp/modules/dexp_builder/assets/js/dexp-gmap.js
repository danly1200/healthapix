(function ($, Drupal, google, drupalSettings) {
  "use strict";
  //Create map marker
  var createMarker = function ($marker, map, infowindow) {
    var marker = new google.maps.Marker({
      position: new google.maps.LatLng($marker.data('lat'), $marker.data('lng')),
      map: map,
      icon: $marker.data('icon') || '',
      animation: google.maps.Animation.DROP,
      title: $(this).text()
    });
    marker.setMap(map);
    var $info = $('<div class="infobox" style="width:300px;"></div>');
    if ($marker.data('build-infobox') === 1 || false) {
      $info.append('<div class="infobox-wrapper">' + $marker.data('infobox') + '</div>');
    } else {
      if ($marker.data('title') || false) {
        $info.append('<h3 class="title"><a href="' + '#' + '">' + $marker.data('title') + '</a></h3>');
      }
      if ($marker.data('address') || false) {
        $info.append('<div class="address">' + $marker.data('address') + '</div>');
      }
    }
    google.maps.event.addListener(marker, 'click', (function (marker) {
      return function () {
        infowindow.setContent('<div class="infobox">' + $info.html() + '</div>');
        infowindow.open(map, marker);
      };
    })(marker));
    return marker;
  };
  Drupal.behaviors.dexp_builder_gmap = {
    attach: function () {
      $('.dexp-builder-gmap').once('process').each(function () {
        var $map = $(this),
          mapid = $map.attr('id'),
          $markers = $map.find('.dexp-builder-gmap-marker');
        var zoom = $map.data('zoom') || 14;
        var type = $map.data('map-style');
        var styles = '';
        if (type === 'color') {
          styles = [{
            "stylers": [{
              "hue": "#21C2F8"
            }, {
              "gamma": 1
            }]
          }];
          if (typeof drupalSettings.drupalexp !== 'undefined') {
            styles = [{
              "stylers": [{
                "hue": drupalSettings.drupalexp.base_color
              }, {
                "gamma": 1
              }]
            }];
          }
        }
        $map.height($map.data('width'));
        $map.height($map.data('height'));
        var map = null;
        var markers = [];
        var infowindow = new google.maps.InfoWindow();
        var mapOptions = {
          scrollwheel: false,
          zoom: zoom,
          styles: styles
        };
        var map_center = false;
        map = new google.maps.Map(document.getElementById(mapid), mapOptions);
        if ($map.data('custom-style') && type === 'custom') {
          var styledMapType = new google.maps.StyledMapType($map.data('custom-style'));
          map.mapTypes.set('styled_map', styledMapType);
          map.setMapTypeId('styled_map');
        }
        $markers.each(function () {
          var $this = $(this);
          if (!map_center) {
            map.setCenter(new google.maps.LatLng($this.data('lat'), $this.data('lng')));
            map_center = true;
          }
          markers.push(createMarker($this, map, infowindow));
          if (markers.length > 1) {
            var bounds = new google.maps.LatLngBounds();
            for (var i = 0; i < markers.length; i++) {
              bounds.extend(markers[i].getPosition());
            }
            map.fitBounds(bounds);
          }
        });
      });
    }
  };
})(jQuery, Drupal, google, drupalSettings);