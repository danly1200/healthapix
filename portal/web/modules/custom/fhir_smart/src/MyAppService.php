<?php

namespace Drupal\fhir_smart;

use Drupal\user\Entity\User;
use Apigee\Edge\Api\Management\Controller\DeveloperAppCredentialController;
use Drupal\Core\Url;


/**
 * Class MyAppService.
 */
class MyAppService {

  /**
   * Constructs a new MyAppService object.
   */
  public function __construct() {

  }

  /**
   *  delete myapp.
   */
  public function deleteMyApp($app_id) {
    $entity = \Drupal::entityTypeManager()
      ->getStorage("developer_app")
      ->load($app_id);
    $entity->delete();
  }

  /**
   *  create myapp.
   */
  public function createMyApp($email_id, $app_name, $display_name, $selected_products, $smart_app_url, $redirect_url, $app_type, $fhir_app_type, $fhir_version, $fhir_server_id, $checked_standard_scopes, $checked_user_scopes, $checked_patient_scopes,$fhir_server_name, $jwks_uri) {

    $requested_scopes = !empty($checked_user_scopes)?$checked_user_scopes . " ":"" ;
    $requested_scopes .= !empty($checked_patient_scopes)?$checked_patient_scopes . " ":"";
    $requested_scopes .= !empty($checked_standard_scopes)?$checked_standard_scopes:"";
    $userId = \Drupal::currentUser()->id();
    $userDetails = User::load($userId);
    $developer_app = \Drupal::entityTypeManager()
      ->getStorage('developer_app')
      ->create(
        [
          'name' => $app_name,
          'displayName' => $display_name,
          'field_smart_launch_url' => $smart_app_url,
          'field_redirect_url' => $redirect_url,
          'field_fhir_version' => $fhir_version,
          'field_app_type' => $app_type,
          'field_fhir_app_type' => $fhir_app_type,
          'field_fhir_server_id' => $fhir_server_id,
          'field_standard_scopes' => $checked_standard_scopes,
          'field_user_scopes' => $checked_user_scopes,
          'field_patient_scopes' => $checked_patient_scopes,
          'field_fhir_server' => $fhir_server_name,
          'field_dataset' => ' ',
          'field_fhirstore' => ' ',
          'field_location' => ' ',
          'field_project' => ' ',
          'field_jwks_uri' => $jwks_uri,
          'field_approved_scopes' => ' NA ',
          'field_requested_scopes' => $requested_scopes,
        ]
      );

    $developer_app->setAppOwner($email_id);
    $developer_app->save();

    // Associate the product to the app.
    $service = new DeveloperAppCredentialController(\Drupal::service('apigee_edge.sdk_connector')
      ->getOrganization(), $developer_app->getDeveloperId(), $developer_app->getName(), \Drupal::service('apigee_edge.sdk_connector')
      ->getClient());
    /** @var \Apigee\Edge\Api\Management\Entity\AppCredential[] $credentials */
    $credentials = $developer_app->getCredentials();
    /** @var \Apigee\Edge\Api\Management\Entity\AppCredential $credential */
    $credential = reset($credentials);
    $credential_lifetime = \Drupal::configFactory()
      ->get('apigee_edge.developer_app_settings')
      ->get('credential_lifetime');

    $node = \Drupal::entityTypeManager()->getStorage('node')->load($fhir_server_id);
    $fhir_server_name = $node->get('field_fhir_api_products')->getValue()[0]['target_id'];
    $app_client_id = $credential->getConsumerKey();

//    if($fhir_app_type == 'smart'){
      $user = \Drupal::currentUser();
      $user_email = $user->getEmail();

      // Get approval links
      $links =  getApproveRejectLinks(
        $developer_app->getName(),
        $credential->getConsumerKey(),
        $developer_app->getAppId(),
        'create',
        $user_email,
        $fhir_server_name,
        $requested_scopes,
        $fhir_app_type);
      // Send approval mail to the product owner
      $mailManager = \Drupal::service('plugin.manager.mail');

      $module = 'fhir_app';
      $key = 'create_apigee_app';

      // Get the product owner attribute
      $to = getProductOwnerEmail($fhir_server_name);
    if($fhir_app_type == 'smart'){
      $params['body'] = "Submitter:

App Developer email: ". $user_email ."
App Developer name: ". $userDetails->get('first_name')->getValue()[0]['value'] . "  ". $userDetails->get('last_name')->getValue()[0]['value'] ."

Approval request:

App Name: ". $developer_app->getName() ."
FHIR APIs : ". $fhir_server_name ."
Requested Scope: ". $requested_scopes ."

To Approve this app, Click here - " . $links->approve . "

To Reject this app, Click here - " .  $links->revoke . "


Regards,
HealthAPix Support Team"; // email message body
    } else {
      $params['body'] = "Submitter:

App Developer email: ". $user_email ."
App Developer name: ". $userDetails->get('first_name')->getValue()[0]['value'] . "  ". $userDetails->get('last_name')->getValue()[0]['value'] ."

Approval request:

App Name: ". $developer_app->getName() ."
FHIR APIs : ". $fhir_server_name ."

To Approve this app, Click here - " . $links->approve . "

To Reject this app, Click here - " .  $links->revoke . "


Regards,
HealthAPix Support Team"; // email message body
    }


      $params['subject'] = 'New App '. $developer_app->getName() . ' needs approval'; // email subject
      $langcode = \Drupal::currentUser()->getPreferredLangcode();
      $send = true;

      $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
      if ($result['result'] !== true) {
        \Drupal::messenger()->addMessage(t('There was a problem sending your message and it was not sent.'), 'error');
      }
      else {
        \Drupal::messenger()->addMessage(t('Mail has been sent successfully.'));
      }
//    }


    if ($credential_lifetime === 0) {
      $service->addProducts($credential->id(), $selected_products);
    }
    else {
      $service->delete($credential->id());
      // The value of -1 indicates no set expiry. But the value of 0 is not
      // acceptable by the server (InvalidValueForExpiresIn).
      $service->generate($selected_products, $developer_app->getAttributes(), $developer_app->getCallbackUrl(), [], $credential_lifetime * 86400000);
    }
  }

