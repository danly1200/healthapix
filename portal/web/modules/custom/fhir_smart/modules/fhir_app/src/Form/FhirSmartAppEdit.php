<?php


namespace Drupal\fhir_app\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Class FhirSmartAppEdit.
 */
class FhirSmartAppEdit extends FormBase {

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
    return 'fhir_smart_app_edit';
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
    ];

    $form['fhir_server'] = [
      '#type' => 'select',
      '#title' => $this->t('FHIR Server'),
      '#options' => $this->getFhirs()['node_titles'],
      '#size' => 64,
      '#weight' => '0',
      '#default_value' => $service['fhir_server_id'],
      '#option_attributes' => $this->getFhirs()['node_version']
    ];
    $form['smart_app_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Smart Launch Url'),
      '#maxlength' => 64,
      '#size' => 64,
      '#weight' => '0',
      '#default_value' => $service['field_smart_launch_url'],
    ];
    $form['redirect_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Redirect Url'),
      '#maxlength' => 64,
      '#size' => 64,
      '#weight' => '0',
      '#default_value' => $service['field_redirect_url'],
    ];
    $form['app_type'] = [
      '#type' => 'select',
      '#title' => $this->t('App Type'),
      '#options' => [
        'Public' => $this->t('Public'),
        'Confidential' => $this->t('Confidential'),
      ],
      '#size' => 64,
      '#weight' => '0',
      '#default_value' => $service['field_app_type'],
    ];
    $form['scopes'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Scopes'),
      '#markup' => '<h3 class="scope-head">'.$this->t("Scopes").'</h3>',
      '#weight' => '0',
    ];
    $form['scopes']['scope_tabs'] = array(
      '#markup' =>'<ul class="scope-tabs-wrap"><li class="scope-tab-active scope-tab" data-panel="standard-scope-panel">Standard Scopes</li><li class="scope-tab" data-panel="user-scope-panel">User Scopes</li><li class="scope-tab" data-panel="patient-scope-panel">Patient Scopes</li></ul>',
      '#weight' => '0',
    );
    $form['scopes']['standard_scopes'] = [
      '#prefix'     => '<div class="standard-scope-panel scope-panel">',
      '#suffix'     => '</div>',
      '#type' => 'checkboxes',
      '#title' => $this->t('Standard Scopes'),
      '#options' => [
        'launch' => $this->t('Launch'),
        'profile' => $this->t('profile'),
        'open_id' => $this->t('open id'),
        'offline_access' => $this->t('offline Access'),
        'online_access' => $this->t('online Access'),
      ],
      '#weight' => '0',
    ];

    $form['scopes']['user_scopes'] = [
      '#prefix'     => '<div class="user-scope-panel scope-panel">',
      '#suffix'     => '</div>',
      '#type' => 'checkboxes',
      '#title' => $this->t('User Scopes'),
      '#options' => [
        'allergy_intolerance' => $this->t('Allergy Intolerance'),
        'appointment' => $this->t('Appointment'),
        'binay' => $this->t('binay'),
      ],
      '#weight' => '0',
    ];

    $form['scopes']['patient_scopes'] = [
      '#prefix'     => '<div class="patient-scope-panel scope-panel">',
      '#suffix'     => '</div>',
      '#type' => 'checkboxes',
      '#title' => $this->t('Patient Scopes'),
      '#options' => [
        'care_plan' => $this->t('Care Plan'),
        'condition' => $this->t('Condition'),
        'contract' => $this->t('Contract'),
      ],
      '#weight' => '0',
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];


    return $form;
  }

  /*
    *  Helper function to have fhir server titles.
    */

  public function getFhirs() {
    // query to get all the node of fhir content type
    $query = \Drupal::database()->select('node_field_data', 'n');
    $query->fields('n', ['title']);
    $query->fields('n', ['nid']);
    $query->fields('nff', ['field_fhir_version_autofill_target_id']);
    $query->join('node__field_fhir_version_autofill', 'nff', 'n.nid = nff.entity_id');
    $query->condition('n.status', '1');
    $query->condition('n.type', 'fhir_servers');
    $server_nodes = $query->execute()->fetchAll();

    $node_version = [];
    foreach ($server_nodes as $titles) {
      $node_title[$titles->nid] = $titles->title;
      $term = Term::load($titles->field_fhir_version_autofill_target_id);
      $name = $term->getName();
      $node_version[$titles->nid] = $name;
    }
    return ['node_titles' => $node_title, 'node_version' => $node_version];
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
    $app_id = \Drupal::routeMatch()->getRawParameter('app_id');
    $app_attribute['app_name'] = $form_state->getValue('app_name');
    $app_attribute['app_type'] = $form_state->getValue('app_type');
    $app_attribute['smart_app_url'] = $form_state->getValue('smart_app_url');
    $app_attribute['redirect_url'] = $form_state->getValue('redirect_url');
    $app_attribute['fhir_server'] = $form_state->getValue('fhir_server');
    $app_attribute['fhir_app_type'] = 'smart';
    $selected_products[] = $this->getFhirsProducts($fhir_server_id);
    $fhir_version = $this->getFhirVersion($fhir_server_id);
    $app_attribute['fhir_version'] = $fhir_version;
    $email_id = \Drupal::currentUser()->getEmail();
    //    $app_attribute['smart_app_url']= $form_state->getValue('smart_app_url');
    $service = \Drupal::service('fhir_smart.app')
      ->updateMyApp($app_id, $app_attribute, $selected_products, $email_id);
    // TODO: Implement submitForm() method.
  }
}
