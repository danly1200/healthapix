<?php


namespace Drupal\fhir_app\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\taxonomy\Entity\Term;

/**
 * Class FhirSAppEdit.
 */
class FhirAppEdit extends FormBase {

  /**
   * Returns a unique string identifying the form.
   *
   * The returned ID should be a unique string that can be a valid PHP function
   * name, since it's used in hook implementation names such as
   * hook_form_FORM_ID_alter().
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    // TODO: Implement getFormId() method.
    return 'fhir_app_edit';
  }

  /**
   * Form constructor.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The form structure.
   */
  public function buildForm(array $form, FormStateInterface $form_state, $app_id = NULL) {
    // TODO: Implement buildForm() method.
    $service = \Drupal::service('fhir_smart.app')
      ->fetchAppDetials($app_id);
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
      '#default_value' => $service['app_name'],
      '#disabled' => true
    ];
    $form['fhir_server'] = [
      '#type' => 'select',
      '#title' => $this->t('FHIR Server'),
      '#options' => $this->getFhirs()['node_titles'],
      '#size' => 64,
      '#weight' => '0',
      '#default_value' => $service['fhir_server_id'],
      '#option_attributes' => $this->getFhirs()['node_version'],
      '#fhir_server_name' => $this->getFhirs()['server_name'],
      '#disabled' => true,
      '#description' => 'Server cannot be changed, register a new app.'
    ];
    $form['redirect_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Redirect Url (Optional)'),
      '#maxlength' => 255,
      '#size' => 255,
      '#weight' => '0',
      '#default_value' => $service['field_redirect_url'],
    ];
    $form['jwks_uri'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Jwks-uri (Optional for non-backend services flow)'),
      '#maxlength' => 255,
      '#size' => 255,
      '#weight' => '0',
      '#default_value' => $service['field_jwks_uri'],
    ];

    if ($service['field_fhir_app_type'] == 'smart') {
      $form['smart_app_url'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Smart Launch Url'),
        '#maxlength' => 255,
        '#size' => 255,
        '#weight' => '0',
        '#default_value' => $service['field_smart_launch_url'],
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
        '#default_value' => $service['field_app_type'],
      ];
//      $form['scopes'] = [
//        '#type' => 'fieldset',
//        '#title' => $this->t('Scopes'),
//        '#markup' => '<h3 class="scope-head">' . $this->t("Scopes") . '</h3>',
//        '#weight' => '0',
//      ];
//      $form['scopes']['scope_tabs'] = [
//        '#markup' => '<ul class="scope-tabs-wrap"><li class="scope-tab-active scope-tab" data-panel="standard-scope-panel">Standard Scopes</li><li class="scope-tab" data-panel="user-scope-panel">User Scopes</li><li class="scope-tab" data-panel="patient-scope-panel">Patient Scopes</li></ul>',
//        '#weight' => '0',
//      ];
//      $form['scopes']['standard_scopes'] = [
//        '#prefix' => '<div class="standard-scope-panel scope-panel">',
//        '#suffix' => '</div>',
//        '#type' => 'checkboxes',
//        '#title' => $this->t('Standard Scopes'),
//        '#options' => [
//          'launch/patient' => $this->t('launch/patient'),
//          'fhirUser' => $this->t('fhirUser'),
//          'openid' => $this->t('openid'),
//          'offline_access' => $this->t('offline Access'),
//          'online_access' => $this->t('online Access'),
//        ],
//        '#default_value' => explode(" ", $service['field_standard_scopes']),
//        '#weight' => '0',
//      ];
//      $enabledScopes = \Drupal::service('ghc_app_scopes_configurations.default')
//        ->getScopes();
//
//      $form['scopes']['user_scopes'] = [
//        '#prefix' => '<div class="user-scope-panel scope-panel mdc-data-table">',
//        '#suffix' => '</div>',
//        '#type' => 'fieldset',
//        '#title' => $this->t('User Scopes'),
//        '#weight' => '0',
//      ];
//
//      $form['scopes']['patient_scopes'] = [
//        '#prefix' => '<div class="patient-scope-panel scope-panel">',
//        '#suffix' => '</div>',
//        '#type' => 'fieldset',
//        '#title' => $this->t('Patient Scopes'),
//        '#weight' => '0',
//      ];
//
//      $form['scopes']['user_scopes']['useroptions' . '_all'] = [
//        '#prefix' => '<div data-vname="DSTU2$#STU3$#R4" class="mdc-data-table__row user-field-op">',
//        '#suffix' => '</div>',
//        '#type' => 'checkboxes',
//        '#title' => $this->t('User/*'),
//        '#options' => [
//          'user/*.read' => 'User/*.read',
//          'user/*.write' => 'User/*.write',
//        ],
//        '#default_value' => explode(" ", isset($service['field_user_scopes']) ? $service['field_user_scopes'] : NULL),
//      ];
//
//      $form['scopes']['patient_scopes']['patientoptions' . '_all'] = [
//        '#prefix' => '<div data-vname="DSTU2$#STU3$#R4" class="mdc-data-table__row user-field-op">',
//        '#suffix' => '</div>',
//        '#type' => 'checkboxes',
//        '#title' => $this->t('Patient/*'),
//        '#options' => [
//          'patient/*.read' => 'Patient/*.read',
//          'patient/*.write' => 'Patient/*.write',
//        ],
//        '#default_value' => explode(" ", isset($service['field_patient_scopes']) ? $service['field_patient_scopes'] : NULL),
//      ];

//      foreach ($enabledScopes as $key => $enabledScope) {
//        $userScopes = [];
//        $patientScopes = [];
//        $v_name_arr = [];
//        if(is_array($enabledScope['api_version'])) {
//          foreach ($enabledScope['api_version'] as $ke => $tid ) {
//            $version = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($tid);
//            if($version) {
//              $v_name_arr[] = strtoupper($version->getName());
//            }
//          }
//          $v_name = implode("$#", $v_name_arr);
//        } else {
//          $version_id = $enabledScope['api_version'];
//          if($version_id) {
//            $version = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($version_id);
//            if($version) {
//              $v_name = strtoupper($version->getName());
//            }
//          }
//        }
//
////        $version_id = $enabledScope['api_version'];
////        $version = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($version_id);
////        $v_name = strtoupper($version->getName());
//
//        if ($enabledScope['patient_scope']['show_read_for_patient_scope'] == 1) {
//          $patientScopes[$enabledScope['patient_scope']['read_scope_value']] = $this->t('Read');
//        }
//        if ($enabledScope['patient_scope']['show_write_for_patient_scope'] == 1) {
//          $patientScopes[$enabledScope['patient_scope']['write_scope_value']] = $this->t('Write');
//        }
//        if ($enabledScope['user_scope']['show_read_for_user_scope'] == 1) {
//          $userScopes[$enabledScope['user_scope']['read_scope_value']] = $this->t('Read');
//        }
//        if ($enabledScope['user_scope']['show_write_for_user_scope'] == 1) {
//          $userScopes[$enabledScope['user_scope']['write_scope_value']] = $this->t('Write');
//        }
//
//        if (!empty($userScopes)) {
//          $form['scopes']['user_scopes']['useroptions' . $key] = [
//            '#prefix' => '<div data-vname="'.$v_name.'" class="mdc-data-table__row user-field-op">',
//            '#suffix' => '</div>',
//            '#type' => 'checkboxes',
//            '#title' => $this->t($enabledScope['resource']),
//            '#options' => $userScopes,
//            '#default_value' => explode(" ", isset($service['field_user_scopes']) ? $service['field_user_scopes'] : NULL),
//          ];
//        }
//        if (!empty($patientScopes)) {
//          $form['scopes']['patient_scopes']['patientoptions' . $key] = [
//            '#prefix' => '<div data-vname="'.$v_name.'" class="mdc-data-table__row user-field-op">',
//            '#suffix' => '</div>',
//            '#type' => 'checkboxes',
//            '#title' => $this->t($enabledScope['resource']),
//            '#options' => $patientScopes,
//            '#default_value' => explode(" ", isset($service['field_patient_scopes']) ? $service['field_patient_scopes'] : NULL),
//          ];
//        }
//
//      }

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
      if($service['fhir_server_id']) {
        $node = \Drupal::entityTypeManager()->getStorage('node')->load($service['fhir_server_id']);
        if($node ){
          $fhir_server_name = $node->get('field_fhir_api_products')->getValue()[0]['target_id'];
        }
      }
      $existing_scope_values = [];
      $scopes_list = getAppScopeList($fhir_server_name);
      if($service['field_standard_scopes']) {
        $standard_scope_values = explode(' ', $service['field_standard_scopes']);
        foreach($standard_scope_values as $each_standard_scope) {
          $existing_scope_values[$each_standard_scope] = $each_standard_scope;
        }
      }
      if($service['field_user_scopes']) {
        $user_scope_values = explode(' ', $service['field_user_scopes']);
        foreach($user_scope_values as $each_user_scope) {
          $existing_scope_values[$each_user_scope] = $each_user_scope;
        }
      }
      if($service['field_patient_scopes']) {
        $patient_scope_values = explode(' ', $service['field_patient_scopes']);
        foreach($patient_scope_values as $each_patient_scope) {
          $existing_scope_values[$each_patient_scope] = $each_patient_scope;
        }
      }


      $form['scopes_multi_select'] = [
        '#type' => 'select',
        '#multiple' => true,
        '#options' => $scopes_list,
        '#title' => 'Incoming APIs',
        '#theme' => 'dual_multi_select',
        '#attributes' => ['id' => 'scopes-multiselect-container'],
        '#default_value' => $existing_scope_values,
      ];
    }

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

  public function validateForm(array &$form, FormStateInterface $form_state) {

    $fhir_app_type = $form_state->getValue('fhir_app_type');
    $app_name = $form_state->getValue('app_name');

    if ($app_name) {
      if (preg_match('/[^a-z0-9 _-]+/i', $app_name)) {
        $form_state->setErrorByName('app_name', t(' Special Characters are not allowed for App Name.'));
      }
    }

    if ($fhir_app_type == 'smart') {
      $smart_app_url = $form_state->getValue('smart_app_url');
      if (filter_var($smart_app_url, FILTER_VALIDATE_URL) === FALSE) {
        $form_state->setErrorByName('smart_app_url', t('Smart Launch Url is not valid.'));
      }
    }
    $fhir_server_id  = $form_state->getValue('fhir_server');
    $node = \Drupal::entityTypeManager()->getStorage('node')->load($fhir_server_id);
    $fhir_server_name = $node->get('field_fhir_api_products')->getValue()[0]['target_id'];
    $to = getProductOwnerEmail($fhir_server_name);
    if(!$to) {
      $form_state->setErrorByName('smart_app_url', t('Approver email not configured for API product.'));
    }
    $scopes = $form_state->getValue('scopes_multi_select');

    if(empty($scopes) && $fhir_app_type == 'smart') {
      $form_state->setErrorByName('scopes_multi_select', t('Scopes field is a required field.'));
    }
    $errors = $form_state->getErrors();
    if(!$errors) {
      // check if we are able to get the api key for the app approval workflow
      getxapikey();
    }
    parent::validateForm($form, $form_state); // TODO: Change the autogenerated stub
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $fhir_server_id = $form_state->getValue('fhir_server');
    $jwks_uri = $form_state->getValue('jwks_uri');

    $app_id = \Drupal::routeMatch()->getRawParameter('app_id');
    $app_attribute['app_name'] = $form_state->getValue('app_name');
    $app_attribute['app_type'] = $form_state->getValue('app_type');

    $app_attribute['redirect_url'] = $form_state->getValue('redirect_url');
    $app_attribute['fhir_server_id'] = $form_state->getValue('fhir_server');

    $fhir_server_name = \Drupal::service('fhir_smart.app')->getFhirServerName($fhir_server_id);
    $app_attribute['fhir_server_name'] = $fhir_server_name;

    if ($form_state->getValue('smart_app_url')) {
      $app_attribute['smart_app_url'] = $form_state->getValue('smart_app_url');
      $app_attribute['fhir_app_type'] = 'smart';
    }
    else {
      $app_attribute['smart_app_url'] = "";
      $app_attribute['fhir_app_type'] = 'normal';
    }

    $selected_products[] = $this->getFhirsProducts($fhir_server_id);
    $fhir_version = $this->getFhirVersion($fhir_server_id);
    $app_attribute['fhir_version'] = $fhir_version;
    $email_id = \Drupal::currentUser()->getEmail();

    $standard_scopes = $form_state->getValue('standard_scopes');

//    $enabledScopes = \Drupal::service('ghc_app_scopes_configurations.default')
//      ->getScopes();
//    $user_scopes_op = [];
//    $patient_scopes_op =[];


//    foreach ($enabledScopes as $key => $enabledScope) {
//      $term_values = [
//        'vid' => 'api_version',
//        'name'=> $fhir_version,
//      ];
//      $fhir_term_res = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties($term_values);
//      foreach ($fhir_term_res as $term) {
//        $values = [
//          'type' => 'smartdocs',
//          'field_api_version'=> $term->id(),
//        ];
//        $fhir_res = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties($values);
//        foreach ($fhir_res as $res_node) {
//          $title = preg_replace('/\s/', '', $res_node->getTitle());
//          $resources[] = $title;
//        }
//      }
//      if(!empty($resources)) {
//        if(in_array($key, $resources)) {
//          $user_scopes_op[] = $form_state->getValue('useroptions' . $key);
//          $patient_scopes_op[] = $form_state->getValue('patientoptions' . $key);
//        }
//      }
//    }

//    $new_key = '_all';
//    $user_scopes_op[] = $form_state->getValue('useroptions' . $new_key);
//    $patient_scopes_op[] = $form_state->getValue('patientoptions' . $new_key);

//    if(!empty($user_scopes_op)) {
//      foreach ($user_scopes_op as $user_scopes_op_val) {
//        if(!empty($user_scopes_op_val)) {
//          foreach ($user_scopes_op_val as $k => $value) {
//            if ($value !== 0) {
//              $user_scope_list[] = $value;
//            }
//          }
//        }
//      }
//    }
//    if(!empty($patient_scopes_op)) {
//      foreach ($patient_scopes_op as $patient_scopes_op_val) {
//        if(!empty($patient_scopes_op_val)) {
//          foreach ($patient_scopes_op_val as $ke => $value) {
//            if ($value !== 0) {
//              $patient_scope_list[] = $value;
//            }
//          }
//        }
//      }
//    }

    // Get the list of scopes
//    foreach ($standard_scopes as $standard_scope) {
//      if ($standard_scope !== 0) {
//        $standard_scope_list[] = $standard_scope;
//      }
//    }
//    $checked_standard_scopes = implode(" ", $standard_scope_list);
//    $checked_user_scopes = implode(" ", $user_scope_list);
//    $checked_patient_scopes = implode(" ", $patient_scope_list);
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
    $service = \Drupal::service('fhir_smart.app')
      ->updateMyApp($app_id, $app_attribute, $selected_products, $email_id, $checked_standard_scopes, $checked_user_scopes, $checked_patient_scopes, $jwks_uri);
    drupal_set_message(t('Your @appname app is updated successfully.', ['@appname' => $app_attribute['app_name']]), 'status');
    $fhir_page = new RedirectResponse(URL::fromUserInput('/user/my-apps#'.$app_attribute['fhir_app_type'])
      ->toString());
    $fhir_page->send();
  }
}