  /**
   *  Update myapp.
   */
  public function updateMyApp($app_id, $app_ttributes, $selectedproducts, $email_id, $checked_standard_scopes, $checked_user_scopes, $checked_patient_scopes, $jwks_uri) {
    $entity = \Drupal::entityTypeManager()
      ->getStorage("developer_app")
      ->load($app_id);
    $userId = \Drupal::currentUser()->id();
    $userDetails = User::load($userId);
    $app_type = $entity->get('field_fhir_app_type')->getValue()[0]['value'];

    if($app_type == 'smart') {
      $existing_approved_scopes = getApprovedScopes($entity->getName(), $entity->getDeveloperId());

      $requested_scopes = !empty( $checked_user_scopes )? $checked_user_scopes . " ":"" ;
      $requested_scopes .= !empty( $checked_patient_scopes )? $checked_patient_scopes . " ":"";
      $requested_scopes .= !empty( $checked_standard_scopes )? $checked_standard_scopes:"";
      $send_approval_mail = TRUE;
      if(trim($existing_approved_scopes) != 'NA') {
        $requested_scope_array = explode(' ', $requested_scopes);
        $existing_approved_scope_array = explode(' ', $existing_approved_scopes);
        $new_scopes = array_diff($requested_scope_array, $existing_approved_scope_array);
        if(empty($new_scopes)) {
          $send_approval_mail = FALSE;
        }
      }
      // if scopes are removed, then update the patient, user and the standard scopes
      $scopes_removed_flag = FALSE;
      if(trim($existing_approved_scopes) != 'NA') {
        $requested_scope_array = explode(' ', $requested_scopes);
        $existing_approved_scope_array = explode(' ', $existing_approved_scopes);
        $removed_scopes = array_diff($existing_approved_scope_array, $requested_scope_array);
        if(!empty($removed_scopes) && !$send_approval_mail) {
          $scopes_removed_flag = TRUE;
        }
      }
    }


    $entity->set('displayName', $app_ttributes['app_name']);
    $entity->set('field_app_type', $app_ttributes['app_type']);
    $entity->set('field_redirect_url', $app_ttributes['redirect_url']);
    $entity->set('field_fhir_version', $app_ttributes['fhir_version']);
    //    $entity->set('field_fhir_app_type', $app_ttributes['fhir_app_type']);
    $entity->set('field_fhir_server_id', $app_ttributes['fhir_server_id']);
    // scopes to update for apigee end.
    if($scopes_removed_flag && $app_type == 'smart') {
       $entity->set('field_standard_scopes', $checked_standard_scopes);
       $entity->set('field_user_scopes', $checked_user_scopes);
       $entity->set('field_patient_scopes', $checked_patient_scopes);
       if($existing_approved_scopes) {
         $existing_approved_scope_array = explode(' ', $existing_approved_scopes);
         $new_approved_scopes = array_diff($existing_approved_scope_array, $removed_scopes);
         $entity->set('field_approved_scopes',implode(' ', $new_approved_scopes));
       }
    }

    $entity->set('field_fhir_server', $app_ttributes['fhir_server_name']);
    $entity->set('field_dataset', '');
    $entity->set('field_fhirstore', '');
    $entity->set('field_location', '');
    $entity->set('field_project', '');
    $entity->set('field_jwks_uri', $jwks_uri);
    $entity->set('field_requested_scopes', $requested_scopes);
    if ($app_ttributes['smart_app_url']) {
      $entity->set('field_smart_launch_url', $app_ttributes['smart_app_url']);
    }
    $entity->setAppOwner($email_id);
    $entity->save();

    // unset the existing the product for the app.
    //_unset_products($app_id); // commenting this code as we need not delete the api product everytime the app is updated

    // Associate the product to the app.
    $service = new DeveloperAppCredentialController(\Drupal::service('apigee_edge.sdk_connector')
      ->getOrganization(), $entity->getDeveloperId(), $entity->getName(), \Drupal::service('apigee_edge.sdk_connector')
      ->getClient());
    /** @var \Apigee\Edge\Api\Management\Entity\AppCredential[] $credentials */
    $credentials = $entity->getCredentials();
    /** @var \Apigee\Edge\Api\Management\Entity\AppCredential $credential */
    $credential = reset($credentials);

    $credential_lifetime = \Drupal::configFactory()
      ->get('apigee_edge.developer_app_settings')
      ->get('credential_lifetime');
    if($app_type == 'smart') {
      if ($send_approval_mail) {
        $node = \Drupal::entityTypeManager()->getStorage('node')->load($app_ttributes['fhir_server_id']);
        $fhir_server_name = $node->get('field_fhir_api_products')->getValue()[0]['target_id'];
        $app_client_id = $credential->getConsumerKey();
        $user = \Drupal::currentUser();
        $user_email = $user->getEmail();
        // Get approval links
        $links =  getApproveRejectLinks(
          $entity->getName(),
          $credential->getConsumerKey(),
          $entity->getAppId(),
          'update',
          $user_email,
          $fhir_server_name,
          $requested_scopes,
          $app_ttributes['fhir_app_type']);
        // Send approval mail to the product owner
        $mailManager = \Drupal::service('plugin.manager.mail');

        $module = 'fhir_app';
        $key = 'update_apigee_app';

        // Get the product owner attribute
        $to = getProductOwnerEmail($fhir_server_name);
        if($credential_lifetime == 0) {
          $params['body'] = "Submitter:

App Developer email: ".$user_email ."
App Developer name: ". $userDetails->get('first_name')->getValue()[0]['value'] . "  ". $userDetails->get('last_name')->getValue()[0]['value'] ."

Approval request:

App Name: ". $entity->getName() . "
FHIR APIs : ". $app_ttributes['fhir_server_name'] ."
Requested Scope: ". $requested_scopes ."
Previously Approved Scope: ". $existing_approved_scopes ."


To Approve this app, Click here - " . $links->approve . "

To Reject this app, Click here - " .  $links->revoke . "

To Cancel this app, Click here - " .  $links->cancel . "

Regards,
HealthAPix Support Team"; // email message body

        } else {
          $params['body'] = "Submitter:

App Developer email: ". $entity->getDeveloperId() ."
App Developer name: {name}

Approval request:

App Name: ". $entity->getName() . "
FHIR APIs : ". $app_ttributes['fhir_server_name'] ."
Requested Scope: ". $requested_scopes ."
Previously Approved Scope: ". $existing_approved_scopes ."

To Approve this app, Click here - " . $links->approve . "

To Reject this app, Click here - " .  $links->revoke . "


Regards,
HealthAPix Support Team"; // email message body
        }

        $params['subject'] = 'Update App '. $entity->getName() . ' needs approval'; // email subject
        $langcode = \Drupal::currentUser()->getPreferredLangcode();
        $send = true;

        $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
        if ($result['result'] !== true) {
          \Drupal::messenger()->addMessage(t('There was a problem sending your message and it was not sent.'), 'error');
        }
        else {
          \Drupal::messenger()->addMessage(t('Mail has been sent successfully.'));
        }
      }
    }

    if ($credential_lifetime == 0) {
      // $service->addProducts($credential->id(), $selectedproducts); // commenting this code as removal if the api product is commented
    }
    else {
      // Generate new credentials only when the scopes are updated. If the scopes are not added, then do not generate the credentials.
      if($send_approval_mail) {
        $service->delete($credential->id());
        // The value of -1 indicates no set expiry. But the value of 0 is not
        // acceptable by the server (InvalidValueForExpiresIn).
        $service->generate($selectedproducts, $entity->getAttributes(), $entity->getCallbackUrl(), [], $credential_lifetime * 86400000);
      }
    }
  }


