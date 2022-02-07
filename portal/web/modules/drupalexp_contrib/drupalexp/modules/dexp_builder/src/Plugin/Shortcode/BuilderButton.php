<?php

namespace Drupal\dexp_builder\Plugin\Shortcode;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\Language;
use Drupal\Core\Template\Attribute;

/**
 * Provides a shortcode for button.
 *
 * @Shortcode(
 *   id = "dexp_builder_button",
 *   title = @Translation("Button"),
 *   description = @Translation("Builds a button element"),
 *   group = @Translation("Content"),
 *   child = {},
 * )
 */
class BuilderButton extends BuilderElement {

  public function process(array $attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrObj = $this->createAttribute($attributes);
    $attrs = $this->getAttributes(array(
      'title' => '',
      'icon' => '',
      'add_icon' => '',
      'icon_library' => '',
      'icon_align' => '',
      'link' => '#',
      'link_target' => '_self',
      'type' => 'btn-default',
      'size' => '',
      'padding' => '',
      'outline' => '',
      'font_size' => '',
      'radius' => '',
      'class' => '',
      'block' => '',
        ), $attributes
    );
    $attrObj->addClass('dexp-button btn');
    $attrObj->addClass($attrs['class']);
    if($attrs['outline']){
      $attrs['type'] = str_replace('btn-','btn-outline-', $attrs['type']);
    }
    if($attrs['block']){
      $attrObj->addClass('btn-block');
    }
    $attrObj->addClass($attrs['type']);
    $style = '';
    if($attrs['size'] != 'btn-ct'){
      $attrObj->addClass($attrs['size']);
    }
    if($attrs['size'] == 'btn-ct'){
      if ($attrs['padding'] != '') {
        $style .= 'padding:' . $attrs['padding'] . ';';
      }
      if ($attrs['font_size'] != '') {
        $style .= ' font-size:' . $attrs['font_size'] . ';';
      }
    }
    if ($attrs['radius'] != '') {
      $style .= ' border-radius:' . $attrs['radius'] . ';';
    }
    if($style){
      $attrObj->setAttribute('style', $style);
    }
    if ($attrs['icon_align'] && $attrs['icon'] && $attrs['add_icon']) {
      $attrObj->addClass('icon-' . $attrs['icon_align']);
    }
    if(!$attrs['add_icon']){
      $attrs['icon'] = '';
    }
    $attrObj->setAttribute('target', $attrs['link_target']);
    if($attrs['link']){
      $link = $this->getLink($attrs['link']);
      $attrObj->setAttribute('href', $link);
    }
    $output = array(
      '#theme' => 'dexp_builder_button',
      '#title' => $attrs['title'],
      '#icon' => $attrs['icon'],
      '#attributes' => $attrObj,
      '#attached' => ['library' => ['dexp_builder/button']],
    );
    if($attrs['icon_library'] && ($icon_plugin = \Drupal::service('dexp_builder.fonticon')->getFontIconPlugin($attrs['icon_library']))){
      $output['#attached']['library'][] = $icon_plugin->library();
    }
    return $this->render($output);
  }

  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);

    $form['general_options']['title'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Button Text'),
      '#default_value' => $this->get('title', ''),
      '#description' => $this->t('Enter your desired text to use as the button.'),
      '#required' => true,
    );
    $form['general_options']['link'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Link'),
      '#description' => $this->t('Enter the destination URL.'),
      '#default_value' => $this->get('link', '#'),
    );
    $form['general_options']['link_target'] = array(
      '#type' => 'select',
      '#options' => [
        '_self' => $this->t('Same window'),
        '_blank' => $this->t('New window'),
      ],
      '#title' => $this->t('Target'),
      '#default_value' => $this->get('link_target'),
      '#description' => $this->t('Set target attribute for link.'),
    );
    $form['general_options']['add_icon'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Add icon?'),
      '#default_value' => $this->get('add_icon', ''),
      '#description' => $this->t('Using this field to hundreds an icon will display in the button.')
    );
    $form['general_options']['icon'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Icon'),
      '#default_value' => $this->get('icon', ''),
      '#attributes' => ['class' => ['icon-select']],
      '#description' => $this->t('Select an icon from library.'),
      '#states' => [
        'visible' => [
          ':input[name=add_icon]' => ['checked' => TRUE],
        ]
      ]
    );
    $form['icon_library'] = array(
      '#type' => 'hidden',
      '#default_value' => $this->get('icon_library', ''),
    );
    $form['general_options']['icon_align'] = array(
      '#type' => 'select',
      '#title' => $this->t('Icon Alignment'),
      '#options' => array('left' => $this->t('Left'), 'right' => $this->t('Right')),
      '#default_value' => $this->get('icon_align', 'left'),
      '#description' => $this->t('Select alignment for the icon.'),
      '#states' => [
        'visible' => [
          ':input[name=add_icon]' => ['checked' => TRUE],
        ]
      ]
    );
    
	  $form['general_options']['button_style'] = array(
      '#type' => 'details',
      '#title' => t('Style Settings'),
      '#open' => FALSE,
    );
    $form['general_options']['button_style']['type'] = array(
      '#type' => 'select',
      '#title' => $this->t('Button Style'),
      '#description' => $this->t('Select button styles from the list, we includes several predefined <a target="_blank" href="https://getbootstrap.com/docs/4.1/components/buttons/#examples">bookstrap button styles</a>. Default is DEXP builder style. '),
      '#options' => array(
        'btn-default' => $this->t('Default'), 
        'btn-primary' => $this->t('Primary'), 
        'btn-secondary' => $this->t('Secondary'), 
        'btn-success' => $this->t('Success'), 
        'btn-info' => $this->t('Info'), 
        'btn-warning' => $this->t('Warning'), 
        'btn-danger' => $this->t('Danger'), 
        'btn-light' => $this->t('Light'), 
        'btn-dark' => $this->t('Dark'),
        'btn-link' => $this->t('Link')
      ),
      '#default_value' => $this->get('type', 'btn-default'),
    );
    $form['general_options']['button_style']['outline'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Outline buttons?'),
      '#default_value' => $this->get('outline', ''),
      '#description' => $this->t('In need of a button, but not the hefty background colors they bring? <a href="https://getbootstrap.com/docs/4.1/components/buttons/#outline-buttons" target="_blank">See more</a>'),
    );
    $form['general_options']['button_style']['size'] = array(
      '#type' => 'select',
      '#title' => $this->t('Button Size'),
      '#description' => $this->t('Select button size to display from the list. Default is DEXP builder size.'),
      '#options' => array(
        '' => $this->t('Default'), 
        'btn-lg' => $this->t('Large'), 
        'btn-sm' => $this->t('Small'),
        'btn-ct' => $this->t('Custom size'), 
      ),
      '#default_value' => $this->get('size', ''),
    );
    $form['general_options']['button_style']['font_size'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Font Size'),
      '#size' => 17,
      '#description' => $this->t('Set font size for button, in pixels or percentage, for example: 20px or 80%.'),
      '#default_value' => $this->get('font_size', ''),
      '#states' => [
        'visible' => [
          ':input[name=size]' => ['value' => 'btn-ct'],
        ]
      ]
    );
    $form['general_options']['button_style']['padding'] = array(
      '#type' => 'textfield',
      '#size' => 20,
      '#title' => $this->t('Padding'),
      '#description' => $this->t('Define paddings for top, right, bottom and left for button. Negative values are not allowed. For example: 15px 35px 15px 35px.'),
      '#default_value' => $this->get('padding', ''),
      '#states' => [
        'visible' => [
          ':input[name=size]' => ['value' => 'btn-ct'],
        ]
      ]
    );
    $form['general_options']['button_style']['block'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Block button?'),
      '#default_value' => $this->get('block', ''),
      '#description' => $this->t('Create block level buttons—those that span the full width of a parent—by adding'),
    );
    $form['general_options']['button_style']['radius'] = array(
      '#type' => 'textfield',
      '#size' => 20,
      '#title' => $this->t('Border Radius'),
      '#description' => $this->t('This field defines the radius of the button corners. For example: 15px 35px 15px 35px.'),
      '#default_value' => $this->get('radius', ''),
    );
    $form['general_options']['class'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Custom class'),
      '#description' => $this->t('Adding a custom class allows you to target the button easily within your custom codes.'),
      '#default_value' => $this->get('class', ''),
    );
    $form['design_options'] += $this->designOptions();
    unset($form['design_options']['background']);
    unset($form['design_options']['padding']);
    unset($form['design_options']['border']);
    $form['design_options']['margin']['#open'] = TRUE;
    $form['animate_options'] += $this->animateOptions();
    return $form;
  }

}
