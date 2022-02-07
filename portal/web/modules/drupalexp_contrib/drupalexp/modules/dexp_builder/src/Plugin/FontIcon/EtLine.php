<?php

namespace Drupal\dexp_builder\Plugin\FontIcon;

use Drupal\dexp_builder\Plugin\FontIconBase;

/**
 * The fontawesome
 * 
 * @FontIcon(
 *    id = "etline",
 *    title = "ET Line"
 * )
 */
class EtLine extends FontIconBase {

  public function icons() {
    return array(
      ['class' => 'icon-mobile'], ['class' => 'icon-laptop'], ['class' => 'icon-desktop'], ['class' => 'icon-tablet'], ['class' => 'icon-phone'], ['class' => 'icon-document'], ['class' => 'icon-documents'], ['class' => 'icon-search'], ['class' => 'icon-clipboard'], ['class' => 'icon-newspaper'], ['class' => 'icon-notebook'], ['class' => 'icon-book-open'], ['class' => 'icon-browser'], ['class' => 'icon-calendar'], ['class' => 'icon-presentation'], ['class' => 'icon-picture'], ['class' => 'icon-pictures'], ['class' => 'icon-video'], ['class' => 'icon-camera'], ['class' => 'icon-printer'], ['class' => 'icon-toolbox'], ['class' => 'icon-briefcase'], ['class' => 'icon-wallet'], ['class' => 'icon-gift'], ['class' => 'icon-bargraph'], ['class' => 'icon-grid'], ['class' => 'icon-expand'], ['class' => 'icon-focus'], ['class' => 'icon-edit'], ['class' => 'icon-adjustments'], ['class' => 'icon-ribbon'], ['class' => 'icon-hourglass'], ['class' => 'icon-lock'], ['class' => 'icon-megaphone'], ['class' => 'icon-shield'], ['class' => 'icon-trophy'], ['class' => 'icon-flag'], ['class' => 'icon-map'], ['class' => 'icon-puzzle'], ['class' => 'icon-basket'], ['class' => 'icon-envelope'], ['class' => 'icon-streetsign'], ['class' => 'icon-telescope'], ['class' => 'icon-gears'], ['class' => 'icon-key'], ['class' => 'icon-paperclip'], ['class' => 'icon-attachment'], ['class' => 'icon-pricetags'], ['class' => 'icon-lightbulb'], ['class' => 'icon-layers'], ['class' => 'icon-pencil'], ['class' => 'icon-tools'], ['class' => 'icon-tools-2'], ['class' => 'icon-scissors'], ['class' => 'icon-paintbrush'], ['class' => 'icon-magnifying-glass'], ['class' => 'icon-circle-compass'], ['class' => 'icon-linegraph'], ['class' => 'icon-mic'], ['class' => 'icon-strategy'], ['class' => 'icon-beaker'], ['class' => 'icon-caution'], ['class' => 'icon-recycle'], ['class' => 'icon-anchor'], ['class' => 'icon-profile-male'], ['class' => 'icon-profile-female'], ['class' => 'icon-bike'], ['class' => 'icon-wine'], ['class' => 'icon-hotairballoon'], ['class' => 'icon-globe'], ['class' => 'icon-genius'], ['class' => 'icon-map-pin'], ['class' => 'icon-dial'], ['class' => 'icon-chat'], ['class' => 'icon-heart'], ['class' => 'icon-cloud'], ['class' => 'icon-upload'], ['class' => 'icon-download'], ['class' => 'icon-target'], ['class' => 'icon-hazardous'], ['class' => 'icon-piechart'], ['class' => 'icon-speedometer'], ['class' => 'icon-global'], ['class' => 'icon-compass'], ['class' => 'icon-lifesaver'], ['class' => 'icon-clock'], ['class' => 'icon-aperture'], ['class' => 'icon-quote'], ['class' => 'icon-scope'], ['class' => 'icon-alarmclock'], ['class' => 'icon-refresh'], ['class' => 'icon-happy'], ['class' => 'icon-sad'], ['class' => 'icon-facebook'], ['class' => 'icon-twitter'], ['class' => 'icon-googleplus'], ['class' => 'icon-rss'], ['class' => 'icon-tumblr'], ['class' => 'icon-linkedin'], ['class' => 'icon-dribbble'], 
    );
  }
  
  public function library(){
    return 'dexp_builder/etline';
  }
}
