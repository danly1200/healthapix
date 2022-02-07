<?php

/**
 * @file
 * Contains \Drupal\dexp_layerslider\Entity\Slider.
 */

namespace Drupal\dexp_layerslider\Entity;

use Drupal\views\EntityViewsData;
use Drupal\views\EntityViewsDataInterface;

/**
 * Provides Views data for Slider entities.
 */
class SliderViewsData extends EntityViewsData implements EntityViewsDataInterface {
  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['dexp_slider']['table']['base'] = array(
      'field' => 'id',
      'title' => $this->t('Slider'),
      'help' => $this->t('The Slider ID.'),
    );
    return $data;
  }

}
