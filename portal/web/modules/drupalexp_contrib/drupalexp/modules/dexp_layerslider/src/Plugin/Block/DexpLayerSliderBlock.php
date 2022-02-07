<?php

namespace Drupal\dexp_layerslider\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\dexp_layerslider\Entity\Slider;

/**
 * Provides a 'LayerSlider' Block
 *
 * @Block(
 *   id = "dexp_layerslider",
 *   admin_label = @Translation("Dexp LayerSlider"),
 *   category = @Translation("Dexp LayerSlider"),
 *   deriver = "Drupal\dexp_layerslider\Plugin\Derivative\DexpLayerSliderBlock",
 * )
 */
class DexpLayerSliderBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $slider_id = $this->getDerivativeId();
    $slider = $slider = Slider::load($slider_id);
    $html_id = \Drupal\Component\Utility\Html::getUniqueId('dexp_layerslider_' . REQUEST_TIME);
    return [
      '#theme' => 'dexp_layerslider_slider',
      '#slider' => $slider,
      '#html_id' => $html_id,
      '#cache' => [
        'max-age' => 0,
      ],
      '#attached' => [
        'library' => ['dexp_layerslider/frontend'],
        'drupalSettings' => ['dexp_layerslider_settings' => [$html_id => $slider->getSettings()],],
      ],
      '#contextual_links' => [
        'dexp_layerslider' => [
          'route_parameters' => ['dexp_slider' => $slider_id]
        ],
      ]
    ];
  }

}
