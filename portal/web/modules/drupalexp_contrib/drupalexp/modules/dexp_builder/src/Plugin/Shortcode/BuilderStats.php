<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Drupal\dexp_builder\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\Core\Template\Attribute;
use Drupal\file\Entity\File;

/**
 * @Shortcode(
 *   id = "dexp_builder_stats",
 *   title = @Translation("Statistics Counter"),
 *   description = @Translation("Render statistics counter element"),
 *   group = @Translation("Content"),
 *   child = {}
 * )
 */
class BuilderStats extends BuilderElement{
  public function process(array $attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs = $this->getAttributes(array(
      'title' => '',
      'render_el' => 'h3',
      'icon_type' => 'icon',
      'icon' => '',
      'icon_image' => '',
      'icon_size' => '',
      'icon_library' => '',
      'number' => '',
      'duration' => 2000,
      'counter_prefix' => '',
      'counter_suffix' => '',
      'decimal' => '',
      'class' => '',
        ), $attributes
    );
    $attrObject = $this->createAttribute($attributes);
    $attrObject->addClass($attrs['class']);
    $icon = '';
    $iconAttribute = new Attribute();
    switch($attrs['icon_type']){
      case 'icon':
        $iconAttribute->addClass($attrs['icon']);
        if($attrs['icon_size']){
          $iconAttribute->setAttribute('style', 'font-size: ' . $attrs['icon_size']);
        }
        $icon = '<i' . $iconAttribute->__toString() . '"></i>';
        break;
      case 'image':
        $fid = str_replace('file:', '', $attrs['icon_image']);
        if($file = File::load($fid)){
          $icon = [
            '#theme' => 'image',
            '#uri' => $file->getFileUri(),
          ];
        }
        break;
    }
    $title = '';
    if($attrs['title']){
      $title = ['#markup' => '<' . $attrs['render_el'] . ' class="stats-title">' . $attrs['title'] . '</' . $attrs['render_el'] . '>'];
    }
    $output = [
      '#theme' => 'dexp_builder_stats',
      '#title' => $title,
      '#icon' => $icon,
      '#number' => $attrs['number'],
      '#duration' => $attrs['duration'],
      '#counter_prefix' => $attrs['counter_prefix'],
      '#counter_suffix' => $attrs['counter_suffix'],
      '#decimal' => $attrs['decimal'],
      '#attributes' => $attrObject,
      '#attached' => ['library' => ['dexp_builder/stats']],
    ];
    if ($attrs['icon_library'] && ($icon_plugin = \Drupal::service('dexp_builder.fonticon')->getFontIconPlugin($attrs['icon_library']))) {
      $output['#attached']['library'][] = $icon_plugin->library();
    }
    return $this->render($output);
  }
  
  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    $form['general_options']['title'] = array(
      '#title' => $this->t('Title'),
      '#type' => 'textfield',
      '#default_value' => $this->get('title'),
      '#description' => $this->t('Enter your desired text to use as the addon title. Leave blank if no title is needed.')
    );
    $form['general_options']['render_el'] = array(
      '#type' => 'select',
      '#title' => $this->t('Render Element'),
      '#options' => array(
        'h1' => 'H1',
        'h2' => 'H2',
        'h3' => 'H3',
        'h4' => 'H4',
        'h5' => 'H5',
        'h6' => 'H6',
        'div' => 'DIV',
      ),
      '#default_value' => $this->get('render_el','h3'),
      '#description' => $this->t('Select Title HTML element from the list.')
    );
    $form['general_options']['icon_type'] = array(
      '#type' => 'select',
      '#title' => $this->t('User Icon?'),
      '#options' => [
        'none' => $this->t('None'),
        'icon' => $this->t('Font Icon'),
        'image' => $this->t('Image Icon'),
      ],
      '#default_value' => $this->get('icon_type','none'),
      '#description' => $this->t('Using this field to hundreds an icon or image will display in the counter.')
    );
    $form['general_options']['icon'] = array(
      '#type' => 'textfield',
      '#default_value' => $this->get('icon', ''),
      '#attributes' => ['class' => ['icon-select']],
      '#description' => $this->t('Select an icon from library.'),
      '#states' => array(
        'visible' => array(
          ':input[name=icon_type]' => array('value' => 'icon'),
        ),
      ),
    );
    $form['general_options']['icon_size'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Font Size'),
      '#size' => 17,
      '#default_value' => $this->get('icon_size', ''),
      '#description' => $this->t('Set font size for icon, in pixels or percentage, for example: 20px or 80%.'),
      '#states' => array(
        'visible' => array(
            ':input[name=icon_type]' => array('value' => 'icon'),
          ),
      ),
    );
    $form['general_options']['icon_library'] = array(
      '#type' => 'hidden',
      '#default_value' => $this->get('icon_library', ''),
    );
    $form['general_options']['icon_image'] = array(
      '#type' => 'image_browser',
      '#title' => $this->t('Image'),
      '#default_value' => $this->get('icon_image'),
      '#description' => $this->t('Select an image icon from your Drupal library directory or upload a picture.'),
      '#states' => array(
        'visible' => array(
            ':input[name=icon_type]' => array('value' => 'image'),
          ),
      ),
    );
    $form['general_options']['number'] = array(
      '#title' => $this->t('Counter Number'),
      '#type' => 'number',
      '#min' => 1,
      '#required' => true,
      '#default_value' => $this->get('number',100),
      '#description' => $this->t('The number of statistics you want to count, in unsigned integer.'),
    );
    $form['general_options']['number_setting'] = array(
      '#type' => 'details',
      '#title' => $this->t('Number Settings'),
      '#open' => FALSE,
      'duration' => array(
        '#title' => $this->t('Duration'),
        '#type' => 'number',
        '#required' =>true,
        '#min' => 500,
        '#max' => 5000,
        '#default_value' => $this->get('duration', 2000),
        '#description' => $this->t('The time\'s property defines how long an animation should take to complete (milliseconds)'),
      ),
      'counter_prefix' => array(
        '#type' => 'textfield',
        '#title' => $this->t('Prefix'),
        '#size' => 17,
        '#default_value' => $this->get('counter_prefix', ''),
        '#description' => $this->t('Input your prefix, for example: $, USD, ERO,...'),
      ),
      'counter_suffix' => array(
        '#type' => 'textfield',
        '#size' => 17,
        '#title' => $this->t('Suffix'),
        '#default_value' => $this->get('counter_suffix', ''),
        '#description' => $this->t('Input your suffix, for example: +, %, kg, km,...'),
      ),
      'decimal' => array(
          '#type' => 'select',
          '#title' => $this->t('Decimal Format'),
          '#options' => array(
            'none' => $this->t('None'),
            ',' => $this->t('Separator (,)'),
            '.' => $this->t('Separator (.)'),
          ),
          '#default_value' => $this->get('decimal','none'),
          '#description' => $this->t('Select decimal format for statistics number. None -> 1234 or (,) -> 1,234 or (.) -> 1.234'),
      ),
    );
    $form['general_options']['class'] = array(
      '#title' => $this->t('Custom class'),
      '#type' => 'textfield',
      '#description' => $this->t('Adding a custom class allows you to target the shortcode easily within your custom codes.'),
      '#default_value' => $this->get('class'),
    );
    $form['design_options'] += $this->designOptions();
    $form['animate_options'] += $this->animateOptions();
    return $form;
  }
  public function processBuilder($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs = $this->getAttributes(array(
      'title' => '',
      'number' => 100,
        ), $attributes
    );
    return '[Statistics Counter: ' . $attrs['title'] . ' ' . $attrs['number'] . ']';
  }
}
