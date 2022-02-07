<?php

namespace Drupal\fhir_conformance_report\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class FhirConformanceVerbiage.
 */
class FhirConformanceVerbiage extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'fhir_conformance_report.fhirconformanceverbiage',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'fhir_conformance_verbiage';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('fhir_conformance_report.fhirconformanceverbiage');
    $form['fhir_conformance_verbiage'] = [
      '#type' => 'textarea',
      '#title' => $this->t('FHIR Conformance Verbiage'),
      '#description' => $this->t('Used to describe about the FHIR conformance page'),
      '#default_value' => $config->get('fhir_conformance_verbiage'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('fhir_conformance_report.fhirconformanceverbiage')
      ->set('fhir_conformance_verbiage', $form_state->getValue('fhir_conformance_verbiage'))
      ->save();
  }

}
