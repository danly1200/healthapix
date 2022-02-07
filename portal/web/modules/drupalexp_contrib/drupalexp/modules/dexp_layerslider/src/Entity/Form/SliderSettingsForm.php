<?php

/**
 * @file
 * Contains \Drupal\dexp_layerslider\Entity\Form\SliderSettingsForm.
 */

namespace Drupal\dexp_layerslider\Entity\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class SliderSettingsForm.
 *
 * @package Drupal\dexp_layerslider\Form
 *
 * @ingroup dexp_layerslider
 */
class SliderSettingsForm extends FormBase {
  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'Slider_settings';
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Empty implementation of the abstract submit class.
  }


  /**
   * Defines the settings form for Slider entities.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   Form definition array.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['Slider_settings']['#markup'] = 'Settings form for Slider entities. Manage field settings here.';
    return $form;
  }

}
