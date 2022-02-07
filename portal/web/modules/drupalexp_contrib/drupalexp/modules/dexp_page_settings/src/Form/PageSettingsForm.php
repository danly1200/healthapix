<?php
namespace Drupal\dexp_page_setting\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class PageSettingsForm extends FormBase{
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dexp_pagesettings_form';
  }
  
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = [];
    $form['custom_logo'] = array(
      '#type' => 'textfield',
      '#title' => 'Custom logo',
    );
    
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    
  }
  
}