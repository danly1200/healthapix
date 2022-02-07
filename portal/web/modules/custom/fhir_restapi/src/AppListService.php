<?php

namespace Drupal\fhir_restapi;

use Drupal\user\Entity\User;


Class AppListService {

  /* appcredlist function fetches all apps, its proxy url and sets state value */
  public function appcredlist() {
    $applist = [];
    $userId = \Drupal::currentUser()->id();
    $userDetails = User::load($userId);
    $developerId = $userDetails->get('apigee_edge_developer_id')->value;
    $storageDeveloperApp = \Drupal::entityTypeManager()
      ->getStorage('developer_app');
    $query = $storageDeveloperApp->getQuery()
      ->condition('developerId', $developerId)
      ->sort('display_name', 'ASC');
    $entityIds = $query->execute();
    $developerApps = $storageDeveloperApp->loadMultiple($entityIds);
    $proxyarray = [];
    $launch_url_array = [];

    $responseNormalApps = [];
    $i = 0;
    $j = 0;

    foreach ($developerApps as $key => $app) {
      $response[$i]['appName'] = $app->get('name')->value;
      if($app->get('field_fhir_app_type')->value == 'normal'){
        $responseNormalApps[$j]['appName'] = $app->get('displayName')->value;
      }
      $fhir_server_id = $app->get('field_fhir_server_id')->value;
      $fhir_server_name = \Drupal::service('fhir_smart.app')
        ->getFhirServerName($fhir_server_id);
      $launch_url = fhir_server_get_launch_base_url($fhir_server_name);

      foreach ($app->getCredentials() as $value) {

        $response[$i]['consumerKey'] = $value->getConsumerKey();
        $response[$i]['consumerSecret'] = $value->getConsumerSecret();
        if($app->get('field_fhir_app_type')->value == 'normal'){
          $responseNormalApps[$j]['consumerKey'] = $value->getConsumerKey();
          $responseNormalApps[$j]['consumerSecret'] = $value->getConsumerSecret();
          $j++;
        }
        $proxy = $value->getapiProducts();


        foreach ($proxy as $keyproxy => $appproxy) {
//          $proxyurl = explode('_', $appproxy->getapiProduct(), 2);
          // remove the suffix '_product' from the api product URL
//          $proxyurl_remove_suffix_product = preg_replace('/_product$/', '', $proxyurl[1]);
//          $version = explode('_', $appproxy->getapiProduct(), 3);

//          if (!empty($proxyurl_remove_suffix_product)) {

            $proxyarray[$key] = $launch_url;
            $parse_url = parse_url($launch_url);
            if($parse_url['scheme'] && $parse_url['host']) {
              $launch_url_base_path = $parse_url['scheme'] ."://". $parse_url['host'] . '/';
            } else {
              $launch_url_base_path = '';
            }

            $launch_url_array[$key] = $launch_url_base_path;
//          }
          break;
        }
      }
      $i++;
    }

    $applist['developerapps'] = $developerApps;
    $applist['proxyarray'] = $proxyarray;
    $applist['launch_url_array'] = $launch_url_array;

    \Drupal::state()->set('app_cred_resource_' . $userId, $responseNormalApps);
    return $applist;

  }
}
