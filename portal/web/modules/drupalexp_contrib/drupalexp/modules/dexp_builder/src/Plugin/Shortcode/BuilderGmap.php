<?php

namespace Drupal\dexp_builder\Plugin\Shortcode;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\Language;

/**
 * Provides a shortcode for gmap.
 *
 * @Shortcode(
 *   id = "dexp_builder_gmap",
 *   title = @Translation("Google Map"),
 *   description = @Translation("Builds Google map element"),
 *   group = @Translation("Content"),
 *   child = {"dexp_builder_gmap_marker"},
 * )
 */
class BuilderGmap extends BuilderElement {

  public function process(array $attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    global $builder_gmap_stack;
    $attrs = $this->getAttributes(array(
      'width' => 0,
      'height' => 0,
      'style' =>'',
      'custom_style' => '',
      'zoom' => '',
      ), $attributes
    );
    if($attrs['width'] == 0){
      $attrs['width'] = '100%';
    }
    if($attrs['height'] == 0){
      $attrs['height'] = '100%';
    }
    $output = [
      '#theme' => 'dexp_builder_gmap',
      '#width' => $attrs['width'],
      '#height' => $attrs['height'],
      '#zoom' => $attrs['zoom'],
      '#markers' => $builder_gmap_stack,
      '#custom_style' => $attrs['custom_style'],
      '#style' => $attrs['style'],
      '#attached' => array(
        'library' => array(
          'dexp_builder/gmap'
        ),
      )
    ];
    $builder_gmap_stack = array();
    return $this->render($output);
  }
  
  public function processBuilder($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    parent::process($attributes, $text, $langcode);

    $output = [
      '#markup' => $text,
      '#attached' => ['library' => ['dexp_builder/gmap-api']],
    ];
    return $this->render($output);
  }

  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    
    $form['general_options']['width'] = array(
      '#type' => 'number',
      '#min' => 0,
      '#default_value' => $this->get('width', 0),
      '#title' => $this->t('Width'),
      '#description' => $this->t('Width of the map, in pixels. Default 0: width of the containing element.'),
    );
    $form['general_options']['height'] = array(
        '#type' => 'number',
        '#min' => 0,
        '#max' => 1200,
        '#default_value' => $this->get('height', 0),
        '#title' => $this->t('Height'),
        '#description' => $this->t('Height of the map, in pixels. Default 0: height of the containing element.'),
    );
    $form['general_options']['zoom'] = array(
      '#type' => 'number',
      '#min' => 0,
      '#max' => 25,
      '#title' => $this->t('Zoom Level'),
      '#default_value' => $this->get('zoom', 14),
      '#description' => $this->t('The initial resolution at which to display the map zoom, larger zoom levels in at a higher resolution.'),
    );
    $form['general_options']['style'] = array(
      '#type' => 'select',
      '#title' => $this->t('Style'),
      '#options' => array('standard' => $this->t('Standard'), 'color' => $this->t('Base Color'), 'custom' => $this->t('Customize')),
      '#default_value' => $this->get('style', 'standard'),
      '#description' => $this->t('Select style from the list. Standard is base style from Google Map; Base Color is base style from your theme; Customize will open a box option to put your customize style.'),
    );
    $form['general_options']['custom_style'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Customize style'),
      '#description' => $this->t('Changing the visual display of such elements as roads, parks, and built-up areas <a href="https://mapstyle.withgoogle.com/" target="_blank">here</a>, then paste json code to the box above'),
      '#default_value' => $this->get('custom_style'),
      '#states' => array(
        'visible' => array(
          ':input[name=style]' => array('value' => 'custom'),
        ),
      ),
    );
    $form['general_options']['class'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Custom class'),
      '#description' => $this->t('Adding a custom class allows you to target the map easily within your custom codes.'),
      '#default_value' => $this->get('class', ''),
    );
    unset($form['animate_options']);
    unset($form['design_options']);
    return $form;
  }

}
