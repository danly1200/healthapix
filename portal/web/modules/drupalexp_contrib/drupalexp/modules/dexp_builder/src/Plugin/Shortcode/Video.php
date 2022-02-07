<?php

namespace Drupal\dexp_builder\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\Core\Template\Attribute;

/**
 * Provides a shortcode for bootstrap row.
 *
 * @Shortcode(
 *   id = "dexp_builder_video",
 *   title = @Translation("Video Embed"),
 *   description = @Translation("Embed youtube and viemo video"),
 *   group = @Translation("Content"),
 *   child = {},
 * )
 */
class Video extends BuilderElement {

  public function process(array $attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    parent::process($attributes, $text, $langcode);
    $attrs = $this->getAttributes(array(
      'type' => 'youtube',
      'id' => '',
      'ratio' => '16by9',
      'autoplay' => 'no',
      'custom_class' => '',
    ),
      $attributes
    );
    $embed_url = "";
    $autoplay = $attrs['autoplay'];
    $ratio = $attrs['autoplay'] == '4by3'? '4by3' : '16by9';
    if($attributes['type'] == 'vimeo'){
      $embed_url = "http://player.vimeo.com/video/{$attributes['id']}?autoplay={$autoplay}&rel=0";
    }else{
      $embed_url = "https://www.youtube.com/embed/{$attributes['id']}?autoplay={$autoplay}&rel=0&html5=1";
    }
    return '<div class="embed-responsive embed-responsive-' . $ratio . ' ' . $attrs['custom_class'] .'"><iframe class="embed-responsive-item" src="' . $embed_url . '"></iframe></div>';
  }
  
  public function processBuilder($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED){
    $attrs = $this->getAttributes(array(
      'type' => 'youtube',
      'id' => '',
      'ratio' => '16by9',
      'autoplay' => 'no',
      'custom_class' => '',
    ),
      $attributes
    );
    
    return '[' . $attrs['type'] . ':' . $attrs['id'] . ']';
  }
      
  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    $form['general_options']['type'] = array(
      '#type' => 'select',
      '#title' => $this->t('Video source'),
      '#options' => ['youtube' => 'Youtube', 'vimeo' => 'Vimeo'],
      '#default_value' => $this->get('type', 'youtube'),
      '#description' => $this->t('Select type of video source from the list.'),
    );
    
    $form['general_options']['id'] = array(
      '#type' => 'textfield',
      '#size' => 20,
      '#required' => true,
      '#title' => $this->t('Video ID'),
      '#default_value' => $this->get('id', ''),
      '#description' => 'Enter your video id, https://www.youtube.com/watch?v=<strong>video_id</strong>, https://vimeo.com/<strong>video_id</strong>',
    );
    
    $form['general_options']['ratio'] = array(
      '#type' => 'select',
      '#title' => $this->t('Video ratio'),
      '#options' => ['4by3' => '4:3', '16by9' => '16:9'],
      '#default_value' => $this->get('ratio', '16by9'),
      '#description' => $this->t('Select video ratio to use different video aspect ratios based on the platform and video format.'),
    );
    
    $form['general_options']['autoplay'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Autoplay'),
      '#default_value' => $this->get('autoplay', 1),
      '#description' => $this->t('If checked, the video will autoplay.'),
    );
    
    $form['general_options']['custom_class'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Custom class'),
      '#description' => $this->t('Adding a custom class allows you to target the video easily within your custom codes.'),
      '#default_value' => $this->get('custom_class', ''),
    );
    unset($form['design_options']);
    unset($form['animate_options']);
    return $form;
  }

}
