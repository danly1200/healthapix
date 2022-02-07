<?php

namespace Drupal\dexp_builder\Plugin\Shortcode;

use Drupal\Core\Language\Language;

/**
 * Provides a shortcode for bootstrap row.
 *
 * @Shortcode(
 *   id = "dexp_builder_tab",
 *   title = @Translation("Tab Item"),
 *   description = @Translation("Tab content"),
 *   group = @Translation("Content"),
 *   parent = {
 *     "dexp_builder_tabs"
 *   },
 *   child = {
 *     "dexp_builder_html",
 *     "dexp_builder_single_image",
 *     "dexp_builder_view_embed"
 *   }
 * )
 */
class BuilderTab extends BuilderElement {

  public function process(array $attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs = $this->getAttributes(array(
      'title' => 'no',
      'title_icon' => '',
      'class' => '',
      'icon_library' => '',
        ), $attributes
    );
    global $builder_tabs_stack;
    $classes = $this->addClass([' '], $attrs['class']);
    $classes = $this->addClass($classes, 'tab-pane');
    if (empty($builder_tabs_stack)) {
      $builder_tabs_stack = array();
      $classes = $this->addClass($classes, 'active');
    }
    $tab_id = \Drupal\Component\Utility\Html::getUniqueId('dexp_builder_tab_' . REQUEST_TIME);
    $output = array(
      'title' => $attrs['title'],
      'title_icon' => $attrs['title_icon'],
      'tab_id' => $tab_id,
      'class' => $classes,
      'content' => $text,
    );
    if($attrs['icon_library'] && ($icon_plugin = \Drupal::service('dexp_builder.fonticon')->getFontIconPlugin($attrs['icon_library']))){
      $output['#attached']['library'] = $icon_plugin->library();
    }
    $builder_tabs_stack[] = $output;
    return '';
  }

  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    $form['general_options']['title'] = array(
      '#type' => 'textfield',
      '#description' => $this->t('Enter your desired text to use as the tab title.'),
      '#required' => true,
      '#size' => 20,
      '#title' => $this->t('Title'),
      '#default_value' => $this->get('title'),
    );
    $form['general_options']['title_icon'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Icon'),
      '#default_value' => $this->get('title_icon',''),
      '#description' => $this->t('Select an icon from library.'),
      '#attributes' => ['class' => ['icon-select']],
    );
    $form['general_options']['icon_library'] = array(
      '#type' => 'hidden',
      '#default_value' => $this->get('icon_library', ''),
    );
    $form['general_options']['custom_class'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Custom class'),
      '#description' => $this->t('Adding a custom class allows you to target the tab item easily within your custom codes.'),
      '#default_value' => $this->get('custom_class'),
    );
    unset($form['design_options']);
    unset($form['animate_options']);
    return $form;
  }

  public function processBuilder($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    return $text;
  }

}
