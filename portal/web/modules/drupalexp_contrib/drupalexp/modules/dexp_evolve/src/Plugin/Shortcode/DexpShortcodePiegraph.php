<?php

namespace Drupal\dexp_evolve\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use \Drupal\Component\Utility\Html;
use Drupal\dexp_builder\Plugin\Shortcode\BuilderElement;

/**
 * Provides a shortcode for Pie Graph.
 *
 * @Shortcode(
 *   id = "dexp_shortcode_piegraph",
 *   title = @Translation("Pie Graph"),
 *   description = @Translation("Pie Graph"),
 *   group = @Translation("Content"),
 *   child = {
 *   "dexp_builder_html"
 * }
 * )
 */
class DexpShortcodePiegraph extends BuilderElement {

    public function process(array $attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
        parent::process($attributes, $text, $langcode);

        $attributes = $this->getAttributes(array(
            'title' => '',
            'percent' => '0',
            'heading_size' => 'h4',
            'title_color' => '',
            'icon_size' => '24',
            'icon_color' => '',
            'bar_color' => '',
            'bar_width' => '10',            
            'icon' => '',
            'class' => '',
                ), $attributes
        );
        $piegraph_id = \Drupal\Component\Utility\Html::getUniqueId('dexp_builder_piegraph_' . REQUEST_TIME);
        $header_style='';
        $icon_style='';
        if($attributes['title_color']!=''){
            $header_style .= 'color:'.$attributes['title_color'].';';
        }
        if($attributes['icon_color']!=''){
            $icon_style .= 'color:'.$attributes['icon_color'].';';
        }
        if($attributes['icon_size']!=''){
            $icon_style .= 'font-size:'.$attributes['icon_size'].';';
        }
        $heading = '<' . $attributes['heading_size'] . ' class="dexp-pie-chart-title" style="'.$header_style.'">' . $attributes['title'] . '</' . $attributes['heading_size'] . '>';
        $icon='<i style="'.$icon_style.'"></i>';
        $return = array(
            '#theme' => 'dexp_evolve_piegraph',
            '#title' => $attributes['title'],
            '#piegraph_id' => $piegraph_id,
            '#heading' => $heading,
            '#class' => $attributes['class'],
            '#percent' => $attributes['percent'],            
            '#icon' => $attributes['icon'],
            '#icon_style' => $icon_style,
            '#bar_color'=>$attributes['bar_color'],
            '#bar_width'=>$attributes['bar_width'],
            '#content' => $text,
            '#attached' => ['library' => 'dexp_evolve/dexp-piegraph'],
        );
        return $this->render($return);
    }

    public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
        $form = parent::settingsForm($form, $form_state);

        $form['general_options']['title'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Title'),
            '#default_value' => $this->get('title', 'Pie Graph Title'),
        );
        $form['general_options']['percent'] = array(
            '#type' => 'number',
            '#title' => $this->t('Percent'),
            '#min' => 0,
            '#max' => 100,
            '#description' => $this->get('Enter value for graph (Note: choose range from 0 to 100).'),
            '#default_value' => $this->get('percent', 100),
        );        
        $form['general_options']['icon'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Icon'),
            '#default_value' => $this->get('icon', ''),
            '#attributes' => ['class' => ['icon-select']],
        );
        $form['general_options']['icon_size'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Icon Size'),
            '#default_value' => $this->get('icon_size', '20px'),
            '#description' => $this->t('Ex: 15px'),
        );
        $form['general_options']['icon_color'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Icon Color'),
            '#default_value' => $this->get('icon_color', '#000000'),
            '#attributes' => ['class' => ['color']],
        );
        $form['general_options']['bar_width'] = array(
            '#type' => 'textfield',            
            '#title' => $this->t('Bar Width'),
            '#default_value' => $this->get('bar_width', '10'),
            '#description' => $this->t('Ex: 20'),     
            '#field_suffix' => $this->t('px'),
        );
        $form['general_options']['bar_color'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Bar Color'),
            '#default_value' => $this->get('bar_color', '#000000'),
            '#attributes' => ['class' => ['color']],
        );
        $form['general_options']['heading_size'] = array(
            '#type' => 'select',
            '#title' => $this->t('Heading size'),
            '#options' => array('h4' => $this->t('Default'), 'h1' => $this->t('Heading 1'), 'h2' => $this->t('Heading 2'), 'h3' => $this->t('Heading 3'), 'h4' => $this->t('Heading 4'), 'h5' => $this->t('Heading 5'), 'h6' => $this->t('Heading 6')),
            '#description'=>'Select your heading size for title.',
            '#default_value' => $this->get('heading_size', 'h4'),
        );
        $form['general_options']['title_color'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Title Color'),
            '#default_value' => $this->get('title_color', '#000000'),
            '#attributes' => ['class' => ['color']],
        );
        $form['general_options']['html_content'] = array(
            '#type' => 'text_format',
			'#format' => 'full_html',
            '#title' => $this->t('Description'),
            '#default_value' => $this->get('html_content', ''),
        );
        $form['general_options']['class'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Custom class'),
            '#default_value' => $this->get('class'),
        );

        return $form;
    }

    public function processBuilder($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
        parent::process($attributes, $text, $langcode);

        return $attributes['percent'].'% '.$attributes['title'];
    }

}
