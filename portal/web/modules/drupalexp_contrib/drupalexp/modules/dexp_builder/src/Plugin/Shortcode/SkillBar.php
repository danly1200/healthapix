<?php

namespace Drupal\dexp_builder\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\Core\Template\Attribute;

/**
 * Provides a shortcode for skillbar.
 *
 * @Shortcode(
 *   id = "dexp_builder_skillbar",
 *   title = @Translation("Progress Bar"),
 *   description = @Translation("Progress Bar"),
 *   group = @Translation("Content"),
 *   child = {}
 * )
 */
class Skillbar extends BuilderElement {

  public function process(array $attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    //parent::process($attributes, $text, $langcode);
    $attrObj = $this->createAttribute($attributes);
    $attrs = $this->getAttributes(array(
      'title' => '',
      'render_el' => '',
      'add_icon' => '',
      'icon' => '',
      'icon_size' => '',
      'percent' => 0,
      'duration' => 0,
      'height' => 5,
      'progress_color' => '',
      'bar_color' => '',
      'striped' => '',
      'animated_striped' => '',
      'class' => '',
        ), $attributes
    );
    $attrObj->addClass($attrs['class']);    
    $title = '';
    if($attrs['title']){
      $icon='';
      if($attrs['add_icon'] && $attrs['icon']){
        $icon='<i class="'.$attrs['icon'].'"></i>';
      }
      $title = ['#markup' => '<' . $attrs['render_el'] . ' class="dexp-progress-bar-title">' . $icon . $attrs['title'] . '</' . $attrs['render_el'] . '>'];      
    }
    // $icon = '';
    // $iconAttribute = new Attribute();
    // if($attrs['add_icon'] && $attrs['icon']){
    //     $iconAttribute->addClass($attrs['icon']);
    //     if($attrs['icon_size']){
    //       $iconAttribute->setAttribute('style', 'font-size: ' . $attrs['icon_size']);
    //     }
    //     $icon = '<div class="dexp-progress-bar-icon"><i' . $iconAttribute->__toString() . '></i></div>';
    // }
    $striped_class='';
    if($attrs['striped']){
      $striped_class=' progress-bar-striped';
      if($attrs['animated_striped']){
        $striped_class = $striped_class.' progress-bar-animated';
      }
    }
    $return = array(
      '#theme' => 'dexp_builder_skillbar',
      '#title' => $title,
      //'#icon' => $icon,
      '#percent' => $attrs['percent'],
      '#duration' => $attrs['duration'],
      '#height' => $attrs['height'] . 'px',
      '#progress_color' => $attrs['progress_color'],
      '#bar_color' => $attrs['bar_color'],
      '#striped_class' => $striped_class,
      '#attributes' => $attrObj,
      '#attached' => ['library' => ['dexp_builder/skillbar']],
    );
    return $this->render($return);
  }

  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    $form['general_options']['title'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => $this->get('title'),
      '#description' => $this->t('Enter your desired text to use as the addon title. Leave blank if no title is needed.')
    );
    $form['general_options']['render_el'] = [
      '#type' => 'select',
      '#title' => $this->t('Render Element'),
      '#options' => [
        'h1' => 'H1',
        'h2' => 'H2',
        'h3' => 'H3',
        'h4' => 'H4',
        'h5' => 'H5',
        'h6' => 'H6',
        'div' => 'DIV',
      ],
      '#default_value' => $this->get('render_el','h2'),
      '#description' => $this->t('Select Title HTML element from the list.')
    ];
    $form['general_options']['add_icon'] = array(
        '#type' => 'checkbox',
        '#title' => $this->t('Add icon?'),
        '#default_value' => $this->get('add_icon', ''),
        '#description' => $this->t('If checked, the select icon will display in the bar.'),
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
    // $form['general_options']['icon_size'] = array(
    //     '#type' => 'textfield',
    //     '#title' => $this->t('Font Size'),
    //     '#size' => 17,
    //     '#default_value' => $this->get('icon_size', ''),
    //     '#description' => $this->t('Set font size for icon, in pixels or percentage, for example: 20px or 80%.'),
    //     '#states' => [
    //         'visible' => [
    //         ':input[name=add_icon]' => ['checked' => TRUE],
    //         ]
    //     ]
    // );
    $form['general_options']['percent'] = array(
      '#type' => 'number',
      '#min' => 1,
      '#max' => 100,
      '#required' => true,
      '#default_value' => $this->get('percent', 50),
      '#title' => $this->t('Percent'),
      '#description' => $this->t('The number of percentage for progress bar (from 1 to 100).'),
    );
    $form['general_options']['height'] = array(
        '#type' => 'number',
        '#min' => 1,
        '#max' => 200,
        '#required' => true,
        '#default_value' => $this->get('height', 5),
        '#title' => $this->t('Height'),
        '#description' => $this->t('Height of the bar, in pixels (from 1 to 200).'),
    );
    $form['general_options']['progress_color'] = array(
      '#type' => 'textfield',
      '#size' => 17,
      '#title' => $this->t('Progress Color'),
      '#default_value' => $this->get('progress_color', ''),
      '#attributes' => ['class' => ['color']],
      '#description' => $this->t('The color using for the progress bar. Default is dexp builder base color.'),
    );
    $form['general_options']['bar_color'] = array(
      '#type' => 'textfield',
      '#size' => 17,
      '#title' => $this->t('Progress Bar Color'),
      '#default_value' => $this->get('bar_color', '#e9ecef'),
      '#attributes' => ['class' => ['color']],
      '#description' => $this->t('The color using for background of the progress bar. Default is dexp builder base color.'),
    );
    $form['general_options']['striped'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Striped Progress Bar?'),
      '#default_value' => $this->get('striped', ''),
      '#description' => $this->t('If checked, will apply a stripe via CSS gradient over the progress bar’s background color.'),
    );
    $form['general_options']['animated_striped'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Animated Stripes Progress Bar?'),
      '#default_value' => $this->get('animated_striped', ''),
      '#description' => $this->t('If checked, will animate the stripes right to left via CSS3 animations.'),
      '#states' => [
        'visible' => [
        ':input[name=striped]' => ['checked' => TRUE],
        ]
      ]
    );
    // $form['general_options']['duration'] = array(
    //   '#type' => 'number',
    //   '#min' => 10,
    //   '#max' => 100,
    //   '#title' => $this->t('Duration'),
    //   '#description' => $this->t('Time to complete one step of process (ms).'),
    //   '#default_value' => $this->get('duration',10),
    // );    
    $form['general_options']['class'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Custom class'),
      '#default_value' => $this->get('class'),
      '#description' => $this->t('Adding a custom class allows you to target the shortcode easily within your custom codes.'),
    );
    $form['design_options'] += $this->designOptions();    
    unset($form['animate_options']);
    return $form;
  }

  public function processBuilder($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs = $this->getAttributes(array(
      'title' => '',
      'icon' => '',
      'percent' => 0,
      'duration' => 0,
      'class' => '',
        ), $attributes
    );
    return '[Progress bar: ' . $attrs['title'] . ' ' . $attrs['percent'] . '%]';
  }

}