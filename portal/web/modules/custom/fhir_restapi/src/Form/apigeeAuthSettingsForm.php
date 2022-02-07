<?php

namespace Drupal\fhir_restapi\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;

/**
 * Configure example settings for this site.
 */
class apigeeAuthSettingsForm extends ConfigFormBase {

  /**
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'fhir_restapi.apigee_auth_url.settings';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'apigee_auth_url_settings';
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
    $config = $this->config(static::SETTINGS);
    $get_cred = fhir_get_edge_cred();
    $apigee_org = $get_cred[2];
    // "dev" is assumed as default value for the apigee
    $apigee_default_env = 'dev';
    // 'apigee.net/v1' is considered as the default domain for the launch URL
    $apigee_default_domain = 'apigee.net/v1';
    $launch_url_base_path = 'https://' . $apigee_org . '-' . $apigee_default_env .'.'. $apigee_default_domain . '/' ;
    $form['launch_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('APIGee Management API URL'),
      '#default_value' => $config->get('launch_url')?$config->get('launch_url'):$launch_url_base_path,
      '#description' => "This is the URL which is used for the Management api calls to apigee and
      also will be used to validate the URL while the file is
      uploaded while registering/updating a fhir server.",
    ];
    $form['apigee_developer_email'] = [
      '#type' => 'textfield',
      '#title' => $this->t('The developer email '),
      '#default_value' => $config->get('apigee_developer_email')?$config->get('apigee_developer_email'): 'appmngr1@gmail.com',
      '#description' => "This is the developer email that is used to create the management app.",
    ];
    $form['apigee_developer_app_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('The developer app name'),
      '#default_value' => $config->get('apigee_developer_app_name')?$config->get('apigee_developer_app_name'): 'DevPortalAppCreator',
      '#description' => "This is the management app name that is created for fetching the x api key.",
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  $values = $form_state->getValues();
  $launch_url = trim($values['launch_url']);

  if($launch_url) {
    $is_valid_url = UrlHelper::isValid($launch_url, $absolute = TRUE);
    if(!$is_valid_url) {
      $form_state->setErrorByName('launch_url', $this->t('Please enter the valid URL.'));
    }
  }


  }
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Retrieve the configuration.
    $this->configFactory->getEditable(static::SETTINGS)
      ->set('launch_url', $form_state->getValue('launch_url'))
      ->set('apigee_developer_email', $form_state->getValue('apigee_developer_email'))
      ->set('apigee_developer_app_name', $form_state->getValue('apigee_developer_app_name'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
