<?php

namespace Drupal\fhir_app\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\taxonomy\Entity\Term;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Url;


/**
 * Class FhirSmartApp.
 */
class FhirSmartApp extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'fhir_smart_app';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // MAking this api call to check if the apix key is available
    getxapikey();

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
      '#options' => $this->getFhirs()['node_titles'],
      '#size' => 64,
      '#weight' => '0',
      '#default_value' => $this->getFhirsDefault(),
      '#option_attributes'=> $this->getFhirs()['node_version'],
      '#fhir_server_name' => $this->getFhirs()['server_name'],

    ];

    $form['smart_app_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Smart Launch Url'),
      '#maxlength' => 255,
      '#size' => 255,
      '#weight' => '0',
      '#required' => TRUE,
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
    $form['app_type'] = [
      '#type' => 'select',
      '#title' => $this->t('App Type (Optional)'),
      '#options' => [
        'public' => $this->t('Public'),
        'confidential' => $this->t('Confidential'),
      ],
      '#size' => 64,
      '#weight' => '0',
      '#default_value' => 'public',

    ];
//    $form['scopes'] = [
//      '#type' => 'fieldset',
//      '#title' => $this->t('Scopes (Optional)'),
//      '#markup' => '<h3 class="scope-head">' . $this->t("Scopes") . '</h3>',
//      '#weight' => '0',
//    ];
//
//    $form['scopes']['scope_tabs'] = [
//      '#markup' => '<ul class="scope-tabs-wrap"><li class="scope-tab-active scope-tab" data-panel="standard-scope-panel">Standard Scopes</li><li class="scope-tab" data-panel="user-scope-panel">User Scopes</li><li class="scope-tab" data-panel="patient-scope-panel">Patient Scopes</li></ul>',
//      '#weight' => '0',
//    ];
//    $form['scopes']['standard_scopes'] = [
//      '#prefix' => '<div class="standard-scope-panel scope-panel">',
//      '#suffix' => '</div>',
//      '#type' => 'checkboxes',
//      '#title' => $this->t('Standard Scopes'),
//      '#options' => [
//        'launch/patient' => $this->t('launch/patient'),
//        'fhirUser' => $this->t('fhirUser'),
//        'openid' => $this->t('openid'),
//        'offline_access' => $this->t('Offline Access'),
//        'online_access' => $this->t('Online Access'),
//      ],
//      '#default_value' => ['launch/patient', 'fhirUser', 'openid', 'online_access'],
//      '#weight' => '0',
//    ];
//    $enabledScopes = \Drupal::service('ghc_app_scopes_configurations.default')
//      ->getScopes();
//
//    $form['scopes']['user_scopes'] = [
//      '#prefix' => '<div class="user-scope-panel scope-panel mdc-data-table">',
//      '#suffix' => '</div>',
//      '#type' => 'fieldset',
//      '#title' => $this->t('User Scopes'),
//      '#weight' => '0',
//    ];
//
//
//    $form['scopes']['patient_scopes'] = [
//      '#prefix' => '<div class="patient-scope-panel scope-panel">',
//      '#suffix' => '</div>',
//      '#type' => 'fieldset',
//      '#title' => $this->t('Patient Scopes'),
//      '#weight' => '0',
//    ];
//
//    $form['scopes']['user_scopes']['useroptions' . '_all'] = [
//      '#prefix' => '<div data-vname="DSTU2$#STU3$#R4" class="mdc-data-table__row user-field-op">',
//      '#suffix' => '</div>',
//      '#type' => 'checkboxes',
//      '#title' => $this->t('User/*'),
//      '#options' => [
//        'user/*.read' => 'User/*.read',
//        'user/*.write' => 'User/*.write',
//      ],
//    ];
//
//    $form['scopes']['patient_scopes']['patientoptions' . '_all'] = [
//      '#prefix' => '<div data-vname="DSTU2$#STU3$#R4" class="mdc-data-table__row user-field-op">',
//      '#suffix' => '</div>',
//      '#type' => 'checkboxes',
//      '#title' => $this->t('Patient/*'),
//      '#options' => [
//        'patient/*.read' => 'Patient/*.read',
//        'patient/*.write' => 'Patient/*.write',
//      ],
//    ];

    $form['radio_for_multi_select'] = [
      '#type' => 'radios',
      '#options' => array(
        'standard_scopes' => $this
          ->t('Standard Scopes'),
        'user_scopes' => $this
          ->t('User Scopes'),
        'patient_scopes' => $this
          ->t('Patient Scopes'),
        'system_scopes' => $this
          ->t('System Scopes'),
      ),
      '#title' => 'Smart Scopes',
    ];
    if($this->getFhirsDefault()) {
      $node = \Drupal::entityTypeManager()->getStorage('node')->load($this->getFhirsDefault());
      $fhir_server_name = $node->get('field_fhir_api_products')->getValue()[0]['target_id'];
    }
    $scopes_list = getAppScopeList($fhir_server_name);
    $form['scopes_multi_select'] = [
      '#type' => 'select',
      '#multiple' => true,
      '#options' => $scopes_list,
      '#title' => 'Incoming APIs',
      '#theme' => 'dual_multi_select',
      '#attributes' => ['id' => 'scopes-multiselect-container'],
      '#validated' => TRUE,
    ];



