<?php

namespace Drupal\dexp_block_settings\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dexp_block_settings_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $config = \Drupal::service('config.factory')->getEditable('dexp_block_settings.settings');
    
    $form['disable_animation'] = [
        '#type' => 'checkbox',
        '#title' => t('Disable Animation'),
        '#default_value' => $config->get('disable_animation'),
    ];
    $form['disable_animation_mobile'] = [
        '#type' => 'checkbox',
        '#title' => t('Disable Animation On Mobile'),
        '#description' => t('Disable Animation on mobile only'),
        '#states' => [
          'visible' => [
            ':input[name=disable_animation]' => [
              'checked' => FALSE,
            ],
          ],
        ],
        '#default_value' => $config->get('disable_animation_mobile'),
    ];
    $form['animation_once'] = [
      '#type' => 'checkbox',
      '#title' => t('Animation first time only'),
      '#description' => t('Choose wheter animation should fire once, or every time you scroll up/down to element'),
      '#states' => [
        'visible' => [
          ':input[name=disable_animation]' => [
            'checked' => FALSE,
          ],
        ],
      ],
      '#default_value' => $config->get('animation_once', false),
    ];
    return parent::buildForm($form, $form_state);
  }
  
  public function getEditableConfigNames() {
    return [
      'dexp_block_settings.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('dexp_block_settings.settings');
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