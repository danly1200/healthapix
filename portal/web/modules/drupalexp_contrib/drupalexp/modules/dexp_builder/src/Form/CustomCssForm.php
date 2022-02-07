<?php

namespace Drupal\dexp_builder\Form;

use Drupal\Core\Form\FormBase;

class CustomCssForm extends FormBase {
  //put your code here
  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form['background_op'] = array(
      '#type' => 'details',
      '#title' => $this->t('Background'),
      '#open' => true,
      'background_image' => [
        '#type' => 'image_browser',
        '#title' => $this->t('Background image'),
      ],
      'background_color' => [
        '#type' => 'textfield',
        '#title' => $this->t('Background color'),
        '#attributes' => ['class' => ['color']],
      ],
    );
    $form['border_op'] = array(
      '#type' => 'details',
      '#title' => $this->t('Border'),
      '#open' => true,
      'border_color' => [
        '#type' => 'textfield',
        '#title' => $this->t('Border color'),
        '#attributes' => ['class' => ['color']],
      ],
      'border_width' => [
        '#type' => 'textfield',
        '#title' => $this->t('Border width'),
      ],
      'border_style' => [
        '#type' => 'select',
        '#title' => $this->t('Border style'),
        '#options' => ['none' => 'none', 'hidden' => 'hidden', 'dotted' => 'dotted', 'dashed' => 'dashed', 'solid' => 'solid', 'double' => 'double', 'groove' => 'groove', 'ridge' => 'ridge', 'inset' => 'inset', 'outset' => 'outset', 'initial' => 'initial', 'inherit' => 'inherit'],
      ],
      'border_radius' => [
        '#type' => 'textfield',
        '#title' => $this->t('Border radius'),
      ],
    );
    $form['padding_op'] = array(
      '#type' => 'details',
      '#title' => $this->t('Padding'),
      '#open' => true,
    );
    $form['margin_op'] = array(
      '#type' => 'details',
      '#title' => $this->t('Margin'),
      '#open' => true,
    );
    $form['actions'] = array(
      '#type' => 'actions',
      'submit' => array(
        '#type' => 'submit',
        '#value' => 'Save',
        '#button_type' => 'primary',
        '#ajax' => array(
          'callback' => '::submitForm',
        ),
      ),
    );
   
    return $form;
  }

  public function getFormId() {
    
  }

  public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    
  }

}
