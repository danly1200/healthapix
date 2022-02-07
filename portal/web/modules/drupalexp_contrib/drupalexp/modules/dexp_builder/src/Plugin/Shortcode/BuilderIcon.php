<?php

namespace Drupal\dexp_builder\Plugin\Shortcode;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\Language;
use Drupal\Core\Template\Attribute;

/**
 * Provides a shortcode for icon.
 *
 * @Shortcode(
 *   id = "dexp_builder_icon",
 *   title = @Translation("Icon"),
 *   description = @Translation("Builds icon element"),
 *   group = @Translation("Content"),
 *   child = {},
 * )
 */
class BuilderIcon extends BuilderElement {

  public function process(array $attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs = $this->getAttributes(array(
      'title' => '',
      'icon' => '',
      'icon_library' => '',
      'font_size' => '',
      'link' => '',
      'link_target' => '_self',
      'class' => '',
      'tooltip' => '',
        ), $attributes
    );

    $attribute = $this->createAttribute($attributes);//new \Drupal\Core\Template\Attribute();
    $attribute->addClass('dexp-icon');
    $attribute->addClass($attrs['icon']);
    $attribute->addClass($attrs['class']);
    if ($attrs['font_size']) {
      $attribute->setAttribute('style', 'font-size: ' . $attrs['font_size']);
    }
    $link = '';
    if($attrs['link']){
      $link = $this->getLink($attrs['link']);
    }
    if ($attrs['tooltip']) {
      $attribute->setAttribute('data-placement', 'auto');
      $attribute->setAttribute('data-toggle', 'tooltip');
      $attribute->setAttribute('title', $attrs['tooltip']);
    }
    $output = array(
      '#theme' => 'dexp_builder_icon',
      '#title' => $attrs['title'],
      '#link' => $link,
      '#link_target' => $attrs['link_target'],
      '#attributes' => $attribute,
    );
    if ($attrs['icon_library'] && ($icon_plugin = \Drupal::service('dexp_builder.fonticon')->getFontIconPlugin($attrs['icon_library']))) {
      $output['#attached']['library'][] = $icon_plugin->library();
    }
    if($attrs['link_target'] == 'popup'){
      $attribute->addClass('dexp-video-popup');
      $output['#link_target'] = '_self';
    }
    if (strpos($attrs['class'], 'dexp-video-popup') !== false) {
      $output['#attached']['library'][] = 'dexp_builder/video-popup';
    }
    return $this->render($output);
  }

  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);

    $form['general_options']['icon'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Icon'),
      '#default_value' => $this->get('icon', ''),
      '#attributes' => ['class' => ['icon-select']],
      '#description' => $this->t('Select an icon from library.'),
      '#required' => true,
    );
    $form['icon_library'] = array(
      '#type' => 'hidden',
      '#default_value' => $this->get('icon_library', ''),
    );
    $form['general_options']['font_size'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Font size'),
      '#default_value' => $this->get('font_size', ''),
      '#size' => 17,
      '#description' => $this->t('Set font size for icon, in pixels or percentage, for example: 20px or 80%.'),
    );
    /* Not use
    $form['general_options']['title'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => $this->get('title', ''),
      '#description' => $this->t('Enter your desired text to use as the tooltip title. Leave blank if no title is needed.')
    );*/
    $form['general_options']['link'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Link'),
      '#description' => $this->t('Enter the destination URL.'),
      '#default_value' => $this->get('link', '#'),
    );
    $form['general_options']['link_target'] = array(
      '#type' => 'select',
      '#options' => [
        '' => $this->t('Same window'),
        '_blank' => $this->t('New window'),
      ],
      '#title' => $this->t('Link target'),
      '#default_value' => $this->get('link_target'),
      '#description' => $this->t('Set target attribute for link.'),
    );
    if(\Drupal::service('module_handler')->moduleExists('colorbox_load')){
      $form['general_options']['link_target']['#options']['popup'] = $this->t('Popup');
    }
    $form['general_options']['tooltip'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Tooltip'),
      '#default_value' => $this->get('tooltip', ''),
      '#description' => $this->t('Enter your desired text to use as the tooltip of icon. Leave blank if no tooltip is needed.')
    );
    $form['general_options']['class'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Custom class'),
      '#description' => $this->t('Adding a custom class allows you to target the icon easily within your custom codes.'),
      '#default_value' => $this->get('class', ''),
    );
    $form['design_options'] += $this->designOptions();    
    unset($form['animate_options']);
    return $form;
  }

}
