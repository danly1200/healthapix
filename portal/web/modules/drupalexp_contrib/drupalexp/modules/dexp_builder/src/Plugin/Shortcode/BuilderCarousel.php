<?php

namespace Drupal\dexp_builder\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use \Drupal\Component\Utility\Html;

/**
 * Provides a shortcode for Carousel.
 *
 * @Shortcode(
 *   id = "dexp_builder_carousel",
 *   title = @Translation("Carousel Item"),
 *   description = @Translation("Carousel content"),
 *   group = @Translation("Content"),
 *   parent = {
 *     "dexp_builder_carousels"
 *   },
 * )
 */
class BuilderCarousel extends BuilderElement {

  public function process(array $attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    parent::process($attributes, $text, $langcode);
    $attributes = $this->getAttributes(array(
      'class' => '',
        ), $attributes
    );
    global $builder_carousels_stack;
    if (empty($builder_carousels_stack)) {
      $builder_carousels_stack = array();
    }
	
    $output = [
	      'class' => $attributes['class'],
        'content' => $text,
    ];

    $builder_carousels_stack[] = $output;
    return '';
  }

  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    $form['general_options']['class'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Custom class'),
      '#default_value' => $this->get('class'),
      '#description' => $this->t('Adding a custom class allows you to target the shortcode easily within your custom codes.'),
    );
    unset($form['design_options']);
    unset($form['animate_options']);
    return $form;
  }

   public function processBuilder($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
        parent::process($attributes, $text, $langcode);
		        return $text;
    }
}
