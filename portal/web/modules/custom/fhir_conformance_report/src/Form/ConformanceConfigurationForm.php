<?php

namespace Drupal\fhir_conformance_report\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a form that configures forms module settings.
 */
class ConformanceConfigurationForm extends ConfigFormBase {

  /**
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'fhir_conformance_report.settings';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'fhir_conformance_report_settings_form';
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
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('fhir_conformance_report.settings')->get();
    $form['conformance_metadata'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Metadata Proxy URLs'),
    ];
    $form['conformance_metadata']['dstu2'] = [
      '#type' => 'textfield',
      '#title' => $this->t('DSTU2 Metadata Proxy URL'),
      '#default_value' => $config['dstu2'],
      '#maxlength' => 255,
      '#size' => 100,
    ];
    $form['conformance_metadata']['stu3'] = [
      '#type' => 'textfield',
      '#title' => $this->t('STU3 Metadata Proxy URL'),
      '#default_value' => $config['stu3'],
      '#maxlength' => 255,
      '#size' => 100,
    ];
    $form['conformance_metadata']['r4'] = [
      '#type' => 'textfield',
      '#title' => $this->t('R4 Metadata Proxy URL'),
      '#default_value' => $config['r4'],
      '#maxlength' => 255,
      '#size' => 100,
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    \Drupal::configFactory()->getEditable(static::SETTINGS)->delete();
    $this->configFactory->getEditable(static::SETTINGS)
      // Set the submitted configuration setting.
      ->set('dstu2', $form_state->getValue('dstu2'))
      ->set('stu3', $form_state->getValue('stu3'))
      ->set('r4', $form_state->getValue('r4'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