//    foreach ($enabledScopes as $key => $enabledScope) {
//      $userScopes = [];
//      $patientScopes = [];
//      $v_name_arr = [];
//      if(is_array($enabledScope['api_version'])) {
//        foreach ($enabledScope['api_version'] as $ke => $tid ) {
//          $version = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($tid);
//          if($version) {
//            $v_name_arr[] = strtoupper($version->getName());
//          }
//        }
//        $v_name = implode("$#", $v_name_arr);
//      } else {
//        $version_id = $enabledScope['api_version'];
//        if($version_id) {
//          $version = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($version_id);
//          if($version) {
//            $v_name = strtoupper($version->getName());
//          }
//        }
//      }
//      if ($enabledScope['patient_scope']['show_read_for_patient_scope'] == 1) {
//        $patientScopes[$enabledScope['patient_scope']['read_scope_value']] = $this->t('Read');
//      }
//      if ($enabledScope['patient_scope']['show_write_for_patient_scope'] == 1) {
//        $patientScopes[$enabledScope['patient_scope']['write_scope_value']] = $this->t('Write');
//      }
//      if ($enabledScope['user_scope']['show_read_for_user_scope'] == 1) {
//        $userScopes[$enabledScope['user_scope']['read_scope_value']] = $this->t('Read');
//      }
//      if ($enabledScope['user_scope']['show_write_for_user_scope'] == 1) {
//        $userScopes[$enabledScope['user_scope']['write_scope_value']] = $this->t('Write');
//      }
//
//      if (!empty($userScopes)) {
//        $form['scopes']['user_scopes']['useroptions' . $key] = [
//          '#prefix' => '<div data-vname="'.$v_name.'" class="mdc-data-table__row user-field-op">',
//          '#suffix' => '</div>',
//          '#type' => 'checkboxes',
//          '#title' => $this->t($enabledScope['resource']),
//          '#options' => $userScopes,
//        ];
//      }
//      if (!empty($patientScopes)) {
//        $form['scopes']['patient_scopes']['patientoptions' . $key] = [
//          '#prefix' => '<div data-vname="'.$v_name.'" class="mdc-data-table__row user-field-op">',
//          '#suffix' => '</div>',
//          '#type' => 'checkboxes',
//          '#title' => $this->t($enabledScope['resource']),
//          '#options' => $patientScopes,
//          //  '#default_value' => 'Array',
//        ];
//      }
//    }

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
    $form['#after_build'] = ['fhir_create_app_after_build'];
    $form['#attached']['library'][] = 'fhir_app/fhir_apps.app_customizations';
    return $form;
  }




  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    $smart_app_url = $form_state->getValue('smart_app_url');
    $app_name = $form_state->getValue('app_name');
    if ($app_name) {
      if (preg_match('/[^a-z0-9 _-]+/i', $app_name)) {
        $form_state->setErrorByName('app_name', t(' Special Characters are not allowed for App Name'));
      }
    }
    if (filter_var($smart_app_url, FILTER_VALIDATE_URL) === FALSE) {
      $form_state->setErrorByName('smart_app_url', t('Smart Launch Url is not valid.'));
    }
    $fhir_server_id  = $form_state->getValue('fhir_server');
    $node = \Drupal::entityTypeManager()->getStorage('node')->load($fhir_server_id);
    $fhir_server_name = $node->get('field_fhir_api_products')->getValue()[0]['target_id'];
    $to = getProductOwnerEmail($fhir_server_name);
    if(!$to) {
      $form_state->setErrorByName('smart_app_url', t('Approver email not configured for API product.'));
    }
    $scopes = $form_state->getValue('scopes_multi_select');

    if(empty($scopes)) {
      $form_state->setErrorByName('scopes_multi_select', t('Scopes field is a required field.'));
    }
    // Add the condition to check for the existing app names
    $existing_app_names = getAppNames();
    if(in_array(strtolower($app_name), $existing_app_names)) {
      $form_state->setErrorByName('app_name', t('App name already exists.'));
    }
    $errors = $form_state->getErrors();
    if(!$errors) {
      // check if we are able to get the api key for the app approval workflow
      getxapikey();
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Current user Email id.
    $email_id = \Drupal::currentUser()->getEmail();

    // App details.
    $jwks_uri = $form_state->getValue('jwks_uri');
    $app_name = $form_state->getValue('app_name');
    $display_name = $form_state->getValue('app_name');
    $fhir_server_id = $form_state->getValue('fhir_server');
    $smart_app_url = $form_state->getValue('smart_app_url');
    $redirect_url = $form_state->getValue('redirect_url');
    $app_type = $form_state->getValue('app_type');
    $selected_products[] = $this->getFhirsProducts($fhir_server_id);
    $fhir_server_name = \Drupal::service('fhir_smart.app')
      ->getFhirServerName($fhir_server_id);


//    $standard_scopes = $form_state->getValue('standard_scopes');
//    //$user_scopes = $form_state->getValue('user_scopes');
//    // $patient_scopes = $form_state->getValue('patient_scopes');
//    $enabledScopes = \Drupal::service('ghc_app_scopes_configurations.default')
//      ->getScopes();
//    $user_scopes_op = [];
//    $patient_scopes_op = [];
//    foreach ($enabledScopes as $key => $enabledScope) {
//      $user_scopes_op[] = $form_state->getValue('useroptions' . $key);
//      $patient_scopes_op[] = $form_state->getValue('patientoptions' . $key);
//    }

//    $new_key = '_all';
//    $user_scopes_op[] = $form_state->getValue('useroptions' . $new_key);
//    $patient_scopes_op[] = $form_state->getValue('patientoptions' . $new_key);
//
//    foreach ($user_scopes_op as $user_scopes_op_val) {
//      foreach ($user_scopes_op_val as $k => $value) {
//        if ($value !== 0) {
//          $user_scope_list[] = $value;
//        }
//      }
//    }
//    foreach ($patient_scopes_op as $patient_scopes_op_val) {
//      foreach ($patient_scopes_op_val as $ke => $value) {
//        if ($value !== 0) {
//          $patient_scope_list[] = $value;
//        }
//      }
//
//    }
//    $checked_standard_scopes ='';
//    // Get the list of scopes
//    foreach ($standard_scopes as $standard_scope) {
//      if ($standard_scope !== 0) {
//        $standard_scope_list[] = $standard_scope;
//      }
//    }
    $user_scope_list = [];
    $patient_scope_list = [];
    $standard_scope_list = [];
    $all_scopes = $form_state->getValue('scopes_multi_select');
    foreach($all_scopes as $each_all_scope) {
      if(substr($each_all_scope, 0,5) == 'user/') {
        $user_scope_list[] = $each_all_scope;
      } else if(substr($each_all_scope,0,8) == 'patient/') {
        $patient_scope_list[] = $each_all_scope;
      } else {
        $standard_scope_list[] = $each_all_scope;
      }
    }

    $checked_standard_scopes = implode(" ", $standard_scope_list);
    $checked_user_scopes = implode(" ", $user_scope_list);
    $checked_patient_scopes = implode(" ", $patient_scope_list);
    $fhir_version = $this->getFhirVersion($fhir_server_id);

    // get the value from the query param.
    $fhir_app_type = 'smart';
    $service = \Drupal::service('fhir_smart.app')
      ->createMyApp($email_id, $app_name, $display_name, $selected_products,
        $smart_app_url, $redirect_url, $app_type, $fhir_app_type, $fhir_version, $fhir_server_id, $checked_standard_scopes, $checked_user_scopes, $checked_patient_scopes,$fhir_server_name, $jwks_uri);

    drupal_set_message(t('Your @appname app is created successfully.', ['@appname' => $app_name]), 'status');

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
    $query->fields('api', ['field_fhir_api_products_target_id']);
    $query->fields('n', ['nid']);
    $query->fields('nff', ['field_fhir_version_autofill_target_id']);
    $query->join('node__field_fhir_version_autofill', 'nff', 'n.nid = nff.entity_id');
    $query->join('node__field_fhir_api_products', 'api', 'n.nid = api.entity_id');
    $query->condition('n.status', '1');
    $query->condition('n.type', 'fhir_servers');
    $server_nodes = $query->execute()->fetchAll();

    $node_version = [];
    $fhir_server_name = [];
    foreach ($server_nodes as $titles) {
      $node_title[$titles->nid] = $titles->title;
      $term = Term::load($titles->field_fhir_version_autofill_target_id);
      $name = $term->getName();
      $node_version[$titles->nid] = $name;
      $fhir_server_name[$titles->nid] = $titles->field_fhir_api_products_target_id;
    }

    return ['node_titles' => $node_title, 'node_version' => $node_version, 'server_name' => $fhir_server_name];
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
}

