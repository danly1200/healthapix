<?php

namespace Drupal\dexp_builder\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dexp_builder_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $config = \Drupal::service('config.factory')->getEditable('dexp_builder.settings');
    
    $form['gmap_api'] = [
        '#type' => 'textfield',
        '#title' => t('Gmap API'),
        '#default_value' => $config->get('gmap_api'),
        '#description' => $this->t('Click <a target="_blank" href="https://developers.google.com/maps/documentation/javascript/get-api-key">here</a> to get your API key')
    ];
    return parent::buildForm($form, $form_state);
  }
  
  public function getEditableConfigNames() {
    return [
      'dexp_builder.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('dexp_builder.settings');
    foreach($form_state->getValues() as $key => $value){
      if(!in_array($key, array('form_build_id','submit','form_token', 'form_id', 'op'))){
        $config->set($key, $value);
      }
    }
    $config->save();
    parent::submitForm($form, $form_state);
  }

}