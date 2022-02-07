<?php

namespace Drupal\dexp_builder\Plugin\Shortcode;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\Language;

/**
 * Provides a shortcode for title.
 *
 * @Shortcode(
 *   id = "dexp_builder_googlechart",
 *   title = @Translation("Google Chart"),
 *   description = @Translation("Google Charts provides a perfect way to visualize data on your website. From simple line charts to pie chart or column chart,..."),
 *   group = @Translation("Content"),
 *   child = {}
 * )
 */
class BuilderGoogleChart extends BuilderElement {

  public function process(array $attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs = $this->getAttributes(array(
        'title' => '',
        'render_el' => 'h3',
        'chart_type' => 'area',
        'chart_values' => '',
        'chart_options' => '',
        'width' => 0,
        'height' => 0,
        'class' => '',
        ), $attributes
    );
    $attrObj = $this->createAttribute($attributes);
    $attrObj->addClass('dexp-google-chart');
    $attrObj->addClass($attrs['class']);
    $values = '';
    if($attrs['chart_values']){
        $values = base64_decode($attrs['chart_values']);
    }
    $options = '';
    if($attrs['chart_options']){
        $options = base64_decode($attrs['chart_options']);
    }
    $chart_size = '';
    if($attrs['width']){
        $chart_size .= 'width: ' . $attrs['width'] . 'px;';
    }
    if($attrs['height']){
        $chart_size .= 'height: ' . $attrs['height'] . 'px;';
    }
    $title = '';
    if($attrs['title']){
        $title = ['#markup' => '<' . $attrs['render_el'] . ' class="dexp-google-chart-title">' . $attrs['title'] . '</' . $attrs['render_el'] . '>'];
    }
    $return = [
        '#theme' => 'dexp_builder_googlechart',
        '#title' => $title,
        '#chart_values' => $values,
        '#chart_size' => $chart_size,
        '#chart_type' => $attrs['chart_type'],
        '#chart_options' => $options,
        '#attributes' => $attrObj,
        '#attached' => ['library' => 'dexp_builder/googlechart'],
    ];
    return $this->render($return);
  }
  
  public function processBuilder($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED){
    $attrs = $this->getAttributes(array(
      'title' => '',
      'chart_type' => '',
      'chart_values' => '',
      'chart_options' => ''
        ), $attributes
    );
    return '[Google ' . $attrs['chart_type'] . ' chart: ' . $attrs['title'] . ']';
  }

  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form  = parent::settingsForm($form, $form_state);
    $form['general_options']['title'] = array(
        '#type' => 'textfield',
        '#title' => $this->t('Title'),
        '#default_value' => $this->get('title'),
        '#description' => $this->t('Enter your desired text to use as the chart title. Leave blank if no title is needed.')
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
    $form['general_options']['chart_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Chart Types'),
      '#options' => [
        'area' => $this->t('Area Chart'),
        'bar' => $this->t('Bar Chart'),
        'column' => $this->t('Column Chart'),
        'line' => $this->t('Line Chart'),
        'pie' => $this->t('Pie Chart')
      ],
      '#description' => $this->t('We are providers some of chart types from Google Charts. We hope it\'s enough for your website.'),
      '#default_value' => $this->get('chart_type','area'),
    ];
    $form['general_options']['chart_values'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Data Values'),
      '#encode' => 1,
      '#default_value' => $this->get('chart_values'),
      '#description' => $this->t('Following Google Chart, <a href="https://developers.google.com/chart/interactive/docs/" target="_blank">click here</a>, then select chart type to see what you need, for example Pie Chart data:
<pre>[
    ["Task", "Hours per Day"],
    ["Work",     11],
    ["Eat",      2],
    ["Commute",  2],
    ["Watch TV", 2],
    ["Sleep",    7]
]</pre>')];
    $form['general_options']['chart_options'] = array(
      '#type' => 'textarea',
      '#default_value' => $this->get('chart_options'),
      '#title' => $this->t('Chart Options'),
      '#encode' => 1,
      '#description' => $this->t('Following Google configuration options, for example Pie Chart <a href="https://developers.google.com/chart/interactive/docs/gallery/piechart#configuration-options" target="_blank">click here</a>, options example bellow:
<pre>{
    colors:["red","blue","green","yellow","black"],
    pieSliceText: "none",
    legend: {position: "labeled", textStyle: {color: "blue", fontSize: 16}},
    pieHole: 0.5
}</pre>'),
    );
    $form['general_options']['width'] = array(
        '#type' => 'number',
        '#min' => 0,
        '#max' => 1200,
        '#default_value' => $this->get('width', 0),
        '#title' => $this->t('Width'),
        '#description' => $this->t('Width of the chart, in pixels. Default 0: width of the containing element.'),
    );
    $form['general_options']['height'] = array(
        '#type' => 'number',
        '#min' => 0,
        '#max' => 1200,
        '#default_value' => $this->get('height', 0),
        '#title' => $this->t('Height'),
        '#description' => $this->t('Height of the chart, in pixels. Default 0: height of the containing element.'),
    );
    $form['general_options']['class'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Custom class'),
      '#default_value' => $this->get('class'),
    ];
    $form['design_options'] += $this->designOptions();
    unset($form['animate_options']);
    return $form;
  }
}