<?php

namespace Drupal\dexp_builder\Plugin\Shortcode;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\Language;

/**
 * Provides a shortcode gmap marker icon.
 *
 * @Shortcode(
 *   id = "dexp_builder_gmap_marker",
 *   title = @Translation("Google Map Marker"),
 *   description = @Translation("Builds Google map marker element"),
 *   group = @Translation("Content"),
 *   parent = {"dexp_builder_gmap"},
 *   child = {}
 * )
 */
class BuilderGmapMarker extends BuilderElement {

  public function process(array $attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs = $this->getAttributes(array(
      'title' => '',
      'address' => '',
      'lng' => '',
      'lat' => '',
      'icon' => '',
      'class' => '',
      'content' => '',
      'info_box' =>false,
      ), $attributes
    );
    global $builder_gmap_stack;
    if($attrs['icon']){
      $fid = str_replace('file:', '', $attrs['icon']);
      $file = \Drupal\file\Entity\File::load($fid);
      $attrs['icon'] = file_create_url($file->getFileUri());
    }
    if (empty($builder_gmap_stack)) {
      $builder_gmap_stack = array();
    }
    $attrs['content'] = $text;
    $builder_gmap_stack[] = $attrs;
    return '';
  }
  
  public function processBuilder($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs = $this->getAttributes(array(
      'title' => '',
      'address' => '',
      'image' => '',
      'class' => '',    
      ), $attributes
    );
    $output = [
      '#markup' => '<span class="fa fa-map-marker"></span> ' . $attrs['title'] . ': ' . $attrs['address'],
      '#attached' => ['library' => ['dexp_builder/gmap-api']],
    ];
    return $this->render($output);
  }
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);

    $form['general_options']['title'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => $this->get('title', ''),
      '#required' => true,
      '#description' => $this->t('Enter your desired text to use as the marker title.')
    );
    $form['general_options']['address'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Address'),
      '#default_value' => $this->get('address', ''),
      '#required' => true,
      '#description' => $this->t('Type an address or name of a place. You can enter for all kinds of things, like post offices, bus stops, or street names, using Google Maps.'),
    );
    $form['general_options']['lat'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Longitude'),
      '#default_value' => $this->get('lat', ''),
      '#required' => true,
      '#size' => 20,
      '#description' => $this->t('The GPS Longitude coordinates of a point on Google maps, get your <a href="https://www.maps.ie/coordinates.html" target="_blank">here</a>.'),
    );
    $form['general_options']['lng'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Latitude'),
      '#default_value' => $this->get('lng', ''),
      '#required' => true,
      '#size' => 20,
      '#description' => $this->t('The GPS Latitude coordinates of a point on Google maps, get your <a href="https://www.maps.ie/coordinates.html" target="_blank">here</a>.'),
    );
    /*
    $form['general_options']['find_ln_lg'] = array(
      '#type' => 'button',
      '#value' => 'Find longitude, latitude from address',
      '#id' => 'dexp-builder-gmap-marker-find-ln-lg',
    );
    $form['general_options']['gmap_preview'] = array(
      '#markup' => '<div id="dexp-builder-gmap-preview"></div>',
    );*/
    $form['general_options']['icon'] = array(
      '#type' => 'image_browser',
      '#title' => $this->t('Marker Icon'),
      '#default_value' => $this->get('icon'),
      '#description' => $this->t('Select an image icon from your Drupal library directory or upload a picture.'),
    );
    $form['general_options']['info_box'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Info Windows?'),
      '#default_value' => $this->get('info_box', false),
      '#description' => $this->t('An InfoWindow displays content (usually text or images) in a popup window above the map, at a given location.')
    );
    $form['general_options']['html_content'] = array(
      '#type' => 'text_format',
      '#title' => $this->t('Add an info window'),
      '#format' => 'full_html',
      '#description' => $this->t('By default, the info box will show the title and address, you can build your own info box content by using this field.'),
      '#default_value' => $this->get('html_content'),
      '#states' => [
        'visible' => [
          ':input[name=info_box]' => ['checked' => TRUE],
        ]
      ]
    );
    $form['general_options']['class'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Custom class'),
      '#description' => $this->t('Adding a custom class allows you to target the marker easily within your custom codes.'),
      '#default_value' => $this->get('class', ''),
    );
    $form['#attached']['library'][] = 'dexp_builder/gmap-admin';
    unset($form['animate_options']);
    unset($form['design_options']);
    return $form;
  }

}
