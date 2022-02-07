<?php
/**
 * Provides a apigee developer app list
 *
 * @RestResource(
 *   id = "apigee_cred_resource",
 *   label = @Translation("Apigee app list"),
 *   uri_paths = {
 *     "canonical" = "/fhir_apigee_apps"
 *   }
 * )
 */

namespace Drupal\fhir_apigee_cred_api\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;


class ApigeeCred extends ResourceBase {

  /**
   * Responds to entity GET requests.
   *
   * @return \Drupal\rest\ResourceResponse
   */
  public function get() {
    $userId = \Drupal::currentUser()->id();
    $response = [];
    $apps_response = \Drupal::state()->get('app_cred_resource_'.$userId);
    if ($apps_response) {
      $response = $apps_response;
    }
    else {
      $service_response =  \Drupal::service('fhir_restapi.credlist')->appcredlist();
      $response = \Drupal::state()->get('app_cred_resource_'.$userId);
    }
    $build = [
      '#cache' => [
        'max-age' => 300,
      ],
    ];

    return (new ResourceResponse($response, 200))->addCacheableDependency($build);

  }
}
