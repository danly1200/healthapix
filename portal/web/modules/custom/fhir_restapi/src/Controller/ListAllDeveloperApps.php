<?php

namespace Drupal\fhir_restapi\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class ListAllDeveloperApps class.
 */
class ListAllDeveloperApps extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'fhir_restapi.settings',
    ];
  }

  //const SETTINGS = 'fhir_restapi.settings';
  /*
   * function getAppsList
   *
   * @return string
   * Returns the List of My Apps.
   */
  public function getAppsList() {
    $vname = 'fhir_datasets';
    $response = [];
    $datasets = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadTree($vname, 0, NULL);
    if (!empty($datasets)) {
      foreach ($datasets as $dataset) {
        $dataset_filter[$dataset->tid] = $dataset->name;
      }
    }

    $userId = \Drupal::currentUser()->id();
//    $userDetails = User::load($userId);
//    $developerId = $userDetails->get('apigee_edge_developer_id')->value;
//    $storageDeveloperApp = \Drupal::entityTypeManager()
//      ->getStorage('developer_app');
//    $query = $storageDeveloperApp->getQuery()
//      ->condition('developerId', $developerId);
//    $entityIds = $query->execute();
//    $developerApps = $storageDeveloperApp->loadMultiple($entityIds);
//    $proxyarray = [];
//
//    $i = 0;
//    foreach ($developerApps as $key => $app) {
//      $response[$i]['appName'] = $app->get('name')->value;
//      foreach ($app->getCredentials() as $value) {
//        $response[$i]['consumerKey'] = $value->getConsumerKey();
//        $response[$i]['consumerSecret'] = $value->getConsumerSecret();
//        $proxy = $value->getapiProducts();
//        foreach ($proxy as $keyproxy => $appproxy) {
//          $proxyurl = explode('_', $appproxy->getapiProduct(), 2);
//          if (!empty($proxyurl[1])) {
//            $proxyarray[$key] = 'https://healthapix-demo-test.apigee.net/v1/ghc/' . $proxyurl[1];
//          }
//          break;
//        }
//      }
//      $i++;
//    }
    $service =  \Drupal::service('fhir_restapi.credlist')->appcredlist();
    return [
      '#theme' => 'smart__apps__list',
      '#my_apps' => $service['developerapps'],
      '#user_id' => $userId,
      '#app_proxy' => $service['proxyarray'],
      '#launch_url_array' => $service['launch_url_array'],
      '#dataset_filter' => $dataset_filter,
      '#attached' => [
        'library' => [
          'fhir_restapi/fhir_restapi.myapps',
        ],
      ],
      '#cache' => [
        'contexts' => ['user'],
      ],
    ];
  }
}
