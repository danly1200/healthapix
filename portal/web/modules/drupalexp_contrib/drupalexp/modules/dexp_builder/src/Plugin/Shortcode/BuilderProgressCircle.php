<?php

namespace Drupal\dexp_builder\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\Core\Template\Attribute;

/**
 * Provides a shortcode for progress circle.
 *
 * @Shortcode(
 *   id = "dexp_builder_progress_circle",
 *   title = @Translation("Progress Circle"),
 *   description = @Translation("Progress Circle"),
 *   group = @Translation("Content"),
 *   child = {}
 * )
 */
class BuilderProgressCircle extends BuilderElement {

  public function process(array $attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    //parent::process($attributes, $text, $langcode);
    $attrs = $this->getAttributes(array(
      'title' => '',
      'render_el' => 'h3',
      'add_icon' => '',
      'icon' => '',
      'icon_size' => '',
      'icon_library' => '',
      'percent' => '0',
      'align' => 'center',
      'radius' => '100',
      'border' => '4',
      'progress_color' => '#0078BB',
      'bar_color' => '#F1F1F1',
      'duration' => 20,
      'class' => '',
        ), $attributes
    );
    $attrObj = $this->createAttribute($attributes);
    $attrObj->addClass('dexp-builder-progress-circle');
    $attrObj->addClass($attrs['class']);
    $icon = '';
    $iconAttribute = new Attribute();
    if($attrs['add_icon'] && $attrs['icon']){
        $iconAttribute->addClass($attrs['icon']);
        if($attrs['icon_size']){
          $iconAttribute->setAttribute('style', 'font-size: ' . $attrs['icon_size']);
        }
        $icon = '<i' . $iconAttribute->__toString() . '></i>';
    }
    $title = '';
    if($attrs['title']){
      $title = ['#markup' => '<' . $attrs['render_el'] . ' class="dexp-progress-circle-title">' . $attrs['title'] . '</' . $attrs['render_el'] . '>'];
    }
    $radius = $attrs['radius'];
    $border = $attrs['border'];
    $progress_color = $attrs['progress_color'] ? $attrs['progress_color'] : '#0078BB';
    $bar_color = $attrs['bar_color'] ? $attrs['bar_color'] : '#F1F1F1';
    $size = $radius * 2;
    $inner_size = $size - ($border * 2);
    $progress_circle_wrapper = [
        'width: ' . $size . 'px',
        'height: ' . $size . 'px',
    ];
    if($attrs['align'] == 'center'){
        $progress_circle_wrapper[] = 'margin: 0 auto';
    }
    if($attrs['align'] == 'right'){
        $progress_circle_wrapper[] = 'margin-left: calc(100% - '. $size .'px)';
    }
    $progress_circle = [
        'border:' . $border . 'px solid ' . $bar_color
    ];
    $progress_circle_data = [
        'data-percent="' . $attrs['percent'] . '"',
        'data-radius="' . $attrs['radius'] . '"',
        'data-bar="' . $bar_color . '"',
        'data-color="' . $progress_color . '"',
        'data-duration="' . $attrs['duration'] . '"'
    ];
    $progress_bar_size = [
        'width: ' . $size . 'px',
        'height: ' . $size . 'px',
        'left: calc(50% - '. $radius . 'px)',
        'top: calc(50% - '. $radius .'px)',
        'clip: rect(0,' . $size . 'px,' . $size . 'px,' . $radius . 'px)'
    ];
    $progress_color_size = [
        'width: ' . $size . 'px',
        'height: ' . $size . 'px',
        'left: calc(50% - '. $radius . 'px)',
        'top: calc(50% - '. $radius .'px)',
        'border:' . $border . 'px solid ' . $progress_color,
        'clip: rect(0,' . $radius . 'px,' . $size . 'px,0)'
    ];
    $progress_content_size = [
        'width: ' . $inner_size . 'px',
        'height: ' . $inner_size . 'px',
        'left: '. $border . 'px',
        'top: '. $border . 'px',
    ];
    $progress = '<div class="dexp-progress-circle-wrapper" style="' . implode(';',$progress_circle_wrapper) . '">';
    $progress .= '<div class="dexp-progress-circle" style="' . implode(';',$progress_circle) . '" ' . implode(' ',$progress_circle_data) . '>';
    $progress .= '<div class="dexp-progress-bar" style="' . implode(';',$progress_bar_size) . '">';
    $progress .= '<div class="dexp-progress-color" style="' . implode(';',$progress_color_size) . '"></div></div>';
    $progress .= '<div class="dexp-progress-content" style="' . implode(';',$progress_content_size) . '">';
    $progress .= '<div class="dexp-progress-content-wrapper">';
    $progress .= $icon;
    $progress .= '<span></span></div></div></div></div>';
    $return = array(
      '#theme' => 'dexp_builder_progress_circle',
      '#title' => $title,
      '#progress' => $progress,
      '#content' => $text,
      '#attributes' => $attrObj,
      '#attached' => ['library' => ['dexp_builder/progresscircle']],
    );
    if($attrs['add_icon'] && $attrs['icon_library'] && ($icon_plugin = \Drupal::service('dexp_builder.fonticon')->getFontIconPlugin($attrs['icon_library']))){
        $output['#attached']['library'] = $icon_plugin->library();
    }
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
        '#description' => $this->t('If checked, the select icon will display in the circle.')
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
    $form['general_options']['icon_size'] = array(
        '#type' => 'textfield',
        '#title' => $this->t('Font Size'),
        '#size' => 17,
        '#default_value' => $this->get('icon_size', ''),
        '#description' => $this->t('Set font size for icon, in pixels or percentage, for example: 20px or 80%.'),
        '#states' => [
            'visible' => [
            ':input[name=add_icon]' => ['checked' => TRUE],
            ]
        ]
    );
    $form['general_options']['percent'] = array(
      '#type' => 'number',
      '#min' => 1,
      '#max' => 100,
      '#required' => true,
      '#default_value' => $this->get('percent', 50),
      '#title' => $this->t('Percent'),
      '#description' => $this->t('The number of percentage for circle (from 1 to 100).'),
    );
    $form['general_options']['progress'] = array(
        '#type' => 'details',
        '#title' => $this->t('Progress Settings'),
        '#open' => FALSE,
    );
    $form['general_options']['progress']['align'] = [
      '#type' => 'select',
      '#title' => $this->t('Progress Alignment'),
      '#options' => [
        'left' => $this->t('Left'),
        'center' => $this->t('Center'),
        'right' => $this->t('Right')
      ],
      '#default_value' => $this->get('align','center'),
      '#description' => $this->t('Set the alignment of the circle.')
    ];
    $form['general_options']['progress']['radius'] = array(
      '#type' => 'number',
      '#min' => 20,
      '#max' => 500,
      '#required' => true,
      '#title' => $this->t('Circle Radius'),
      '#description' => $this->t('Set radius of the circle, in pixels (from 20 to 500).'),
      '#default_value' => $this->get('radius',100),
    );
    $form['general_options']['progress']['border'] = array(
      '#type' => 'number',
      '#min' => 1,
      '#max' => 50,
      '#required' => true,
      '#title' => $this->t('Circle Border'),
      '#default_value' => $this->get('border',4),
      '#description' => $this->t('Set border width of the circle, in pixels (from 1 to 50).'),
    );
    $form['general_options']['progress']['progress_color'] = array(
      '#type' => 'textfield',
      '#size' => 17,
      '#title' => $this->t('Progress Color'),
      '#default_value' => $this->get('progress_color', ''),
      '#attributes' => ['class' => ['color']],
      '#description' => $this->t('The color using for the progress circle. Default is dexp builder base color.'),
    );
    $form['general_options']['progress']['bar_color'] = array(
      '#type' => 'textfield',
      '#size' => 17,
      '#title' => $this->t('Progress Bar Color'),
      '#default_value' => $this->get('bar_color', ''),
      '#attributes' => ['class' => ['color']],
      '#description' => $this->t('The color using for background of the progress circle. Default is dexp builder base color.'),
    );
    $form['general_options']['progress']['duration'] = array(
      '#type' => 'number',
      '#min' => 10,
      '#max' => 100,
      '#title' => $this->t('Duration'),
      '#description' => $this->t('Time to complete one step of process (ms)'),
      '#default_value' => $this->get('duration',20),
    );
    $form['general_options']['html_content'] = array(
        '#type' => 'text_format',
        '#format' => 'full_html',
        '#title' => $this->t('Content'),
        '#default_value' => $this->get('html_content', 'Leave blank if no content is needed.'),
        '#description' => $this->t('Enter the content you want to display with the progress circle. Use the text editor to bring necessary customization to your content. Leave blank if no content is needed.')
    );
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
      'percent' => '0',
        ), $attributes
    );
    return '[Progress Circle: ' . $attrs['title'] . ' ' . $attrs['percent'] . '%]';
  }

}