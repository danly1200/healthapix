<?php

namespace Drupal\dexp_builder\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\Core\Template\Attribute;
/**
 * Provides a shortcode for model popup element.
 *
 * @Shortcode(
 *   id = "dexp_builder_model",
 *   title = @Translation("Model Popup"),
 *   description = @Translation("Builds a model popup with any content"),
 *   group = @Translation("Content"),
 * )
 */
class BuilderModel extends BuilderElement{
  public function process(array $attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrObj = $this->createAttribute($attributes);
    $attrs = $this->getAttributes(array(
      'class' => '',
        ), $attributes
    );
    $attrObj->addClass($attrs['class']);
    return '<div' . $attrObj->__toString() . '>' . $text . '</div>';
  }
  
  public function processBuilder($attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    return $text;
  }
  
  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    $form['general_options']['class'] = array(
        '#type' => 'textfield',
        '#title' => $this->t('Custom class'),
        '#description' => $this->t('Adding a custom class allows you to target the shortcode easily within your custom codes.'),
        '#default_value' => $this->get('class', ''),
    );
    unset($form['design_options']);
    unset($form['animate_options']);
    return $form;
  }
}
