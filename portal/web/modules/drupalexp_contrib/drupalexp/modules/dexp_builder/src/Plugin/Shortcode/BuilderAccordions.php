<?php

namespace Drupal\dexp_builder\Plugin\Shortcode;

use Drupal\Core\Language\Language;
/**
 * Provides a shortcode for bootstrap row.
 *
 * @Shortcode(
 *   id = "dexp_builder_accordions",
 *   title = @Translation("Accordions"),
 *   description = @Translation("Togglable Accordions"),
 *   group = @Translation("Content"),
 *   child = {
 *     "dexp_builder_accordion"
 *   }
 * )
 */
class BuilderAccordions extends BuilderElement {

  public function process(array $attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    parent::process($attributes, $text, $langcode);
    
    $attributes = $this->getAttributes(array(
      'class' => '',
      'fade' => '',
        ), $attributes
    );
    $return =array(
      '#theme' => 'dexp_builder_accordions',
      '#content' => $text,
      '#class' => $attributes['class'],
    );
    return $this->render($return);
  }

  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    
    $form['general_options']['fade'] = array(
      '#type' => 'select',
      '#description' => $this->t('Select effect from the list.'),
      '#title' => $this->t('Accordion effect'),
      '#options' => array('' => $this->t('None'), 'fade' => $this->t('Fade')),
      '#default_value' => $this->get('fade'),
    );
    
    $form['general_options']['class'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Custom class'),
      '#description' => $this->t('Adding a custom class allows you to target the accordion easily within your custom codes.'),
      '#default_value' => $this->get('class'),
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