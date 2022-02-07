<?php

namespace Drupal\dexp_builder\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\Core\Template\Attribute;
/**
 * Provides a shortcode for container element.
 *
 * @Shortcode(
 *   id = "dexp_builder_container",
 *   title = @Translation("Container"),
 *   description = @Translation("Builds a div with any class"),
 *   group = @Translation("Layout"),
 * )
 */
class BuilderContainer extends BuilderElement{
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
    $form['design_options'] += $this->designOptions();
    $form['design_options']['custom_css'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Custom CSS'),
      '#default_value' => $this->get('custom_css',''),
      '#description' => $this->t('Adding a custom CSS allows you to target the shortcode easily within your custom codes.'),
    );
    $form['animate_options'] += $this->animateOptions();
    return $form;
  }
}
