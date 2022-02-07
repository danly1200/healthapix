<?php
/**
 * @file
 * Contains \Drupal\fhir_custom_api_form\Form\ApiForm.
 */

namespace Drupal\fhir_custom_api_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class ApiForm extends FormBase {
  /**
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'custom_api_form.settings';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'apis_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      static::SETTINGS,
    ];
  }

  /**
   * Form constructor.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The form structure.
   * @var $api_val get api product from my apps form
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // TODO: Implement buildForm() method.
    $config = $this->config(static::SETTINGS);
    $api_val = \Drupal::config('fhir_restapi.settings')
      ->get('variable_options');
    $form['api_option'] = [
      '#type' => 'checkboxes',
      '#title' => ('Select Which APIs require custom attributes'),
      '#options' => $api_val,
      '#default_value' => $config->get('api_option'),
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
    ];
    return $form;
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
    // TODO: Implement submitForm() method.
    // Retrieve the configuration.
    $this->configFactory->getEditable(static::SETTINGS)
      // Set the submitted configuration setting.
      ->set('api_option', $form_state->getValue('api_option'))
      ->save();
    $set_api = [];
    $val_api = $form_state->getValues('api_option');
    $apis = $val_api['api_option'];
    if ($apis != NULL) {
      foreach ($apis as $key => $value) {
        if (!empty($value)) {
          $set_api[] = $key;
        }
      }
    }
    // variable is set for selected api products
    $config = \Drupal::service('config.factory')
      ->getEditable('fhir_custom_api_form.settings');
    $config->set('variable_api_selected', $set_api)
      ->save();
    drupal_set_message('Saved');
  }
}
