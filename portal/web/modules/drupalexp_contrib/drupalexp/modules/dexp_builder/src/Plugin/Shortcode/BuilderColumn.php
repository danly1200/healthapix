<?php

namespace Drupal\dexp_builder\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\Core\Template\Attribute;
/**
 * Provides a shortcode for bootstrap row.
 *
 * @Shortcode(
 *   id = "dexp_builder_col",
 *   title = @Translation("Column"),
 *   description = @Translation("Builds a div with col-screen-size class"),
 *   group = @Translation("Layout"),
 * )
 */
class BuilderColumn extends BuilderElement {

  public function process(array $attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrObj = $this->createAttribute($attributes);
    $attrs = $this->getAttributes(array(
      'class' => '',
      'xs' => '',
      'sm' => '',
      'md' => '',
      'lg' => '',
      'xl' => '',
        ), $attributes
    );
    $attrObj->addClass($attrs['class']);
    $prev_size = '';
    foreach (['xs', 'sm', 'md', 'lg', 'xl'] as $size) {
      //if ($attrs[$size]) {
        if($size == 'xs'){
          if(empty($attrs[$size])){
            $attrObj->addClass('col');
          }else{
            $attrObj->addClass('col-' . $attrs[$size]);
          }
        }else{
          if($attrs[$size] != 'not-set' && $attrs[$size] != $prev_size){
            if(empty($attrs[$size])){
              $attrObj->addClass('col-' . $size);
            }else{
              $attrObj->addClass('col-' . $size . '-' . $attrs[$size]);
            }
          }
        }
      //}
      $prev_size = $attrs[$size];
    }
    return '<div' . $attrObj->__toString() . '>' . $text . '</div>';
  }

  public function processBuilder($attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    return $text;
  }

  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    $col_xs_options = array(
      '' => $this->t('Equal width'),
      'auto' => $this->t('Auto width, base on it\'s content'),
      '1' => '1/12',
      '2' => '2/12',
      '3' => '3/12',
      '4' => '4/12',
      '5' => '5/12',
      '6' => '6/12',
      '7' => '7/12',
      '8' => '8/12',
      '9' => '9/12',
      '10' => '10/12',
      '11' => '11/12',
      '12' => '12/12',
    );
    $col_options = array(
      'not-set' => $this->t('Not set (same as previous screen)'),
      '' => $this->t('Equal width'),
      'auto' => $this->t('Auto width, base on it\'s content'),
      '1' => '1/12',
      '2' => '2/12',
      '3' => '3/12',
      '4' => '4/12',
      '5' => '5/12',
      '6' => '6/12',
      '7' => '7/12',
      '8' => '8/12',
      '9' => '9/12',
      '10' => '10/12',
      '11' => '11/12',
      '12' => '12/12',
    );
    $form['general_options']['xs'] = array(
      '#type' => 'select',
      '#title' => $this->t('Extra small (<576px)'),
      '#min' => 0,
      '#max' => 12,
      '#default_value' => $this->get('xs', ''),
      '#options' => $col_xs_options,
    );

    $form['general_options']['sm'] = array(
      '#type' => 'select',
      '#title' => $this->t('Small (≥576px)'),
      '#min' => 0,
      '#max' => 12,
      '#default_value' => $this->get('sm', ''),
      '#options' => $col_options,
    );

    $form['general_options']['md'] = array(
      '#type' => 'select',
      '#title' => $this->t('Medium (≥768px)'),
      '#min' => 0,
      '#max' => 12,
      '#default_value' => $this->get('md', ''),
      '#options' => $col_options,
    );

    $form['general_options']['lg'] = array(
      '#type' => 'select',
      '#title' => $this->t('Large (≥992px)'),
      '#min' => 0,
      '#max' => 12,
      '#default_value' => $this->get('lg', ''),
      '#options' => $col_options,
    );

    $form['general_options']['xl'] = array(
      '#type' => 'select',
      '#title' => $this->t('Extra large (≥1200px)'),
      '#min' => 0,
      '#max' => 12,
      '#default_value' => $this->get('xl', 'not-set'),
      '#options' => $col_options,
    );

    $form['general_options']['class'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Custom class'),
      '#description' => $this->t('Adding a custom class allows you to target the column easily within your custom codes.'),
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
