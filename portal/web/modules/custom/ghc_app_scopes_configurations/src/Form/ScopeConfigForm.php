<?php

namespace Drupal\ghc_app_scopes_configurations\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;

/**
 * Class ScopeConfigForm.
 */
class ScopeConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'ghc_app_scopes_configurations.scopeconfig',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'scope_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config('ghc_app_scopes_configurations.scopeconfig');
    $nids = \Drupal::entityQuery('node')->condition('type','smartdocs')->execute();
    $nodes =  Node::loadMultiple($nids);

    foreach ($nodes as $node) {
//      $resource_name = $node->label();
      $resource_name =  preg_replace('/\s/', '', $node->label());
      $form[$resource_name] = [
        '#type' => 'fieldset',
        '#title' => $this->t($resource_name),
      ];
      $form[$resource_name][$resource_name.'___showToPatientScope'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Show this resource for patient Scope'),
        '#default_value' => $config->get($resource_name.'___showToPatientScope'),
      ];

      $form[$resource_name][$resource_name.'___showToUserScope'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Show this resource for User Scope'),
        '#default_value' => $config->get($resource_name.'___showToUserScope'),
      ];

        $form[$resource_name][$resource_name.'_patient'] = [
          '#type' => 'fieldset',
          '#title' => $this->t('Patient Scope'),
        ];

        $form[$resource_name][$resource_name.'_patient'][$resource_name.'___showWritePatient'] = [
            '#type' => 'checkbox',
            '#title' => $this->t('Show WRITE'),
            '#default_value' => $config->get($resource_name.'___showWritePatient'),
//            '#attributes' => array('checked' => 'checked')
        ];

        $form[$resource_name][$resource_name.'_patient'][$resource_name.'___showReadPatient'] = [
            '#type' => 'checkbox',
            '#title' => $this->t('Show Read'),
            '#default_value' => $config->get($resource_name.'___showReadPatient'),
          ];


      $form[$resource_name][$resource_name.'_user'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('User Scope'),
      ];

      $form[$resource_name][$resource_name.'_user'][$resource_name.'___showWriteUser'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Show WRITE'),
        '#default_value' => $config->get($resource_name.'___showWriteUser'),
      ];

      $form[$resource_name][$resource_name.'_user'][$resource_name.'___showReadUser'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Show Read'),
        '#default_value' => $config->get($resource_name.'___showReadUser'),
      ];
    }


    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    parent::submitForm($form, $form_state);

    $values = $form_state->getValues();
    $config = $this->config('ghc_app_scopes_configurations.scopeconfig');

    $unwantedValues = $form_state->getCleanValueKeys();
    $unwantedValues[] = 'submit';

    foreach ($unwantedValues as $unwantedValue){
      unset($values[$unwantedValue]);
    }

    $config->setData($values);
    $config->save();

  }



}
