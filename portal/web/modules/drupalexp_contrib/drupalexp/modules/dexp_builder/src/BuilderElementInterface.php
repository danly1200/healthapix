<?php

namespace Drupal\dexp_builder;

use Drupal\Core\Language\Language;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provide an interface for BuilderElement
 */
interface BuilderElementInterface {

  public function processBuilder($attr, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED);
  
  public function settingsForm(array $form, FormStateInterface $form_state);
}
