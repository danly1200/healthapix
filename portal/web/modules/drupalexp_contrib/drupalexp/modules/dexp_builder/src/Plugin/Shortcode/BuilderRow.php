<?php

namespace Drupal\dexp_builder\Plugin\Shortcode;

use Drupal\Core\Language\Language;

/**
 * Provides a shortcode for bootstrap row.
 *
 * @Shortcode(
 *   id = "dexp_builder_row",
 *   title = @Translation("Row"),
 *   description = @Translation("Builds a div with row or row-fluid class"),
 *   group = @Translation("Layout"),
 *   child = {"dexp_builder_col"},
 * )
 */

class BuilderRow extends BuilderElement{

  public function process(array $attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrObj = $this->createAttribute($attributes);
    $attrs = $this->getAttributes(array(
      'fluid' => 'no',
      'class' => '',
      'wrapper' => '',
      'wrapper_class' => '',
      'html_id' => '',
        ), $attributes);
    $attrObj->addClass('builder-row');
    if($attrs['html_id']){
      $attrObj->setAttribute('id', $attrs['html_id']);
    }
    //$attrObj->addClass($attrs['class']);
    $row_class = $attrs['fluid'] == 'yes' ? 'row-fluid' : 'row';
    $row_class .= ' ' . $attrs['class'];
    if($attrs['wrapper']){
      return '<div' . $attrObj->__toString() . '><div class="'. $attrs['wrapper_class'] .'"><div class="' . $row_class . '">' . $text . '</div></div></div>';
    }else{
      return '<div' . $attrObj->__toString() . '><div class="' . $row_class . '">' . $text . '</div></div>';
    }
  }
  
  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    
    $form['general_options']['class'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Class'),
      '#description' => $this->t('Adding a custom class allows you to target the row easily within your custom codes.'),
      '#default_value' => $this->get('class', ''),
    );
    
    $form['general_options']['equal_height'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Equal height columns?'),
      '#description' => $this->t('If checked, make all of its columns automatically be of equal height.'),
      '#default_value' => $this->get('equal_height', 0),
    );

    $form['general_options']['wrapper'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Add wrapper?'),
      '#description' => $this->t('Adding div element to wrap row.'),
      '#default_value' => $this->get('wrapper', 0),
    );
    
    $form['general_options']['wrapper_class'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Wrapper class'),
      '#description' => $this->t('Adding a class allows you to target the wrapper row. For example: container'),
      '#default_value' => $this->get('wrapper_class', ''),
      '#states' => [
        'visible' => [
          ':input[name=wrapper]' => ['checked' => TRUE],
        ]
      ]
    );
    $form['general_options']['html_id'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('HTML ID'),
      '#description' => $this->t('Provide special HTML ID. Usefull for onepage style.'),
      '#default_value' => $this->get('html_id', ''),
    );
    $form['design_options'] += $this->designOptions();
    $form['animate_options'] += $this->animateOptions();
    
    return $form;
  }
  
  public function processBuilder($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    return $text;
  }

}