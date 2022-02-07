<?php

namespace Drupal\dexp_builder\Plugin\Shortcode;

use Drupal\Core\Language\Language;

/**
 * Provides a shortcode for accordion.
 *
 * @Shortcode(
 *   id = "dexp_builder_accordion",
 *   title = @Translation("Accordion Item"),
 *   description = @Translation("Accordion content"),
 *   group = @Translation("Content"),
 *   parent = {
 *     "dexp_builder_accordions"
 *   },
 *   child = {
 *     "dexp_builder_html",
 *     "dexp_builder_single_image",
 *     "dexp_builder_view_embed"
 *   }
 * )
 */
class BuilderAccordion extends BuilderElement {

  public function process(array $attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs = $this->getAttributes(array(
      'title' => 'no',
      'render_el' => 'h3',
      'title_icon' => '',
      'class' => '',
      'icon_library' => '',
      'expanded' => 0,
        ), $attributes
    );
    $output = array(
      '#theme' => 'dexp_builder_accordion',
      '#title' => $attrs['title'],
      '#render_el' => $attrs['render_el'],
      '#icon' => $attrs['title_icon'],
      '#class' => $attrs['class'],
      '#content' => $text,
      '#expanded' => $attrs['expanded'],
    );
    if($attrs['icon_library'] && ($icon_plugin = \Drupal::service('dexp_builder.fonticon')->getFontIconPlugin($attrs['icon_library']))){
      $output['#attached']['library'] = $icon_plugin->library();
    }

    return $this->render($output);
  }

  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    $form['general_options']['title'] = array(
      '#type' => 'textfield',
      '#required' => true,
      '#title' => $this->t('Title'),
      '#default_value' => $this->get('title'),
      '#description' => $this->t('Enter your desired text to use as the accordion title.')
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
      '#default_value' => $this->get('render_el','h3'),
      '#description' => $this->t('Select Title HTML element from the list.')
    ];
    $form['general_options']['title_icon'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Icon'),
      '#default_value' => $this->get('title_icon'),
      '#description' => $this->t('Select an icon from library. Leave blank if no icon is needed.'),
      '#attributes' => ['class' => ['icon-select']],
    );
    $form['general_options']['icon_library'] = array(
      '#type' => 'hidden',
      '#default_value' => $this->get('icon_library', ''),
    );
    $form['general_options']['expanded'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Show as expanded?'),
      '#description' => $this->t('If checked, the content of this accordion will open.'),
      '#default_value' => $this->get('expanded', 0),
    );
    $form['general_options']['custom_class'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Custom class'),
      '#description' => $this->t('Adding a custom class allows you to target the accordion easily within your custom codes.'),
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
