<?php

namespace Drupal\fhir_conformance_report\Controller;

use Drupal\Core\Controller\ControllerBase;
use Http\Client\Exception\RequestException;

/**
* FHIR conformance report controller.
*/
class ConformanceReport extends ControllerBase {

  /**
   * Returns a render-able array for a test page.
   */
  public function content() {
    $conformance_config = $this->config('fhir_conformance_report.settings')
      ->get();
    if (!empty($conformance_config)) {
      foreach ($conformance_config as $v_name => $version_metadata_url) {
        $versions[] = $v_name;
        if (!empty($version_metadata_url)) {

          $state_cache = \Drupal::state()->get($version_metadata_url);
          if($state_cache){
            $response = $state_cache;
          }else{
            $client = \Drupal::httpClient();
            try {
              $response = $client->get($version_metadata_url, [
                'headers' => [
                  'Content-Type' => 'application/hal+json',
                ],
              ]);
              $data = $response->getBody();
            } catch (RequestException $e) {
              watchdog_exception('fhir_conformance_report', $e->getMessage());
            }
            $response = \GuzzleHttp\json_decode($data);
            \Drupal::state()->set($version_metadata_url, $response);
          }
          //$response = \GuzzleHttp\json_decode($data);

          $fhir_version_no = $response->fhirVersion;
          $format = $response->format;
          $conformance_data[$v_name]['fhir_version'] = $fhir_version_no;
          $conformance_data[$v_name]['format'] = $format;
          foreach ($response->rest[0]->resource as $key => $resource) {
            $api_resources[] = $resource->type;
            $interactions = [];
            $search_params = [];
            if (!empty($resource->searchParam)) {
              foreach ($resource->searchParam as $s_val) {
                $search_params[$s_val->name] = $s_val->type;
              }
            }

            foreach ($resource->interaction as $int_val) {
              $interactions[] = $int_val->code;
            }
            $resources_data[$resource->type]['interactions'] = $interactions;
            $resources_data[$resource->type]['search_params'] = $search_params;
          }

          $conformance_data[$v_name]['resources_data'] = $resources_data;
          unset($response);
          unset($resources_data);
          unset($data);

        }
      }
    }

      $resources_list = array_unique($api_resources, SORT_STRING);
      unset($api_resources);
      foreach ($resources_list as $key => $resource_val) {

        foreach ($versions as $vername) {
          foreach ($conformance_data[$vername]['resources_data'] as $resource_name => $res_val) {
            if ($resource_name == $resource_val) {
              foreach ($res_val['search_params'] as $param_name => $param_val) {
                $resource_search_params[$resource_val][$param_name] = $param_val;
              }
            }
          }
        }

      }
      $order_array = ['r4', 'stu3', 'dstu2'];
    uksort($conformance_data, function($key1, $key2) use ($order_array) {
      return (array_search($key1, $order_array) > array_search($key2, $order_array));
    });
    
      return [
        '#theme' => 'conformance__report',
        '#cache' => ['max-age' => 0],
        '#conformance_data' => $conformance_data,
        '#resources_list' => $resources_list,
        '#resource_search_params' => $resource_search_params,
        '#conformance_verbiage' => \Drupal::config('fhir_conformance_report.fhirconformanceverbiage')->get('fhir_conformance_verbiage'),
        '#attached' => [
          'library' => [
            'fhir_conformance_report/fhir_conformance_report.displayData',
          ],
        ],
      ];
  }
}
