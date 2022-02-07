<?php

namespace Drupal\dexp\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dexp_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    return parent::buildForm($form, $form_state);
  }
  
  public function getEditableConfigNames() {
    return [
      'dexp.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('dexp.settings');
    foreach($form_state->getValues() as $key => $value){
      if(!in_array($key, array('form_build_id','submit','form_token', 'form_id', 'op'))){
        $config->set($key, $value);
      }
    }
    $config->save();
    parent::submitForm($form, $form_state);
    drupal_flush_all_caches();
  }

}
