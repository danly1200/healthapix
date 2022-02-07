<?php
/**
 * @file
 * Contains \Drupal\dexp_bxslider\Plugin\Field\FieldFormatter\bxSliderFormatter.
 */
namespace Drupal\dexp_slick\Plugin\Field\FieldFormatter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\image\Plugin\Field\FieldFormatter\ImageFormatter;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'Slick Carousel' formatter.
 *
 * @FieldFormatter(
 *   id = "dexp_slick_format",
 *   label = @Translation("Slick Carousel"),
 *   field_types = {
 *     "image"
 *   }
 * )
 */
class SlickCarouselFormatter extends ImageFormatter{
  
  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = array();
    $settings = $this->getSettings();

    $summary[] = t('Displays image as slick carousel format.');

    return $summary;
  }
  
  public static function defaultSettings() {
		return array(
			'link_class' => '',
      'slick_settings' => [
        'dots' => 0,
        'arrows' => 1,
        'autoplay' => 1,
        'autoplaySpeed' => 500,
      ],
		) + parent::defaultSettings();
	}
  
  public function settingsForm(array $form, FormStateInterface $form_state){
    $element = parent::settingsForm($form, $form_state);
    $element['link_class'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Link Class(es)'),
      '#default_value' => $this->getSetting('link_class'),
      '#description' => $this->t('Custom link class. Example: colorbox'),
      '#states' => [
        'invisible' => [
          ':input[name*=\\[image_link\\]]' => ['value' => '']
        ]
      ],
    ];
    $element['slick_settings'] = [
      '#type' => 'details',
      '#title' => $this->t('Slick Carousel Settings'),
    ];
    $element['slick_settings']['autoplay'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Auto Play'),
      '#default_value' => $this->getSetting('slick_settings')['autoplay'],
    ];
    $element['slick_settings']['autoplaySpeed'] = [
      '#type' => 'number',
      '#min' => 100,
      '#field_suffix' => 'ms',
      '#title' => $this->t('Auto Play Speed'),
      '#default_value' => $this->getSetting('slick_settings')['autoplaySpeed'],
      '#states' => [
        'visible' => [
          ':input[name*=\\[autoplay\\]]' => ['checked' => TRUE]
        ]
      ],
    ];
    $element['slick_settings']['arrows'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show Next/Prev Controls'),
      '#default_value' => $this->getSetting('slick_settings')['arrows'],
    ];
    $element['slick_settings']['dots'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show pager'),
      '#default_value' => $this->getSetting('slick_settings')['dots'],
    ];
    return $element;
  }
  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $render = parent::viewElements($items, $langcode);
    //$files = $this->getEntitiesToView($items, $langcode);
    if($link_class = $this->getSetting('link_class')){
      foreach($render as &$item){
        $item['#item_attributes']['class'][] = $link_class;
      }
    }
    return array(
      '#theme' => 'dexp_slick_image',
      '#items' => $render,
      '#settings' => $this->getSettings(),
      '#attached' => ['library' => ['dexp_slick/dexp_slick']],
    );
  }
}
