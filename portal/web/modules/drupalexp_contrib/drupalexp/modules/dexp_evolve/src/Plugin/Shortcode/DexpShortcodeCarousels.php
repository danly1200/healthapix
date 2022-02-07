<?php

namespace Drupal\dexp_evolve\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use \Drupal\Component\Utility\Html;
use Drupal\dexp_builder\Plugin\Shortcode\BuilderElement;
use Drupal\Core\Template\Attribute;

/**
 * Provides a shortcode for Carousel wrapper.
 *
 * @Shortcode(
 *   id = "dexp_shortcode_carousels",
 *   title = @Translation("Carousels"),
 *   description = @Translation("Carousel wrapper"),
 *   group = @Translation("Content"),
 *   child = {
 *     "dexp_shortcode_carousel"
 *   }
 * )
 */
class DexpShortcodeCarousels extends BuilderElement {

  public function process(array $attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    parent::process($attributes, $text, $langcode);

    $attrs = $this->getAttributes(array(
      'carousels' => '',
	  'control' => true,
	  'pager' => false,
	  'interval' => 5000,
	  'keyboard' => false,
	  'pause' => false,
	  'ride' => false,	  
      'class' => '',  
        ), $attributes
    );
    $carousel_wrapper_id = Html::getUniqueId('dexp_builder_carousel_wrapper_' . REQUEST_TIME);
    global $builder_carousels_stack;
	// $pause = 'null';
	// if($attrs['pause']==true){
		// $pause = 'hover';
	// }
	$ride='false';
	if($attrs['ride']==true){
		$ride = 'carousel';
	}
	$interval = 5000;
	if($attrs['interval']!=null && $attrs['interval']>0){
		$interval = $attrs['interval'];
	}
	$attribute = new Attribute();
	$attribute->addClass('dexp-carousels carousel slide');
    $attribute->addClass($attrs['class']);
	$attribute->setAttribute('data-ride', $ride);
	//$attribute->setAttribute('data-pause', $pause);
	$attribute->setAttribute('data-interval', $interval);
	//$attribute->setAttribute('data-keyboard', $attrs['keyboard']);
	
    $output = array(
      '#theme' => 'dexp_evolve_carousels',      
      '#carousels' => $builder_carousels_stack,
      '#wrapper_id' => $carousel_wrapper_id,
	  '#control' => $attrs['control'],
	  '#pager' => $attrs['pager'],
	  '#attributes' => $attribute,
	  //'#keyboard' => $attrs['keyboard'],
	  //'#pause' => $pause,
	  //'#ride' => $ride,	  
      //'#class' => $attrs['class'],
    );
    
    return $this->render($output);
  }

  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
	$form['general_options']['control'] = array(
        '#type' => 'checkbox',
        '#title' => $this->t('Display control'),
        '#default_value' => $this->get('control', true),
    );
	$form['general_options']['pager'] = array(
        '#type' => 'checkbox',
        '#title' => $this->t('Display pager'),
        '#default_value' => $this->get('pager'),
    );	
	// $form['general_options']['keyboard'] = array(
        // '#type' => 'checkbox',
        // '#title' => $this->t('Carousel keyboard'),
		// '#description' => $this->t('Whether the carousel should react to keyboard events.'),
        // '#default_value' => $this->get('keyboard'),
    // );
	// $form['general_options']['pause'] = array(
        // '#type' => 'checkbox',
        // '#title' => $this->t('Pause on hover'),
		// '#description' => $this->t('If checked, pauses the cycling of the carousel on mouseenter and resumes the cycling of the carousel on mouseleave. If set to null, hovering over the carousel will not pause it.'),
        // '#default_value' => $this->get('pause'),
    // );
	$form['general_options']['ride'] = array(
        '#type' => 'checkbox',
        '#title' => $this->t('Ride'),
		'#description' => $this->t('If checked, autoplays the carousel on load, else autoplays the carousel after the user manually cycles the first item.'),
        '#default_value' => $this->get('ride'),
    );
	$form['general_options']['interval'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Carousel interval'),
	  '#description' => $this->t('The amount of time to delay between automatically cycling an item'),
      '#default_value' => $this->get('interval', 5000),
    );
    $form['general_options']['class'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Custom class'),
      '#default_value' => $this->get('class'),
    );

    return $form;
  }

  public function processBuilder($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    return $text;
  }

}
