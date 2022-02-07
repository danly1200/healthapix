<?php

/**
 * @file
 * Contains \Drupal\dexp_layerslider\Plugin\Derivative\DexpLayerSliderBlock.
 */

namespace Drupal\dexp_layerslider\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;

/**
 * Provides blocks which belong to MD Slider.
 */
class DexpLayerSliderBlock extends DeriverBase {

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $select = db_select('dexp_slider', 'slider');
    $select->fields('slider');
    $sliders = $select->execute()->fetchAll();
    
    foreach ($sliders as $slide) {
      $this->derivatives[$slide->id] = $base_plugin_definition;
      $this->derivatives[$slide->id]['admin_label'] = $slide->name;
    }
    
    return $this->derivatives;
  }
}