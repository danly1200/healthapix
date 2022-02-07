<?php

namespace Drupal\dexp_builder\Plugin\Shortcode;

use Drupal\Core\Language\Language;

/**
 * Provides a shortcode for title.
 *
 * @Shortcode(
 *   id = "dexp_builder_title",
 *   title = @Translation("Title"),
 *   description = @Translation("Render Title element like block title"),
 *   group = @Translation("Content"),
 *   child = {}
 * )
 */
class BuilderTitle extends BuilderElement {

  public function process(array $attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs = $this->getAttributes(array(
      'title' => '',
      'render_el' => 'h2',
      'subtitle' => '',
      'backword' => '',
      'class' => '',
        ), $attributes
    );
    $output = [
      '#theme' => 'dexp_builder_title',
      '#title' => $attrs['title'],
      '#render_el' => $attrs['render_el'],
      '#subtitle' => $attrs['subtitle'],
      '#backword' => $attrs['backword'],
      '#class' => $attrs['class'],
    ];
    return $this->render($output);
  }
  
  public function processBuilder($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED){
    $attrs = $this->getAttributes(array(
      'title' => '',
      'subtitle' => '',
      'backword' => '',
      'class' => '',
        ), $attributes
    );
    return '<h2>' . $attrs['title'] . '</h2><p>' . $attrs['subtitle'] . '</p>';
  }

  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form  = parent::settingsForm($form, $form_state);
    $form['general_options']['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => $this->get('title'),
    ];
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
      ],
      '#default_value' => $this->get('render_el','h2'),
    ];
    $form['general_options']['subtitle'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Sub title'),
      '#default_value' => $this->get('subtitle'),
    ];
    $form['general_options']['backword'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Backword'),
      '#default_value' => $this->get('backword'),
    ];
    $form['general_options']['class'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Custom class'),
      '#default_value' => $this->get('class'),
    ];
    unset($form['design_options']);
    unset($form['animate_options']);
    return $form;
  }
}