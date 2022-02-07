<?php
/**
 * @file
 * Contains \Drupal\dexp_bxslider\Plugin\Field\FieldFormatter\bxSliderFormatter.
 */
namespace Drupal\dexp\Plugin\Field\FieldFormatter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\image\Plugin\Field\FieldFormatter\ImageFormatter;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'Bootstrap Carousel' formatter.
 *
 * @FieldFormatter(
 *   id = "dexp_bootstrap_carousel_format",
 *   label = @Translation("Bootstrap Carousel"),
 *   field_types = {
 *     "image"
 *   }
 * )
 */
class BootstrapCarouselFotmatter extends ImageFormatter{
  
  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = array();
    
    $summary[] = t('Displays image as bootstrap carousel format.');

    return $summary;
  }
  
  public static function defaultSettings() {
		return array(
			'link_class' => '',
      'bootstrap_carousel_settings' => [
        'dots' => 0,
        'controls' => 1,
        'next_text' => '<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>',
        'prev_text' => '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>',
        'interval' => 5000,
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
    $element['bootstrap_carousel_settings'] = [
      '#type' => 'details',
      '#title' => $this->t('Carousel Settings'),
    ];
    $element['bootstrap_carousel_settings']['interval'] = [
      '#type' => 'number',
      '#min' => 100,
      '#field_suffix' => 'ms',
      '#title' => $this->t('Interval'),
      '#description' => $this->t('The amount of time to delay between automatically cycling an item. If false, carousel will not automatically cycle.'),
      '#default_value' => $this->getSetting('bootstrap_carousel_settings')['interval'],
    ];
    $element['bootstrap_carousel_settings']['controls'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show Next/Prev Controls'),
      '#default_value' => $this->getSetting('bootstrap_carousel_settings')['controls'],
    ];
    $element['bootstrap_carousel_settings']['next_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Next Text'),
      '#default_value' => isset($this->getSetting('bootstrap_carousel_settings')['next_text'])?$this->getSetting('bootstrap_carousel_settings')['next_text']:'',
      '#states' => [
        'visible' => [
          ':input[name*=controls]' => ['checked' => true],
        ]
      ],
    ];
    $element['bootstrap_carousel_settings']['prev_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Prev Text'),
      '#default_value' => isset($this->getSetting('bootstrap_carousel_settings')['prev_text'])?$this->getSetting('bootstrap_carousel_settings')['prev_text']:'',
      '#states' => [
        'visible' => [
          ':input[name*=controls]' => ['checked' => true],
        ]
      ],
    ];
    $element['bootstrap_carousel_settings']['dots'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show pager'),
      '#default_value' => $this->getSetting('bootstrap_carousel_settings')['dots'],
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
      '#theme' => 'dexp_bootstrap_carousel',
      '#items' => $render,
      '#settings' => $this->getSettings(),
      //'#attached' => ['library' => ['dexp_slick/dexp_slick']],
    );
  }
}
