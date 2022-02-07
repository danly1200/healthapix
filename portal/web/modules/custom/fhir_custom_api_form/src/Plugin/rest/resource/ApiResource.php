<?php
/**
 * Provides a selected API Resource from fhir custom api form
 *
 * @RestResource(
 *   id = "api_resource",
 *   label = @Translation("Selected API Resource"),
 *   uri_paths = {
 *     "canonical" = "/fhir_get_api/api_resource"
 *   }
 * )
 */

namespace Drupal\fhir_custom_api_form\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;

class ApiResource extends ResourceBase {

  /**
   * Responds to entity GET requests.
   *
   * @return \Drupal\rest\ResourceResponse
   */
  public function get() {
    /**
     * @var  $api_val send selected api value in response
     */
    $api_val = \Drupal::config('fhir_custom_api_form.settings')
      ->get('variable_api_selected');
    return new ResourceResponse($api_val);
  }
}