  public function listingMyApp($userId) {
    $userDetails = User::load($userId);
    $developerId = $userDetails->get('apigee_edge_developer_id')->value;
    $storageDeveloperApp = \Drupal::entityTypeManager()
      ->getStorage('developer_app');
    $query = $storageDeveloperApp->getQuery()
      ->condition('developerId', $developerId);
    $entityIds = $query->execute();
    $developerApps = $storageDeveloperApp->loadMultiple($entityIds);
    return $developerApps;
  }

  /**
   *  Fetch Normal/Smart App Details wrt app_id .
   */
  public function fetchAppDetials($app_id) {

    $entity = \Drupal::entityTypeManager()
      ->getStorage("developer_app")
      ->load($app_id);
    $app_details['app_name'] = $entity->get('displayName')
      ->get(0)
      ->getValue()['value'];

    // Get app type public or confidentials.
    if ($entity->get('field_app_type')->get(0)) {
      $app_details['field_app_type'] = $entity->get('field_app_type')
        ->get(0)
        ->getValue()['value'];
    }

    // Get app type normal or smart.
    if ($entity->get('field_fhir_app_type')
        ->get(0)
        ->getValue()['value'] == 'smart') {
      $app_details['field_smart_launch_url'] = $entity->get('field_smart_launch_url')->get(0) ? $entity->get('field_smart_launch_url')->get(0)->getValue()['value'] : '';
    }

    // Get redirect url
    if ($entity->get('field_jwks_uri')->get(0)) {
      $app_details['field_jwks_uri'] = $entity->get('field_jwks_uri')
        ->get(0)
        ->getValue()['value'];
    }

    // Get redirect url
    if ($entity->get('field_redirect_url')->get(0)) {
      $app_details['field_redirect_url'] = $entity->get('field_redirect_url')
        ->get(0)
        ->getValue()['value'];
    }

    if ($entity->get('field_fhir_version')->get(0)) {
      $app_details['field_fhir_version'] = $entity->get('field_fhir_version')
        ->get(0)
        ->getValue()['value'];
    }

    if ($entity->get('field_fhir_app_type')->get(0)) {
      $app_details['field_fhir_app_type'] = $entity->get('field_fhir_app_type')
        ->get(0)
        ->getValue()['value'];
    }
    if ($entity->get('field_fhir_server_id')->get(0)) {
      $app_details['fhir_server_id'] = $entity->get('field_fhir_server_id')
        ->get(0)
        ->getValue()['value'];
    }

    // Scopes for normal and smart app
    if ($entity->get('field_standard_scopes')->get(0)) {
      $app_details['field_standard_scopes'] = $entity->get('field_standard_scopes')
        ->get(0)
        ->getValue()['value'];
    }
    if ($entity->get('field_patient_scopes')->get(0)) {
      $app_details['field_patient_scopes'] = $entity->get('field_patient_scopes')
        ->get(0)
        ->getValue()['value'];
    }
    if ($entity->get('field_user_scopes')->get(0)) {
      $app_details['field_user_scopes'] = $entity->get('field_user_scopes')
        ->get(0)
        ->getValue()['value'];
    }
    return $app_details;

  }

    /*
   * Funtion to get fhirservername.
   */

    public function getFhirServerName($nid)
    {
        $query = \Drupal::database()->select('node_field_data', 'n');
        $query->fields('n', ['title', 'nid']);
        $query->condition('n.nid', $nid);
        $query->condition('n.status', '1');
        $query->condition('n.type', 'fhir_servers');
        $server_nodes = $query->execute()->fetchAll();
        foreach ($server_nodes as $titles) {
            $node_title[$titles->nid] = $titles->title;
        }
        return $titles->title;
    }

}
/**
 *  function to delete the product for the existing app.
 */
function _unset_products($app_id) {
  $entity = \Drupal::entityTypeManager()->getStorage("developer_app")->load($app_id);
  $credentials = $entity->getCredentials();
  $credential = reset($credentials);
  $products = $credential->getApiProducts();
  foreach($products as $product) {
    $service = new DeveloperAppCredentialController(\Drupal::service('apigee_edge.sdk_connector')
      ->getOrganization(), $entity->getDeveloperId(), $entity->getName(), \Drupal::service('apigee_edge.sdk_connector')
      ->getClient());
    $service->deleteApiProduct($credential->id(),$product->getApiproduct());
  }
}


