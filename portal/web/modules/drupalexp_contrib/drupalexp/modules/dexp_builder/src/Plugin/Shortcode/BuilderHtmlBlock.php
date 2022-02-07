<?php

namespace Drupal\dexp_builder\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\Core\Template\Attribute;

/**
 * Provides a shortcode for bootstrap row.
 *
 * @Shortcode(
 *   id = "dexp_builder_html",
 *   title = @Translation("HTML Block"),
 *   description = @Translation("Add Html block content"),
 *   group = @Translation("Content"),
 *   child = {}
 * )
 */
class BuilderHtmlBlock extends BuilderElement{

  public function process(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    /*$css = $this->getCSS($attrs);
    $attrs = $this->getAttributes(array(
      'class' => '',
      'animate' => '',
      'animate_delay' => 0,
        ), $attrs
    );*/
    $attribute = $this->createAttribute($attrs);
    $attribute->addClass($attrs['class']);
    /*$attribute->setAttribute('style', $css);
    if ($attrs['animate']) {
      $attribute->addClass('animated dexp-animate');
      $attribute->setAttribute('data-animate', $attrs['animate']);
      $attribute->setAttribute('data-animate-delay', $attrs['animate_delay']);
    }*/
    return '<div' . $attribute->__toString() . '>' . $text . '</div>';
  }

  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);

    $form['general_options']['html_content'] = array(
      '#type' => 'text_format',
      '#title' => $this->t('HTML Content'),
      '#format' => 'full_html',
      '#default_value' => $this->get('html_content', '<p>HTML block content</p>'),
      '#description' => $this->t('Use this field to enter the content you want to display with the content addon. Use the text editor to bring necessary customization to your content.')
    );

    $form['general_options']['class'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Custom Class'),
      '#description' => $this->t('Adding a custom class allows you to target the shortcode easily within your custom codes.'),
      '#default_value' => $this->get('class', ''),
    );

    $form['design_options'] += $this->designOptions();
    $form['animate_options'] += $this->animateOptions();

    return $form;
  }

  public function processBuilder($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    return $text;
  }

}
