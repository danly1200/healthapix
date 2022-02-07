<?php

namespace Drupal\fhir_app\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\taxonomy\Entity\Term;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Url;

/**
 * Class Fhir Normal App.
 */
class FhirNormalApp extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'fhir_normal_app';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = [
      '#prefix' => '<div class="app-create-form">',
      '#suffix' => '</div>',
    ];
    $form['app_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('App Name'),
      '#maxlength' => 64,
      '#size' => 64,
      '#weight' => '0',
      '#required' => TRUE,
    ];
    $form['fhir_server'] = [
      '#type' => 'select',
      '#title' => $this->t('FHIR Server (Optional)'),
      '#options' => $this->getFhirs(),
      '#size' => 64,
      '#weight' => '0',
      '#weight' => '0',
      '#default_value' =>  $this->getFhirsDefault(),
    ];
    $form['jwks_uri'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Jwks-uri (Optional for non-backend services flow)'),
      '#maxlength' => 255,
      '#size' => 255,
      '#weight' => '0',
    ];
    $form['redirect_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Redirect Url (Optional)'),
      '#maxlength' => 255,
      '#size' => 255,
      '#weight' => '0',
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];
    $form['submit_cancel'] = [
      '#prefix' => '<a class="cancel-fhir-server submit-btn mdc-button mdc-button--unelevated" href="/user/my-apps">',
      '#suffix' => '</a>',
      '#type' => 'markup',
      '#weight' => 999,
      '#markup' => t('CANCEL'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $app_name = $form_state->getValue('app_name');
    if ($app_name) {
      if (preg_match('/[^a-z0-9 _-]+/i', $app_name)) {
        $form_state->setErrorByName('app_name', t(' Special Characters are not allowed for App Name.'));
      }
    }
    $existing_app_names = getAppNames();

    if(in_array(strtolower($app_name), $existing_app_names)) {
      $form_state->setErrorByName('app_name', t('App name already exists.'));
    }
//    foreach ($form_state->getValues() as $key => $value) {
//      // @TODO: Validate fields.
//    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Current user Email id.
    $email_id = \Drupal::currentUser()->getEmail();

    // App details.
    $app_name = $form_state->getValue('app_name');
    $display_name = $form_state->getValue('app_name');
    $jwks_uri = $form_state->getValue('jwks_uri');

    $fhir_server_id = $form_state->getValue('fhir_server');
    $smart_app_url = '';
    $redirect_url = $form_state->getValue('redirect_url');
    $app_type = $form_state->getValue('app_type');
    $selected_products[] = $this->getFhirsProducts($fhir_server_id);
      $fhir_server_name = \Drupal::service('fhir_smart.app')
          ->getFhirServerName($fhir_server_id);



      $standard_scopes = $form_state->getValue('standard_scopes');
    $user_scopes = $form_state->getValue('user_scopes');
    $patient_scopes = $form_state->getValue('patient_scopes');

    // Get the list of scopes
    foreach ($standard_scopes as $standard_scope) {
      if ($standard_scope !== 0) {
        $standard_scope_list[] = $standard_scope;
      }
    }
    $checked_standard_scopes = implode(" ", $standard_scope_list);

    foreach ($user_scopes as $user_scope) {
      if ($user_scope !== 0) {
        $user_scope_list[] = $user_scope;
      }
    }
    $checked_user_scopes = implode(" ", $user_scope_list);


    foreach ($patient_scopes as $patient_scope) {
      if ($patient_scope !== 0) {
        $patient_scope_list[] = $patient_scope;
      }
    }
    $checked_patient_scopes = implode(" ", $patient_scope_list);

    $fhir_version = $this->getFhirVersion($fhir_server_id);
    // get the value from the query param.
    $fhir_app_type = 'normal';
    $service = \Drupal::service('fhir_smart.app')
      ->createMyApp($email_id, $app_name, $display_name, $selected_products,
        $smart_app_url, $redirect_url, $app_type, $fhir_app_type, $fhir_version, $fhir_server_id, $checked_standard_scopes, $checked_user_scopes, $checked_patient_scopes,$fhir_server_name, $jwks_uri);
    drupal_set_message(t('Your @appname app is created successfully.', array('@appname' => $app_name)), 'status');
    $fhir_page = new RedirectResponse(URL::fromUserInput('/user/my-apps#'.$fhir_app_type)
      ->toString());
    $fhir_page->send();
  }

  /*
   *  Helper function to have fhir server titles.
   */

  public function getFhirs() {
    // query to get all the node of fhir content type
    $query = \Drupal::database()->select('node_field_data', 'n');
    $query->fields('n', ['title']);
    $query->fields('n', ['nid']);
    $query->condition('n.status', '1');
    $query->condition('n.type', 'fhir_servers');
    $server_nodes = $query->execute()->fetchAll();
    foreach ($server_nodes as $titles) {
      $node_title[$titles->nid] = $titles->title;
    }
    return $node_title;
  }

  /**
   *  Helper function to have the fhir products.
   */
  public function getFhirsProducts($nid) {
    $query = \Drupal::database()
      ->select('node__field_fhir_api_products', 'fap');
    $query->fields('fap', ['field_fhir_api_products_target_id']);
    $query->condition('fap.entity_id', $nid);
    $apigee_product = $query->execute()->fetchAll();
    foreach ($apigee_product as $product) {
      $product_title = $product->field_fhir_api_products_target_id;
    }
    return $product_title;
  }

  /**
   *  Helper function to have the fhir version.
   */
  public function getFhirVersion($nid) {
    $query = \Drupal::database()
      ->select('node__field_fhir_version_autofill', 'fv');
    $query->fields('fv', ['field_fhir_version_autofill_target_id']);
    $query->condition('fv.bundle', 'fhir_servers');
    $query->condition('fv.entity_id', $nid);
    $fhir_verisons = $query->execute()->fetchAll();
    foreach ($fhir_verisons as $version) {
      $fhirversion = $version->field_fhir_version_autofill_target_id;
    }
    $fhirversionName = \Drupal\taxonomy\Entity\Term::load($fhirversion)
      ->get('name')->value;
    return $fhirversionName;
  }

  /*
  *  Helper function to have fhir server titles.
  */
  public function getFhirsDefault() {
    // query to get all the node of fhir content type
    $query = \Drupal::database()->select('node_field_data', 'n');
    $query->fields('n', ['title']);
    $query->fields('n', ['nid']);
    $query->condition('n.status', '1');
    $query->condition('n.type', 'fhir_servers');
    $server_nodes = $query->execute()->fetchAll();
    foreach ($server_nodes as $titles) {
      $node_title[$titles->nid] = $titles->title;
    }
    return $titles->nid;
  }
}
